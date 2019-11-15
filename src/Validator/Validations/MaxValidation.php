<?php
	namespace Library\Validator\Validations;
  /*** class MaxValidation ***/
  
class MaxValidation extends AbstractValidation
{

	protected $max;

    public function __construct($errormsg=null, $max=255)
    {
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit avoir maximum %2$d caractères';
		}
    	parent::__construct($errormsg);
    	$this->setMax($max);
    }
    
    public function isValid($var)
    {
		//if (empty($var)) return false;
    	if(is_numeric($var))
    		return $this->checkNumeric($var);
    	else if(is_string($var))
    		return $this->checkString($var);
    	else if(is_int($var))
    		return $this->checkInt($var);
    	else if(is_float($var))
    		return $this->checkFloat($var);
    }

	public function parseParam($param) {
		if (is_string($param)) {
			list($max, $message) = array_pad(explode(',', $param), 2, '');
			if (!empty($max))
				$this->setMax($max);
			if (!empty($message))
				$this->setErrorMsg($message);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->max];
	}

    protected function checkString($var)
    {
		$len = strlen($var);
        if ($len > $this->max)
        	return false;
        	
        return true;
    }    
    
    protected function checkInt($var)
    {
        if ($var > $this->max)
        	return false;
        	
        return true;
    }    

    protected function checkFloat($var)
    {
        if ($var > $this->max)
        	return false;
        	
        return true;
    }    

    protected function checkNumeric($var)
    {
		if (($val = $this->get_numeric($var)) !== null) {
        	if($val > $this->max)
        		return false;
        }
        return true;
    }    

	public function setMax($max = 255)
	{
		$this->max = $this->get_numeric($max);
		//lancer une exception si max === null;
		if ($this->max === null || $this->max <= 0) {
			throw new InvalidArgumentException('Argument invalide, $max doit être de type numeric  plus grand que 0 ex: 256 ou \'256\'');
		}
		return $this;
	}

	protected function get_numeric($val)
	{
		if (is_numeric($val)) {
			return $val + 0;
		}
		return null;
	} 	
}
