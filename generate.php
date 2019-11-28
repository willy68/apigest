#!/usr/bin/php
<?php

class Generate
{

  protected $query = "SHOW TABLES FROM ";

  protected $dao = null;

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

    $this->dao = $db;
            
    return $db;
  }

  public function getTables($dao, $query)
  {
    $tables = $dao->query($query);
    return $tables;
  }

  public function getColumns($dao, $query)
  {
    $columns = $dao->query($query);
    return $columns;
  }
  
  public function getColumnsQuery($db, $table)
  {
    return "SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$table}'
    AND COLUMN_NAME NOT IN ('id','created_at','updated_at','password')";
  }

  public function getColumnsArray($sql)
  {
    $columns = $this->getColumns($this->dao, $sql);
    $cols = array();
    while ($column = $columns->fetch(PDO::FETCH_ASSOC)) {
      $cols[] = $column['COLUMN_NAME'];
    }
    return $cols;
  }

  public function parseCommandLine() {
    // $opts = getopt('m::d::t::' ,['make::', 'dir::', 'template::']);
    global $argv;

    if ('cli' === PHP_SAPI) {
      parse_str(join("&", array_slice($argv, 1)), $_GET);
    } else {
      $this->nl = '<br />';
    }

    $options = &$this->options;
    // $template = $this->template;

    foreach($_GET as $get => $option) {
      switch($get) {
        case 'm':
        case 'make':
          if (!empty($option)) {
            $options[$option] = true;
          } else {
            exit('Aucune action demandée, fin du programme!'.$this->nl);
          }
        break;
        case 'd':
        case 'dir':
          if (!empty($option)) {
            $options['dir'] = $option;
          }
        break;
        case 'tpl':
        case 'template':
          if (!empty($option)) {
            $options['template'] = $option;
          }
        break;
        case 'a':
        case 'application':
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
        case 't':
        case 'table':
          $options['table'] = $option;
        break;
        default:
        exit('Commande introuvable: '.$get."\n");
      }
    }
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

  public function getActiveRecordPHP($model_name)
  {
    $model_class = ucfirst($model_name);
    return "<?php
  
class {$model_class} extends ActiveRecord\Model {
  static \$table_name = '{$model_name}';
}
\n";
  }

  public function saveModel($model_name, $filename)
  {
    $model = $this->getActiveRecordPHP($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeModels()
  {
    $tables = $this->getTables($this->dao, $this->query.$this->options['db']);
  
    $dir = $this->options['models_dir'];
    $this->createDir($dir);  
    
    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($model_name).'.php';
      $this->saveModel($model_name, $file);
    }
  }

  public function makeModel()
  {
    if (isset($this->options['table'])) {
      $table = $this->options['table'];
      $dir = $this->options['models_dir'];
      $this->createDir($dir);

      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($table).'.php';
      $this->saveModel($table, $file);
    } else {
      exit("Option [table] ou [t] manquante ex:".$this->nl."
      ./generate m=model t=user");
    }
}

public function getControllerPHP($model_name)
{
  $model_class = ucfirst($model_name);
  $app = 'Frontend';
  if (isset($this->options['app'])) {
    $app = ucfirst($this->options['app']);
  } 

  if (isset($this->options['template']) && file_exists($this->options['template'])) {

    $sql = $this->getColumnsQuery($this->options['db'], $model_name);
    
    $columns = $this->getColumnsArray($sql);
    $attributes = '';
    $i = 0;
    foreach ($columns as $column) {
      $attributes .= "'{$column}' => \$request->postData('{$column}')";
      $i++;
      if ($i < count($columns)) $attributes .= ',';
      $attributes .= "\n";
    }
    $controller = include $this->options['template'];
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

public function saveController($model_name, $filename)
  {
    $model = $this->getControllerPHP($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeControllers()
  {
    $tables = $this->getTables($this->dao, $this->query.$this->options['db']);
  
    $dir = $this->options['controllers_dir'];
    $this->createDir($dir);

    while ($table = $tables->fetch()) {
      $model_name = $table[0];
      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($model_name).'Controller.php';
      $this->saveController($model_name, $file);
    }
  }

  public function makeController()
  {
    if (isset($this->options['table'])) {
      $table = $this->options['table'];
      $dir = $this->options['controllers_dir'];
      $this->createDir($dir);

      $file = $dir.DIRECTORY_SEPARATOR.ucfirst($table).'Controller.php';
      $this->saveController($table, $file);
    } else {
      exit("Option <table> ou <t> manquante ex:
      ./generate m=controller t=user");
    }
  }

  public function getRouteXML($model_name)
  {
    if (isset($this->options['template']) && file_exists($this->options['template'])) {
      $route = include $this->options['template'];
      return $route;
    } else {
      return
    "    <route url=\"/{$model_name}(\\?.+=.+)*\" module=\"{$model_name}\" action=\"list\" vars=\"params\"/>
    <route url=\"/{$model_name}/([0-9+])(\\?.+=.+)*\" module=\"{$model_name}\" action=\"by_id\" vars=\"id,params\"/>
";
    }
  }
  
  public function saveRoute($model_name, $filename)
  {
    $model = $this->getRouteXML($model_name);
    $this->saveFile($model, $filename);
  }

  public function makeRoutes()
  {
    $tables = $this->getTables($this->dao, $this->query.$this->options['db']);
  
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

  public function makeRoute()
  {
    if (isset($this->options['table'])) {
      $table = $this->options['table'];
      $dir = $this->options['routes_dir'];
      $this->createDir($dir);
      
      $model = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<routes>
";
      $filename = $dir.DIRECTORY_SEPARATOR.'route.xml';
      $model .= $this->getRouteXML($table);
      $this->saveFile($model."</routes>", $filename);
    } else {
      exit("Option <table> ou <t> manquante ex:
      ./generate m=route t=user");
    }
  }

  public function run()
  {
    $this->parseCommandLine();

    $dao = $this->getMysqlConnexion($this->options['host'], $this->options['db'], 
    $this->options['user'], $this->options['password']);

    if (isset($this->options['models']) && $this->options['models'] === true) {
      $this->makeModels();
    }
    if (isset($this->options['controllers']) && $this->options['controllers'] === true) {
      $this->makeControllers();
    }
    if (isset($this->options['routes']) && $this->options['routes'] === true) {
      $this->makeRoutes();
    }

    if (isset($this->options['model']) && $this->options['model'] === true) {
      $this->makeModel();
    }
    if (isset($this->options['controller']) && $this->options['controller'] === true) {
      $this->makeController();
    }
    if (isset($this->options['route']) && $this->options['route'] === true) {
      $this->makeRoute();
    }
  }  
}

$generate = new generate();

$generate->run();
