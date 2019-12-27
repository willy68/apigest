<?php

namespace Applications\Frontend\Modules\User;

class UserController extends \Applications\Frontend\Modules\ApiController
{

	protected function getList(\Library\HTTPRequest $request)
	{
		$options = array();

		if ($request->getExists('id')) {
			$options['conditions'] = array('entreprise_id = ?', $request->getData('entreprise_id'));
		}

		if ($request->getExists('limit')) {
			$options['limit'] = $request->getData('limit');
		}

		if ($request->getExists('order')) {
			$options['order'] = $request->getData('order');
		}

		try {
			if (!empty($options)) {
				$users = \User::all($options);
			} else {
				$users = \User::all();
			}
		} catch (\ActiveRecord\RecordNotFound $e) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('User role not found on this server');
			return;
		}

		if (empty($users)) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('Aucunes users trouvées sur ce serveur');
			return;
		}

		$json = $this->jsonArray($users);
		header('Content-Type: application/json; charset=UTF-8');
		$this->page->setOutput($json);
	}

	protected function create(\Library\HTTPRequest $request)
	{
	// A voir!! *******************************
		/* $options = array();
		if ($request->getExists('entreprise_id')) {
			$options['joins'] = array('administrateurs');
			$options['conditions'] = array(
				"`user`.email = ? AND `administrateur`.entreprise_id = ?",
				$request->postData('email'),
				$request->getData('entreprise_id')
			);
		} else {
			$options['conditions'] = array("`user`.email = ?", 
			$request->postData('email'));
		}*/

		try {
			$user = \User::find_by_email(array('email' => $request->postData('email')));
		} catch (\ActiveRecord\RecordNotFound $e) {
		} catch (\Exception $e) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('Un problème est survenu, accès à la base de donnée impossible');
			return;
		}

		if ($user) {
			header('HTTP/1.1 400 Bad request');
			exit('Email ' . $request->postData('email') . ' allready exists');
		}

		$pwd = password_hash($request->postData('username') . $request->postData('password'), PASSWORD_BCRYPT, ["cost" => 8]);

		$user = new \User();

		$user->set_attributes(array(
			'username' => $request->postData('username'),
			'email' => $request->postData('email'),
			'password' => $pwd,
			'role' => $request->postData('role') ? $request->postData('role') : 'Admin'
		));

		if ($user->save()) {
			if ($request->getExists('entreprise_id') && $request->getData('entreprise_id') !== null) {
				$admin = new \Administrateur();
				$admin->set_attributes(array(
					'user_id' => $user->id,
					'entreprise_id' => $request->getData('entreprise_id')
				));
				$admin->save();
			}
			header('Content-Type: application/json; charset=UTF-8');
			$this->page->setOutput($user->to_json());
		} else {
			header('HTTP/1.1 400 Bad request');
			$this->page->setOutput('400 Bad request');
		}
	}

	protected function get(\Library\HTTPRequest $request)
	{
		try {
			$user = \User::find($request->getData('id'));
		} catch (\ActiveRecord\RecordNotFound $e) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('User not found on this server');
			return;
		}

		$json = $user->to_json();

		header('Content-Type: application/json; charset=UTF-8');
		$this->page->setOutput($json);
	}

	protected function update(\Library\HTTPRequest $request)
	{
		$id = $request->getData('id');

		try {
			$user = \User::find($id);
		} catch (\ActiveRecord\RecordNotFound $e) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('User not found on this server');
			return;
		}
		if ($user->update_attributes($request->post())) {
			header('Content-Type: application/json; charset=UTF-8');
			$this->page->setOutput($user->to_json());
		} else {
			header('HTTP/1.1 400 Bad request');
			$this->page->setOutput('400 Bad request');
		}
	}

	protected function delete(\Library\HTTPRequest $request)
	{
		$id = $request->getData('id');

		try {
			$user = \User::find($id);
		} catch (\ActiveRecord\RecordNotFound $e) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('User not found on this server');
			return;
		}

		if ($user->delete()) {
			header('Content-Type: application/json; charset=UTF-8');
			$this->page->setOutput($user->to_json());
		} else {
			header('HTTP/1.1 400 Bad request');
			$this->page->setOutput('400 Bad request');
		}
	}

	public function executeLogin(\Library\HTTPRequest $request)
	{
		$options = array();
		/*SELECT `user`.* FROM `user`
		INNER JOIN `administrateur` ON(`administrateur`.user_id = `user`.id)
		WHERE `administrateur`.`entreprise_id` = $request->getData('entreprise_id') AND `user`.`email` = $request->postData('email')*/
		if ($request->getExists('entreprise_id')) {
			$options['joins'] = array('administrateurs');
			$options['conditions'] = array(
				"`user`.email = ? AND `administrateur`.entreprise_id = ?",
				$request->postData('email'),
				$request->getData('entreprise_id')
			);
		} else {
			$options['conditions'] = array("`user`.email = ?", 
			$request->postData('email'));
		}

		$user = \User::find($options);
		// $user = \User::find_by_email(array('email' => $request->postData('email')));
		if (!$user) {
			header('HTTP/1.1 404 Not Found');
			$this->page->setOutput('User not found on this server');
			return;
		}

		$token = $this->authenticate($request, $user->username, $user->email, $user->role, $user->password, 900, 0);
		if ($token) {
			header('Content-Type: application/json; charset=UTF-8');
			$userJwt = [
				'id' => $user->id,
				'username' => $user->username,
				'email' => $user->email,
				'role' => $user->role,
				'token' => $token
			];
			$json = json_encode($userJwt);
			$this->page->setOutput($json);
		} else {
			header('HTTP/1.1 401 Unauthorized');
			$this->page->setOutput('Authentication failed');
		}
	}
}
