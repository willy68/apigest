<?php
	namespace Library;
	
	require_once 'Utils.php';

    abstract class Manager
    {
        protected $dao;
        protected $table;
		protected $sql = null;
		protected $query_string;
        
        public function __construct($dao)
        {
            $this->dao = $dao;
            $this->sql = new SQLBuilder($this->dao, $this->table);
        }
        
    	public function getTable()
    	{
    		return $this->table;
    	}
    	
    	public function getDao()
    	{
    		return $this->dao;
    	}
    	
    	public function getNextId()
    	{
    		$sql = 'SHOW TABLE STATUS LIKE "'.$this->getTable().'"';
    		if($stat = $this->dao->query($sql)->fetch())
    		{
	    		$lastid = $stat['Auto_increment'];
    			return $lastid;
    		}
    		else
    			return null;

		}
		
    	public function showColumns($columns)
    	{
			if(!is_string($columns))
			{
				throw new InvalidArgumentException( 'Le paramètre $columns passé doit être une chaine valide');
			}

    		$sql = 'SHOW COLUMNS FROM '.$this->getTable().' LIKE "'.$columns.'"';
    		if($column = $this->dao->query($sql)->fetch())
    			return $column;
    		else
    			return null;
    	}

		//param $columns: string
		public function getEnumFromColumns($columns)
		{
			if($ligne = $this->showColumns($columns))
			{
				$type = substr($ligne['Type'], 6, (strlen($ligne['Type'])-8));
				$enum = preg_split('#\',\'#', $type);
				return $enum;
			}
			return null;
		}
		
		public function getQuery()
		{
			return $this->sql->to_s();
		}
		
		public function getBindValues()
		{
			return $this->sql->bind_values();
		}
		
		public function select($select = '*', $quote = true)
		{
			if(!is_string($select))
			{
				throw new InvalidArgumentException( 'Le paramètre $select passé doit être une chaine valide');
			}
			$this->sql->select($select,$quote);
			return $this;
		}
		
		/* array('key1' => $value1, ...) || 'key', $value  */
		public function where($key, $values = null  )
		{
			//Format array('key1' => $value1, ...)
			if(is_hash($key))
			{
				$this->sql->where($key);
			}
			//Format ('key', $value)
			else if(is_string($key))
			{
				$this->sql->where(array($key => $values));	
			}
			
			return $this;			
		}
		
		/* array('key1' => $value1, ...) || 'key', $value */
		public function or_where($key, $values = null  )
		{			
			//Format array('key1' => $value1, ...)
			if(is_hash($key))
			{
				$this->sql->or_where($key);
			}
			else if(is_string($key))
			{
				$this->sql->or_where(array($key => $values));	
			}
			
			return $this;			
		}
		
		public function setWhereOp($op)
		{
			$this->sql->set_where_op($op);
			return $this;
		}
        
		public function order($order,$sens = 'ASC')
		{
			$this->sql->order($order,$sens);
			return $this;
		}

		public function group($group)
		{
			$this->sql->group($group);
			return $this;
		}

		public function having($having)
		{
			$this->sql->having($having);
			return $this;
		}

		public function limit($limit)
		{
			$this->sql->limit($limit);
			return $this;
		}

		public function offset($offset)
		{
			$this->sql->offset($offset);
			return $this;
		}

		public function joins($joins)
		{
			$this->sql->joins($joins);
			return $this;
		}

		public function insert($hash, $pk=null, $sequence_name=null)
		{
			$this->sql->insert($hash, $pk, $sequence_name);
			return $this;
		}

		public function update($mixed)
		{
			$this->sql->update($mixed);
			return $this;
		}
		
		public function flush()
		{
			$this->sql->flush();
			return $this;
		}
		
		public function getFetch()
		{
			$q = $this->prepare();
			if($q)
			{
				$q->execute();
				if($result = $q->fetch(PDO::FETCH_ASSOC))
				{
					$q->closeCursor();
					return $result;	
				}
			}
			return null;			
		}

		public function getFetchAll()
		{
			$result = array();
			
			$q = $this->prepare();
			
			if($q)
			{
				$q->execute();
				while($ligne = $q->fetch(PDO::FETCH_ASSOC))
				{
					$result[] = $ligne;
				}
				$q->closeCursor();
				return $result;	
			}

			return null;			
		}

		/* string $sql avec marqueurs anonyme, 
		** array() $values tableau associatif
		** return PDOStatement 
		**/
		public function prepare($sql = null, $values = null )
		{
			if($sql == null)
				$sql = $this->sql->to_s();
			if($values == null)
				$values = $this->sql->bind_values();
			
			$q = $this->dao->prepare($sql);
			
			$i = 1;
			foreach( $values as $value)
			{
				if(is_int($value))
                    $param = PDO::PARAM_INT;
                elseif(is_bool($value))
                    $param = PDO::PARAM_BOOL;
                elseif(is_null($value))
                    $param = PDO::PARAM_NULL;
                elseif(is_string($value))
                    $param = PDO::PARAM_STR;
                    
				$q->bindValue($i,$value,$param);
				$i++;
			}
			return $q;			
		}
		
		public function executeAndFetch(PDOStatement $q)
		{
			$q->execute();
			if($result = $q->fetch(PDO::FETCH_ASSOC))
			{
				$q->closeCursor();
				return $result;
			}
				
			return null;
		}

		public function executeAndFetchAll(PDOStatement $q)
		{
			$result = array();
			$q->execute();
			while($ligne = $q->fetch(PDO::FETCH_ASSOC))
			{
				$result[] = $ligne;
			}

			$q->closeCursor();
			return $result;
		}

		public function execute()
		{
			$q = $this->prepare();
			if($q)
			{
				$q->execute();
				return $q;
			}
			return null;
		}
    } 
