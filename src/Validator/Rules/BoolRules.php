<?php
  /*** class StringRules ***/
  
class BoolRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Champ invalide'))
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
	        if(filter_var($var, FILTER_VALIDATE_BOOLEAN) === false)
	        {
				$this->errormsg = $this->message;
				return false;
	        }
            return true;
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
    
} /*** end of class ***/
