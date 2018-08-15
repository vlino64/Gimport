<?php
	ini_set("session.cookie_lifetime","7200");
	ini_set("session.gc_maxlifetime","7200");
	session_start();
	require_once('./bbdd/connect.php');
	require_once('./func/constants.php');
	require_once('./func/generic.php');	
	
	if (isset($_SESSION['professor'])) {
		insertaLogProfessor($db,$_SESSION['professor'],TIPUS_ACCIO_LOGOUT);
	}
	else if (isset($_SESSION['alumne'])){
		insertaLogAlumne($db,$_SESSION['alumne'],TIPUS_ACCIO_LOGOUT);
	}
	else if (isset($_SESSION['familia_1'])){
		insertaLogAlumne($db,$_SESSION['familia_1'],TIPUS_ACCIO_LOGOUT);
	}
	else if (isset($_SESSION['familia_2'])){
		insertaLogAlumne($db,$_SESSION['familia_2'],TIPUS_ACCIO_LOGOUT);
	}
	
	session_unset();
	$adrecaRetorn =  'XXXXXXXXXXXXXXXXXXXX';
      if ($_GET['google'] == 1) {
	header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue='.$adrecaRetorn.'');
      }
      else {
        header('Location: index.php');
      }
?>
