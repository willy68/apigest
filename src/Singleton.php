<?php
	namespace Library;

	abstract class Singleton
	{
		protected static $instance = null;

		protected function __construct()
		{
		}

		public static function getInstance()
		{
			if (null === static::$instance)
			{
				static::$instance = new static();
			}
			return static::$instance;
		}

		final private function  __clone() { }
	}