<?php
	namespace Library\Validator\Validations;
  /*** class RequiredValidation ***/
  
class RequiredValidation extends AbstractValidation
{

    public function __construct($errormsg =  'Le champ %1$s est obligatoire')
    {
    	parent::__construct($errormsg);
    }
    
    public function isValid($var)
    {
    	return $this->is_set($var);
    }
    
    /**
     *
     * @Check if POST variable is set
     *
     * @access private
     *
     * @param string $var The POST variable to check
     *
     */
    protected function is_set($var)
    {
		$check = true;
        if(!isset($var))
			$check = false;
		else if(is_array($var))
			$check = !empty($var);
        else if(is_string($var)) // une chaine constituée que d'espaces est considérée comme vide!
        	$check = (bool) strlen(trim($var));
        else
        	$check = !empty($var); // autre type de variable
        	
        return $check;
    }    
    

}
