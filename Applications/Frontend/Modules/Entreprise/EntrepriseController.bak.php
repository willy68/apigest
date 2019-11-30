<?php
	namespace Applications\Frontend\Modules\Entreprise;

	class EntrepriseController extends \Applications\Frontend\BackController
	{

	/*	public function beforeList(\Library\HTTPRequest $request)
		{
			//Test if user can list Entreprises with token
			if ($this->method === 'GET') {
				if (!$this->isAuthorized()) {
					header('HTTP/1.1 401 Unauthorized');
					exit('Utilisateur non authentifié');
				}
			}
		}*/

		public function executeList(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getEntreprisesList($request);
			}
			else if ($this->method === 'POST') {
				$this->createEntreprise($request);
			}
		}

		private function getEntreprisesList(\Library\HTTPRequest $request)
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

			$entreprises = \Entreprise::all($options);

			if (empty($entreprises))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes entreprises trouvées sur ce serveur');
				return;
			}

			$i = 0;
			foreach ( $entreprises as $entreprise ) {
				$js = $entreprise->to_json ();
				if ($i !== 0)
					$json .= "," . $js;
				else
					$json = $js;
				$i ++;
			}
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput("[" . $json . "]");
		}

		private function createEntreprise(\Library\HTTPRequest $request)
		{
			$entreprise = \Entreprise::find_by_siret(array( 'siret' => $request->postData('siret')));
			if ($entreprise) {
				header('HTTP/1.1 403 Forbiden');
				exit ('L\'entreprise ' . $request->postData('siret') . ' existe déjà');
			}

			$entreprise = new \Entreprise();

			$entreprise->set_attributes(array('siret' => $request->postData("siret"),
								'nom' => $request->postData("nom"),
								'ape' => $request->postData("ape"),
								'tva_intracom' => $request->postData('tva_intracom'),
								'adresse' => $request->postData('role'),
								'suite_adresse' => $request->postData("suite_adresse"),
								'cp' => $request->postData('cp'),
								'ville' => $request->postData('ville'),
								'tel' => $request->postData('tel'),
								'portable' => $request->postData('portable'),
								'email' => $request->postData('email'),
								'regime_commercial' => $request->postData('regime_commercial')
							));

			if ($entreprise->save())
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($entreprise->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		public function beforeBy_id(\Library\HTTPRequest $request)
		{
			//Test if Entreprise can get, update or delete a Entreprise with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_id(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getEntreprise($request);
			}
			else if ($this->method === 'PUT') {
				$this->updateEntreprise($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteEntreprise($request);
			}
		}

		private function getEntreprise(\Library\HTTPRequest $request)
		{
			try {
				$entreprise = \Entreprise::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}

			$json = $entreprise->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

		private function updateEntreprise(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$entreprise = \Entreprise::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}
			if ($entreprise->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($entreprise->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}
		
		private function deleteEntreprise(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$entreprise = \Entreprise::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}
			
			if ($entreprise->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($entreprise->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}

		public function beforeBy_name(\Library\HTTPRequest $request)
		{
			//Test if Entreprise can get, update or delete a Entreprise with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_name(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->getEntrepriseBy_name($request);
			}
/*			else if ($this->method === 'PUT') {
				$this->updatesEntreprise($request);
			}
			else if ($this->method === 'DELETE') {
				$this->deleteEntreprise($request);
			}*/
		}

		private function getEntrepriseBy_name(\Library\HTTPRequest $request)
		{
			try {
				$entreprise = \Entreprise::find_by_name(array( 'nom' => $request->getData('nom')));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Entreprise not found on this server');
				return;
			}

			$json = $entreprise->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

	}
