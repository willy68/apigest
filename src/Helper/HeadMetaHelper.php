<?php
	namespace Library\Helper;
	
	class HeadMetaHelper extends WidgetHelper
	{		
		public function __construct()
		{
			$this->name = 'HeadMetaHelper';
		}
		
		public function metas()
		{
			return $this->widgets;
		}
		
		public function headMeta($options = null, $action = WidgetHelper::APPEND)
		{
			if (null !== $options && is_array($options))
			{
				$this->addWidget($options, $action);
			}
		
			return $this;
		}
		
		public function buildWidget($options)
		{
			$meta = new\Library\Html\Meta($options);
			return $meta;	
		}
		
		public function addMeta($options = array(), $action = WidgetHelper::APPEND)
		{
			return $this->addWidget($options, $action);
		}

		public function appendMeta($name, $content)
		{
			return $this->addWidget(array('name'=> $name, 'content'=> $content));
		}
		
		public function prependMeta($name, $content)
		{
			return $this->addWidget(array('name'=> $name, 'content'=> $content), WidgetHelper::PREPEND);
		}
	
	}
