<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

# Cargamos la librería dompdf.
require_once('../dompdf/autoload.inc.php');
 
# Contenido HTML del documento que queremos generar en PDF.
$mode_impresio = 1;
$data_inici    = $_REQUEST['data_inici'];
$data_fi       = $_REQUEST['data_fi'];
$c_alumne      = $_REQUEST['c_alumne'];
$percent       = $_REQUEST['percent'];

$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-16)."assist_see.php?hr=1&data_inici=".$data_inici."&mode_impresio=".$mode_impresio;
$fitxer_sortida .= "&data_fi=".$data_fi."&c_alumne=".$c_alumne."&percent=".$percent;

if ( isset($_REQUEST['grup_materia'])) {
	$grup_materia    = $_REQUEST['grup_materia'];
	$fitxer_sortida .= "&grup_materia=".$grup_materia;
}
else {
	$idgrups         = $_REQUEST['idgrups'];
	$idmateria       = $_REQUEST['idmateria'];
	$fitxer_sortida .= "&idgrups=".$idgrups."&idmateria=".$idmateria;
}

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
$dompdf->stream('Informe.pdf');
?>