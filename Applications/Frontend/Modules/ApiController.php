<?php
  namespace Applications\Frontend\Modules;

  class ApiController extends \Applications\Frontend\BackController
  {
    public function authenticated($check)
    {
      if (!$check) return;
      if (!$this->isAuthorized()) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Utilisateur non authentifié');
      }
    }

    public function jsonArray($records)
    {
      $i = 0;
      $json = null;
      foreach ($records as $record) {
        $js = $record->to_json();
        if ($i !== 0) {
            $json .= "," . $js;
        } else {
            $json = $js;
        }
        $i ++;
      }
      return $json;
    }

    public function beforeList(\Library\HTTPRequest $request)
    {
        //Test if user can list users with token
        if ($this->method === 'GET') {
          $this->authenticated(true);
        }
    }

    public function executeList(\Library\HTTPRequest $request)
    {
        if ($this->method === 'GET') {
            $this->getList($request);
        } elseif ($this->method === 'POST') {
            $this->create($request);
        }
    }

    public function beforeBy_id(\Library\HTTPRequest $request)
    {
        //Test if user can get, update or delete a record with token
        $this->authenticated(true);
    }

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

  }