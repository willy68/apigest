<?php
	namespace Library\Helper;
	
	abstract class WidgetHelper extends Helper
	{
		const SET = 'SET';
		const PREPEND = 'PREPEND';
		const APPEND = 'APPEND';
		
		protected $widgets = array();
		
		public function widgets()
		{
			return $this->widgets;
		}
				
		public function setWidget($options = array())
		{
			if (!empty($options))
			{
				$widget = $this->buildWidget($options);
				$this->widgets = array($widget);
			}
			return $this;
		}
		
		public function addWidget($options = array(), $action = WidgetHelper::APPEND)
		{
			if (!empty($options))
			{
				$widget = $this->buildWidget($options);
				switch ($action)
				{
					case WidgetHelper::SET:
						$this->widgets = array($widget);
						break;
					case WidgetHelper::PREPEND:
						array_unshift($this->widgets, $widget);
						break;
					case WidgetHelper::APPEND:
						$this->widgets[] = $widget;
					break;
				}
			}
			return $this;
		}
		
		public function render()
		{
			$widgets = '';
			
			foreach ($this->widgets as $widget)
			{
				$widgets .= $widget->buildWidget();
			}
			return $widgets;
		}
		
		abstract public function buildWidget($options);
			
	}
