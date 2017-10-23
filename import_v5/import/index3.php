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
session_start();
require_once('../../bbdd/connect.php');
include("../funcions/funcions_generals.php");
include("../funcions/func_prof_alum.php");

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

function mostrarReferencia1()
	{
	if ((document.fcontacto.geisoft[0].checked == true)||(document.fcontacto.geisoft[1].checked == true)||(document.fcontacto.geisoft[2].checked == true)||(document.fcontacto.geisoft[3].checked == true)||(document.fcontacto.geisoft[4].checked == true))
		{
		document.getElementById('segonacarrega').style.display='block';
		document.getElementById('submit').style.display='none';
		}
	if (document.fcontacto.geisoft[5].checked == true)
		{
		document.getElementById('segonacarrega').style.display='none';
		document.getElementById('submit').style.display='block';
		}
	document.getElementById('upload').style.display='none';
	document.getElementById('pujaonoeso').style.display='none';
	} 

function mostrarReferencia2()
	{
	if ((document.fcontacto.segona[0].checked == true) || (document.fcontacto.segona[1].checked == true))
		{
		document.getElementById('fitxers').style.display='block';
		}
	document.getElementById('upload').style.display='none';
	} 



function mostrarReferencia3()
	{
	if ((document.fcontacto.fitxerorg[0].checked == true) || (document.fcontacto.fitxerorg[1].checked == true)  || (document.fcontacto.fitxerorg[2].checked == true))
		{
		document.getElementById('pujaono').style.display='block';
		}
	} 
 
 function mostrarReferencia4()
	{
	if (document.fcontacto.carrega2[0].checked == true)
		{
		document.getElementById('submit').style.display='block';
		}
	if (document.fcontacto.carrega2[1].checked == true)
		{
		document.getElementById('upload').style.display='block';
		document.getElementById('submit').style.display='block';
		}
	} 
 
 
 
</script>


</head>

<body>

<?php
	




	$tmp_name = $_FILES["archivo"]["tmp_name"];
	if ($tmp_name =="")
		{
		//echo "Utilitzarem un fitxer carregat anteriorment.<br>";
		$_SESSION['upload_saga'] = '../uploads/pujat_saga.xml';
                $exportsagaxml="../uploads/pujat_saga.xml";
		}
	else
		{
		echo "<br>";
		//$tmp_name = $_FILES["archivo"]["tmp_name"];
		$exportsagaxml="../uploads/pujat_saga.xml";
		$_SESSION['upload_saga'] = '../uploads/pujat_saga.xml';
		move_uploaded_file($tmp_name,$exportsagaxml);
		
		//Netegem el fitxer d'apostrofs
		$str=implode("\n",file('../uploads/pujat_saga.xml'));
		$fp=fopen('../uploads/pujat_saga.xml','w');
		$find[]='&apos;';
		$replace[]=' ';
		$str=str_replace($find,$replace,$str);
		fwrite($fp,$str,strlen($str));
		}
	
   
   
   

   //echo "<br>>>> ".$carrega;


    ?>

    <form enctype="multipart/form-data" action="./index4.php" method="post" name="fcontacto">
    <br><br><br>
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
            <tr><td align="center"><p>Qui programa utilitzes per crear els horaris<br /></td></tr>
            <tr><td align="center">
            <input type="radio" name="geisoft" value="0" id="geisoft_0" onclick="mostrarReferencia1();" /> GPuntis<br>
            <input type="radio" name="geisoft" value="1" id="geisoft_1" onclick="mostrarReferencia1();" /> GHC Peñalara<br>
            <input type="radio" name="geisoft" value="2" id="geisoft_2" onclick="mostrarReferencia1();" /> Kronowin<br>
            <input type="radio" name="geisoft" value="3" id="geisoft_3" onclick="mostrarReferencia1();" /> HorW (Sevilla) <br>
            <input type="radio" name="geisoft" value="4" id="geisoft_4" onclick="mostrarReferencia1();" /> Horaris aSc (Només càrregues Duals) <br>
            <input type="radio" name="geisoft" value="5" id="geisoft_5" onclick="mostrarReferencia1();" /> No utilitzo cap programa<br>
            </td></tr>
    </table>
    <br>
    <div id="segonacarrega" style="display:none;">
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
            <tr><td align="center"><p>Fitxers d'horaris<br /></td></tr>
            <tr><td align="center">
       <input type="radio" name="segona" value="0" id="segona_0" onclick="mostrarReferencia2();" /> Primera càrrega<br>
            <input type="radio" name="segona" value="1" id="segona_1" onclick="mostrarReferencia2();"  /> Segona càrrega i posteriors<br>
            </td></tr>
    </table>
    </div>
    <br>


    <div id="fitxers" style="display:none;">
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
            <tr><td align="center"><p>Quin és el contingut del fitxer ?<br /></td></tr>
            <tr><td align="center">
            <input type="radio" name="fitxerorg" value="0" id="fitx_0" onclick="mostrarReferencia3();" /> Només ESO/BAT/CAS/CCFF LOGSE<br>
            <input type="radio" name="fitxerorg" value="1" id="fitx_1" onclick="mostrarReferencia3();" /> Només CCFF LOE<br>
       <input type="radio" name="fitxerorg" value="2" id="fitx_2" onclick="mostrarReferencia3();" /> Dual (CCFF LOE + ESO/BAT)<br>
            </td></tr>
    </table>
    </div>


    <!-- #############   UN FITXER pujaonoeso ################ -->
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
            <tr><td align="center"><div id="pujaono" style="display:none;">
    <p> Indica el que vulguis fer</p>
    <input type="radio" name="carrega2" value="1" id="carrega_0" onclick="mostrarReferencia4()" > <b>Utilitzar un fitxer d'horaris carregat prèviament. </b> 

                    <?php 
                    $dia=date("d F Y ",filemtime('../uploads/pujat_horaris.xml'));
                    $hora=date("H:i:s.",filemtime('../uploads/pujat_horaris.xml'));
                    print("<font color=\"red\">Es tracta  d'un fitxer carregat ".$dia." a les ".$hora."</font>" ); 
                    ?>

    <br>
    <input type="radio" name="carrega2" value="0" id="carrega_1" onclick="mostrarReferencia4()" > <b>Carregar un fitxer nou d'horaris</b>.<br>
    </div></td></tr>
    </table>
    <!-- ############################# -->


    <!-- #############   UN FITXER  upload ################ -->
    <div id="upload" style="display:none;">
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d" >
            <tr><td></td><td></td></tr>
            <tr colspan="2" align="center">
            <td align="center">
                    <br>
                    <?php echo "ARXIU D'HORARIS A CARREGAR  "; ?>
                    <input name="archivo" type="file" id="archivoeso">
                    </table>
            </td>
            </tr>
    </table>
    </div>
    <!-- ############################# -->


    <!-- #############   UN FITXER submit ################ -->
    <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
            <tr><td align="center"><div id="submit" style="display:none;">
            <input name="boton" type="submit" id="boton" value="Envia la configuració">
    </div></td></tr>
    </table>
    <!-- ############################# -->

    </form>

    </body>

    



