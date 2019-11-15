<?php
namespace Library\Validator;

/*
									//Validation class name => Without args array for class validation
	$options sous la forme: array( 'RequiredValidation',
									//Validation class name => Some args array for class validation
								   'MaxValidation' => array( 'errorMsg' => 'Error message', 'max' => 50)) 
									 


*/

class Validation
{

    /*
    * @the error message
    */
    protected $errormsg = array();

	protected $validationRules = array();

    public function __construct($options = array())
    {
	
		if (!empty($options))
		{
			$this->setRules($options);
		}
    }
    
	public function setRules(array $options)
	{
		foreach ($options as $key => $value)
		{
			//If $key is int, $value is the class name validation without param
            if ($key === (int)$key)
			{
				$this->validationRules[ucfirst($value)] = array();
			}
			//$key is associative string validation class name is in $key and array param is the value
            elseif ($key === (string) $key && is_array($value))
            {
            	$this->validationRules[ucfirst($key)] = $value;
            }
		}
		return $this;
	}

    public function isValid($var)
	{
		$valid = true;

		foreach ($this->validationRules as $key => $val)
		{
			$className = '\\Library\\Validator\\Validations\\'. $key;
			$validation = new $className();
			if (!empty($val))
			{
				$validation->hydrate($val);
			}
			if (!$validation->isValid($var))
			{
				$this->setErrorMsg($validation->getErrorMsg());
				$valid = false;
			}
		}	
		return $valid;
	}    

	public function getErrorMsg($asArray = false)
	{
		if (!$asArray)
		{
			$error = '';
			$i = 0;
			foreach ($this->errormsg as $msg)
			{
				$error .= ($i > 0)? "\n\t" : ''.$msg;
				$i++;
			}
			return $error;
		}
		else
		return $this->errormsg;
	}
	
	public function setErrorMsg($errormsg)
	{
		if(is_string($errormsg))
			$this->errormsg[] = $errormsg;
		
		return $this;
	}
}
