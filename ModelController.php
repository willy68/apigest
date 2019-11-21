<?php
$controller = "<?php
	namespace Applications\{$Frontend}\Modules\{$Entreprise};

class {$model_class}Controller extends \Applications\{$Frontend}\BackController
	{

	/*	public function beforeList(\Library\HTTPRequest $request)
		{
			//Test if user can list {$model_class}s with token
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
				$this->get{$model_class}sList($request);
			}
			else if ($this->method === 'POST') {
				$this->create{$model_class}($request);
			}
		}

		private function get{$model_class}sList(\Library\HTTPRequest $request)
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

			${$model_name}s = \{$model_class}::all($options);

			if (empty(${$model_name}s))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes {$model_name}s trouvées sur ce serveur');
				return;
			}

			$i = 0;
			foreach ( ${$model_name}s as ${$model_name} ) {
				$js = ${$model_name}->to_json ();
				if ($i !== 0)
					$json .= ',' . $js;
				else
					$json = $js;
				$i ++;
			}
			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput('[' . $json . ']');
		}

		private function create{$model_class}(\Library\HTTPRequest $request)
		{
			${$model_name} = \{$model_class}::find_by_siret(array( 'siret' => $request->postData('siret')));
			if (${$model_name}) {
				header('HTTP/1.1 403 Forbiden');
				exit ('L\'{$model_name} ' . $request->postData('siret') . ' existe déjà');
			}

			${$model_name} = new \{$model_class}();

			${$model_name}->set_attributes(array('siret' => $request->postData('siret'),
								'nom' => $request->postData('nom'),
								'ape' => $request->postData('ape'),
								'tva_intracom' => $request->postData('tva_intracom'),
								'adresse' => $request->postData('role'),
								'suite_adresse' => $request->postData('suite_adresse'),
								'cp' => $request->postData('cp'),
								'ville' => $request->postData('ville'),
								'tel' => $request->postData('tel'),
								'portable' => $request->postData('portable'),
								'email' => $request->postData('email'),
								'regime_commercial' => $request->postData('regime_commercial')
							));

			if (${$model_name}->save())
			{
				header ('Content-Type: application/json; charset=UTF-8');
				$this->page->setOutput(${$model_name}->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		public function beforeBy_id(\Library\HTTPRequest $request)
		{
			//Test if {$model_class} can get, update or delete a {$model_class} with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_id(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->get{$model_class}($request);
			}
			else if ($this->method === 'PUT') {
				$this->update{$model_class}($request);
			}
			else if ($this->method === 'DELETE') {
				$this->delete{$model_class}($request);
			}
		}

		private function get{$model_class}(\Library\HTTPRequest $request)
		{
			try {
				${$model_name} = \{$model_class}::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('{$model_class} not found on this server');
				return;
			}

			$json = ${$model_name}->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

		private function update{$model_class}(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				${$model_name} = \{$model_class}::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('{$model_class} not found on this server');
				return;
			}
			if (${$model_name}->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput(${$model_name}->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}
		
		private function delete{$model_class}(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				${$model_name} = \{$model_class}::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('{$model_class} not found on this server');
				return;
			}
			
			if (${$model_name}->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput(${$model_name}->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
			
		}

		public function beforeBy_name(\Library\HTTPRequest $request)
		{
			//Test if {$model_class} can get, update or delete a {$model_class} with token
			if (!$this->isAuthorized()) {
				header('HTTP/1.1 401 Unauthorized');
				exit('Utilisateur non authentifié');
			}
		}

		public function executeBy_name(\Library\HTTPRequest $request)
		{
			if($this->method === 'GET') {
				$this->get{$model_class}By_name($request);
			}
/*			else if ($this->method === 'PUT') {
				$this->updates{$model_class}($request);
			}
			else if ($this->method === 'DELETE') {
				$this->delete{$model_class}($request);
			}*/
		}

		private function get{$model_class}By_name(\Library\HTTPRequest $request)
		{
			try {
				${$model_name} = \{$model_class}::find_by_name(array( 'nom' => $request->getData('nom')));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('{$model_class} not found on this server');
				return;
			}

			$json = ${$model_name}->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);

		}

	}
"

