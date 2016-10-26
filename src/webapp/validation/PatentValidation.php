<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Patent;

class PatentValidation {

    private $validationErrors = [];

    public function __construct($company, $title) {
        return $this->validate($company, $title);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    private function validate($company, $title)
    {
        if (empty($company)) {
            $this->validationErrors[] = "Company/User needed";

        }
        if (empty($title)) {
            $this->validationErrors[] = "Title needed";
        }

        return $this->validationErrors;
    }


}
