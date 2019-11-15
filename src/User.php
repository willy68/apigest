<?php
	namespace Library;

	session_start();

	class User extends ApplicationComponent
	{
		public function getAttribute($attr)
		{
			return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
		}

		public function getFlash()
		{
			$flash = $_SESSION['flash'];
			unset($_SESSION['flash']);

			return $flash;
		}

		public function hasFlash()
		{
			return isset($_SESSION['flash']);
		}

		public function setAttribute($attr, $value)
		{
			$_SESSION[$attr] = $value;
		}

		public function isAuthenticated()
		{
			return ($_SESSION['auth'] = $this->_isConnected()) === true;
			//return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
		}

		public function setAuthenticated($authenticated = true)
		{
			if (!is_bool($authenticated))
			{
				throw new \InvalidArgumentException('Le valeur spécifié à la méthode User::setAuthenticated() doit être un boolean');
			}

			if ($authenticated === true) {
				$this->_connect();
			}
			else {
				$this->_disconnect();
			}

			$_SESSION['auth'] = $authenticated;
		}

		protected function _isConnected(){
	
			if( !isset($_SESSION['ip']) or $_SESSION['ip'] != sha1($_SERVER['REMOTE_ADDR']) 
				or !isset($_SESSION['userAgent']) or $_SESSION['userAgent'] != sha1($_SERVER['HTTP_USER_AGENT']) ) { 
				return false;
			}else if(
				(int)$this->app()->config()->getIniVar('session', 'session.timeout.enable') === 1 ) { 
					if (!isset($_SESSION['timeout'])) {
						//on regenere un nouvel id de session
						session_regenerate_id(true);
						return false;
					}
					else if (((int)$_SESSION['timeout'] - time() ) < 0) {
						$this->_disconnect();
						//$_SESSION['newid'] = true;
						return false;
					}
			}/*else if(
				_root::getConfigVar('security.xsrf.checkReferer.enabled') ==1 
				and isset($_SERVER['HTTP_REFERER'])){

				if(isset($_SERVER['HTTPS']) ){
					$sPattern='https://'.$_SERVER['SERVER_NAME'];
		
				}else{
					$sPattern='http://'.$_SERVER['SERVER_NAME'];
				}		
				$urllen=strlen($sPattern);

				if( substr($_SERVER['HTTP_REFERER'],0,$urllen)!=$sPattern ){
					return false;
				}

			}*/
		
			 if((int)$this->app()->config()->getIniVar('session', 'session.timeout.enable') === 1) {
			 	$_SESSION['timeout'] = (time() + (int)$this->app()->config()->getIniVar('session', 'session.timeout.lifetime') );
			 }

			return true;
		}

		protected function _connect()
		{
			//on regenere un nouvel id de session
			session_regenerate_id(true);

			$_SESSION['newid'] = true;

			$_SESSION['ip'] = sha1($_SERVER['REMOTE_ADDR']);
			if(isset($_SERVER['HTTP_USER_AGENT'])) {
				$_SESSION['userAgent'] = sha1($_SERVER['HTTP_USER_AGENT']);
			}else {
				$_SESSION['userAgent'] = sha1('noUserAgent');
			}
			 if((int)$this->app()->config()->getIniVar('session', 'session.timeout.enable') === 1) {
				$_SESSION['timeout'] = (time() + (int)$this->app()->config()->getIniVar('session', 'session.timeout.lifetime') );
			}
		}

		protected function _disconnect()
		{
			$_SESSION = array();

			//on regenere un nouvel id de session
			session_regenerate_id(true);
			$_SESSION['newid'] = true;
		}

		public function setFlash($value)
		{
			$_SESSION['flash'] = $value;
		}
	}
