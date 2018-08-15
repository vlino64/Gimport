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
                
		$subject  =	"[GEISoft] Registre de control d'assistència de la setmana ";
		
        $to = $correuProf;
        //$to = "vlino64@gmail.com";
                
		$content = "<br><br>Professor:       ".$nomProf."<br><br>";
        $content .= " A continuació s'indiquen les sessions en les que no consta que s'hagi fet el control d'assistència durant la darrera setmana.. <br><br>";    
        
        foreach ($arrprofessorat as $professor){
           $content .= $professor[0]." || ".$professor[1]." || ".$professor[2]." || ".$professor[3]." || ".$professor[4]." || ".$professor[5]."<br>";
           }

                //echo "<br>".$header."<br>";
                //echo "<br>".$footer."<br>";
                //echo "<br>".$to."<br>";
                //echo "<br>".$subject."<br>";
                //echo "<br>".$content."<br>";
		
				
		mail($to,$subject,$content.$footer,$header);
		
/* ********************************************************* */
/* ********************************************************* */
?>
