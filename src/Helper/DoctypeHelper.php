<?php
	namespace Library\Helper;
	
	class DoctypeHelper extends WidgetHelper
	{
		
		public function __construct()
		{
			$this->name = 'DoctypeHelper';
		}
		
		public function doctypes()
		{
			return $this->widgets;
		}
		
		public function doctype($options = null)
		{
			if (null !== $options && is_array($options))
			{
				$this->addWidget($options, WidgetHelper::SET);
			}
		
			return $this;
		}
		
		public function buildWidget($options)
		{
			$doctype = new \Library\Html\Doctype($options);
			return $doctype;	
		}
		
		public function setDoctype($options = array())
		{
			return $this->addWidget($options, WidgetHelper::SET);
		}
		
		public function getDoctype()
		{
			if (!isset($this->widgets[0]))
				$this->addWidget(array('doctype' => \Library\Html\Doctype::HTML5, WidgetHelper::SET));
				
			return $this->widgets[0];
		}
		
		public function isXhtml()
		{
			$doctype = $this->getDoctype()->render();
			
			return (stristr($doctype, 'xhtml') ? true : false);
		}
		
		public function isHtml5()
		{
			$doctype = $this->getDoctype()->render();
			
			return (stristr($doctype, '<!DOCTYPE html>') ? true : false);		
		}

	}
