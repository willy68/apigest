<?php
	namespace Library\Form;

	class Form extends \Library\Html\HtmlElement
	{
		protected $fields = array();
		protected $source = array();
		protected $rules  = array();
		protected $errors = array();

		public function __construct(array $options = null, array $source = null)
		{
			parent::__construct($options);

			if (!array_key_exists('method', $this->attributs)) $this->attributs['method'] = 'POST';
			if (!array_key_exists('action', $this->attributs)) $this->attributs['action'] = '';
			if (!array_key_exists('accept-charset', $this->attributs)) $this->attributs['accept-charset'] = 'UTF-8';

			if ($source != null)
				$this->setSource($source);
		}

		public function addField(Field $field)
		{
			$attr = $field->name(); // On récupère le nom du champ
			if (!empty($this->source))
			{
				if (array_key_exists($attr,$this->source))
					$field->setValue($this->source[$attr]);
			}

			$this->fields[] = $field; // On ajoute le champ passé en argument à la liste des champs

			$this->setFieldRules($field);

			return $this;
		}

		public function addFields(array $fields)
		{
			if (is_array($fields) && !empty($fields)) {
				foreach ($fields as $fieldName => $options) {
					list($className, $name, $value) = array_pad(explode(':', $fieldName), 3, '');
					if (!empty($className)) {
						$fieldClass = '\\Library\\Form\\'.ucfirst($className);
						if (!empty($name))
							$options['name'] = $name;
						if (!empty($value))
							$options['value'] = $value;
						$this->addField(new $fieldClass($options));
					}
				}
			}
			return $this;
		}

		public function setSource(array $source)
		{
			if(is_array($source) && !empty($source))
			{
				$this->source = $source;
				if (!empty($this->fields))
					$this->hydrateFields($this->source);
			}
			return $this;
		}

		public function hydrateFields(array $source)
		{
			foreach ($this->fields as $field)
			{
				$attr = $field->name();
				if (array_key_exists($attr,$source))
					$field->setValue($source[$attr]);
			}
		}

		public function open(array $options = null)
		{
			if ($options !== null) {
				$this->hydrate($options);
			}

			$view = '';

			$view .= '<form ';

			if (!empty($this->attributs)) {
				$view .= $this->renderAttributs();
			}

			$view .= '>'."\r\n";

			return $view;
		}
		
		public function close()
		{
			$view = '';
			$view .= '</form>'."\r\n";

			return $view;
			
		}

		public function buildWidget()
		{
			$view = '';
			// On génère un par un les champs du formulaire
			foreach ($this->fields as $field)
			{
				$view .= $field->render();
			}
			return $view;
		}

		public function isValid(array $source = null)
		{
			if ($source != null)
				$this->setSource($source);

			$valid = true;

			if (!empty($this->fields)) {
				// On vérifie que tous les champs sont valides
				foreach ($this->fields as $field)
				{
					if (!$field->isValid())
					{
						$valid = false;
						$this->errors[$field->name()] = $field->errors();
					}
					$this->update($field);
				}
			}
			else if (!empty($this->source) && !empty($this->rules)) {
				$validation = new \Library\Validator\ValidationRules();

				foreach ($this->source as $key => $value) {
					if (array_key_exists($key, $this->rules)) {
						$validation->setFieldName($key)->setRules($this->rules[$key]);
						if (!$validation->isValid($value)) {
							$valid = false;
							$this->errors[$key] = $validation->getErrorMsg(true);
						}
						$validation->clean();
					}
				}
			}
			return $valid;
		}

		public function hasError($name)
		{
			return array_key_exists($name, $this->errors);
		}
		
		public function error($name)
		{
			if ($this->hasError($name)) {
				return $this->errors[$name];
			}
			return [];
		}

		public function firstError($name)
		{
			if ($this->hasError($name)) {
				return $this->errors[$name][0];
			}
			return '';
		}

		public function updateSource(Field $field)
		{
			$attr = $field->name();
			if (!empty($this->source))
			{
				if (array_key_exists($attr, $this->source))
				{
					$this->source[$attr] = $field->value();
				}
			}
		}

		public function update(Field $field)
		{
			$this->updateSource($field);
		}
		
		public function errors()
		{
			return $this->errors;
		}

		public function source()
		{
			return $this->source;
		}

		public function rules()
		{
			return $this->rules;
		}

		public function method()
		{
			if (array_key_exists('method', $this->attributs))
				return $this->attributs['method'];
			
			return null;
		}

		public function action()
		{
			if (array_key_exists('action', $this->attributs))
				return $this->attributs['action'];

			return null;
		}

		public function charset()
		{
			if (array_key_exists('accept-charset', $this->attributs))
				return $this->attributs['accept-charset'];
			
			return null;
		}

		public function getField($fieldName)
		{
			foreach ($this->fields as $field)
			{
				if ($field->name() === $fieldName)
				{
				    return $field;
				}
			}
			return null;
		}

		public function setMethod($method)
		{
			if (is_string($method)) {
				$this->attributs['method'] = $method;
			}
		}

		public function setAction($action)
		{
			if (is_string($action)) {
				$this->attributs['action'] = $action;
			}
		}

		public function setCharset($charset)
		{
			if (is_string($charset)) {
				$this->attributs['accept-charset'] = $charset;
			}
		}

		public function setRules(array $rules)
		{
			if (!empty($rules)) {
				$this->rules = $rules;
			}

			if (!empty($this->fields)) {
				$this->setFieldsRules();
			}
			return $this;
		}
		
		public function setFieldsRules()
		{
			foreach ($this->fields as $field) {
				$this->setFieldRules($field);
			}
			return $this;
		}

		public function setFieldRules(Field $field)
		{
			if (array_key_exists($field->name(), $this->rules)) {
				$field->setRules($this->rules[$field->name()]);
			}
			return $this;
		}

		public function input($className, $name, $value = null, array $options = null)
		{
			if (!is_string($className)) {
				throw new \RuntimeException('Le nom de la classe doit être spécifié');
			}
			if (!is_string($name)) {
				throw new \RuntimeException('Le nom du champ doit être spécifié');
			}

			if ($options === null) $options = array();

			$options['name'] = $name;

			if ($value !== null) {
				$options['value'] = $value;
			}

			$className = '\\Library\\Form\\'.ucfirst($className);
			$input = new $className($options);

			$this->addField($input);

			return $input->render();
		}

		public function text($name, $value = null, array $options = null)
		{
			return $this->input('StringField', $name, $value, $options);
		}

		public function textarea($name, $value = null, array $options = null)
		{
			return $this->input('TextField', $name, $value, $options);
		}
		
		public function ckeditor($name, $value = null, array $options = null)
		{
			return $this->input('CKeditorField', $name, $value, $options);
		}

		public function password($name, $value = null, array $options = null)
		{
			return $this->input('PasswordField', $name, $value, $options);
		}

		public function button($name, $value = null, array $options = null)
		{
			return $this->input('Button', $name, $value, $options);
		}

	}

