<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

    public function __construct($company, $title, $description)
    {
        return $this->validate($company, $title, $description);
    }

  //  public function fileError($file)
    //{
      //  return $this->validationErrors[] = "Please upload correct file in pdf format";
  //  }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($company, $title, $description)
    {
        if(!isset($description) || trim($description) == "") {
            $this->validationErrors[] = "Description needed, can only contain letters and numbers";
        }

        if(empty($title) or strlen($title) > 25) {
            $this->validationErrors[] = "Title needed";
        }

        if(empty($company) or strlen($company) > 25) {
            $this->validationErrors[] = "Company/User needed";

        }

      //  if(empty($file) ) {
        //  $this->validationErrors[] = "PDF file needed";
        //}

        return $this->validationErrors;
    }

}
