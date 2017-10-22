<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$nom      = isset($_REQUEST['nom'])      ? str_replace("'","\'",$_REQUEST['nom'])      : '';
$adreca   = isset($_REQUEST['adreca'])   ? str_replace("'","\'",$_REQUEST['adreca'])   : '';
$cp       = isset($_REQUEST['cp'])       ? str_replace("'","\'",$_REQUEST['cp'])       : '';
$poblacio = isset($_REQUEST['poblacio']) ? str_replace("'","\'",$_REQUEST['poblacio']) : '';
$tlf      = isset($_REQUEST['tlf'])      ? str_replace("'","\'",$_REQUEST['tlf'])      : '';
$fax      = isset($_REQUEST['fax'])      ? str_replace("'","\'",$_REQUEST['fax'])      : '';
$email    = isset($_REQUEST['email'])    ? str_replace("'","\'",$_REQUEST['email'])    : '';
$prof_env_sms = $_REQUEST['prof_env_sms'];

$sql = "UPDATE dades_centre SET nom='$nom',adreca='$adreca',cp='$cp',poblacio='$poblacio',tlf='$tlf',fax='$fax',email='$email',prof_env_sms='$prof_env_sms' WHERE iddades_centre=1";

$result = $db->query($sql);

echo json_encode(array('success'=>true));

//mysql_close();
?>
