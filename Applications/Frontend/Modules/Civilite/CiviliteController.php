<?php

namespace Applications\Frontend\Modules\Civilite;

class CiviliteController extends \Applications\Frontend\Modules\ApiController
{

  protected function getList(\Library\HTTPRequest $request)
  {
    $options = array();

    if ($request->getExists('entreprise_id') && $request->getData('entreprise_id')) {
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
        $civilites = \Civilite::all($options);
      } else {
        $civilites = \Civilite::all();
      }
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Civilite not found on this server');
      return;
    }

    if (empty($civilites)) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Aucunes civilites trouvées sur ce serveur');
      return;
    }

    $json = $this->jsonArray($civilites);
    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function create(\Library\HTTPRequest $request)
  {
    $civilite = new \Civilite();

    $civilite->set_attributes(array(
      'entreprise_id' => $request->postData('entreprise_id'),
      'libelle' => $request->postData('libelle')
    ));

    try {
      if ($civilite->save()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($civilite->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\DatabaseException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible d\'enregistrer la civilité');
    }
  }

  protected function get(\Library\HTTPRequest $request)
  {
    try {
      $civilite = \Civilite::find($request->getData('id'));
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Civilite not found on this server');
      return;
    }

    $json = $civilite->to_json();

    header('Content-Type: application/json; charset=UTF-8');
    $this->page->setOutput($json);
  }

  protected function update(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $civilite = \Civilite::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Civilite not found on this server');
      return;
    }

    try {
      if ($civilite->update_attributes($request->post())) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($civilite->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\DatabaseException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de sauvegarder la civilité');
    }
  }

  protected function delete(\Library\HTTPRequest $request)
  {
    $id = $request->getData('id');

    try {
      $civilite = \Civilite::find($id);
    } catch (\ActiveRecord\RecordNotFound $e) {
      header('HTTP/1.1 404 Not Found');
      $this->page->setOutput('Civilite not found on this server');
      return;
    }

    try {
      if ($civilite->delete()) {
        header('Content-Type: application/json; charset=UTF-8');
        $this->page->setOutput($civilite->to_json());
      } else {
        header('HTTP/1.1 400 Bad request');
        $this->page->setOutput('400 Bad request');
      }
    } catch (\ActiveRecord\DatabaseException $e) {
      header('HTTP/1.1 400 Bad request');
      $this->page->setOutput('Un problème est survenu, impossible de supprimer la civilité');
    }
  }
}
