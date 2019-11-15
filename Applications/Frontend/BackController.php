<?php
	namespace Applications\Frontend;
	
	abstract class BackController extends \Library\Controller
	{
		protected $method;
		protected $jwt = null;
		
		public function before(\Library\HTTPRequest $request)
		{
			$this->method = $_SERVER['REQUEST_METHOD'];
			if ($this->method === 'OPTIONS') {
			 header('Access-Control-Allow-Headers: x-requested-with, content-type, origin, authorization, accept, client-security-token, user-agent');
			 header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
			 exit;
			}

			$headers = $request->apacheHeaders();

			if ($this->method === 'POST' || $this->method === 'PUT') {
				$post = file_get_contents("php://input");
				$_POST = json_decode($post,true);
			}

			$jwt = null;
			if(isset($headers['Authorization'])){
				list($jwt) = sscanf( $headers['Authorization'], 'Bearer %s');
				if ($jwt) {
					$this->jwt = $jwt;
				}
			}
		}

		public function after(\Library\HTTPRequest $request)
		{
			//Refresh token if exists in headers!
			if ($this->jwt) {
				require_once __DIR__.'/../../Library/Jwt/jwt.php';
				$key = file_get_contents(__DIR__."/Crypto/pass_salt.txt");
				try {
					$jwt = \JWT::refreshToken($this->jwt, $key, 900);
					header('Authorization: Bearer ' . $jwt);
					header('Access-Control-Expose-Headers: Authorization');
				}
				catch(\UnexpectedValueException $e) {
					
				}
			}
		}

		public function setLayout()
		{
			//$this->page->setLayout(__DIR__.'/Templates/layout.php');
		}

		protected function isAuthorized()
		{
			if ($this->jwt) {
				require_once __DIR__.'/../../Library/Jwt/jwt.php'; // no autoload!
				$key = file_get_contents(__DIR__."/Crypto/pass_salt.txt");
				try {
					$payload = \JWT::decode($this->jwt, $key);
				
				}
				catch(\UnexpectedValueException $e) {
					return false;
				}catch(\DomainException $e) {
					return false;
				}
				return true;
			}
			return false;
		}

		protected function authenticate( \Library\HTTPRequest $request, $username, $email, $role, $password, $exp = 900, $nbf = 0)
		{
			$jwt = null;
			$passCrypt = $username.$request->postData('password');

			if (password_verify($passCrypt, $password)) {
			
				$jwt = $this->createJwt($username, $email, $role, $exp, $nbf);

			}
			return $jwt;
		}
		
		protected function createJwt($username, $email, $role, $exp = 900, $nbf = 0)
		{
			$jwt = null;
			require_once __DIR__.'/../../Library/Jwt/jwt.php'; // no autoload!

			$key = file_get_contents(__DIR__."/Crypto/pass_salt.txt");

			$tokenId    = base64_encode(mcrypt_create_iv(32));
			$issuedAt   = time();
			$notBefore  = $issuedAt + $nbf;            // Adding 0 seconds
			$expire     = $notBefore + $exp;           // Adding 900 seconds
			$serverName = $_SERVER['SERVER_NAME'];     // Retrieve the server name from config file

			/*
			 * Create the token as an array
			 */
			$data = [
				'iat'  => $issuedAt,         // Issued at: time when the token was generated
				'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
				'iss'  => $serverName,       // Issuer
				'nbf'  => $notBefore,        // Not before
				'exp'  => $expire,           // Expire
				'data' => [                  // Data related to the signer user
					'username' => $username, // username from the users table
					'email' => $email,       // User email
					'role'  => $role         // User role (user,admin)
				]
			];

			try {
				$jwt = \JWT::encode($data, $key);
			}
			catch(\UnexpectedValueException $e) {
				return null;
			}catch(\DomainException $e) {
				return null;
			}

			return $jwt;		
		}

	}
