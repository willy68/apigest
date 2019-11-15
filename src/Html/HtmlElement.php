<?php
	namespace Library\Html;

	abstract class HtmlElement
	{
		protected $attributs = array();
		protected $value;
		protected $openHTML;
		protected $closeHTML;

		public function __construct(array $options = null)
		{
			if ($options !== null && !empty($options))
			{
				$this->hydrate($options);
			}
		}

		abstract public function buildWidget();

		public function renderAttributs()
		{
			$attr = '';
			foreach ($this->attributs as $key => $value)
			{
				$attr .= ' '.$key.'="'.$value.'"';
			}
			return $attr;
		}

		public function render()
		{
			$widget = '';
	
			if (!empty($this->openHTML))
				$widget .= $this->openHTML;
		
			$widget .= $this->buildWidget();
	
			if (!empty($this->closeHTML))
				$widget .= $this->closeHTML;
		
			return $widget;
		}

		public function hydrate($options)
		{
			foreach ($options as $type => $value)
			{
				$method = 'set'.ucfirst($type);
				
				if (is_callable(array($this, $method)))
				{
					$this->$method($value);
				}
			}
		}

		public function attributs()
		{
			return $this->attributs;
		}

		public function attribut($attr)
		{
			if (is_string($attr) && array_key_exists($attr, $this->attributs)) {
				return $this->attributs[$attr];
			}
			return null;
		}

		public function className()
		{
			if (array_key_exists('class', $this->attributs))
				return $this->attributs['class'];

			return null;
		}

		public function customHTML()
		{
			return array('openHTML' => $this->openHTML, 'closeHTML' => $this->closeHTML);
		}

		public function id()
		{
			if (array_key_exists('id', $this->attributs))
				return $this->attributs['id'];

			return null;
		}

		public function value()
		{
			return $this->value;
		}

		public function setAttributs(array $attributs)
		{
			if (!empty($attributs))
			{
				foreach($attributs as $key =>$value) {
					$this->attributs[$key] = $value;
				}
			}
			return $this;
		}

		public function setClassName($class)
		{
			if (is_string($class))
			{
				if (array_key_exists('class', $this->attributs))
					$this->attributs['class'] .= ' '.$class;
				else
					$this->attributs['class'] = $class;
			}
			return $this;
		}

		public function setCustomHtml(array $customHTML)
		{
			if (is_array($customHTML) && !empty($customHTML))
			{
				if (array_key_exists('openHTML', $customHTML))
					$this->openHTML = $customHTML['openHTML'];
				if (array_key_exists('closeHTML', $customHTML))
					$this->closeHTML = $customHTML['closeHTML'];
			}
			return $this;
		}

		public function setId($id)
		{
			if (is_string($id))
			{
				$this->attributs['id'] = $id;
			}
			return $this;

		}

		public function setValue($value)
		{
			if (is_string($value))
			{
				$this->value = $value;
			}
			return $this;
		}
	}

