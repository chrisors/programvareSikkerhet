<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

<<<<<<< HEAD
    public function __construct($company, $title) {
        return $this->validate($company, $title);
=======
    public function __construct($title, $company, $file, $description)
    {
        return $this->validate($title, $company, $file, $description);
>>>>>>> parent of 7119a4c... File restriction to pdf
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

<<<<<<< HEAD
    public function validate($company, $title)
=======
    private function validate($title, $company, $file, $description)
>>>>>>> parent of 7119a4c... File restriction to pdf
    {
        if ($company == null) {
            $this->validationErrors[] = "Company/User needed";

        }
<<<<<<< HEAD
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }

=======

        //if(empty($file)) {
          //  $this->validationErrors[] = "Please upload correct file in pdf format";
        //}
>>>>>>> parent of 7119a4c... File restriction to pdf
        return $this->validationErrors;
    }


}
