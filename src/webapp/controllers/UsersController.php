<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Phone;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;

class UsersController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function show($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");

        } else {
            $user = $this->userRepository->findByUser($username);

            if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

                $this->render('users/showExtended.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            } else if ($this->auth->check()) {

                $this->render('users/show.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            }
        }
    }

    public function newuser()
    {
        if ($this->auth->guest()) {
            return $this->render('users/new.twig', []);
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request   = $this->app->request;
        $username  = $request->post('user');
        $password  = $request->post('pass');
        $firstName = $request->post('first_name');
        $lastName  = $request->post('last_name');
        $phone     = $request->post('phone');
        $company   = $request->post('company');

        $token     = $request->post('token');

        $validation = new RegistrationFormValidation($username, $password, $firstName, $lastName, $phone, $company);

        if(!$this->token->validate($token)){
          $this->app->flashNow('info', 'An error has occurred');
          $this->render('users/new.twig', ['user' => $user]);
          return;
        }

        if ($validation->isGoodToGo()) {
            $password = $password;
            $password = $this->hash->make($password);
            $user = new User($username, $password, $firstName, $lastName, $phone, $company);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = join("<br>\n", $validation->getValidationErrors());
        $this->app->flashNow('error', $errors);
        $this->render('users/new.twig', ['username' => $username]);
    }

    public function edit()
    {
        $this->makeSureUserIsAuthenticated();

        $this->render('users/edit.twig', [
            'user' => $this->auth->user()
        ]);
    }

    public function update()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request    = $this->app->request;
        $email      = $request->post('email');
        $firstName  = $request->post('first_name');
        $lastName   = $request->post('last_name');
        $phone      = $request->post('phone');
        $company    = $request->post('company');
        $token      = $request->post('token');

        if(!$this->token->validate($token)){
          $this->app->flashNow('info', 'An error has occurred');
          return $this->render('users/edit.twig', ['user' => $user]);
        }

        $validation = new EditUserFormValidation($email, $phone, $company);

        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setCompany($company);
            $user->setPhone(new Phone($phone));
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $this->userRepository->save($user);

            $this->app->flashNow('info', 'Your profile was successfully saved.');
            return $this->render('users/edit.twig', ['user' => $user]);
        }

        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        $this->render('users/edit.twig', ['user' => $user]);
    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }

    public function all()
    {
        $this->render('users.twig', [
          'users' => $this->userRepository->all()
        ]);
    }
}
