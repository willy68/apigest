<?php

include "connect_bd.php";

$query = "SHOW TABLES FROM {$db}";

function getMysqlConnexion($host, $dbname, $user, $password) {
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

function getActiveRecordPHP($model_name) {
	$model_table = ucfirst($model_name);
  return "<?php \n
	class {$model_table} extends ActiveRecord\Model { \n
		static \$table_name = '{$model_name}'; \n
	} \n
	";
}

if ( isset( $argv ) ) {
    parse_str(
        join( "&", array_slice( $argv, 1 )
    ), $_GET );
}

$dao = getMysqlConnexion($host, $db, $user, $password);

$tables = $dao->query($query);

while ($table = $tables->fetch()) {
	$model_name = $table[0];
	$model = getActiveRecordPHP($model_name);
	print($model);
	print("\n");
}

