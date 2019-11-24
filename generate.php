#!/usr/bin/php
<?php

class Generate
{

  protected $query = "SHOW TABLES FROM ";

  protected $options = array();

  protected $template = array();

  protected $nl = "\n";

  public function __construct()
  {
    $this->options['host'] = 'localhost';
    $this->options['db'] = 'paysagest';
    $this->options['user'] = 'root';
    $this->options['password'] = '';
    $this->options['models_dir'] = __DIR__.DIRECTORY_SEPARATOR.'generated_models';
    $this->options['controllers_dir'] = __DIR__.DIRECTORY_SEPARATOR.'generated_controllers';
    $this->options['routes_dir'] = __DIR__.DIRECTORY_SEPARATOR.'generated_routes';

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
    $model_class = ucfirst($model_name);
    $app = 'Frontend';
    if (isset($this->options['app'])) {
      $app = ucfirst($this->options['app']);
    } 

    if (isset($this->template['controller']) && file_exists($this->template['controller'])) {
      include $this->template['controller'];
      return $controller;
    } else {
      return "<?php
namespace Applications\\{$app}\Modules\\{$model_class};
    
  class {$model_class}Controller extends \Applications\\{$app}\BackController
  {

  }
\n";
    }
  }

  public function getRouteXML($model_name)
  {
    if (isset($this->template['route']) && file_exists($this->template['route'])) {
      include $this->template['route'];
      return $route;
    } else {
      return
    "    <route url=\"/{$model_name}(\\?.+=.+)*\" module=\"{$model_name}\" action=\"list\" vars=\"params\"/>
    <route url=\"/{$model_name}/([0-9+])(\\?.+=.+)*\" module=\"{$model_name}\" action=\"by_id\" vars=\"id,params\"/>
";
    }
  }
  
  public function parseCommandLine() {
    global $argv;

    if (isset($argv)) {
      parse_str(join("&", array_slice($argv, 1)), $_GET);
    } else {
      $this->nl = '<br />';
    }

    $options = &$this->options;
    $template = &$this->template;

    $callback = function($option, $get) use (&$options,&$template){
      switch($get) {
        case 'ms':
        case 'models':
          $options['models'] = true;
          if (!empty($option)) {
            $template['model'] = $option;
          }
        break;
        case 'm':
        case 'model':
          $options['model'] = true;
          if (!empty($option)) {
            $template['model'] = $option;
          }
        break;
        case 'cs':
        case 'controllers':
          $options['controllers'] = true;
          if (!empty($option)) {
            $template['controller'] = $option;
          }
        break;
        case 'c':
        case 'controller':
          $options['controller'] = true;
          if (!empty($option)) {
            $template['controller'] = $option;
          }
        break;
        case 'rs':
        case 'routes':
          $options['routes'] = true;
          if (!empty($option)) {
            $template['route'] = $option;
          }
        break;
        case 'r':
        case 'route':
          $options['route'] = true;
          if (!empty($option)) {
            $template['route'] = $option;
          }
        break;
        case 'md':
        case 'models_dir':
          $options['models_dir'] = $option;
        break;
        case 'cd':
        case 'controllers_dir':
          $options['controllers_dir'] = $option;
        break;
        case 'rd':
        case 'routes_dir':
          $options['routes_dir'] = $option;
        break;
        case 'a':
        case 'app':
          $options['app'] = $option;
        break;
        case 'h':
        case 'host':
          $options['host'] = $option;
        break;
        case 'db':
          $options['db'] = $option;
        break;
        case 'u':
        case 'user':
          $options['user'] = $option;
        break;
        case 'p':
        case 'password':
          $options['password'] = $option;
        break;
        default:
        exit('Commande introuvable: '.$get."\n");
      }
    };

    array_walk($_GET, $callback);

  }

  public function createDir($dir)
  {
    if (!is_dir($dir)) {
      $oldumask = umask(0);
      if (!mkdir($dir, 0777, true)) {
        umask($oldumask);
        exit('Impossible de créer le dossier '.$dir.$this->nl);
      }
      umask($oldumask);
      echo "Creation du dossier ".$dir.$this->nl;
    }   
  }

  public function saveFile($model, $filename)
  {
    if (!file_exists($filename)) {
      if (($handle = fopen($filename, 'x'))) {
        fwrite($handle, $model);
        fclose($handle);
        chmod($filename, 0666);
        echo "Ecriture du fichier ".$filename.$this->nl;
      }
    } else {
      echo "Le fichier ".$filename." existe déjà, opération non permise".$this->nl;
    }
  }

  public function saveModel($model_name, $filename)
  {
    $model = $this->getActiveRecordPHP($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeModels($dao)
  {
    $tables = $this->getTables($dao, $this->query.$this->options['db']);
  
    $dir = $this->options['models_dir'];
    $this->createDir($dir);  
    
    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($model_name).'.php';
      $this->saveModel($model_name, $file);
    }
  }

  public function saveController($model_name, $filename)
  {
    $model = $this->getControllerPHP($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeControllers($dao)
  {
    $tables = $this->getTables($dao, $this->query.$this->options['db']);
  
    $dir = $this->options['controllers_dir'];
    $this->createDir($dir);

    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($model_name).'Controller.php';
      $this->saveController($model_name, $file);
    }
  }

  public function saveRoute($model_name, $filename)
  {
    $model = $this->getRouteXML($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeRoutes($dao)
  {
    $tables = $this->getTables($dao, $this->query.$this->options['db']);
  
    $dir = $this->options['routes_dir'];
    $this->createDir($dir);

    $model = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<routes>
";
    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $filename = $dir.DIRECTORY_SEPARATOR.'route.xml';
      $model .= $this->getRouteXML($model_name);
    }
    $this->saveFile($model."</routes>", $filename);
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
    if (isset($this->options['routes']) && $this->options['routes'] === true) {
      $this->makeRoutes($dao);
    }
  }  
}

$generate = new generate();

$generate->run();
