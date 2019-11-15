<?php
	namespace Library;
	
    abstract class Record implements \ArrayAccess
    {
        protected $erreurs = array(),
                  $id;
        
        public function __construct(array $donnees = array())
        {
            if (!empty($donnees))
            {
                $this->hydrate($donnees);
            }
        }
        
        public function isNew()
        {
            return empty($this->id);
        }
        
        public function isEmpty()
        {
            foreach ($this as $attr => $value)
            {
                if ($value instanceof Record)
                {
                    if (!$value->isEmpty())
                    {
                        return false;
                    }
                }
                elseif(!empty($value))
                {
                    return false;
                }
            }
            
            return true;
        }
        
        public function erreurs()
        {
            return $this->erreurs;
        }
        
        public function id()
        {
            return $this->id;
        }
        
        public function setId($id)
        {
            $this->id = (int) $id;
        }
        
        public function hydrate(array $donnees)
        {
            foreach ($donnees as $attribut => $valeur)
            {
                $methode = 'set'.ucfirst($attribut);
                
                if (is_callable(array($this, $methode)))
                {
                    $this->$methode($valeur);
                }
            }
        }

		public function toArray()
		{
			$record = array();
			foreach ($this as $attribut => $valeur)
			{
				if (isset($this->$attribut) && is_callable(array($this, $attribut)))
				{
					$record[$attribut] = $this->$attribut();
				}
			}
			return $record;
		}
        
        public function offsetGet($var)
        {
            if (isset($this->$var) && is_callable(array($this, $var)))
            {
                return $this->$var();
            }
        }
        
        public function offsetSet($var, $value)
        {
            $method = 'set'.ucfirst($var);
            
            if (isset($this->$var) && is_callable(array($this, $method)))
            {
                $this->$method($value);
            }
        }
        
        public function offsetExists($var)
        {
            return isset($this->$var) && is_callable(array($this, $var));
        }
        
        public function offsetUnset($var)
        {
            throw new Exception('Impossible de supprimer une quelconque valeur');
        }
    } 
