<?php
	namespace Library;

	class Cache extends ApplicationComponent
	{
		protected $file = '';
		protected $output = '';
		protected $lifetime = 0;

		public function __construct($file, $lifetime)
		{
			$this->setFile($file);
			$this->setLifetime($lifetime);
		}

		public function setFile($file)
		{
			if (!is_string($file) || empty($file))
			{
				throw new \InvalidArgumentException('Le fichier cache doit être une chaine de caractères valide');
			}
			$this->file = $file;
			return $this;
		}

		public function cacheExists() 
		{
			return file_exists($this->file);
		}

		public function filemtime()
		{
			clearstatcache();
			return filemtime($this->file);
		}

		public function setLifetime($lifetime)
		{
			if (!is_int($lifetime) || $lifetime < 1)
			{
				throw new \InvalidArgumentException('Le temps d\'existence du fichier cache doit être un entier valide'
													.'superieur à 1');
			}
			$this->lifetime = $lifetime;
			return $this;
		}

		public function checkLifetime()
		{
			if (time() - $this->filemtime() > $this->lifetime) {
				$this->destroy();
				return false;
			}
			else {
				return true;
			}
		}

		public function save()
		{
			if (false === @file_put_contents($this->file, $this->output)) {
				chmod($this->file, 0777);
				return false;
			}
			else {
				return true;
			}
		}

		public function read()
		{
			if ($this->cacheExists()) {
				$this->output = file_get_contents($this->file);
				return $this->output;
			}
			return false;
		}

		public function destroy()
		{
			if ($this->cacheExists()) {
				unlink($this->file);
				clearstatcache();
			}
			return $this;
		}
		
		public function cleanAll($dir)
		{
			if (!is_string($dir) || empty($dir))
			{
				throw new \InvalidArgumentException('Le dossier cache doit être une chaine de caractères valide');
			}
			if ($handle = opendir($dir)) {
				while (false !== ($entry = readdir($handle))) {
					if( "." == $entry || ".." == $entry ){
						continue;
					}					
					if (!is_dir($entry)) {
						unlink($dir.'/'.$entry);
					}
					else {
						$this->cleanAll($entry);
					}
				}
				closedir($handle);
			}
			return $this;
		}

		public function setOutput($output)
		{

			$this->output = $output;
			return $this;
		}

		public function getOutput()
		{
			return $this->output;
		}


	}
