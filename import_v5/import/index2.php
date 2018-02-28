<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:index.php
 * Autor: Víctor Lino
 * Descripció: Pàgina de selecció d'opcions
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
session_start();
require_once('../../pdo/bbdd/connect.php');
include("../funcions/funcions_generals.php");
include("../funcions/modificacions_bdd.php");
include("../funcions/func_prof_alum.php");
ini_set("display_errors", 1);

//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
    header("location: ../login/access-denied.php");
    exit();
}
?>

<html>
    <head>
        <title>Menú Saga</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
        </script>
    </head>

    <body>

<?php
//  ********************************************************
$carrega = $_POST['carrega'];
//echo "<br>Càrrega:".$carrega;
if (($carrega != 2) AND ( $carrega != 3)) {
    nova_taula_fases($db);
}
if ($carrega == 0) {
    nova_taula_equivalencies($db);
}
if ($carrega < 2) {
    modificacions($db);
}

$camps = array();
$camps = recuperacampdedades($camps, $db);

$geisoft = $_POST['geisoft'];
introduir_fase('carrega', $carrega, $db);

// Password vlino 2017/18
$passGestio = '1b2eec2a86b48664f98a7fae4aeec75f';

if (($carrega == 0) AND ( $geisoft == 0)) {
    buidatge('total', $db);
    $sql = "INSERT INTO `professors` (`idprofessors`, `codi_professor`, `activat`, `historic`) VALUES ";
    $sql .= "(417, 'admin', 'N', 'N'), ";
    $sql .= "(418, 'vlino', 'N', 'N'); ";
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "INSERT INTO `contacte_professor` (`id_professor`, `id_tipus_contacte`, `Valor`) VALUES ";
    $sql .= "(417, 21, 'admin'), ";
    $sql .= "(417, 1, 'Administrador Tutoria'), ";
    $sql .= "(417, '" . $camps['nom_profe'] . "', 'Administrador'), ";
    $sql .= "(417, '" . $camps['cognoms_profe'] . "', 'Tutoria'), ";
    $sql .= "(417, '" . $camps['email'] . "', 'admin@tutoria.cat'), ";
    $sql .= "(417, 20, '42a44cdb0bddac0b342e64674123bab1'), ";
    $sql .= "(417, 12, '625 418 436  '), ";
    $sql .= "(418, 21, 'vlino'), ";
    $sql .= "(418, 1, 'Víctor Lino Martínez'), ";
    $sql .= "(418, '" . $camps['nom_profe'] . "', 'Víctor'), ";
    $sql .= "(418, '" . $camps['cognoms_profe'] . "' , 'Lino Martínez'), ";
    $sql .= "(418, '" . $camps['email'] . "', 'victor.lino@copernic.cat'), ";
    $sql .= "(418, 20,'" . $passGestio . "'), ";
    $sql .= "(418, 12, '625401274'); ";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
    $sql = "INSERT INTO `professor_carrec` (`idprofessors`, `idcarrecs`, `idgrups`, `principal`) VALUES ";
    $sql .= "(417, 4, 0, 0), ";
    $sql .= "(418, 4, 0, 0);  ";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
}

//  ********************************************************
if ($carrega == 1) {
    $sql = "UPDATE `contacte_professor` SET `Valor` = '" . $passGestio . "' ";
    $sql .= "WHERE id_professor = 418 AND id_tipus_contacte = 20; ";
    //echo "<br>".$sql;
    $result = $db->prepare($sql);
    $result->execute();
}


$tmp_name = $_FILES["archivo"]["tmp_name"];
if ($tmp_name == "") {
    //echo "Utilitzarem un fitxer carregat anteriorment.<br>";
    $_SESSION['upload_alumnes'] = '../uploads/alumnes.csv';
    $exportsagaxml = "../uploads/alumnes.csv";
} else {
    //$tmp_name = $_FILES["archivo"]["tmp_name"];
    $exportsagaxml = "../uploads/alumnes.csv";
    $_SESSION['upload_alumnes'] = '../uploads/alumnes.csv';
    move_uploaded_file($tmp_name, $exportsagaxml);
    $today = date("d-m-Y");
    $time = date("H-i-s");
    $newname = $exportsagaxml . "_" . $today . "_" . $time;
    if (!copy($exportsagaxml, $newname)) {
        echo "failed to copy";
    }
    //Netegem el fitxer d'apostrofs
    $str = implode("\n", file('../uploads/alumnes.csv'));
    $fp = fopen('../uploads/alumnes.csv', 'w');
    $find[] = '&apos;';
    $replace[] = ' ';
    $str = str_replace($find, $replace, $str);
    fwrite($fp, $str, strlen($str));
}





//echo "<br>>>> ".$carrega;
//echo "<br>>>> ".$geisoft;
introduir_fase('geisoft', $geisoft, $db);
if (isset($_POST['sincro']))
    $sincro = 1; //echo "<br>>>> ".$sincro;
else
    $sincro = 0;
introduir_fase('sincro', $sincro, $db);

$aprofitar_saga = $_POST['carrega2'];
introduir_fase('aprofitar_saga', $aprofitar_saga, $db);

//mysql_close($conexion);
// Si només és una actualitzacio, anem al formulari
if ($carrega == 3) {
    $page = "./seleccio_actualitzacio_alumnat.php";
    $sec = "0";
    header("Refresh: $sec; url=$page");
} else {
    ?>

            <form enctype="multipart/form-data" action="./index3.php" method="post" name="fcontacto">
                <br><br><br>
                <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
                    <tr><td align="center"><p>Per completar algunes informacions hauries de carregar el fitxer de SAGA <br>que teniu generat del curs passat<br /></td></tr>
                </table>
                <br>



                <table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
                    <tr><td align="center"><div id="pujaono" >
                                <p> Indica el que vulguis fer</p>
                                <input type="radio" name="carrega2" value="1" id="carrega_0" onclick="mostrarReferencia4()" > <b>Utilitzar un fitxer de SAGA carregat prèviament. </b> 

                                <?php
                                if (file_exists('../uploads/pujat_saga.xml')) {
                                    $dia = date("d F Y ", filemtime('../uploads/pujat_saga.xml'));
                                    $hora = date("H:i:s.", filemtime('../uploads/pujat_saga.xml'));
                                    print("<font color=\"red\">Es tracta  d'un fitxer carregat " . $dia . " a les " . $hora . "</font>");
                                } else {
                                    print("<font color=\"red\">No hi ha fitxer previ</font>");
                                }
                                ?>

                                <br>
                                <input type="radio" name="carrega2" value="0" id="carrega_1" onclick="mostrarReferencia4()" > <b>Carregar un fitxer nou de SAGA</b>.<br>
                            </div></td></tr>
                </table>
                <!-- ############################# -->


                <!-- #############   UN FITXER  upload ################ -->
                <div id="upload">
                    <table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
                        <tr><td></td><td></td></tr>
                        <tr colspan="2" align="center">
                            <td align="center">
                                <br>
    <?php echo "ARXIU DE SAGA A CARREGAR  "; ?>
                                <input name="archivo" type="file" id="archivoeso">
                    </table>
                    </td>
                    </tr>
                    </table>
                </div>
                <!-- ############################# -->


                <!-- #############   UN FITXER submit ################ -->
                <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
                    <tr><td align="center"><div id="submit" >
                                <input name="boton" type="submit" id="boton" value="Envia la configuració">
                            </div></td></tr>
                </table>
                <!-- ############################# -->

            </form>

        </body>

<?php } ?>



