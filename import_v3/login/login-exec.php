<?php
/*---------------------------------------------------------------
* Aplicatiu: Softpack . Paquete integrado herramientas web
* Fitxer: login-exec.php
* Autor: Jatinder(phpsense)/ Modif: Victor Lino
* Descripció: Fitxer: Realtzació del login
* Pre condi.:
* Post cond.:
----------------------------------------------------------------*/
	//Start session
	session_start();


	//Include database connection details
	include('../config.php');
	include('../funcions/funcions_generals.php');
        
        //canviem el nom de la connexió per adequar-lo a gimport
	//$connexio = $conn;
	
        echo "Generem automàticament el fitxer de configuració ...<br>";
	//munta_el_config();
	require_once('../config.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$login = clean($_POST['login']);
	//echo $login; echo "<br>";
	$password = clean($_POST['password']);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: login-form.php");
		exit();
	}
	
	$camps=array();
	recuperacampdedades($camps);
	
	//Create query
	$qry="SELECT idcarrecs FROM carrecs WHERE nom_carrec='SUPERADMINISTRADOR';";
	$result=mysql_query($qry,$connexio);
	$idsuper=mysql_result($result,0);
	//echo "<br>".$idsuper;
		
	$qry="SELECT id_professor FROM contacte_professor WHERE id_tipus_contacte='".$camps['login']."' AND valor='".$login."';";
        echo "<br>".$qry;
	$result=mysql_query($qry,$connexio);if (!$result) {die(_ERR_SELECT_PROF.mysql_error());	}
	$fila=  mysql_fetch_row($result);
	$idprofessor = $fila[0];
	
        //echo "<br>".$idprofessor;
	$qry="SELECT id_professor FROM contacte_professor WHERE id_tipus_contacte='".$camps['contrasenya']."' AND valor='".md5($_POST['password'])."' ";
	$qry.="AND id_professor='".$idprofessor."'";
	$result=mysql_query($qry,$connexio);if (!$result) {die(_ERR_SELECT_ID_PROF.mysql_error());	}
	
        //echo "<br>".$qry;
        if (mysql_num_rows($result)==1)
		{
		$qry="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$idprofessor."' AND idcarrecs='".$idsuper."'";
		//echo "<br>".$qry;
		$result=mysql_query($qry,$connexio);
		if(mysql_num_rows($result)>=1)
			{
			//Login Successful
			session_regenerate_id();
			$access="access_ok";
			$_SESSION['SESS_MEMBER'] = $access;
			//echo $_SESSION['SESS_MEMBER'];
			header("location: ../import/index.php");
			}
		else 
			{
			//Login failed
			header("location: login-failed.php");
			//echo "login failed";
			exit();
			}		
		}
	else
		{	
		//Login failed
		header("location: access-denied.php");	
		exit();
		}		

?>
