<?php
/*---------------------------------------------------------------
* Aplicatiu: Carrega automàtica de dades
* Fitxer: alum_act.php
* Autor: Víctor Lino
* Descripció: Genera l'històric dels alumnes
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../bbdd/connect.php');
include("../funcions/funcions_generals.php");
include("../funcions/func_historic.php");
session_start();

?>
<html>
<head>
<title>Gestió d'històrics</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>

<?php 

//	$conexion=mysql_connect(localhost,$_USR_GASSIST,$_PASS_GASSIST);
//	$db=mysql_select_db($_BD_GASSIST,$conexion);
//	mysql_set_charset("utf8");

	
	$data_inici=$_POST['data_inici'];
	if ($data_inici=="") {$data_inici="01-01-10";}
	$data=explode("-",$data_inici);	
	$databd_inici=$data[2]."-".$data[1]."-".$data[0];
	$data_inici=strtotime($databd_inici);
	$data_fi=$_POST['data_fi'];
	$data=explode("-",$data_fi);	
	$databd_fi=$data[2]."-".$data[1]."-".$data[0];
	$data_fi=strtotime($databd_fi);
	$interval=date('m/d/Y',$data_inici)."__".date('m/d/Y',$data_fi);
	$interval=str_replace('/','',$interval);
	$nova_data=date("j-m-y");

	
	if ((!data_correcta($databd_fi)) OR ($data_fi<=$data_inici))
		{
		print("<h4>La data o el seu format no són correctes</h4>");
		$page="../saga_gp/menu.php";
		$sec="3";
		header("Refresh: $sec; url=$page");
		}
	else
		{
                hist_abs_ret_inc($databd_inici,$databd_fi,$nova_data);
                hist_ccc($databd_inici,$databd_fi,$nova_data);
                hist_sortides($databd_inici,$databd_fi,$nova_data);        
                hist_abs_prof($databd_inici,$databd_fi,$nova_data);
                }
        // Actualtzem la data per no superposar trapàs d'informació
        //echo $databd_fi."<br>";
        $invert = explode("-",$databd_fi); 
        $invert[0]=$invert[0]+2000;
        $fecha_invert = $invert[0]."-".$invert[1]."-".$invert[2]; 
        $sql2="INSERT INTO `HIST_dates` (`idhist_dates`,`Dates_historics`)VALUES (NULL,'".$fecha_invert."');";
        $result2=mysql_query($sql2);
        //echo $sql2;
        if (!$result2) 	{die(_ERR_INSERT_HIST_ALUMNE . mysql_error());}

        introduir_fase('historic',1);

        $page = "../import/menu.php";
        $sec="0";
        header("Refresh: $sec; url=$page");	

?>


</body>
