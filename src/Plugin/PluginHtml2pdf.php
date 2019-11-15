<?php
	namespace Library\Plugin;
	require_once '../Library/Html2pdf/vendor/autoload.php';

	class PluginHtml2pdf extends AbstractPlugin
	{
		protected $html2pdf = null;

		public function initialize()
		{
			if ($this->initialized()) return true;

			try
			{
			$this->html2pdf = new \HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
			//$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			//$html2pdf->createIndex('Sommaire', 25, 12, false, true, 1);
			//$html2pdf->Output('bookmark.pdf');
			}
			catch(\HTML2PDF_exception $e) {
				return ($this->initialized = false);
			}

			return ($this->initialized = true);
		}

		public function getHtml2pdf()
		{
			if (!$this->initialized() || $this->html2pdf === null) $this->initialize();

			return $this->html2pdf;
		}

		public function getObject()
		{
			return $this->getHtml2pdf();
		}

	}
