<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class SessionsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function newSession()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('sessions/new.twig', []);
    }

    public function create()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');
        $token   = $request->post('token');
        $bad_login_limit = 4;
        $lockout_time = 600;
        $failed_logins = $this->userRepository->readLoginAttempts($user);
        $first_failed_login = $this->userRepository->readFirstFailedLogin($user);

        if(($failed_logins >= $bad_login_limit) && (time() - $first_failed_login < $lockout_time)) {
            $this->app->flashNow('error', "You are currently locked out for 10min");
        } else {

          if( time() - $first_failed_login > $lockout_time ) {
            // first unsuccessful login since $lockout_time on the last one expired
            $first_failed_login = time(); // commit to DB
            $failed_logins = 1; // commit to db
            $this->userRepository->updateLoginAttempts($failed_logins, $first_failed_login, $user);
            $this->app->flashNow('error', "Incorrect user/pass for $user, $bad_login_limit attempts left");
          } else {
            $failed_logins++; // commit to db.
            $attempts_left = ($bad_login_limit + 1) - $failed_logins;
            $first_failed_login = time(); // commit to DB
            $this->userRepository->updateLoginAttempts($failed_logins, $first_failed_login, $user);
            $this->app->flashNow('error', "Incorrect user/pass for $user, $attempts_left attempts left");
          }
          if ($this->auth->checkCredentials($user, $pass) && $this->token->validate($token)) {
              //both username/password and token must match
              $_SESSION['user'] = $user;
          /*  setcookie("user", $user);
              setcookie("password",  $pass);
              $isAdmin = $this->auth->user()->isAdmin();

              if ($isAdmin) {
                setcookie("isadmin", "yes");
              } else {
                  setcookie("isadmin", "no");
              }
          */
              $this->app->flash('info', "You are now successfully logged in as $user.");
              $this->app->redirect('/');
              return;
          }

        }
        $this->render('sessions/new.twig', []);
    }

    public function destroy()
    {
        $this->auth->logout();
        $this->app->redirect('/');
//        $this->app->redirect('http://www.ntnu.no/');
    }
}
