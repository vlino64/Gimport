<?php

require_once(dirname(dirname(__FILE__)).'/bbdd/connect.php');
require_once(dirname(dirname(__FILE__)).'/func/constants.php');
require_once(dirname(dirname(__FILE__)).'/func/generic.php');

/* ********************************************************* */
// Enviem els correus pertinents, segons la configuració 
/* ********************************************************* */
        $header  = 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $header .= 'From: '.getDadesCentre($db)["nom"]."<no-reply@geisoft.cat>".'' . "\r\n";
		
        $footer  = "\r\n ==============\r\n";
        $footer .= "Nota: Aquest correu s'ha enviat des d'una adreça  de correu electrònic que no accepta correus entrants.\r\n";
        $footer .= "Si us plau, no respongueu aquest missatge\r\n";
                
	$subject  =	"[GEISoft] Informe alumnes de la teva tutoria (".$nomGrup.") ";
		
        $to = $correuProf;
                
        $content = "<br><br>Tutor: ".$nomProf."<br><br>";
        $content .= "A continuació disposes d'un llistat dels alumnes de la teva tutoria i un recull numèric de les incidències d'aquesta setmana. <br><br>";    
        
        $count = 0;
        foreach ($arrAlumnat as $alumnes){
           $count++;
        }

        for ($i = 0 ; $i < $count ; $i++){
            if (($arrAlumnat[$i][2] == 0) && ($arrAlumnat[$i][3] == 0) && ($arrAlumnat[$i][4] == 0) && ($arrAlumnat[$i][5] == 0) && ($arrAlumnat[$i][6] == 0)){
                $content .= "* ".$arrAlumnat[$i][1]." || Sense Incidències!.<br>";
            } 
            else {
                $content .= "* ".$arrAlumnat[$i][1]." || ".$arrAlumnat[$i][2]." absències || ".$arrAlumnat[$i][3];
                $content .= " retards || ".$arrAlumnat[$i][4]." justificacions || ".$arrAlumnat[$i][5]." seguiments || ".$arrAlumnat[$i][6];
                $content .= " CCC<br>";                
            }
           }
           
        $content .= "<br>** CCC - Conductes contràries a la convivència. <br><br>";       

//        echo "<br>".$header."<br>";
//        echo "<br>".$footer."<br>";
//        echo "<br>".$to."<br>";
//        echo "<br>".$subject."<br>";
//        echo "<br>".$content."<br>";

				
		mail($to,$subject,$content.$footer,$header);
		
/* ********************************************************* */
/* ********************************************************* */
?>
