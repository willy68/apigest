<?php
	namespace Library\Plugin;
	require_once '../Library/Dompdf/vendor/autoload.php';
	//require_once '../Library/Dompdf/autoload.inc.php';

	use Dompdf\Dompdf;

	class PluginDompdf extends AbstractPlugin
	{
		protected $dompdf = null;

		public function initialize()
		{
			if ($this->initialized()) return true;

			//try
			//{
			$this->dompdf = new dompdf();
			$this->dompdf->set_option('isHtml5ParserEnabled', true);
			$this->dompdf->setPaper('A4');

			return ($this->initialized = true);
		}

		public function getDompdf()
		{
			if (!$this->initialized() || $this->dompdf === null) $this->initialize();

			return $this->dompdf;
		}

		public function getObject()
		{
			return $this->getDompdf();
		}

	}
