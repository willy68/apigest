<?php
	namespace Library\Validator\Rules;

  /*** class StringMaxRules ***/
  
class StringMaxRules extends StringRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct($options = array('max' => PHP_INT_MAX))
    {
    	parent::__construct($options);
    }
    
	public function setMax($max)
	{
		$this->addValidation(new \Library\Validator\Validation\MaxValidation($max, $this->message));
		return $this;
	}    

} /*** end of class ***/
