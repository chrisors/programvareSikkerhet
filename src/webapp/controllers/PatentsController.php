<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Patent;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\validation\PatentValidation;

class PatentsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

      if ($this->auth->guest()) {
          $this->app->flash("info", "You must be logged in to see all patents");
          $this->app->redirect("/login");
      }

        $patent = $this->patentRepository->all();
        if($patent != null)
        {
            $patent->sortByDate();
        }
        $users = $this->userRepository->all();
        $this->render('patents/index.twig', ['patent' => $patent, 'users' => $users]);
    }

    public function search()
    {
      $request = $this->app->request;
      $company = $request->post('patentsSearch');
      $title = $company;
      $patent = $this->patentRepository->all();
      $users = $this->userRepository->all();
      if (preg_match("/([A-Za-z0-9]+)/", $company))
      {
        $searchQuery = $this->patentRepository->searchPatents($company, $title);
      }else{
        $searchQuery = "";
      }

      $this->render('patents/index.twig', [
          'patent' => $patent,
          'user' => $user,
          'search' => $searchQuery
      ]);
    }

    public function show($patentId)
    {
        $patent = $this->patentRepository->find($patentId);
        $username = $_SESSION['user'];
        $user = $this->userRepository->findByUser($username);
        $request = $this->app->request;
        $message = $request->get('msg');
        $variables = [];

        if($message) {
            $variables['msg'] = $message;

        }

        $this->render('patents/show.twig', [
            'patent' => $patent,
            'user' => $user,
            'flash' => $variables
        ]);

    }

    public function newpatent()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            $this->render('patents/new.twig', ['username' => $username]);
        } else {

            $this->app->flash('error', "You need to be logged in to register a patent");
            $this->app->redirect("/");
        }

    }
    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged on to register a patent");
            $this->app->redirect("/login");
        } else {
            $request     = $this->app->request;
            $title       = $request->post('title');
            $company     = $request->post('company');
            $description = $request->post('description');
            $date        = date("dmY");
            $file        = $_FILES['uploaded']['name'];



          $validation = new PatentValidation($company, $title, $description, $file);

            if ($validation->isGoodToGo()) {
                  $file = $this -> startUpload();
                $patent = new Patent($company, $title, $file, $description, $date);
                $savedPatent = $this->patentRepository->save($patent);
                $this->app->redirect('/patents/' . $savedPatent . '?msg="Patent succesfully registered');
            }
        }
            $errors = join("<br>\n", $validation->getValidationErrors());
            $this->app->flashNow('error', $errors);
            $this->app->render('patents/new.twig');
    }

    public function startUpload()
    {
        if(isset($_POST['submit']))
        {
            $target_dir =  getcwd()."/web/uploads/";
            $targetFile = $target_dir . basename($_FILES['uploaded']['name']);
            if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $targetFile))
            {
                return $targetFile;
            }
        }
    }

}
