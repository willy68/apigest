<?php
	namespace Applications\Frontend\Modules\Client;

	class ClientController extends \Applications\Frontend\BackController
	{

		public function beforeList(\Library\HTTPRequest $request)
		{
			//Test if user can list or create clients with token
				if (!$this->isAuthorized()) {
					header('HTTP/1.1 401 Unauthorized');
					exit('Utilisateur non authentifié');
				}
		}

		public function executeList(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getClientsList($request);
			}
			else if ($this->method === 'POST') {
				$this->createClient($request);
			}
		}

		private function getClientsList(\Library\HTTPRequest $request)
		{
			$options = array();

			if ($request->getExists('id')) {
				$options['id'] = $request->getData('id');
			}

			if ($request->getExists('limit')) {
				$options['limit'] = $request->getData('limit');
			}
			
			if ($request->getExists('order')) {
				$options['order'] = $request->getData('order');
			}

			$clients = \Client::all($options);

			if (empty($clients))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Clients not found on this server');
				return;
			}

			$i = 0;
			foreach ( $clients as $client ) {
				$js = $client->to_json ();
				if ($i !== 0)
					$json .= "," . $js;
				else
					$json = $js;
				$i ++;
			}
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput("[" . $json . "]");
		}

		private function createClient(\Library\HTTPRequest $request)
		{
			$client = \Client::find_by_code_client(array( 'code_client' => $request->postData('code_client')));
			if ($client) {
				header('HTTP/1.1 403 Forbiden');
				exit ('Code client ' . $request->postData('code_client') . ' allready exists');
			}

			$client = new \Client();

			$client->set_attributes(array(
								'entreprise_id' => $request->postData('entreprise_id'),
								'code_client' => $request->postData('code_client'),
								'civilite' => $request->postData('civilite'),
								'nom' => $request->postData('nom'),
								'prenom' => $request->postData('prenom'),
								'tel' => $request->postData('tel'),
								'portable' => $request->postData('portable'),
								'email' => $request->postData("email"),
								'tva_intracom' => $request->postData('tva_intracom')
			      ));

			if ($client->save())
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($client->to_json());
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
				$this->getClient($request);
			}
			else if ($this->method === 'PUT') {
				$this->updateClient($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteClient($request);
			}
		}

		private function getClient(\Library\HTTPRequest $request)
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

		private function updateClient(\Library\HTTPRequest $request)
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
		
		private function deleteClient(\Library\HTTPRequest $request)
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

		public function beforeBy_nom(\Library\HTTPRequest $request)
		{
			//Test if user can get, update or delete a Client with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_nom(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getClientBy_nom($request);
			}
/*			else if ($this->method === 'PUT') {
				$this->updatesClient($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteClient($request);
			}*/
		}

		private function getClientBy_nom(\Library\HTTPRequest $request)
		{
			try {
				$client = \Client::find_by_nom(array( 'nom' => $request->getData('nom')));
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

	}
