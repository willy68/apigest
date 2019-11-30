<?php
namespace Applications\Frontend\Modules\Client;

class ClientController extends \Applications\Frontend\Modules\ApiController
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
          $clients = \Client::all($options);
        } else {
          $clients = \Client::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User role not found on this server');
				return;
			}

			if (empty($clients))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes clients trouvées sur ce serveur');
				return;
			}

      $json = $this->jsonArray($clients);
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

      $client = new \Client();

			$client->set_attributes(array(/*
    'entreprise_id' => $request->postData('entreprise_id'),
    'code_client' => $request->postData('code_client'),
    'civilite' => $request->postData('civilite'),
    'nom' => $request->postData('nom'),
    'prenom' => $request->postData('prenom'),
    'tel' => $request->postData('tel'),
    'portable' => $request->postData('portable'),
    'email' => $request->postData('email'),
    'tva_intracom' => $request->postData('tva_intracom')
*/
							));

			if ($client->save())
			{
				header ('Content-Type: application/json; charset=UTF-8');
				$this->page->setOutput($client->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$client = \Client::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Client not found on this server');
				return;
			}

			$json = $client->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$client = \Client::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Client not found on this server');
				return;
			}
			if ($client->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($client->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}
		
		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$client = \Client::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Client not found on this server');
				return;
			}
			
			if ($client->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($client->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}

	}
