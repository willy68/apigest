<?php
	namespace Library\Validator\Validations;
  /*** class AbstractValidation ***/
  
abstract class AbstractValidation
{

    /*
    * @the error message
    */
    protected $errormsg = '';

    public function __construct($errorMsg=null)
    {
    	$this->setErrorMsg($errorMsg);
    }

	public function hydrate(array $options)
	{

		foreach ($options as $key => $value)
		{
        	$method = 'set'.ucfirst($key);
                
            if (is_callable(array($this, $method)))
            {
            	$this->$method($value);
            }
		}
	}

	abstract public function isValid($var);
	
	public function parseParam($param) 
	{
		if (is_string($param)) {
			list($message) = array_pad(explode(',', $param), 1, '');
			if (!empty($message))
				$this->setErrorMsg($message);
		}
	}
	
	public function getParamAsArray()
	{
		return [];
	}

	public function getErrorMsg()
	{
		return $this->errormsg;
	}
	
	public function getErrorMessage($fieldName = null, $value = null)
	{
		$param = [];
		$param = $this->getParamAsArray();
		if ($fieldName !== null) {
			array_unshift($param, $fieldName);
		}
		if ($value !== null) {
			array_push($param, $value);
		}
		return vsprintf($this->errormsg, $param);
	}
	
	public function setErrorMsg($errormsg)
	{
		if(is_string($errormsg))
			$this->errormsg = $errormsg;
		return $this;
	}
}
