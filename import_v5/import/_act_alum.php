<?php
/*---------------------------------------------------------------
* Aplicatiu: sms_gest. Programa de gestió de sms de GEIsoft
* Fitxer: alum_act.php
* Autor: VÃíctor Lino
* Descripció: Actualitza i dóna d'alta els alumnes
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
require_once('../../bbdd/connect.php');
include("../funcions/func_prof_alum.php");
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
		$camps=array();
		recuperacampdedades($camps,$db);

		$recompte=$_POST['recompte'];
                echo "<br>".$recompte;
		$noap=0;
		
		// Preparem el fitxer
                $data = date("Ymd");
                $hora = date("hi");
                $_SESSION['alumnat.csv'] = '../uploads/'.$data.$hora.'alumnat.csv';
		$myFile = "../uploads/".$data.$hora."alumnat.csv";
		$fh = fopen($myFile, 'w') or die("can't open file");
		$stringData="nom_i_cognoms,usuari,login,password\n";		
		fwrite($fh, $stringData);
	
		for ($i=1;$i<=$recompte;$i++)
			{
			//echo "<br>>>>".$i." >>> ".$recompte;
			$nom=$_POST['nomalumsaga'.$i];
			$nom=neteja_apostrofs($nom);
			$cognom1=$_POST['cog1alumsaga'.$i];
			$cognom1=neteja_apostrofs($cognom1);
			$cognom2=$_POST['cog2alumsaga'.$i];
			$cognom2=neteja_apostrofs($cognom2);
			$id_saga=$_POST['idalumsaga'.$i];
                        $data_naixement=$_POST['naixement'.$i];
			
			// *************************
			// Fem l'alta
			// *************************
			$alta="alta".$i;
			//echo $_POST[$alta]."<br>";
			if($_POST[$alta])
				{
				//echo "Entra ALTA<br>";
				$user=$cognom1." ".$cognom2.", ".$nom;
				$nom_complet=$user;
				$pass="";
				
				genera($user,$pass,$nom,$cognom1,$cognom2,true,$db);
				// En aquest cas no utiltzem a generació del password. Agafem com password el nom d'usuari
				// i que actualitzen la password qun facin login
				$pass=md5($user);
				//echo $user.">>>".$pass;
				
				$sql="INSERT INTO `alumnes`(codi_alumnes_saga,activat) ";
				$sql.="VALUES ('".$id_saga."','S');";
				//echo $sql."<br>";
				$result=mysql_query($sql);
				if (!$result) 
					{
					die(_ERR_INSERT_ALUM . mysql_error());
					}
				//echo "S'ha insertat ".$nom;echo "<br>";
				

				
				$id=extreu_id(alumnes,codi_alumnes_saga,idalumnes,$id_saga);
				//echo "<br>".$id;
							
				$sql="INSERT INTO `contacte_alumne`(id_alumne,id_tipus_contacte,Valor) ";
				$sql.="VALUES ('".$id."','".$camps[nom_complet]."','".$nom_complet."'),";
				$sql.="('".$id."','".$camps[login]."','".$user."'),";
				$sql.="('".$id."','".$camps[iden_ref]."','".$id_saga."'),";
				$sql.="('".$id."','".$camps[nom_alumne]."','".$nom."'),";
				$sql.="('".$id."','".$camps[cognom1_alumne]."','".$cognom1."'),";
				$sql.="('".$id."','".$camps[cognom2_alumne]."','".$cognom2."'),";
                                $sql.="('".$id."','".$camps[data_naixement]."','".$data_naixement."'),";
				$md5pass=md5($user);
				$sql.="('".$id."','".$camps[contrasenya]."','".$md5pass."');";
				//echo $sql."<br>";
				$result=mysql_query($sql);
				if (!$result) 
					{
					die(_ERR_INSERT_ALUM_CONTACT . mysql_error());
					}
				//print("L'alumne/a d'alta: ".$nom_complet." Nom d'usuari: ".$user."<br>");
				//Escrivim en el csv
				$stringData=$nom.",".$cognom1." ".$cognom2.",".$user.",".$user."\n";		
				fwrite($fh, $stringData);
				
				// Ara, si s'escau actualitzem a la gestió centralitzada de GEISOFT
				
				carrega_dades_families($id,$id_saga);

				}
		
			}
		fclose($fh);
		introduir_fase('families',1);
		introduir_fase('alumnat',1);

		die("<script>location.href = './menu.php'</script>");
?>
</body>

	




