<?php
namespace Applications\Frontend\Modules\Root;

class RootController extends \Applications\Frontend\Modules\ApiController
	{

		protected function getList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists('limit')) {
				$options['limit'] = $request->getData('limit');
			}
			
			if ($request->getExists('order')) {
				$options['order'] = $request->getData('order');
			}

			try {
				if (!empty($options)) {
				  $roots = \Root::all($options);
				} else {
				  $roots = \Root::all();
				}
			} catch (\ActiveRecord\RecordNotFound $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Root users not found on this server');
				return;
			}

			if (empty($roots))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes roots trouvées sur ce serveur');
				return;
			}

		    $json = $this->jsonArray($roots);
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($json);
			}

		protected function create(\Library\HTTPRequest $request)
		{
			try {
		  	$root = \Root::find_by_email(array( 'email' => $request->postData('email')));
		  	if ($root) {
		      header('HTTP/1.1 403 Forbiden');
		      exit('Email ' . $request->postData('email') . ' allready exists');
		  	}
		  } catch(\ActiveRecord\RecordNotFound $e) {
		  
			}

      $root = new \Root();

		  $root->set_attributes(array(
    		'username' => $request->postData('username'),
    		'email' => $request->postData('email'),
    		'role' => $request->postData('role')
			));

			if ($root->save())
			{
				header ('Content-Type: application/json; charset=UTF-8');
				$this->page->setOutput($root->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$root = \Root::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Root not found on this server');
				return;
			}

			$json = $root->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$root = \Root::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Root not found on this server');
				return;
			}
			if ($root->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($root->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}
		
		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$root = \Root::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Root not found on this server');
				return;
			}
			
			if ($root->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($root->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}

	}
