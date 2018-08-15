<?php

/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.0
 * @Developer:  Federico Guzman (federicoguzman@gmail.com)
 * */
/* Los headers permiten acceso desde otro dominio (CORS) a nuestro REST API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro RESTfull API a el mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro RESTfull API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 * */
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

//include_once '../include/Config.php';
//include_once '../include/DbHandler.php';
/* Puedes utilizar este file para conectar con base de datos incluido en este demo; 
 * si lo usas debes eliminar el include_once del file Config ya que le mismo está incluido en DBHandler 
 * */
//require_once('../../bbdd/connect.php');
// Per carregar la API KEY
require_once('../../func/constants.php');



require '../libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* Usando GET para consultar los autos */

$app->get('/profes_get',  'authenticate', function() {

    $response = array();
    $result = array();
//    $db = new DbHandler();
    $db = getConnection();
    $sql = "SELECT idprofessors,codi_professor,activat,historic FROM professors WHERE idprofessors = 414;";
    $rs = $db->query($sql);
    foreach ($rs->fetchAll() as $row) {
        array_push($result,array(
                'idprofessors' => $row['idprofessors'],
                'codi_professor'  => $row['codi_professor'],
                'activat' => $row['activat'],
                'historic' => $row['historic'])) ;
    }
    
    $response["error"] = false;
    $response["message"] = "Profes Carregats: " . count($result); //podemos usar count() para conocer el total de valores de un array
    $response["profes"] = $result;

    echoResponse(200, $response);
});

/* Usando POST para crear un auto */

$app->post('/profes_post', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('codi_professor', 'activat', 'historic'));

    $response = array();
    //capturamos los parametros recibidos y los almacxenamos como un nuevo array
    $param['codi_professor'] = $app->request->post('codi_professor');
    $param['activat'] = $app->request->post('activat');
    $param['historic'] = $app->request->post('historic');

    $db = getConnection();
    $sql = "INSERT INTO professors(codi_professor,activat,historic) "
            . "VALUES ("
            . "'".$param['codi_professor']."',"
            . "'".$param['activat']."',"
            . "'".$param['historic']."');";
    $rs = $db->query($sql);

    /* Podemos crear un metodo que almacene el nuevo auto, por ejemplo: */
    //$auto = $db->createAuto($param);

    if (is_array($param)) {
        $response["error"] = false;
        $response["message"] = "Professor creado satisfactoriamente!";
        $response["auto"] = $param;
    } else {
        $response["error"] = true;
        $response["message"] = "Error al crear professor. Por favor intenta nuevamente.";
    }
    echoResponse(201, $response);
});

/* corremos la aplicación */
$app->run();

/* * ********************* USEFULL FUNCTIONS ************************************* */

/**
 * Verificando los parametros requeridos en el metodo o endpoint
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);

        $app->stop();
    }
}

/**
 * Validando parametro email si necesario; un Extra ;)
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);

        $app->stop();
    }
}

/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

/**
 * Agregando un leyer intermedio e autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        //$db = new DbHandler(); //utilizar para manejar autenticacion contra base de datos
        // get the api key
        $token = $headers['Authorization'];

        // validating api key
        if (!($token == API_KEY)) { //API_KEY declarada en Config.php
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Acceso denegado. Token inválido";
            echoResponse(401, $response);

            $app->stop(); //Detenemos la ejecución del programa al no validar
        } else {
            //procede utilizar el recurso o metodo del llamado
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Falta token de autorización";
        echoResponse(400, $response);

        $app->stop();
    }
}

function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="vlino";
    $dbname="cooper_actual";
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

?>
