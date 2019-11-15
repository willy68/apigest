<?php
  /*** class StringRangeRules ***/
  
class StringRangeRules extends StringRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('min' => 0, 'max' => PHP_INT_MAX))
    {
    	parent::__construct($options);
    }

	public function setMin($min)
	{
		$this->addValidation(new MinValidation($min, $this->message));
		return $this;
	}

	public function setMax($max)
	{
		$this->addValidation(new MaxValidation($max, $this->message));
		return $this;
	}

} /*** end of class ***/
