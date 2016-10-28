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
      $company = $request->post('patentsSearch'); //this work
      $title = $company;
      $patent = $this->patentRepository->all();
      $users = $this->userRepository->all();

      $searchQuery = $this->patentRepository->searchPatents($company, $title);
//      $test = $this->patentRepository->find($company);

//      echo '<pre>'; print_r($test); echo '</pre>';
//      echo '<pre>'; print_r($searchQuery); echo '</pre>';

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
            $description = $request->post('description');
            $company     = $request->post('company');
            $date        = date("dmY");

            $validation = new PatentValidation($company, $title, $description);

          if ($validation->isGoodToGo()) {

            if(isset($_POST['submit']))
            {
                $target_dir =  getcwd()."/web/uploads/";
                $tarsearchPatentsgetFile = $target_dir . basename($_FILES['uploaded']['name']);
                $ext = pathinfo($targetFile, PATHINFO_EXTENSION);

                if ($ext === 'pdf' || empty($_FILES['uploaded']['name'])) {

                  if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $targetFile))
                  {

                    $patent = new Patent($company, $title, $description, $date, $file);
                    $savedPatent = $this->patentRepository->save($patent);
                    $this->app->redirect('/patents/' . $savedPatent . '?msg="Patent succesfully registered');
                    return $targetFile;

                  }

                  $patent = new Patent($company, $title, $description, $date, $file);
                  $savedPatent = $this->patentRepository->save($patent);
                  $this->app->redirect('/patents/' . $savedPatent . '?msg="Patent succesfully registered');
                }else {
                  $this->app->flashNow('error', 'PDF files only');
                  $this->app->render('patents/new.twig');
                }
            }
          }else {
            $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
            $this->app->render('patents/new.twig');
          }
        }
    }
    
}
