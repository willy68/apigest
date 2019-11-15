<?php
  /*** class StringRules ***/
  
class Ipv6Rules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Adresse IP invalide'))
    {
    	parent::__construct($options);
    }

	/*
	* return true ou false
	*/	
	public function validate($var)
	{
        if($this->is_set($var))
        {
	        if(filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
	        {
				$this->errormsg = $this->message;
				return false;
	        }
            return true;
        }
        return false,;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
        return $var;
    }
    
} /*** end of class ***/
