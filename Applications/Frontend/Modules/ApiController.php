<?php

namespace Applications\Frontend\Modules;

/**
 * ApiController class
 * @author William Lety <william.lety@gmail.com>
 */
class ApiController extends \Applications\Frontend\BackController
{
  /**
   * Determine si l'on check l'authentification
   * de l'utilisateur
   * 
   * @var boolean
   */
  protected $checkAuth = true;

  /**
   * Change la variable checkAuth
   *
   * @param boolean $check
   * @return void
   */
  public function setCheckAuth($check)
  {
    $this->checkAuth = $check;
  }

  /**
   * Test l'authentification de l'utilisateur
   *
   * @return void
   */
  public function authenticated()
  {
    if (!$this->checkAuth) return;
    if (!$this->isAuthorized()) {
      header('HTTP/1.1 401 Unauthorized');
      exit('Utilisateur non authentifié');
    }
  }

  /**
   * Surcharge la fonction de la classe \Library\Controller
   * Aucune vue associé, ne fait donc rien
   *
   * @param string $view
   * @return void
   */
  public function setView($view)
  {
  }

  /**
   * Transform le tableau $record
   * en un tableau d'objets json
   * 
   * @param array $records
   * @return string
   */
  public function jsonArray($records)
  {
    $i = 0;
    $json = null;
    foreach ($records as $record) {
      $json .= $record->to_json();
      $i++;
      if ($i < count($records)) {
        $json .= ',';
      }
    }
    return '[' . $json . ']';
  }

  /**
   * Test si l'utilisateur peut lister les enregistrements de la table
   * par la methode GET
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function beforeList(\Library\HTTPRequest $request)
  {
    //Test if user can list table records with token
    if ($this->method === 'GET') {
      $this->authenticated();
    }
  }

  /**
   * Execute l'action 'List'
   * suivant la methode GET ou POST
   * 
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function executeList(\Library\HTTPRequest $request)
  {
    if ($this->method === 'GET') {
      $this->getList($request);
    } elseif ($this->method === 'POST') {
      $this->create($request);
    }
  }

  /**
   * Récupère la liste d'enregistrements
   * a implementer dans une classe dérivée
   * 
   * @param \Library\HTTPRequest $request
   * @return void
   */
  protected function getList(\Library\HTTPRequest $request)
  {
  }

  /**
   * Test si l'utilisateur peut créer un enregistrement de la table user
   * par la methode POST
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function beforeCreate(\Library\HTTPRequest $request)
  {
    //Test if user can list table records with token
    if ($this->method === 'POST') {
      $this->authenticated();
    }
  }

  /**
   * Execute l'action 'create' suivant la methode POST
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function executeCreate(\Library\HTTPRequest $request)
  {
    if ($this->method === 'POST') {
      $this->create($request);
    }
  }

  /**
   * Crée un enregistrement
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  protected function create(\Library\HTTPRequest $request)
  {
  }

  /**
   * Test si l'utilisateur peut executer les actions
   * get, update ou delete sur un enregistrement par son id
   * 
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function beforeBy_id(\Library\HTTPRequest $request)
  {
    //Test if user can get, update or delete a record with token
    $this->authenticated();
  }

  /**
   * Execute les actions get, update ou delete
   * par l'id de l'enregistrement
   * 
   * @param \Library\HTTPRequest $request
   * @return void
   */
  public function executeBy_id(\Library\HTTPRequest $request)
  {
    if ($this->method === 'GET') {
      $this->get($request);
    } elseif ($this->method === 'PUT') {
      $this->update($request);
    } elseif ($this->method === 'DELETE') {
      $this->delete($request);
    }
  }

  /**
   * Récupère un enregistrement par son id
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  protected function get(\Library\HTTPRequest $request)
  {
  }

  /**
   * Mise à jour d'un enregistrement par son id
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  protected function update(\Library\HTTPRequest $request)
  {
  }

  /**
   * Detruit un enregistrement par son id
   *
   * @param \Library\HTTPRequest $request
   * @return void
   */
  protected function delete(\Library\HTTPRequest $request)
  {
  }
}
