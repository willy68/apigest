<?php
  /*** class DateRules ***/
  
class DateRules extends AbstractRules
{

	protected $regex = "/^((((19|20)(([02468][048])|([13579][26]))-02-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0[1-9])|(1[0-2]))-((0[1-9])|(1\d)|(2[0-8])))|((((0[13578])|(1[02]))-31)|(((0[1,3-9])|(1[0-2]))-(29|30)))))$/";
	
	protected $format = '';
	
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options = array('format' => 'Y-m-d', 'message' => 'Date invalide')) 
    {
    	parent::__construct($options);
    }

	public function setFormat($format)
	{
		if(is_string($format))
			$this->format = $format;
		return $this;
	}
	/*
	* return true ou false
	*/	
	public function validate($var)
	{
        if($this->is_set($var))
        {
	    	return $this->checkDate($this->format, $var);
	    }
	    return false;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
    	return $var;
    }
    
    /*
    *
    * chechDate from format default 'Y-m-d'
    *
    * param $var variable date to check
    *
    * param $format format of $var
    */
    protected function checkDate($format, $var)
    {
    	$date = DateTime::createFromFormat($format, $var);
	    if(!$date)
	    {
			$this->errormsg = $this->message;
		    return false;
	    }
	    return true;
    }
    
} /*** end of class ***/
