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
	require_once('../../pdo/bbdd/connect.php');
	include('../funcions/funcions_generals.php');
        
        $db->exec("set names utf8");
        
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Function to sanitize values received from the form. Prevents SQL injection
//	function clean($str) {
//		$str = @trim($str);
//		if(get_magic_quotes_gpc()) {
//			$str = stripslashes($str);
//		}
//		return mysql_real_escape_string($str);
//	}
	
	//Sanitize the POST values
//	$login = clean($_POST['login']);
        $login = $_POST['login'];
//	echo ">>>>".$login; echo "<br>";
//	$password = clean($_POST['password']);
        $password = $_POST['password'];
//	echo ">>>>".$password; echo "<br>";
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
	$camps =recuperacampdedades($camps,$db);
	
	//Create query
	$qry="SELECT idcarrecs FROM carrecs WHERE nom_carrec='SUPERADMINISTRADOR';";
	$result=$db->query($qry);
        foreach ($result -> fetchAll() as $fila) {}
	$idsuper= $fila['idcarrecs'];
//	echo "<br>".$idsuper;
		
	$qry="SELECT id_professor FROM contacte_professor WHERE id_tipus_contacte='".$camps['login']."' AND valor='".$login."';";
//        echo "<br>".$qry;
	$result=$db->query($qry);
        if (!$result) {die(_ERR_SELECT_PROF.mysql_error());	}
        foreach ($result -> fetchAll() as $fila){}
        $idprofessor = $fila['id_professor'];
	
//        echo "<br>".$idprofessor;
	$qry="SELECT id_professor FROM contacte_professor WHERE id_tipus_contacte='".$camps['contrasenya']."' AND valor='".md5($_POST['password'])."' ";
	$qry.="AND id_professor='".$idprofessor."'";
      
	$result=$db->query($qry);
        if (!$result) {die(_ERR_SELECT_ID_PROF.mysql_error());	}
	$present = $result->rowCount();
        
        if ($result->rowCount() == 1)
		{
		$qry="SELECT idprofessor_carrec FROM professor_carrec WHERE idprofessors='".$idprofessor."' AND idcarrecs='".$idsuper."'";
		$result= $db->query($qry);
		if($result->fetchColumn() >= 1)
			{
			//Login Successful
			session_regenerate_id();
			$access="access_ok";
			$_SESSION['SESS_MEMBER'] = $access;
//			echo $_SESSION['SESS_MEMBER'];
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
