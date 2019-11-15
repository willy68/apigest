<?php
  /*** class StringRules ***/
  
class EmailRules extends AbstractRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Email invalide'))
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
	        if(filter_var($var, FILTER_VALIDATE_EMAIL) === false)
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
        $email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $var );
        return (string) filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
} /*** end of class ***/
