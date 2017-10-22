<?php 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

require_once('../dompdf/autoload.inc.php');

$data_inici = isset($_REQUEST['data_inici']) ? $_REQUEST['data_inici'] : '';
$data_fi    = isset($_REQUEST['data_fi']) ? $_REQUEST['data_fi'] : '';
$idgrups    = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;
$idmateria  = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0;
 
# Contenido HTML del documento que queremos generar en PDF.
$fitxer_sortida  = "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-23);
$fitxer_sortida .= "informes_grup_see.php?hr=1&idgrups=".$idgrups."&idmateria=".$idmateria."&data_inici=".$data_inici."&data_fi=".$data_fi;

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
$dompdf->stream('Informes_Alumnes.pdf');
?>