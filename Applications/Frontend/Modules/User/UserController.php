<?php
	namespace Applications\Frontend\Modules\User;

	class UserController extends \Applications\Frontend\BackController
	{

		public function beforeList(\Library\HTTPRequest $request)
		{
			//Test if user can list users with token
			if ($this->method === 'GET') {
				if (!$this->isAuthorized()) {
					header('HTTP/1.1 401 Unauthorized');
					exit('Utilisateur non authentifié');
				}
			}
		}

		public function executeList(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getUsersList($request);
			}
			else if ($this->method === 'POST') {
				$this->createUser($request);
			}
		}

		private function getUsersList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists('entreprise_id')) {
				$options['entreprise_id'] = $request->getData('entreprise_id');
			}

			if ($request->getExists('limit')) {
				$options['limit'] = $request->getData('limit');
			}
			
			if ($request->getExists('order')) {
				$options['order'] = $request->getData('order');
			}

			$users = \User::all($options);

			if (empty($users))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Users not found on this server');
				return;
			}

			$i = 0;
			foreach ( $users as $user ) {
				$js = $user->to_json ();
				if ($i !== 0)
					$json .= "," . $js;
				else
					$json = $js;
				$i ++;
			}
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput("[" . $json . "]");
		}

		private function createUser(\Library\HTTPRequest $request)
		{
			$user = \User::find_by_email(array( 'email' => $request->postData('email')));
			if ($user) {
				header('HTTP/1.1 403 Forbiden');
				exit ('Email ' . $request->postData('email') . ' allready exists');
			}

			$user = new \User();

			$pwd = password_hash($request->postData('username').$request->postData('password'), PASSWORD_BCRYPT, ["cost" => 8]);

			$user->set_attributes(array('entreprise_id' => $request->postData("entreprise_id"),
								'username' => $request->postData("username"),
								'email' => $request->postData("email"),
								'password' => $pwd,
								'role' => $request->postData('role')));

			if ($user->save())
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($user->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		public function beforeBy_id(\Library\HTTPRequest $request)
		{
			//Test if user can get, update or delete a user with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_id(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getUser($request);
			}
			else if ($this->method === 'PUT') {
				$this->updateUser($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteUser($request);
			}
		}

		private function getUser(\Library\HTTPRequest $request)
		{
			try {
				$user = \User::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User not found on this server');
				return;
			}

			$json = $user->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

		private function updateUser(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$user = \User::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User not found on this server');
				return;
			}
			if ($user->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($user->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}
		
		private function deleteUser(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$user = \User::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User not found on this server');
				return;
			}
			
			if ($user->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($user->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}

		public function beforeBy_username(\Library\HTTPRequest $request)
		{
			//Test if user can get, update or delete a user with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_username(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getUserBy_username($request);
			}
/*			else if ($this->method === 'PUT') {
				$this->updatesUser($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteUser($request);
			}*/
		}

		private function getUserBy_username(\Library\HTTPRequest $request)
		{
			try {
				$user = \User::find_by_username(array( 'username' => $request->getData('username')));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User not found on this server');
				return;
			}

			$json = $user->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

		public function executeLogin(\Library\HTTPRequest $request)
		{
			$user = \User::find_by_email(array( 'email' => $request->postData('email')));
			if (!$user) {
				header('HTTP/1.1 404 Not Found');
				exit('User not found on this server');
			}

			if ($request->getData('entreprise_id') !== $user->entreprise_id) {
				header('HTTP/1.1 403 Not Forbiden');
				exit('User is not from this compagny');
			}

			$token = $this->authenticate($request, $user->username, $user->email, $user->role, $user->password, 900, 0);
			if ($token) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$userJwt = ['id' => $user->id, 
							'username' => $user->username, 
							'email' => $user->email, 
							'role' => $user->role, 
							'token' => $token];
				$json = json_encode($userJwt);
				$this->page->setOutput($json);
			}
			else {
				header('HTTP/1.1 401 Unauthorized');
				exit('Authentication failed');
			}
		}

	}
