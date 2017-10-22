<?php
require_once('../bbdd/connect_sms.php');
//require_once('../func/seguretat.php');
$dbSMS->exec("set names utf8");

$id   = $_REQUEST['id']; 
$sql  = "select * from vista_log_sms where id_env=$id";
$rs   = $dbSMS->query($sql);
foreach($rs->fetchAll() as $row) {
    $item = $row;
}
//$aa = explode('-',$id);
?>
    
<table class="dv-table" border="0" style="width:100%;">
<tr>
    <td style="border:0" valign=top width="60">
    <b>Tlf. destinatari</b><br><?= $item['telefon'] ?><br>
    <td width=2>&nbsp;</td>
    <td style="border:0" valign=top width=250>
    <b>Contingut</b><br><?= $item['content'] ?><br>
    <td width=2>&nbsp;</td>
</tr>
</table>
                            
<?php
$rs->closeCursor();
//mysql_close();
?>