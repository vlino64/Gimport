<?php
  class Db {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}
    
       
    
    public static function getInstance() {
        
        define("DB", "cooper_actual");
        define("USER", "root");
        define("PASS", "vlino");
        
      if (!isset(self::$instance)) {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        self::$instance = new PDO('mysql:host=localhost;dbname=cooper_actual', 'root', 'vlino', $pdo_options);
        //self::$instance = new PDO('mysql:host=localhost;dbname=cooper_prova', 'toni_2016', 'toni_2016', $pdo_options);
        //self::$instance -> exec("SET CHARACTER SET utf8");
      }
      return self::$instance;
    }
  }
  
  $db = Db::getInstance();
?>

<?php
/*ini_set("session.cookie_lifetime","7200");
ini_set("session.gc_maxlifetime","7200");

define("DB_HOST", "localhost");
//define("DB_NAME", "joan_oro");
define("DB_NAME", "cooper_actual");
//define("DB_USER", "toni_2016");
//define("DB_PASS", "toni_2016");
define("DB_USER", "root");
define("DB_PASS", "");

$conn = @mysql_connect(DB_HOST,DB_USER,DB_PASS);

if (!$conn) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME, $conn);*/
?>
