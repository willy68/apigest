<?php
	namespace Library;

	class I18n
	{
		protected static $lang = 'fr';
		protected static $translate = array();
		protected static $dir = array();

		public static function setLang($lang)
		{
			if (is_string($lang))
			{
				self::$lang = ucfirst($lang);
				self::$translate = array();
			}
			//return $this
		}

		public static function addDir($dir)
		{
			if (is_string($dir))
			{
				self::$dir[] = $dir;
			}
			//return $this;
		}

		public static function loadTranslate($cat)
		{
			foreach (self::$dir as $dir)
			{
				$file = $dir . '/' . self::$lang . '/' . $cat . '.php';
				if (file_exists($file))
				{
					$stack = self::loadFile($file);
					if (empty(self::$translate[$cat]))
						self::$translate[$cat] = array();

					self::$translate[$cat] = array_merge(self::$translate[$cat], $stack);

				}
			}
		}

		protected static function loadFile($file)
		{
			return include $file;
		}

		public static function exists($key)
		{
			list( $c, $k, $t) = array_pad(explode('.', $key), 3, '');

			if (!array_key_exists($c, self::$translate)) return false;

			if (empty($t))
				return array_key_exists($k, self::$translate[$c]) ? true : false;
			else
				return is_array(self::$translate[$c][$k]) && array_key_exists($t, self::$translate[$c][$k]) ? true : false;
		}

		public static function __i($key)
		{
			$message = 'Il n\'y a pas de traduction pour cette entrée.';

			list( $c, $k, $t) = array_pad(explode('.', $key), 3, '');

			if (!self::exists($key)) self::loadTranslate($c);

			if (self::exists($key)) {
				if (empty($t))
					$message = self::$translate[$c][$k];
				else
					$message = self::$translate[$c][$k][$t];
			}

			 return $message;
		}
	}

	function __($key)
	{
		return I18n::__i($key);
	}
