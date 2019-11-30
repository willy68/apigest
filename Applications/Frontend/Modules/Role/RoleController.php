<?php
namespace Applications\Frontend\Modules\Role;

class RoleController extends \Applications\Frontend\Modules\ApiController
	{

		protected function getList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists('id')) {
			  $options['entreprise_id'] = $request->getData('entreprise_id');
			}

			if ($request->getExists('limit')) {
				$options['limit'] = $request->getData('limit');
			}
			
			if ($request->getExists('order')) {
				$options['order'] = $request->getData('order');
      }

			try {
        if (!empty($options)) {
          $roles = \Role::all($options);
        } else {
          $roles = \Role::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User role not found on this server');
				return;
			}

			if (empty($roles))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes roles trouvées sur ce serveur');
				return;
			}

      $json = $this->jsonArray($roles);
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function create(\Library\HTTPRequest $request)
		{
      /*$user = \User::find_by_email(array( 'email' => $request->postData('email')));
      if ($user) {
          header('HTTP/1.1 403 Forbiden');
          exit('Email ' . $request->postData('email') . ' allready exists');
      }*/

      $role = new \Role();

			$role->set_attributes(array('role' => $request->postData('role')));

			if ($role->save())
			{
				header ('Content-Type: application/json; charset=UTF-8');
				$this->page->setOutput($role->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$role = \Role::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Role not found on this server');
				return;
			}

			$json = $role->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$role = \Role::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Role not found on this server');
				return;
			}
			if ($role->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($role->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}
		
		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$role = \Role::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Role not found on this server');
				return;
			}
			
			if ($role->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($role->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}

	}
