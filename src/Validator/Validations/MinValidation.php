<?php
	namespace Library\Validator\Validations;
  /*** class MinValidation ***/
  
class MinValidation extends AbstractValidation
{

	protected $min;

    public function __construct($errormsg=null, $min = 1)
    {
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit avoir minimum %2$d caractères';
		}
    	parent::__construct($errormsg);
    	$this->setMin($min);
    }

	public function parseParam($param) {
		if (is_string($param)) {
			list($min, $message) = array_pad(explode(',', $param), 2, '');
			if (!empty($message))
				$this->setErrorMsg($message);
			if (!empty($min))
				$this->setMin($min);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->min];
	}

    public function isValid($var)
    {
    	if(is_numeric($var))
    		return $this->checkNumeric($var);
    	else if(is_string($var))
    		return $this->checkString($var);
    	else if(is_int($var))
    		return $this->checkInt($var);
    	else if(is_float($var))
    		return $this->checkFloat($var);
    }
    
    protected function checkString($var)
    {
		$check = true;
        if(strlen($var) < $this->min)
        	$check = false;
        	
        return $check;
    }    
    
    protected function checkInt($var)
    {
		$check = true;
        if($var < $this->min)
        	$check = false;
        	
        return $check;
    }    

    protected function checkFloat($var)
    {
		$check = true;
        if($var < $this->min)
        	$check = false;
        	
        return $check;
    }    

    protected function checkNumeric($var)
    {
		$check = true;
		
		if($val = $this->get_numeric($var) !== null) {
        	if($val < $this->min)
        		$check = false;
        }
        return $check;
    }    

	public function setMin($min = 1)
	{
		$this->min = $this->get_numeric($min);
		//lancer une exception si min === null;
		if($this->min === null || $this->min < 0) {
			throw new InvalidArgumentException('Argument invalide, $min doit être de type numeric plus grand ou égal a 0 ex: 2 ou \'2\'');
		}
	}

	protected function get_numeric($val)
	{
		if (is_numeric($val)) {
			return $val + 0;
		}
		return null;
	} 	
}
