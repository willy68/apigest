<?php
	namespace Library\Form;

	abstract class Field extends \Library\Html\HtmlElement
	{
		protected $errorMessage;
		protected $error;
		protected $onError;
		protected $rules;
		protected $label;
		protected $name;
		protected $validationRules = null;
		protected $disabled;

		public function render()
		{
			$widget = '';

			if (!empty($this->openHTML))
				$widget .= $this->openHTML;
		
			$widget .= $this->buildWidget();

			if (!empty($this->error))
			{
				$widget .= $this->error();
			}

			if (!empty($this->closeHTML))
				$widget .= $this->closeHTML;

			return $widget;
		}

		public function isValid()
		{
			if (!empty($this->rules)) {
				$this->validationRules = new \Library\Validator\ValidationRules($this->rules, $this->name);
				if (!$this->validationRules->isValid($this->value)) {
					//$this->errorMessage = $this->validationRules->getFirstError();
					if ($this->onError instanceof \Closure) {
						$closure = $this->onError;
						$closure();
					}
					return false;
				}
			}
			return true;
		}

		// Getters
		public function disabled()
		{
			if (array_key_exists('disabled', $this->attributs))
				return $this->attributs['disabled'];

			return null;
		}

		public function label()
		{
			return $this->label;
		}

		public function name()
		{
			return $this->name;
		}

		public function error()
		{
			return sprintf($this->error, $this->firstError());
		}

		public function errors()
		{
			if ($this->validationRules !== null) {
				return $this->validationRules->getErrorMsg(true);
			}
			return [];
		}

		public function firstError()
		{
			if ($this->validationRules !== null) {
				return $this->validationRules->getFirstError();
			}
			return '';
		}

		//Setters
		
		public function setError($msg)
		{
			if (is_string($msg))
				$this->error = $msg;
			return $this;
		}

		public function setDisabled($disabled)
		{
			if ($disabled)
			{
				$this->attributs['disabled'] = 'disabled';
			}
			return $this;
		}

		public function setLabel(array $label)
		{
			if (is_array($label) && !empty($label))
			{
				$this->label = new \Library\Html\Label($label);
			}
			return $this;
		}

		public function setName($name)
		{
			if (is_string($name))
			{
				$this->name = $name;
				if (!array_key_exists('id', $this->attributs)) {
					$this->attributs['id'] = $name;
				}
			}
			return $this;
		}

		public function setOnError(\Closure $closure)
		{
			$this->onError = $closure->bindTo($this, $this);
			return $this;
		}

		public function setRules($rules)
		{
			if (is_string($rules)) {
				$this->rules = $rules;
				//$this->validationRules = new \Library\Validator\ValidationRules($rules, $this->name);
			}
		}
	}

