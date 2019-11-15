<?php
	namespace Library;
	
    class PDOFactory
    {
        public static function getMysqlConnexion($host, $dbname, $user, $password)
        {
         try{
            	$db = new \PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
            	$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
		 catch (\PDOException $e) // On attrape les exceptions PDOException
		    {
        		echo 'La connexion a échoué.<br />';
        		echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); // On affiche le n° de l'erreur ainsi que le message
    		}	
            $db->exec("SET NAMES 'utf8'");
            
            return $db;
        }
        				
    } 
