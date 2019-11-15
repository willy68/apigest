<?php
  /*** class NumericMinRules ***/
  
class NumericMinRules extends NumericRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('message' => 'Ce nombre doit être superieur à', 'min' => 0))
    {
    	parent::__construct($options);
    }

	public function setMin($min)
	{
		$this->addValidation(new MinValidation($min, $this->message));
		return $this;
	}

	/*
	* return $var after sanitize
	*/	
    public function sanitize($var)
    {
        return (int) filter_var($var, FILTER_SANITIZE_NUMBER_INT);
    }
    
} /*** end of class ***/
