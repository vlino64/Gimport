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
$data_inici    = $_REQUEST['data_inici'];
$data_fi       = $_REQUEST['data_fi'];

$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-21)."ccc_adm_inf.php?hr=1&data_inici=";
$fitxer_sortida .= $data_inici."&data_fi=".$data_fi."&criteri=".$criteri."&valor_criteri=".$valor_criteri;

$html = file_get_contents($fitxer_sortida);

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
//$dompdf->set_option('isHtml5ParserEnabled', true);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('InformeCCC.pdf');
?>
