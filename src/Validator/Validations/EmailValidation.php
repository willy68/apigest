<?php
	namespace Library\Validator\Validations;
  /*** class EmailValidation ***/
  
class EmailValidation extends AbstractValidation
{

    public function __construct($errormsg=null)
    {
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit être une adresse E-mail valide.';
		}
    	parent::__construct($errormsg);
    }

    public function isValid($var)
    {
    	return $this->checkEmail($var);
    }

    protected function checkEmail($var)
    {
    	if (is_string($var))
    	{
	        if (filter_var($var, FILTER_VALIDATE_EMAIL) !== false)
	        {
				return true;
	        }
         }
        return false;
    }
    
}
