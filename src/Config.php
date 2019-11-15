<?php
	namespace Library;
	
	class Config extends ApplicationComponent
	{
		protected $vars = array();
		protected $cfg = array();
		protected $iniFile = array();
		
		public function get($var)
		{
			if (!$this->vars)
			{
				$xml = new \DOMDocument;
				$xml->load(__DIR__.'/../Applications/'.$this->app()->name().'/Config/app.xml');

				$elements = $xml->getElementsByTagName('define');

				foreach ($elements as $element)
				{
					$this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
				}
			}

			if (isset($this->vars[$var]))
			{
				return $this->vars[$var];
			}

			return null;
		}

		public function parseIniFile($file, $skipcomment = true)
		{
			$cfg = array();
			// Pour utiliser parse_ini_file() par défaut, enlevez /* et */, sinon supprimez ce commentaire (conseillé)
			if($skipcomment && false!==($cfg=@parse_ini_file($file, TRUE)))
			{
				return $cfg;
	 		}
	 		else
			if (file_exists($file) && $fichier_lecture=file($file))
			{
				$i = 0;
				foreach ($fichier_lecture as $ligne)
				{
					$ligne_propre=trim($ligne);
					// Si entête de section
					if (preg_match("#^\[(.+)\]$#", $ligne_propre, $matches))
					{
						$groupe = $matches[1];
						$cfg[$groupe] = array();
					}
					elseif ($ligne_propre[0] != ';')
					{
						list($item, $valeur) = explode("=", $ligne_propre, 2);
						if (!isset($valeur)) // S'il n'y a pas de valeur
							$valeur = ''; // On donne une chaîne vide
						$cfg[$groupe][$item] = rtrim($valeur, "\n\r");
					}
					elseif ($ligne[0] == ';')
					{
						$cfg['#comment'.$i] = rtrim($ligne, "\r\n");
						$i++;
					}
				}
			}
			return $cfg;
		}

		public function saveIniFile($file, $array)
		{
			$fichier_save="";
			foreach ($array as $key => $groupe_n)
			{
				if (strpos($key, '#comment') === 0)
				{
					$fichier_save .= "\n" . $groupe_n;
					continue;
				}
				$fichier_save .= "\n[".$key."]"; // \n est une entrée à la ligne
				foreach ($groupe_n as $key => $item_n)
				{
					$fichier_save .= "\n". $key . "=" . $item_n;
				}
			}
			// On enlève le premier caractère qui est forcément une entrée à la ligne inutile 
			$fichier_save = substr($fichier_save, 1); 
			if (false === @file_put_contents($file, $fichier_save))
			{
				return false;
			}
			else
				return true;
		}

		public function addIniFile($file, $defaultDir = true) {
			if ($defaultDir) $file = __DIR__.'/../Applications/'.$this->app()->name().'/Config/'.$file;
			if (file_exists($file)  && !in_array($file, $this->iniFile)) {
				$this->iniFile[] = $file;
			}
			return $this;
		}

		public function loadCfg()
		{
			if (empty($this->iniFile)) {
				$this->addIniFile('app.ini');
			}
			$temp = array();
			foreach ($this->iniFile as $file){
				$temp = $this->parseIniFile($file);
				if ($temp) {
					$this->cfg = array_merge($this->cfg, $temp);
				}
			}
			return $this;
		}

		public function getIniVar($section, $var = null)
		{
			if (!$this->cfg) {
				$this->loadCfg();
			}

			if ($this->cfg && isset($this->cfg[$section])) {
				if (!$var) {
					return $this->cfg[$section];
				}

				if (isset($this->cfg[$section][$var])) {
					return $this->cfg[$section][$var];
				}
			}
			return null;
		}

		public function getIniVarInFile($file, $section, $var = null)
		{
			$array = $this->parseIniFile($file);
		
			if ($array && isset($array[$section])) {
				if ((!$var)) {
					 return $array[$section];
				}
				else if (isset($array[$section][$var])) {
					return $array[$section][$var];
				}
			}
			return null;
		}
	} 
