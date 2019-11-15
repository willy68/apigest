<?php
	namespace Library\Helper;
	
	class HeadLinkHelper extends WidgetHelper
	{
		public function __construct()
		{
			$this->name = 'HeadLinkHelper';
		}
		
		public function links()
		{
			return $this->widgets;
		}
		
		//'rel' => 'stylesheet' 'href' => /css/style.css', 'type' => 'text/css'
		public function headLink($options = null, $action = WidgetHelper::APPEND)
		{
			if (null !== $options && is_array($options))
			{
				$this->addWidget($options, $action);
			}
		
			return $this;
		}
		
		public function buildWidget($options)
		{
			$link = new \Library\Html\Link($options);
			return $link;	
		}
		
		// Alias ***************************
		
		public function addLink($options = array(), $action = WidgetHelper::APPEND)
		{
			return $this->addWidget($options, $action);
		}
		
		public function appendStyleSheet($href)
		{
			return $this->addWidget(array('href'=> $href, 'rel'=>'stylesheet', 'type'=>'text/css'));
		}
		
		public function prependStyleSheet($href)
		{
			return $this->addWidget(array('href'=> $href, 'rel'=>'stylesheet', 'type'=>'text/css'), WidgetHelper::PREPEND);
		}
	
	}
