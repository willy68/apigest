<?php
	namespace Library\Validator\Validations;
  /*** class DateValidation ***/
  
class DateValidation extends AbstractValidation
{

	protected $format;

    public function __construct($errormsg = null, $format = 'd/m/Y')
    {
		if ($errormsg === null) {
			$errormsg = 'Date invalide, format valide: %2$s';
		}
    	parent::__construct($errormsg);
    	$this->setFormat($format);
    }
    
    public function isValid($var)
    {
    	return $this->checkDate($var);
    }

	public function parseParam($param) {
		if (is_string($param)) {
			list($format, $message) = array_pad(explode(',', $param), 2, '');
			if (!empty($message))
				$this->setErrorMsg($message);
			if (!empty($format)
				$this->setFormat($format);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->format];
	}

    protected function checkDate($var)
    {
    	$date = \DateTime::createFromFormat($this->format, $var);
	    if(!$date)
		    return false;

	    return true;
    }
    
	public function setFormat($format)
	{
		if (is_string($format))
			$this->format = $format;
	}

}
