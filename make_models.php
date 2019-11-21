<?php

include "connect_bd.php";

$query = "SHOW TABLES FROM {$db}";

$models_dir = __DIR__.DIRECTORY_SEPARATOR.'generated_models';

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
	$model_class = ucfirst($model_name);
	return "<?php

class {$model_class} extends ActiveRecord\Model {
 static \$table_name = '{$model_name}';
}
\n";
}

if ( isset( $argv ) ) {
    parse_str(
        join( "&", array_slice( $argv, 1 )
    ), $_GET );
}

if (isset($_GET['models_dir'])) {
	$models_dir = __DIR__.DIRECTORY_SEPARATOR.$_GET['models_dir'];
}

$dao = getMysqlConnexion($host, $db, $user, $password);

$tables = $dao->query($query);

if (!is_dir($models_dir)) {
	mkdir($models_dir, 0777);
}

while ($table = $tables->fetch()) {
	$model_name = $table[0];
	$model = getActiveRecordPHP($model_name);
  if (($handle = fopen($models_dir.DIRECTORY_SEPARATOR.ucfirst($model_name).'.php', 'x'))) {
		fwrite($handle, $model);
		fclose($handle);
	}
	print($model);
}

