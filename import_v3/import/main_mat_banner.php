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

    // Netegem repeticions el fitxer equivalències
    $sql="DELETE FROM `equivalencies` WHERE grup_gp!='' AND grup_ga='' AND grup_saga='';";
    $result=mysql_query($sql);
	if (!$result) {die(_ERR_NETEJA_EQUIV3.mysql_error());}
	
   if(extreu_fase('app_horaris')==5)
      {select_plaestudis_saga();}
   else
      {
      if ((extreu_fase('modalitat_fitxer')==1) OR (extreu_fase('modalitat_fitxer')==2)) 
         {
         ?> 
        <table border = "1" align ="center" width = '65%' >
            <tr><td bgcolor="#ffbf6d" align = "center" ><h2>IMPORTANT. Llegeix atentament</h2></td></tr>
        <tr><td>
                El fitxer d'horaris que has pujat conté móduls i/o unitats formatives de cicles formatius LOE. <br><br>
                <font color="grey"><b> Suposarem que el fitxer de SAGA tambe té carregats els plans d'estudis de Cicles. 
                    Si no estiguessin carregats al fitxer de SAGA només podries escollir l'opció 1. Excepte si els carregues al SAGA i tornes a aquest pas </b></font><br><br>
                A partir d'aquest punt tens dues opcions força diferents:<br><br>
                <b>Opció 1</b> . Tractar les matèries del fitxer d'horaris que siguin LOE  com si fossin matèries de l'ESO o de crèdits LOGSE. 
                Aquesta opció presenta avantatges i inconvenients:<br>
                <ul>Avantatges :<br>
                    <li>La càrrega  partir d'aquest punt és molt més simple</li>
                </ul>
                <ul>Inconvenients
                <li>No es relacionaran els móduls amb les unitats formatives corresponents amb la qual cosa el módul ( matèria introduida en el programa d'horaris) 
                es considerará una matèria anual que no estarà dividida en unitats formatives. per tan no podràs gestionar 
                els seus claendaris en funció dels grups ni es generarán els canvis automàticament quan es passi d'una unitat formativa 
                a una altra</li>
                <li>Una vegada acabada la càrrega hauràs de relacionar manualment cada matèria amb el cicle formatiu que li correspon
                per possibilitar la creació de certs informes de l'aplicació i poder fer cerques correctament</li>
                <li>En definitiva, es tractaran els móduls com matèries d'ESO/BAT/CAS/LOGSE</li>
                </ul>    

                <b>Opció 2</b> Tractar de forma diferenciada les materies ESO/BAT/CAS/LOGSE dels CCFF LOE. 
                Aquesta opció presenta avantatges i inconvenients:<br>
                <ul>Avantatges :<br>
                    <li>Podem gestionar els calendaris de les Ufs, assignar-los dates d'inici i de fi, i en general tota la flexibilitat que les Ufs ens porporcionen.</li>
                    <li>Quan s'acabi una uf i comenci una altra , el canvi es farà de forma automàtica</li>
                    <li>El professorat podrà gestionar-se les dates d'inci i de fi i es farà el cómput d'hores realitzades de cada UF</li>
                    <li>Els alumnes tindran més constància de la UF que estaran fent i podran fer el seguiment de la seva asistència per UFs</li>
                    <li>....</li>
                </ul>
                <ul>Inconvenients
                <li>La càrrega a partir d'aquest punt és més feixuga ja que s'han d'emparellar les matèries amb els móduls de SAGA</li>
                </ul> 
                
                <b>NOTA:</b> En tots dos casos podrés generar informes d'assistència entre dates amb totes les característiques de personalització 
                que els informes tenen.
            </td></tr></table> 
            <form method="post" action="./main_mat.php" enctype="multipart/form-data" id="profform">
            <table align="center">
            <tr><td align="center">
            <input type="radio" name="opc_materies" value="opc1"> Opció 1<br>
            <input type="radio" name="opc_materies" value="opc2"> Opció 2<br><br>
            <input type="checkbox" name="dacord" value="dacord">He llegit atentament les dues opcions.<br>
            </td></tr>
            <tr><td align="center"><input name="boton" type="submit" id="boton" value="Enviar">
            &nbsp&nbsp<input type=button onClick="location.href='./menu.php'" value="Torna al menú!" ></td></tr>
            
            </table>
            </form>
    
    <?php }
        else
            {
            $page = "./main_mat.php";
            $sec="0";
            header("Refresh: $sec; url=$page");
            }
    
    
    
    
         } ?>     

</body>

	




