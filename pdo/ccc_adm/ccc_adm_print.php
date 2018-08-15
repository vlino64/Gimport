<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

# Cargamos la librería dompdf.
require_once('../dompdf/autoload.inc.php');
 
# Contenido HTML del documento que queremos generar en PDF.

$criteri       = isset($_REQUEST['criteri']) ? $_REQUEST['criteri'] : 'CAP';
$valor_criteri = isset($_REQUEST['valor_criteri']) ? $_REQUEST['valor_criteri'] : 0;
$sub_criteri   = isset($_REQUEST['sub_criteri']) ? $_REQUEST['sub_criteri'] : 'idalumne';
$data_inici    = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : getCursActual($db)["data_inici"];
$data_fi       = isset($_REQUEST['data_fi']) ? $_REQUEST['data_fi'] : getCursActual($db)["data_fi"];


$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-17)."ccc_adm_see.php?hr=1&data_inici=";
$fitxer_sortida .= $data_inici."&data_fi=".$data_fi."&criteri=".$criteri."&valor_criteri=".$valor_criteri."&sub_criteri=".$sub_criteri;

$html = file_get_contents($fitxer_sortida);

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A3', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('InformeCCC.pdf');
?>
