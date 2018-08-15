<?php
/**
 *
 * @About:      Database connection manager class
 * @File:       Database.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.0
 * @Developer:  Federico Guzman (federicoguzman@gmail.com)
 **/
class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once (dirname(dirname(dirname(__FILE__)))) . '/bbdd/connect.php';
        // opening db connection
        $db = new Db();
//        $this->conn = $db->connect();
    }
 
    public function createAuto($array)
    {
        //aqui puede incluir la logica para insertar el nuevo auto a tu base de datos
    }
 
}
 
?>