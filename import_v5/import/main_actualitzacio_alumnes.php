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

include("../funcions/func_prof_alum.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");
ini_set("display_errors", 1);

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
	{
	header("location: ../login/access-denied.php");
	exit();
	}

$exportsagaxml=$_SESSION['upload_saga'];

?>
<html>
<head>
<title>Càrrega automàtica SAGA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">

<HTML>
<HEAD>
</head>

<body>

<?php
        if (isset($_POST['alumnes'])){
            if ($_POST['alumnes'] == 0) 
                {
                $tmp_name = $_FILES["archivo"]["tmp_name"];
                if ($tmp_name =="")
                        {
                        //echo "Utilitzarem un fitxer carregat anteriorment.<br>";
                        $_SESSION['upload_alumnes'] = '../uploads/alumnes.csv';
                        $exportsagaxml="../uploads/alumnes.csv";
                        }
                else
                        {
                        echo "<br>";
                        //$tmp_name = $_FILES["archivo"]["tmp_name"];
                        $exportsagaxml="../uploads/alumnes.csv";
                        $_SESSION['upload_alumnes'] = '../uploads/alumnes.csv';
                        move_uploaded_file($tmp_name,$exportsagaxml);
                        $today = date("d-m-Y");$time = date("H-i-s");
                        $newname = $exportsagaxml."_".$today."_".$time;
                        if (!copy($exportsagaxml, $newname)) {
                            echo "failed to copy";
                        }
                                
                        //Netegem el fitxer d'apostrofs
                        $str=implode("\n",file('../uploads/alumnes.csv'));
                        $fp=fopen('../uploads/alumnes.csv','w');
                        $find[]='&apos;';
                        $replace[]=' ';
                        $str=str_replace($find,$replace,$str);
                        fwrite($fp,$str,strlen($str));
                        }

                emparella_grups_actualitzacio_csv();
                }
            else if ($_POST['alumnes'] == 1) 
                {
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
                        $today = date("d-m-Y");$time = date("H-i-s");
                        $newname = $exportsagaxml."_".$today."_".$time;
                        if (!copy($exportsagaxml, $newname)) {
                            echo "failed to copy";
                        }
                        //Netegem el fitxer d'apostrofs
                        $str=implode("\n",file('../uploads/alumnes.csv'));
                        $fp=fopen('../uploads/alumnes.csv','w');
                        $find[]='&apos;';
                        $replace[]=' ';
                        $str=str_replace($find,$replace,$str);
                        fwrite($fp,$str,strlen($str));
                        }

                actualitzar_alumnat($exportsagaxml); 
                }            
        }	




        
	else{
            echo "<b><h2>No has seleccionat d'on vols treure la informació ....</b></h2>";
            $page = "./actualitzacio_seleccio_alumnes.php";
            $sec="2";
            header("Refresh: $sec; url=$page");
        }	
?>
</body>

	




