<?php
  /*** class StringMinRules ***/
  
class StringMinRules extends StringRules
{
    /**
     *
     * @the constructor
     *
     */
    public function __construct(array $options('min' => 0))
    {
    	parent::__construct($options);
    }

	public function setMin($min)
	{
		$this->addValidation(new MinValidation($min, $this->message));
		return $this;
	}

} /*** end of class ***/
