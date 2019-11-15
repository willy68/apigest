<?php
	namespace Library\Validator\Validations;
  /*** class NumericValidation ***/
  
class NumericValidation extends AbstractValidation
{

	protected $min;
	protected $max;

    public function __construct($errormsg=null, $min = 1, $max = 255)
    {
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit être entre %2$d et %3$d';
		}
    	parent::__construct($errormsg);
    	$this->setMin($min);
    	$this->setMax($max);
    }
    
    public function isValid($var)
    {
    	return $this->checkNumeric($var);
    }

	public function parseParam($param) {
		if (is_string($param)) {
			list($min, $max, $message) = array_pad(explode(',', $param), 3, '');
			if (!empty($message))
				$this->setErrorMsg($message);
			if (!empty($min))
				$this->setMin($min);
			if (!empty($max))
				$this->setMax($max);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->min, $this->max];
	}

    protected function checkNumeric($var)
    {
		if (($val = $this->get_numeric($var)) !== null) {
			$options = array();
        	if (!empty($this->min))
        		$options['options']['min_range'] = $this->min;
        	if (!empty($this->max))
        		$options['options']['max_range'] = $this->max;
        		
        	if (($val = filter_var($val, FILTER_VALIDATE_INT, $options)) !== false)
        		return true;
        }
        return false;
    }    

	public function setMin($min = 1)
	{
		$this->min = $this->get_numeric($min);
	}

	public function setMax($max = 255)
	{
		$this->max = $this->get_numeric($max);
	}

	protected function get_numeric($val)
	{
		if (is_numeric($val)) {
			return $val + 0;
		}
		return null;
	} 	
}
