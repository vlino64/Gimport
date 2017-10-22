<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

require_once('../dompdf/autoload.inc.php');
 
# Contenido HTML del documento que queremos generar en PDF.

$mode_impresio = 1;
$idespais_centre  = intval($_REQUEST['idespais_centre']);
  
$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-12);
$fitxer_sortida .= "ec_see.php?hr=1&idespais_centre=".$idespais_centre."&mode_impresio=".$mode_impresio;

$html = file_get_contents($fitxer_sortida);
 
// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('HorariEspaiCentre.pdf');
?>
