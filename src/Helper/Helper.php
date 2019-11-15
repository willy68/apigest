<?php
	namespace Library\Helper;
	
	abstract class Helper
	{
		protected $name;
		
		public function __construct()
		{
			$this->name = '';
		}
		
		public function name()
		{
			return $this->name();
		}
	
	}
