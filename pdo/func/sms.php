<?php
function extreu_saldo($dbSMS,$username) {
	/*$servidor="geisoft.cat:3306";
	$database="sms_geisoft";
	$user= "consulta_sms";
	$password= "consulta";*/
	

    
    $sql="SELECT saldo_sms FROM saldo_sms WHERE login='".$username."';";
    //echo "<br>".$sql;
    $rec = $dbSMS->query($sql);
        
    foreach($rec->fetchAll() as $row) {
        $count++;
        $saldo = $row[0];
    }
        
	/*$conexion=mysql_connect($servidor,$user,$password);
	$db=mysql_select_db($database,$conexion);
	mysql_set_charset("utf8");
	if (!$conexion) 
		{
		die(_ERR_CONEX_BDD._SMS . mysql_error());		
		}*/
	
	/*$result=mysql_query($sql,$conexion);
	if (!$result) {echo "Ha fallat la consulta !!!";}
	$fila=mysql_fetch_row($result);
	$saldo=$fila[0];*/
        
    return $saldo;
}
?>
