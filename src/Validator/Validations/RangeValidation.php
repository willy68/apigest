<?php
	namespace Library\Validator\Validations;
  /*** class RangeValidation ***/
  
class RangeValidation extends AbstractValidation
{

	protected $min;
	protected $max;

	public function __construct($errormsg=null, $min=1, $max=255)
	{
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit avoir entre %2$d et %3$d caractères';
		}
		parent::__construct($errormsg);
		$this->setMin($min);
		$this->setMax($max);
	}

	public function parseParam($param) {
		if (is_string($param)) {
			list($min, $max, $message) = array_pad(explode(',', $param), 3, '');
			if (!empty($min))
				$this->setMin($min);
			if (!empty($max))
				$this->setMax($max);
			if (!empty($message))
				$this->setErrorMsg($message);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->min, $this->max];
	}

	public function isValid($var)
	{
		if (!isset($var)) return false;
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
		$len = strlen($var);
		if ($len < $this->min || $len > $this->max)
			return false;
			
		return true;
	}    

	protected function checkInt($var)
	{
		if ($val < $this->min || $var > $this->max)
			return false;
			
		return true;
	}    

	protected function checkFloat($var)
	{
		if ($val < $this->min || $var > $this->max)
			return false;
			
		return true;
	}    

	protected function checkNumeric($var)
	{
		if (($val = $this->get_numeric($var)) !== null) {
			if($val < $this->min || $val > $this->max)
				return false;
		}
		return true;
	}    

	public function setMax($max = null)
	{
		$this->max = $this->get_numeric($max);
		//lancer une exception si max === null;
		if ($this->max === null || $this->max <= 0) {
			throw new \InvalidArgumentException('Argument invalide, $max doit être de type numeric  plus grand que 0 ex: 256 ou \'256\'');
		}
	}

	public function setMin($min = null)
	{
		$this->min = $this->get_numeric($min);
		//lancer une exception si min === null;
		if($this->min === null || $this->min < 0) {
			throw new \InvalidArgumentException('Argument invalide, $min doit être de type numeric plus grand ou égal a 0 ex: 2 ou \'2\'');
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
