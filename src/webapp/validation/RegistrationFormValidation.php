<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation
{
    //const MIN_USER_LENGTH = 3;

    private $validationErrors = [];

    public function __construct($username, $password, $first_name, $last_name, $phone, $company)
    {
        return $this->validate($username, $password, $first_name, $last_name, $phone, $company);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $first_name, $last_name, $phone, $company)
    {
        if(!preg_match('/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,25}$/', $password)) {
            $this->validationErrors[] = 'Wrong password combination';
        }

        if(empty($first_name)) {
            $this->validationErrors[] = "Please write in your first name";
        }

         if(empty($last_name)) {
            $this->validationErrors[] = "Please write in your last name";
        }

        if(empty($phone)) {
            $this->validationErrors[] = "Please write in your phone number";
        }

        if(strlen($phone) != "8") {
            $this->validationErrors[] = "Phone number must be exactly eight digits";
        }

        if(empty($company)) {
            $this->validationErrors[] = "Please write in company name";
        }

        if(!preg_match('/^[A-Za-z0-9_]+$/',$company)) {
            $this->validationErrors[] = "Company name can only contain letters and numbers";
        }

        if(!preg_match('/^[A-Za-z0-9_]+$/',$username)) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
    }
}
