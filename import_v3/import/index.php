<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:index.php
* Autor: Víctor Lino
* Descripció: Pàgina de selecció d'opcions
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/

include("../config.php");


session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
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

function mostrarReferencia()
{
if (document.fcontacto.geisoft[0].checked == true) 
	{
	document.getElementById('subgeisoft0').style.display='none';
	document.getElementById('subgeisoft1').style.display='block';
	document.getElementById('subgeisoft3').style.display='none';
	document.getElementById('subgeisoft4').style.display='none';
	document.getElementById('subgeisoft2').style.display='none';
	} 

if (document.fcontacto.geisoft[1].checked == true) 
	{
	document.getElementById('subgeisoft0').style.display='block';
	document.getElementById('subgeisoft1').style.display='none';
	document.getElementById('subgeisoft3').style.display='none';
	document.getElementById('subgeisoft4').style.display='none';
	document.getElementById('subgeisoft2').style.display='none';
	} 
}


function mostrarReferencia3()
	{
	if ((document.fcontacto.sincro[1].checked == true)&&(document.fcontacto.geisoft[1].checked == true))
		{
		document.getElementById('subgeisoft0').style.display='block';
		}
	document.getElementById('subgeisoft1').style.display='block';
	document.getElementById('subgeisoft3').style.display='block';
	} 

function mostrarReferencia2()
	{
	if ((document.fcontacto.sincro[1].checked == true)&&(document.fcontacto.geisoft[1].checked == true))
		{
		document.getElementById('subgeisoft0').style.display='block';
		}
	document.getElementById('subgeisoft1').style.display='block';
	} 

function mostrarReferencia4()
	{
	if ((document.fcontacto.sincro[1].checked == true)&&(document.fcontacto.geisoft[1].checked == true))
		{
		document.getElementById('subgeisoft0').style.display='block';
		}
	document.getElementById('subgeisoft1').style.display='block';
	document.getElementById('subgeisoft3').style.display='block';
	if (document.fcontacto.carrega2[1].checked == true)
		{
		document.getElementById('subgeisoft2').style.display='block';
		document.getElementById('subgeisoft4').style.display='none';
		}
	else
		{
		document.getElementById('subgeisoft2').style.display='none';
		document.getElementById('subgeisoft4').style.display='block';
		}


	}	

function Urgente(obj) 
	{
    if (obj.checked) 
		{
        alert('Per poder seguir endavant ho has de tenir sincronitzat.');
        }
    return true;
    }

function doble(obj)
	{
	Urgente(obj);
	location.reload();
	}


</script>


</head>

<body>

<form enctype="multipart/form-data" action="./index2.php" method="post" name="fcontacto">
<br><br><br>
<table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
	<tr><td align="center"><p>Utilitzes la gestió centralitzada d'usuaris de GEISoft:<br /></td></tr>
	<tr><td align="center"><input type="radio" name="geisoft" value="0" id="geisoft_0" onclick="mostrarReferencia();" /> No
	<input type="radio" name="geisoft" value="1" id="geisoft_1" onclick="mostrarReferencia();" /> Si</td></tr>
</table>


<table class="general" width="70%" align="center" bgcolor="#ffbf6d">
	<tr><td align="center"><div id="subgeisoft0" style="display:none;">
	<p>Ho tens tot sincronitzat</p>
	<input type="radio" name="sincro" value="0" id="sincro_0" onclick="doble(this)" /> No
	<input type="radio" name="sincro" value="1" id="sincro_1" onclick="mostrarReferencia2();" /> Si
	</div></td></tr>
</table>

<table class="general" width="70%" align="center" bgcolor="#ffbf6d">
	<tr><td align="center"><div id="subgeisoft1" style="display:none;">
	<p> És la primera càrrega o es tracta d'una actualització</p>
   <input type="radio" name="carrega" value="0" id="carrega_0" onclick="mostrarReferencia3()" > <b>Nou curs. Primera càrrega absoluta</b>.Mai he utilitzat gassist <br>
	<input type="radio" name="carrega" value="1" id="carrega_1" onclick="mostrarReferencia3()" > <b>Nou curs. Primera càrrega de curs o posteriors</b>. Ja he utilitzat gassist, però és un curs nou<br>
	o l'inici d'una segona càrrega<br>
   <input type="radio" name="carrega" value="2" id="carrega_2" onclick="mostrarReferencia3()" > <b>Continuar</b> una càrrega prèvia <br>
   <input type="radio" name="carrega" value="3" id="carrega_3" onclick="mostrarReferencia3()" > <b>Actualitzar alumnat.</b> Completar informació alumnat de SAGA<br>
	---------------------------------------------------------------------------<br>
	<b>En primer lloc, crea una còpia de la teva base de dades</b>
	<input type=button onClick="location.href='./backup.php'" value="Crea una còpia de seguretat de la base de dades !(Recomanat)"><br>
	---------------------------------------------------------------------------
	</td></tr>
</div>
</table>

<table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
	<tr><td align="center"><div id="subgeisoft3" style="display:none;">
<p> Indica el que vulguis fer</p>
<input type="radio" name="carrega2" value="1" id="carrega_0" onclick="mostrarReferencia4()" > <b>Utilitzar un fitxer de SAGA carregat prèviament. </b> 

		<?php 
		$dia=date("d F Y ",filemtime('../uploads/pujat_saga.xml'));
		$hora=date("H:i:s.",filemtime('../uploads/pujat_saga.xml'));
		print("<font color=\"red\">Es tracta  d'un fitxer carregat ".$dia." a les ".$hora."</font>" ); 
		?>

<br>
<input type="radio" name="carrega2" value="0" id="carrega_1" onclick="mostrarReferencia4()" > <b>Carregar un fitxer nou de SAGA</b>. Ja he utilitzat gassist, però és un curs nou<br>
</div></td></tr>
</table>

<table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
	<tr><td align="center"><div id="subgeisoft4" style="display:none;">
	<input name="boton" type="submit" id="boton" value="Envia la configuració">
</div></td></tr>
</table>

<div id="subgeisoft2" style="display:none;">
<table class="general" width="70%" align="center" bgcolor="#ffbf6d">
	<tr><td></td><td></td></tr>

	
	<tr colspan="2" align="center">
	<td align="center">
		<br>
		<?php echo "ARXIU DE SAGA A CARREGAR"; ?>
		<input name="archivo" type="file" id="archivo">
		<br><br><input name="boton" type="submit" id="boton" value="<?php echo "PUJAR"; ?>">
		</form>
		</table>
	</td>
	</tr>
</table>
</div>








</body>





