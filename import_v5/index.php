<?php

// Aquest fitxer simplement comprova si tenim una sessió iniciada i ens envia on calgui.

session_start();

if((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER']!="access_ok")) 
		{
		header("location: ./login/login-form.php");
		exit();
		}
	else
		{
		header("Location:./import/index.php");
		}
?>	




