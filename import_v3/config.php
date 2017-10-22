<?php
// Aquest fitxer s'ha generat automàticament
// Els canvis que puguis introduir, es perdran en regenerar-se
// ==========================================================
          
          
$connexio = @mysql_connect('localhost',root,vlino);
if (!$connexio) {
die('Could not connect: ' . mysql_error());}
mysql_select_db('riquer', $connexio);
mysql_set_charset("utf8");
?>