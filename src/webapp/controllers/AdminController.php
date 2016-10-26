<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    private function checkifAdmin()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
            return;
        }
        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
            return;
        }
    }

    public function index()
    {
        $this->checkifAdmin();

        $variables = [
            'users' => $this->userRepository->all(),
            'patent' => $this->patentRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function destroypatent($patentId)
    {
        $this->checkifAdmin();

        if ($this->patentRepository->deleteByPatentid($patentId) === 1) {
            $this->app->flash('info', "Successfully deleted '$patentId'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error occurred. Unable to delete patent '$patentId'.");
        $this->app->redirect('/admin');
    }

    public function destroyuser($username)
    {
        $this->checkifAdmin();

        if ($this->userRepository->deleteByUsername($username) === 1) {
            $this->app->flash('info', "Successfully deleted '$username'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error occurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }
}
