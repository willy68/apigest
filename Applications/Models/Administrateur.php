<?php
  
class Administrateur extends ActiveRecord\Model {
  static $table_name = 'administrateur';
  static $belongs_to = array( 
    array('user', 'class_name' => 'User'),
    array('entreprise', 'class_name' => 'Entreprise')
  );
}
