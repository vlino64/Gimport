<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

require_once('../dompdf/autoload.inc.php');
 
# Contenido HTML del documento que queremos generar en PDF.

$mode_impresio = 1;
$idgrups  = intval($_REQUEST['idgrups']);
$cursactual       = $_REQUEST['curs'];
$cursliteral      = $_REQUEST['cursliteral'];
  
$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-13);
$fitxer_sortida .= "hor_see.php?hr=1&idgrups=".$idgrups."&mode_impresio=".$mode_impresio;

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
$dompdf->stream('HorariGrup.pdf');
?>