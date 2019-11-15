<?php
	namespace Library\Validator\Validations;
  /*** class ConfirmEmailValidation ***/
  
class EmailConfirmValidation extends EmailValidation
{

	protected $fieldName;
	protected $form;

    public function __construct($errormsg=null, $fieldName=null, $form=null)
    {
		if ($errormsg === null) {
			$errormsg = 'Le champ %1$s doit être un E-mail identique avec le champ %2$s';
		}
    	parent::__construct($errormsg);
    	$this->setFieldName($fieldName);
    	if ($form !== null)
    		$this->setForm($form);
    }
	
    public function isValid($var)
    {
    	if ($this->checkField($var)) {
    		return parent::isValid($var);
    	}
    	else {
    		return false;
    	}
    }

	public function parseParam($param) {
		if (is_string($param)) {
			list($fieldName, $message) = array_pad(explode(',', $param), 2, '');
			if (!empty($message))
				$this->setErrorMsg($message);
			if (!empty($fieldName))
				$this->setFieldName($fieldName);
		}
		return $this;
	}

	public function getParamAsArray()
	{
		return [$this->fieldName];
	}

    protected function checkField($var)
    {
    	if (is_string($var))
    	{
    		if (!empty($this->form)) {
				if (($field = $this->form->getField($this->fieldName)) !== null)
				{
					return $field->value() == $var;
				}
			}
			elseif (isset($_POST[$this->fieldName])) {
				return $_POST[$this->fieldName] === $var;
			}

        }
        return false;
    }

	public function setFieldName($fieldName)
	{
		if (is_string($fieldName))
			$this->fieldName = $fieldName;
	}
	
	public function setForm(\Library\Form\Form $form)
	{
		if ($form instanceof \Library\Form\Form)
		{
			$this->form = $form;
		}
	}
    
}
