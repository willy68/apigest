#!/usr/bin/php
<?php

class Generate
{

  protected $db = null;

  protected $query = "SHOW TABLES FROM ";

  protected $options = array();

  protected $nl = "\n";

  public function __construct()
  {
    $this->options['host'] = 'localhost';
    $this->options['db'] = 'paysagest';
    $this->options['user'] = 'root';
    $this->options['password'] = '';
    $this->options['models_dir'] = __DIR__.DIRECTORY_SEPARATOR.'generated_models';
    $this->options['controllers_dir'] = __DIR__.DIRECTORY_SEPARATOR.'generated_controllers';

    $this->getInclude();

  }

  public function getInclude($filename = "connect_bd.php")
  {
    if (is_file($filename)) {
      include $filename;
      if (isset($connect) && is_array($connect)) {
        $this->setOptions($connect);
      } else {
        if (isset($host)) {
          $this->options['host'] = $host;
        }
        if (isset($db)) {
          $this->options['db'] = $db;
        }
        if (isset($user)) {
          $this->options['user'] = $user;
        }
        if (isset($password)) {
          $this->options['password'] = $password;
        }
      }
    }
  }

  public function setOptions($options = null)
  {
    if ($options && is_array($options)) {
      $this->options = array_merge($this->options, $options);
    } else {
      $this->options = $options;
    }
  }

  public function getMysqlConnexion($host, $dbname, $user, $password)
  {
    try {
      $db = new \PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
      $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) { // On attrape les exceptions PDOException
      echo 'La connexion a échoué.'.$this->nl;
      // On affiche le n° de l'erreur ainsi que le message
      echo 'Informations : [,'. $e->getCode().', ] ,'. $e->getMessage().$this->nl;
      exit("Fin du programme!");
    }
    $db->exec("SET NAMES 'utf8'");
            
    return $db;
  }

  public function getTables($dao, $query)
  {
    $tables = $dao->query($query);
    return $tables;
  }
  
  public function getActiveRecordPHP($model_name)
  {
    $model_class = ucfirst($model_name);
    return "<?php
  
class {$model_class} extends ActiveRecord\Model {
  static \$table_name = '{$model_name}';
}
\n";
  }

  public function getControllerPHP($model_name)
  {

  }
  
  public function parseCommandLine() {
    global $argv;
    if (isset($argv)) {
      parse_str(
      join(
          "&",
          array_slice($argv, 1)
      ),
      $_GET
    );
    } else {
      $this->nl = '<br />';
    }    
    $options = &$this->options;

    $callback = function($option, $get) use (&$options){
      switch($get) {
        case 'models':
          $options['models'] = true;
        break;
        case 'controllers':
          $options['controllers'] = true;
        break;
        case 'models_dir':
          $options['models_dir'] = $option;
        break;
        case 'controllers_dir':
          $options['controllers_dir'] = $option;
        break;
        case 'model_controller':
          $options['model_controller'] = $option;
        break;
        case 'app':
          $options['app'] = $option;
        break;
        case 'host':
          $options['host'] = $option;
        break;
        case 'db':
          $options['db'] = $option;
        break;
        case 'user':
          $options['user'] = $option;
        break;
        case 'password':
          $options['password'] = $option;
        break;
        default:
        exit('Commande introuvable: '.$get."\n");
      }
    };

    array_walk($_GET, $callback);

  }

  public function makeModels($dao)
  {
    $tables = $this->getTables($dao, $this->query.$this->options['db']);
  
    $dir = $this->options['models_dir'];
    if (!is_dir($dir)) {
      $oldumask = umask(0);
      if (!mkdir($dir, 0777, true)) {
        umask($oldumask);
        exit('Impossible de créer le dossier '.$dir.$this->nl);
      }
      umask($oldumask);
      echo "Creation du dossier ".$dir.$this->nl;
    }
  
    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $model = $this->getActiveRecordPHP($model_name);
      $file = $this->options['models_dir'].
      DIRECTORY_SEPARATOR.ucfirst($model_name).'.php';
      if (!file_exists($file)) {
        if (($handle = fopen($file, 'x'))) {
            fwrite($handle, $model);
            fclose($handle);
            chmod($file, 0666);
            echo "Ecriture du fichier ".$file.$this->nl;
        }
      }
    }
  }

  public function makeControllers($dao)
  {

  }

  public function run()
  {
    $this->parseCommandLine();

    $dao = $this->getMysqlConnexion($this->options['host'], $this->options['db'], 
    $this->options['user'], $this->options['password']);

    if (isset($this->options['models']) && $this->options['models'] === true) {
      $this->makeModels($dao);
    }
    if (isset($this->options['controllers']) && $this->options['controllers'] === true) {
      $this->makeControllers($dao);
    }
  }  
}

$generate = new generate();

$generate->run();
