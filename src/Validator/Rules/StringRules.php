<?php
	namespace Library\Validator\Rules;
  /*** class StringRules ***/
  
class StringRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options = array('message' => 'Chaine invalide'))
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
            if(!is_string($var))
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
    	return (string) filter_var($var, FILTER_SANITIZE_STRING);
    }
    
} /*** end of class ***/
