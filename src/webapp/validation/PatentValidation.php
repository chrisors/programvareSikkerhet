<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

    public function __construct($company, $title, $description)
    {
        return $this->validate($company, $title, $description);
    }

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
        if(empty($title)) {
            $this->validationErrors[] = "Title needed";
        }

        if(empty($company)) {
            $this->validationErrors[] = "Company/User needed";

        }

        //if(empty($description) && (!preg_match('/^[A-Za-z0-9_]+$/',$description))) {
          //  $this->validationErrors[] = "Description needed, can only contain letters and numbers";
        //}
    }
}
