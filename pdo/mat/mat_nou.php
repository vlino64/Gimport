<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idplans_estudis = isset($_REQUEST['idplans_estudis']) ? $_REQUEST['idplans_estudis'] : 0;
$nom_materia     = str_replace("'","\'",$_REQUEST['nom_materia']);
$hores_finals    = isset($_REQUEST['hores_finals']) ? $_REQUEST['hores_finals'] : 0;
//$curs_escolar    = 0;
$curs_escolar    = getCursActual($db)["idperiodes_escolars"];

// Insertem la moduls_materies_ufs
$sql = "insert into moduls_materies_ufs (idplans_estudis,hores_finals,Curs) values ($idplans_estudis,$hores_finals,$curs_escolar)";

$result = $db->query($sql);

$sql_nova_materia    = "select moduls_materies_ufs.id_mat_uf_pla from moduls_materies_ufs order by 1 desc limit 0,1";
$result_nova_materia = $db->query($sql_nova_materia);

foreach($result_nova_materia->fetchAll() as $row) {
        $idmateria = $row["id_mat_uf_pla"];
}
//$idmateria = mysql_insert_id();

// Insertem a la taula de relaci� materia
$sql = "insert into materia (idmateria,nom_materia) values ($idmateria,'$nom_materia')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
