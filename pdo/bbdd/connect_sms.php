<?php
  class DbSMS {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
      if (!isset(self::$instance)) {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;        
        self::$instance = new PDO('mysql:host=www.geisoft.cat;dbname=sms_geisoft','consulta_sms','consulta',$pdo_options);
      }
      return self::$instance;
    }
  }
  
  $dbSMS = DbSMS::getInstance();
?>
