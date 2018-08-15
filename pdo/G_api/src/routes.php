<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

$secretkey = "LapunyetErAClAuS3cr3ta";

// Extreu el token de l'array i en comprova la validesa entre 
// el que rep i el que t guardat
function authGuard($jwt) {
    include('../func/func_config.php');

    global $secretkey;
    if (@$jwt['HTTP_AUTHORIZATION'][0]) {
        $array = array();
        try {
            $decoded = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $secretkey, array('HS256'));
            $data = json_decode(json_encode($decoded), true);
            // Extreiem el token vàlid de la base de dades
            $db = getConnection();
            $tokenValid = extreuToken($db);
            //
            if ($jwt['HTTP_AUTHORIZATION'][0] == $tokenValid) {
                $array[0] = true;
                $array[1] = 'secure';
            } else {
                $array[0] = false;
                $array[1] = 'invalid token';
            }
        } catch (UnexpectedValueException $e) {
            $array[0] = false;
            $array[1] = $e->getMessage();
        }
    } else {
        $array[0] = false;
        $array[1] = 'forbidden access';
    }
    return $array;
}

//function authGuard($jwt) {RESERVA
//    global $secretkey;
//    if (@$jwt['HTTP_AUTHORIZATION'][0]) {
//        $array = array();
//        try {
//            $decoded = JWT::decode($jwt['HTTP_AUTHORIZATION'][0], $secretkey, array('HS256'));
//            $data = json_decode(json_encode($decoded), true);
//            $json_file = file_get_contents('../db/user.json');
//            $json_array = json_decode($json_file, true);
//            if ($data['id'] == $json_array['id'] and $data['user'] == $json_array['user'] and $jwt['HTTP_AUTHORIZATION'][0] == $json_array['jwt']) {
//                $array[0] = true;
//                $array[1] = 'secure';
//            } else {
//                $array[0] = false;
//                $array[1] = 'invalid token';
//            }
//        } catch (UnexpectedValueException $e) {
//            $array[0] = false;
//            $array[1] = $e->getMessage();
//        }
//    } else {
//        $array[0] = false;
//        $array[1] = 'forbidden access';
//    }
//    return $array;
//}
// Extreure un professor
$app->get('/profes_GET', function (Request $request, Response $response, array $args) {

    include('../func/func_profs.php');
    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);
    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $db = getConnection();

        // Crida a la consulta i recull el resultat
        $result = profes_GET($db);

        $resposta["error"] = false;
        $resposta["message"] = "Profes Carregats: " . count($result); //podemos usar count() para conocer el total de valores de un array
        $resposta["profes"] = $result;
    }

    print_r($resposta);
    return $resposta;
});

// Extreu l'horari d'un alumne un dia concret
$app->get('/getHorariAlumneDia', function (Request $request, Response $response, array $args) {
    include('../func/func_alumnes.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);
    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $db = getConnection();
        $idAlumne       = $data['HTTP_IDALUMNE'][0];
        $idDia          = $data['HTTP_IDDIA'][0];
        $idCursActual   = $data['HTTP_CURSACTUAL'][0];
        // Crida a la consulta i recull el resultat
        $result = horariAlumneDia($db, $idAlumne, $idDia, $idCursActual);

        $resposta["error"] = false;
        $resposta["configuracions"] = $result;
    }

    print_r($resposta);
    return $resposta;
});


// Extreu la configuració de l'app en quant a possibles accions de les famílies
$app->get('/configApp', function (Request $request, Response $response, array $args) {
    include('../func/func_config.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);
    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $db = getConnection();

        // Crida a la consulta i recull el resultat
        $result = configApp($db);

        $resposta["error"] = false;
        $resposta["configuracions"] = $result;
    }

    print_r($resposta);
    return $resposta;
});

// Rep l'identificador de la familia i retorna l'id i el nom comlet del fill o fills
// que corresponen a aquesta familia
$app->post('/nomCompletAlumne', function (Request $request, Response $response, array $args) {
    include('../func/func_alumnes.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);
    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $db = getConnection();
        $idFamilia = $data['HTTP_IDFAMILIA'][0];
        // Crida a la consulta i recull el resultat
        $result = nomCompletAlumne($db, $idFamilia);

        $resposta["error"] = false;
        $resposta["configuracions"] = $result;
    }

    print_r($resposta);
    return $resposta;
});

// Rep el user de la familia i retonra l'id
$app->post('/idFamilia', function (Request $request, Response $response, array $args) {
    include('../func/func_alumnes.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);

    if (!strcmp($guard[1], "secure")) {
        $result = array();

        $loginFamilia = $data['HTTP_LOGINFAMILIA'][0];
        $db = getConnection();

        // Crida a la consulta i recull el resultat
        $result = getIdFamilia($db, $loginFamilia);

        $resposta["error"] = false;
        $resposta["resultat"] = $result;
    }

    print_r($resposta);
    return $resposta;
});

// Rep un id d'alumne i un camp que s'ha d'extreure
$app->post('/getInfoAlumneFamilia', function (Request $request, Response $response, array $args) {
    include('../func/func_alumnes.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);

    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $idAlumne = $data['HTTP_IDALUMNE'][0];
        $tipusDada = $data['HTTP_TIPUSDADA'][0];
        $db = getConnection();

        // Crida a la consulta i recull el resultat
        $result = getInfoAlumneFamilia($db, $idAlumne, $tipusDada);

        $resposta["error"] = false;
        $resposta["resultat"] = $result;
    }

    print_r($resposta);
    return $resposta;
});


// Desa missatge enviat al tutor
$app->post('/missatgeSendToTutor', function (Request $request, Response $response, array $args) {
    include('../func/func_profs.php');

    global $secretkey;
    $resposta = array();
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);
    $idProfessor = $data['HTTP_PROFESSOR'][0];
    $idAlumne = $data['HTTP_ALUMNE'][0];
    $idGrup = $data['HTTP_GRUP'][0];
    $loginTutor = $data['HTTP_LOGIN'][0];
    $numTutor = $data['HTTP_NUMTUTOR'][0];
    $missatge = $data['HTTP_MISSATGE'][0];
    if (!strcmp($guard[1], "secure")) {
        $result = array();
        $db = getConnection();

        // Crida a la consulta i recull el resultat
        $result = missatgeSendToTutor($db, $idProfessor, $idAlumne, $idGrup, $loginTutor, $numTutor, $missatge);

        $resposta["error"] = false;
        $resposta["configuracions"] = $result;
    }

    return $resposta;
});


// Mètode per provar que s'envia ique el server l'accepta
$app->get('/testJWT', function (Request $request, Response $response, array $args) {

    global $secretkey;
    $array = array();
    $data = $request->getHeaders();
    $guard = authGuard($data);

    return $response->withHeader('Content-type', 'application/json')
                    ->withJson($guard);
});


// Rep el user i password de la família i retorna, si és vàlid , el token
$app->post('/authenticate', function (Request $request, Response $response, array $args) {
    include('../func/func_alumnes.php');
    include('../func/func_config.php');
    global $secretkey;

    $data = $request->getParsedBody();
    //    $data = json_decode($request->getBody());
    $user = $data['user'];
    $pass = $data['password'];
    $db = getConnection();

    // Crida a la consulta i recull el resultat

    $idFamilia = comprovaLogin($db, $user, $pass);

    // check id and user
    if ($idFamilia != -1) {

        $array = array();
        $payload = array(
            "id" => $idFamilia,
            "authoruri" => "https://seegatesite.com",
            "exp" => time() + (3600 * 24 * 1),
        );

        try {
            $token = JWT::encode($payload, $secretkey);
            // L'hem de desar a la base de dades
            insereixToken($db, $token);
            $array['status'] = true;
            $array['token'] = $token;
        } catch (Exception $e) {
            $array['status'] = false;
            $array['token'] = $e->getMessage();
        }
    } else {
        $array['status'] = false;
        $array['token'] = 'User or id not found';
    }

    return $response->withHeader('Content-type', 'application/json')
                    ->withJson($array);
});

// Genera un token. El posa al fitxer. El retorna al client
$app->post('/authenticate2', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();
    //    $data = json_decode($request->getBody());
    $post_id = $data['id'];
    $post_user = $data['user'];

    global $secretkey;


    // Extreu d'un fitxer però hauria d'extreure de la base de daades    
    $json_file = file_get_contents('../db/user.json');
    $json_array = json_decode($json_file, true);

    // Així extreu de la base de dade
    // ...
    // check id and user
    if ($post_id == $json_array['id'] and $post_user == $json_array['user']) {
        $array = array();
        $payload = array(
            "id" => $post_id,
            "user" => $post_user,
            "authoruri" => "https://seegatesite.com",
            "exp" => time() + (3600 * 24 * 1),
        );

        try {

            $array['status'] = true;
            $array['token'] = JWT::encode($payload, $secretkey);

            $dt = array();
            $dt['id'] = $post_id;
            $dt['user'] = $post_user;
            $dt['jwt'] = $array['token'];

            $fp = fopen('../db/user.json', 'w');
            fwrite($fp, json_encode($dt));
            fclose($fp);
        } catch (Exception $e) {
            $array['status'] = false;
            $array['token'] = $e->getMessage();
        }
    } else {
        $array['status'] = false;
        $array['token'] = 'User or id not found';
    }

    return $response->withHeader('Content-type', 'application/json')
                    ->withJson($array);
});

function getConnection() {

    require_once('../../bbdd/connect.php');
    return $db;
}

?>