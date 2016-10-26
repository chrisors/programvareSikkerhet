<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

    public function __construct($title, $company, $file, $description)
    {
        return $this->validate($title, $company, $file, $description);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($title, $company, $file, $description)
    {
        if(!isset($description) || trim($description) == "") {
            $this->validationErrors[] = "Description needed, can only contain letters and numbers";
        }

        if(empty($title)) {
            $this->validationErrors[] = "Title needed";
        }

        if(empty($company)) {
            $this->validationErrors[] = "Company/User needed";

        }

        //if(empty($file)) {
          //  $this->validationErrors[] = "Please upload correct file in pdf format";
        //}
        return $this->validationErrors;
    }
}
