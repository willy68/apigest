<?php

namespace Applications\Frontend\Modules\Adresse_type;

class Adresse_typeController extends \Applications\Frontend\Modules\ApiController
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
        $adresse_types = \Adresse_type::all($options);
      } else {
        $adresse_types = \Adresse_type::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse_type not found on this server');
      return;
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de récuperer la liste de type d\'adresse');
      return;
    }

    if (empty($adresse_types)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes adresse_types trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($adresse_types);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function create(\Library\HTTPRequest $request)
  {
    $adresse_type = new \Adresse_type();

    $adresse_type->set_attributes(array(
      'code' => $request->postData('code'),
      'libelle' => $request->postData('libelle')
    ));

    try {
      if ($adresse_type->save()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse_type->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible d\'enregistrer le type d\'adresse');
    }
  }

  protected function get(\Library\HTTPRequest $request)
  {
    try {
      $adresse_type = \Adresse_type::find($request->getData('id'));
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse_type not found on this server');
      return;
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de récuperer le type d\'adresse');
      return;
    }

    $json = $adresse_type->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function update(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $adresse_type = \Adresse_type::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse_type not found on this server');
      return;
    }

    try {
      if ($adresse_type->update_attributes($request->post())) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse_type->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de sauvegarder le type d\'adresse');
    }
  }

  protected function delete(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $adresse_type = \Adresse_type::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Adresse_type not found on this server');
      return;
    }

    try {
      if ($adresse_type->delete()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($adresse_type->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\ActiveRecordException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de supprimer le type d\'adresse');
      return;
    }
  }
}
