<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:prof-act.php
* Autor: Víctor Lino
* Descripció: Actualització o càrrega de professorat
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include("../config.php");
include("../funcions/func_grups_materies.php");
include("../funcions/funcions_generals.php");

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
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

    include("../config.php");
    
    
    if ($_GET['retorn'] == 'yes')
        {
        ?>
        <script type="text/javascript">
        alert("     Alguna de les opcions no és correcta o manquen configuracions. \n\
        Revisa les opcions escollides. \n\
        En ocasions pot resultar útil buidar el formulari amb el botò inferior \n");
        </script>
        <?php
        }
    
    
    // Netegem repeticions el fitxer equivalències
    $sql="DELETE FROM `equivalencies` WHERE grup_gp!='' AND grup_ga='' AND grup_saga='';";
    $result=mysql_query($sql);
    if (!$result) {die(_ERR_NETEJA_EQUIV3.mysql_error());}
	
//   if(extreu_fase('app_horaris')==5)
//      {
//      // Aqui haurem de fer-lo seleccionar si vol iintroduir-ho tot manualment 
//      //o vol carregar la informació de saga i  fer retocs manuals
//       select_plaestudis_saga();}
//   else
//      {
//      if ((extreu_fase('modalitat_fitxer')==1) OR (extreu_fase('modalitat_fitxer')==2)) 
//         {
         ?> 
<!doctype html>
<!doctype html>
<!doctype html>
<html>
<head>
	<title></title>
</head>
<body>
<p style="text-align: center;"><strong>Llegiu aquestes intruccions i seleccioneu les opcions amb cura. Si teniu dubtes, consulteu amb la persona de contacte de GEISoft</strong></p>

<form action="main_mat.php" method="post" name="formulari matèries" >&nbsp;
<table align="center" border="0" cellpadding="1" cellspacing="1" style="width: 750px">
	<tbody>
		<tr>
			<td style="background-color: rgb(255, 204, 102);">
			<p><input name="programaHoraris" type="radio" value="0" />&nbsp;<b>No utilitzeu cap programa de generaci&oacute; d&#39;horaris.&nbsp;</b><strong style="color: rgb(178, 34, 34); background-color: rgb(255, 204, 102);"><span style="background-color: rgb(218, 165, 32);">Disposeu de dues opcions:</span></strong></p>

			<p style="margin-left: 40px;"><input name="novaIncorporacioNoHoraris" type="radio" value="1" />1. Sou un centre de nova incorporaci&oacute;.&nbsp;<span style="color: rgb(128, 128, 128);">S&#39;incorporaran les mat&egrave;ries/m&ograve;duls/UFs del fitxer de SAGA carregat i si el fitxer &eacute;s del curs passat s&#39;hauran de retocar els canvis que hi puguin haver</span></p>

			<p style="margin-left: 40px;"><span style="color: rgb(128, 128, 128);">_______________________________________________________________________________</span></p>

			<p style="margin-left: 40px;"><span style="color: rgb(128, 128, 128);"><input name="novaIncorporacioNoHoraris" type="radio" value="0" /></span>2. No sou un centre de nova incorporaci&oacute;.<span style="color: rgb(128, 128, 128);">&nbsp;</span><span style="color:#B22222;"><strong><span style="background-color:#DAA520;">Disposeu de dues opcions:</span></strong></span></p>

			<p style="margin-left: 80px;"><input name="mantenirNoHoraris" type="radio" value="1" />a. Mantenir la informaci&oacute; que ja teniu carregada del curs passat&nbsp;<span style="color: rgb(128, 128, 128);">i fer els canvis que puguin ser necessaris. (Recomanada)</span></p>

			<p style="margin-left: 80px;"><input name="mantenirNoHoraris" type="radio" value="0" />b. Incorporar la informaci&oacute; del fitxer de SAGA carregat&nbsp;<span style="color: rgb(128, 128, 128);">i </span><span style="color: rgb(128, 128, 128); background-color: rgb(255, 204, 102);">si el fitxer &eacute;s del curs passat s&#39;hauran de &nbsp;</span><span style="color: rgb(128, 128, 128);">fer els canvis que puguin ser necessaris.&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="background-color: rgb(255, 255, 255);">&nbsp;</td>
		</tr>
		<tr>
			<td style="background-color: rgb(204, 153, 0);">
			<p><input name="programaHoraris" type="radio" value="1" />&nbsp;<b>Utilitzes un programa de generaci&oacute; d&#39;horaris (GPUntis, Pe&ntilde;alara, Horwin, Kronowin o aSc).&nbsp;</b><strong style="color: rgb(178, 34, 34); background-color: rgb(255, 204, 102);">Disposeu de dues opcions:</strong></p>

			<p style="margin-left: 40px;"><input name="tensCCFFLOE" type="radio" value="0" />1. No tenim cicles formatius LOE en el fitxer d'horaris carregat.&nbsp;<span style="color: rgb(128, 128, 128);">Les mat&egrave;ries/cr&egrave;dits es carregaran des del programa d&#39;horaris</span></p>

			<p style="margin-left: 40px;"><span style="color: rgb(128, 128, 128);">______________________________________________________________________________</span></p>

			<p style="margin-left: 40px;"><input name="tensCCFFLOE" type="radio" value="1" />2. Si, tenim cicles formatius LOE en el fitxer d'horaris carregat.&nbsp;<strong style="color: rgb(178, 34, 34); background-color: rgb(255, 204, 102);">Disposeu de dues opcions:</strong></p>

			<p style="margin-left: 80px;"><input name="comMateries" type="radio" value="1" />a. Vull tractar els m&oacute;duls com si si fossin mat&egrave;ries anuals. Sense tractar les unitats formatives.&nbsp;<span style="color: rgb(128, 128, 128);">E</span><span style="color: rgb(128, 128, 128);">s carregaran els m&oacute;duls tal i com estan el programa d&#39;horaris</span></p>

			<p style="margin-left: 80px;"><input name="comMateries" type="radio" value="0" />b. Vull carregar les unitats formatives.&nbsp;<strong style="color: rgb(178, 34, 34); background-color: rgb(255, 204, 102);">Disposeu de dues opcions:</strong></p>

			<p style="margin-left: 120px;"><input name="novaIncorporacioHoraris" type="radio" value="1" />1. Sou un centre de nova incorporaci&oacute;. <span style="color: rgb(128, 128, 128);">Extreurem les mat&egrave;ries i/o cr&egrave;dits del programa d&#39;horaris. Els m&ograve;duls i unitats formatives s&#39;extreuran del fitxer de saga carregat</span></p>

			<p style="margin-left: 120px;"><input name="novaIncorporacioHoraris" type="radio" value="0" />2. No sou centre de nova incorporaci&oacute;.&nbsp;<strong style="color: rgb(178, 34, 34); background-color: rgb(255, 204, 102);">Disposeu de dues opcions:</strong></p>

			<p style="margin-left: 160px;"><input name="mantenirAmbHoraris" type="radio" value="1" />a. Mantenir m&ograve;duls i unitats formatives del curs passat.&nbsp;<span style="color: rgb(128, 128, 128); background-color: rgb(204, 153, 0);">Extreurem les mat&egrave;ries i/o cr&egrave;dits del programa d&#39;horaris. Es respectaran els m&oacute;duls i unitats formatives del curs passat</span><span style="color: rgb(128, 128, 128);">&nbsp;(Recomanada)</span></p>

			<p style="margin-left: 160px;"><input name="mantenirAmbHoraris" type="radio" value="0" />b. Incorporar la informaci&oacute; del fitxer de SAGA carregat.&nbsp;<span style="color: rgb(128, 128, 128); background-color: rgb(204, 153, 0);">Extreurem les mat&egrave;ries i/o cr&egrave;dits del programa d&#39;horaris. E</span><span style="color: rgb(128, 128, 128);">ls m&ograve;duls i ufs s&#39;extreuran de la informaci&oacute; del fitxer de SAGA.&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="text-align: center; background-color: rgb(255, 255, 153);">
			<p><span style="color:#ff0000;"><strong>Si heu respectat la informaci&oacute; de cursos anteriors o heu carregat informaci&oacute; des del fitxer de SAGA, despr&eacute;s de finalitzar aquesta fase, quan el programa us porti al men&uacute;, comproveu i actualitzeu la informaci&oacute; de mat&egrave;ries, m&ograve;duls i unitats formatives &nbsp;a l&#39;aplicaci&oacute; abans de seguir amb els passos de la c&agrave;rrega</strong></span></p>
			</td>
		</tr>
		<tr>
			<td style="text-align: center; background-color: rgb(255, 204, 51);"><input name="Reset" type="reset" value="Buidar formulari" />&nbsp; &nbsp;<input name="Enviar" type="submit" value="Enviar seleccions" />&nbsp; &nbsp;&nbsp;</td>
		</tr>
	</tbody>
</table>
</form>

<div>&nbsp;</div>
</body>
</html>





	




