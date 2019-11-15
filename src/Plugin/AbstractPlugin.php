<?php
	namespace Library\Plugin;

	abstract class AbstractPlugin
	{
		protected $cfg = array();
		protected $initialized = false;

		public function __construct(array $cfg)
		{
			if (!empty($cfg)) {
				$this->cfg = $cfg;
			}
		}

		abstract public function initialize();

		public function initialized()
		{
			return $this->initialized;
		}
	}
