<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:plans_estudis_form.php
* Autor: Víctor Lino
* Descripció: Formulari per la càrrega dels plans d'estudis
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include('funcio.php');
?>
<html>
<head>
<title>Càrrega automàtica Fotos</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>

<body>

<table  align="center" cellpadding="5" border="1">
<?php


        include('../bbdd/connect.php');
        $db->query("SET NAMES 'utf8'");
	
        $sufix="@copernic.cat";
        
        // Crea la carpeta
	chdir('uploads');
	$carpeta=time();
	mkdir($carpeta);
	chdir('..');
	
	// Mou el fitxer a una carpeta
	$tmp_name = $_FILES["archivo"]["tmp_name"];
	move_uploaded_file($tmp_name,'./uploads/'.$carpeta.'/pujatmails_professors.csv');
	
	// Executa la comanda
	chdir('./uploads/'.$carpeta.'/');

        $csvFile = "pujatmails_professors.csv";

        $data= array();
        $professorat = array();
        $data = netejaCsv($csvFile);
        
        print('<table border="1">');
        foreach ($data as $fila)
            {
            //echo "<br>>".$fila;
            $array_fila=explode(",",$fila);
            $nomProfessor = neteja_apostrofs($array_fila[0]);
//            $cognom1Professor = neteja_apostrofs($array_fila[1]);
//            $cognom2Professor = neteja_apostrofs($array_fila[2]);
            $mailProfessor = $array_fila[1];
            
            // Extreiem id de cada professor actiu del centre
            // PENDENT D'OPTIMITZAR
            $sql = "SELECT idprofessors ";
            $sql.= "FROM professors";
            $sql.=" WHERE activat = 'S';";

            $result=$db->query($sql);
            if (!$result) {die(_ERR_LOOK_FOR_ALUM1 . mysql_error());}
            print('<tr>');
            print("<td>".$nomProfessor."</td>");
//            print("<td>".$cognom1Professor."</td>");
//            print("<td>".$cognom2Professor."</td>");
            print("<td>".$mailProfessor."</td>");

            $trobat = 0;
            while ($fila_alum=  mysql_fetch_row($result))
                {
                $idprofessorbd = $fila_alum[0];
    //            echo "<br>".$idprofessorbd;
                $sql2 = "SELECT Valor FROM contacte_professor WHERE id_professor = $idprofessorbd AND id_tipus_contacte =  1";
                $result2=$db->query($sql2);if (!$result2) {die(_ERR_LOOK_FOR_PROF_NOM . mysql_error());}
                $fila_prof2=  mysql_fetch_row($result2);$nombd = $fila_prof2[0];

//                $sql2 = "SELECT Valor FROM contacte_professor WHERE id_professor = $idprofessorbd AND id_tipus_contacte =  4";
//                $result2=$db->query($sql2);if (!$result2) {die(_ERR_LOOK_FOR_ALUM_NOM . mysql_error());}
//                $fila_prof2=  mysql_fetch_row($result2);$cognom1bd = $fila_prof2[0];   
//
//                $sql2 = "SELECT Valor FROM contacte_professor WHERE id_professor = $idprofessorbd AND id_tipus_contacte =  5";
//                $result2=$db->query($sql2);if (!$result2) {die(_ERR_LOOK_FOR_ALUM_NOM . mysql_error());}
//                $fila_prof2=  mysql_fetch_row($result2);$cognom2bd = $fila_prof2[0];            

                // Per quan el correu estigui a contacte_professor
                $sql2 = "SELECT Valor FROM contacte_professor WHERE id_professor = $fila_alum[0] AND id_tipus_contacte =  34";
                $result2=$db->query($sql2);if (!$result2) {die(_ERR_LOOK_FOR_ALUM_MAIL . mysql_error());}
                $fila_prof2=  mysql_fetch_row($result2);$correubd = $fila_prof2[0];


                if (!strcmp($nomProfessor,$nombd)) 
                    {
    //                 print($nomProfessor."====".$cognom1Professor."====".$cognom2Professor."====".$mailProfessor);
    //                print($nombd."====".$cognom1bd."====".$cognom2bd."====".$correubd); 
                    $trobat = 1;
                    if ($correubd == "")
                        {
                        $sql2 = "INSERT INTO contacte_professor(id_professor,id_tipus_contacte,Valor) VALUES ('".$idprofessorbd."',34,'".$mailProfessor."')";
                        echo "<br>".$sql2;
                        $result2=$db->query($sql2);if (!$result2) {die(_ERR_INSERT_CORREU_PROFESSOR . mysql_error());}

                        print("<td><font color ='green'> S'ha actualitzat el correu d'aquest usuari </font></td></tr>");
                        }
                    else if (!strcmp($mailProfessor,$correubd))
                        {
                        print("<td><font color ='orange'> Usuari ja creat. Comprova si ja existeix un altre professor amb aquests dades o si aquest ja ha estat creat </font></td></tr>");
                        }
                    else if (strcmp($mailProfessor,$correubd))
                        {
                        print("<td> <font color ='red'> Existeix un professor amb aquestes dades i un correu diferent<br>".$correu_bd." </font></td></tr>");
                        }
                    //break;
                    }
                }

                if ($trobat == 0) {print("<td> <font color ='red'> No s'ha trobat cap professor amb aquestes dades </font></td></tr>");}


            }
            


?>
</table>

</body>