<?php
    namespace Library\Html;
    
    class Doctype extends HtmlElement
    {
    	const XHTML11             = 'XHTML11';
    	const XHTML1_STRICT       = 'XHTML1_STRICT';
    	const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
    	const XHTML1_FRAMESET     = 'XHTML1_FRAMESET';
    	const XHTML_BASIC1        = 'XHTML_BASIC1';
    	const XHTML5              = 'XHTML5';
    	const HTML4_STRICT        = 'HTML4_STRICT';
    	const HTML4_LOOSE         = 'HTML4_LOOSE';
    	const HTML4_FRAMESET      = 'HTML4_FRAMESET';
    	const HTML5               = 'HTML5';
		
		protected $doctypes = array(
			self::XHTML11 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
			self::XHTML1_STRICT => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			self::XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            self::XHTML1_FRAMESET     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
            self::XHTML_BASIC1        => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">',
            self::XHTML5              => '<!DOCTYPE html>',
            self::HTML4_STRICT        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
            self::HTML4_LOOSE         => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
            self::HTML4_FRAMESET      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
            self::HTML5               => '<!DOCTYPE html>',
            );
                
        protected $doctype = self::HTML5;
        
        public function buildWidget()
        {
            return $this->doctypes[$this->doctype] ."\n\t";        
        }

		public function setDoctype($doctype = self::HTML5)
		{
			if (array_key_exists($doctype, $this->doctypes))
			{
				$this->doctype = $doctype;
			}		
		}        
    }

