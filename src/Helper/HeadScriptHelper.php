<?php
	namespace Library\Helper;
	
	class HeadScriptHelper extends WidgetHelper
	{
		
		public function __construct()
		{
			$this->name = 'HeadScriptHelper';
		}
		
		public function scripts()
		{
			return $this->widgets;
		}
		
		public function headScript($options = null, $action = WidgetHelper::APPEND)
		{
			if (null !== $options && is_array($options))
			{
				$this->addWidget($options, $action);
			}
		
			return $this;
		}
		
		public function buildWidget($options)
		{
			$script = new \Library\Html\Script($options);
			return $script;	
		}
		
		public function addScript($options = array(), $action = WidgetHelper::APPEND)
		{
			return $this->addWidget($options, $action);
		}

		public function appendScript($src)
		{
			return $this->addWidget(array('src'=> $src, 'type'=>'text/javascript'));
		}
		
		public function prependScript($src)
		{
			return $this->addWidget(array('src'=> $src, 'type'=>'text/javascript'), WidgetHelper::PREPEND);		
		}
	
	}
