<?php
namespace Applications\Frontend\Modules\Adresse;

class AdresseController extends \Applications\Frontend\Modules\ApiController
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
          $adresses = \Adresse::all($options);
        } else {
          $adresses = \Adresse::all();
        }
			} catch (\ActiveRecord\RecordNotFound $e) {
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('User role not found on this server');
				return;
			}

			if (empty($adresses))
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Aucunes adresses trouvées sur ce serveur');
				return;
			}

      $json = $this->jsonArray($adresses);
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

      $adresse = new \Adresse();

			$adresse->set_attributes(array(/*
    'client_id' => $request->postData('client_id'),
    'adresse_1' => $request->postData('adresse_1'),
    'adresse_2' => $request->postData('adresse_2'),
    'adresse_3' => $request->postData('adresse_3'),
    'cp' => $request->postData('cp'),
    'ville' => $request->postData('ville'),
    'pays' => $request->postData('pays'),
    'adresse_type_id' => $request->postData('adresse_type_id')
*/
							));

			if ($adresse->save())
			{
				header ('Content-Type: application/json; charset=UTF-8');
				$this->page->setOutput($adresse->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}

		protected function get(\Library\HTTPRequest $request)
		{
			try {
				$adresse = \Adresse::find($request->getData('id'));
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Adresse not found on this server');
				return;
			}

			$json = $adresse->to_json();

			header ( 'Content-Type: application/json; charset=UTF-8' );
			$this->page->setOutput($json);
		}

		protected function update(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$adresse = \Adresse::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Adresse not found on this server');
				return;
			}
			if ($adresse->update_attributes($request->post()))
			{
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($adresse->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}
		}
		
		protected function delete(\Library\HTTPRequest $request)
		{
			$id = $request->getData('id');

			try {
				$adresse = \Adresse::find($id);
			}
			catch(\ActiveRecord\RecordNotFound $e)
			{
				header('HTTP/1.1 404 Not Found');
				$this->page->setOutput('Adresse not found on this server');
				return;
			}
			
			if ($adresse->delete()) {
				header ( 'Content-Type: application/json; charset=UTF-8' );
				$this->page->setOutput($adresse->to_json());
			} else {
				header('HTTP/1.1 400 Bad request');
				$this->page->setOutput('400 Bad request');
			}			
		}

	}
