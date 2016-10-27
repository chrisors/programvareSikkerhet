<?php

namespace tdt4237\webapp\validation;

class EditUserFormValidation
{
    private $validationErrors = [];

    public function __construct($email, $phone, $company)
    {
        $this->validate($email, $phone, $company);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($email, $phone, $company)
    {
        $this->validateEmail($email);
        $this->validatePhone($phone);
        $this->validateCompany($company);
    }

    private function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }

    private function validatePhone($phone)
    {
        if (!is_numeric($phone) or strlen($phone) !="8") {
            $this->validationErrors[] = 'Phone must be eight digits';
        }
    }

    private function validateCompany($company)
    {
      if(empty($company) or strlen($company) > 10000) {
          $this->validationErrors[] = "Please write in company name";
      }

    }
}
