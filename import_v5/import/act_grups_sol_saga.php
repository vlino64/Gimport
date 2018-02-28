<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:grups_act.php
 * Autor: Víctor Lino
 * Descripció: Carrega els grups del fitxer de saga
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/funcions_generals.php");

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
    header("location: ../login/access-denied.php");
    exit();
}


//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");
?>
<html>
    <head>
        <title>Càrrega automàtica SAGA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>
<?php
if (!extreu_fase('segona_carrega', $db)) {
    buidatge('desdegrups', $db);

    // Eliminem tots els carrecs del curs anterior excepte el de superadministrador
    $sql = "DELETE FROM professor_carrec WHERE ((idcarrecs='1') OR (idcarrecs='2'));";
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "DELETE FROM equivalencies WHERE grup_gp!='';";
    $result = $db->prepare($sql);
    $result->execute();
}

$recompte = $_POST['recompte'];
$exportsagaxml = $_SESSION['upload_saga'];
$resultatconsulta = simplexml_load_file($exportsagaxml);
if (!$resultatconsulta) {
    echo "Carrega fallida.";
} else {
    echo "<br>Carrega correcta<br>";

    // Carreguem els grups i el seu torn
    for ($i = 1; $i <= $recompte; $i++) {
        $crea = $_POST['crea_' . $i];
        $id_grup = $_POST['id_grup_' . $i];
        $nom_grup = $_POST['nom_grup_' . $i];
        $id_torn = $_POST['id_torn_' . $i];
        $crea_grup = $_POST['crea_grup_' . $i];
        //echo $id_torn."<br>";
        // control.lem que no estigui deshabilitat i que tingui torn assignat
        if (($id_torn != "0") AND ( $id_torn != "") AND $crea) {
            $sql = "INSERT grups(codi_grup,nom,idtorn) ";
            $sql .= "VALUES ('" . $id_grup . "','" . $nom_grup . "','" . $id_torn . "');";
            $result = $db->prepare($sql);
            $result->execute();
        }
    }
    introduir_fase('grups', 1, $db);
//        mysql_close($conexion);
    die("<script>location.href = './menu.php'</script>");
}// Else principal
?>
    </body>
