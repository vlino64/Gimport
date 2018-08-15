<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:relaciona-pla_estudis.php
 * Autor: Víctor Lino
 * Descripció: Relaciona els diferents plans d'estudis amb les matèries o móduls que conté
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/funcions_generals.php");
include("../funcions/funcions_historic.php");
ini_set("display_errors", 1);

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
    header("location: ../login/access-denied.php");
    exit();
}

//foreach($_FILES as $campo => $texto)
//eval("\$".$campo."='".$texto."';");
?>
<html>
    <head>
        <title>Càrrega automàtica SAGA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
    </head>

</head>

<body>
    <?php
// Modificacions a la base de dades
    $sql = "ALTER TABLE  `plans_estudis` CHANGE  `Acronim_pla_estudis`  `Acronim_pla_estudis` VARCHAR( 20 ) ";
    $sql .= "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
    $result = $db->prepare($sql);
    $result->execute();

    $sql = "ALTER TABLE  `plans_estudis` CHANGE  `Nom_plan_estudis`  `Nom_plan_estudis` VARCHAR( 80 ) ";
    $sql .= "CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
    $result = $db->prepare($sql);
    $result->execute();

// Comprovem si el camp codi_modul existeix. Si no existeix, el crearem i afegirem un valor per defecte
    $sql = "SHOW COLUMNS FROM `moduls` LIKE 'codi_modul'";
    $result = $db->prepare($sql);
    $result->execute();
    $exists = $result->rowCount()==1 ? TRUE : FALSE;
    if (!$exists) {
        $sql = "ALTER TABLE  `moduls` ADD  `codi_modul` VARCHAR( 30 ) NOT NULL ";
        $result = $db->prepare($sql);
        $result->execute();
    }

// Extreiem dates de periode escolar
    $sql2 = "SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S'";
//echo $sql2."<br>";
    $result = $db->prepare($sql);
    $result->execute();
    $fila = $result->fetch();


    $recompte = $_POST['recompte'];
    $exportsagaxml = $_SESSION['upload_saga'];
    $resultatconsulta = simplexml_load_file($exportsagaxml);
    if (!$resultatconsulta) {
        echo "Carrega fallida.";
    }
//Carreguem plans estudis i materies
    for ($i = 1; $i <= $recompte; $i++) {
        $codi_etapa = $_POST['placurt' . $i];
        $codi_etapa = neteja_apostrofs($codi_etapa);
        $codi_subetapa = $_POST['plamig' . $i];
        $codi_subetapa = neteja_apostrofs($codi_subetapa);
        $nom_pla = $_POST['plallarg' . $i];
        $nom_pla_per_comparar = $nom_pla;
        $nom_pla = neteja_apostrofs($nom_pla);
        $tipus_pla = $_POST['etapa' . $i];
        $tipus_pla = neteja_apostrofs($tipus_pla);
        //echo "!!!!!!!!!!!".$tipus_pla."<br>";
        $sql = "INSERT plans_estudis(activat,Nom_plan_estudis,Acronim_pla_estudis) ";
        $sql .= "VALUES ('S','" . $nom_pla . "','" . $codi_etapa . "(" . $codi_subetapa . ")');";
        $result = $db->prepare($sql);
        $result->execute();
        // Extreiem l'id del pla que acabem d'introduir
        $id_pla = extreu_id('plans_estudis', 'Nom_plan_estudis', 'idplans_estudis', $nom_pla, $db);
        //echo $id_pla."<br>";	
        foreach ($resultatconsulta->{'plans-estudi'}->{'pla-estudis'} as $pla) {
            if ((!strcmp($codi_etapa, $pla['etapa'])) && (!strcmp($codi_subetapa, $pla['subetapa'])) && (!strcmp($nom_pla_per_comparar, $pla['nom']))) {
                //echo $codi_etapa." > ".$pla[etapa]." ||| ".$codi_subetapa." > ".$pla[subetapa]." ||| ".$nom_pla." > ".$pla[nom]."<br>";
                //echo "Tipus_pla: ".$tipus_pla."<br>";	
                switch ($tipus_pla) {
                    // En blanc
                    case "0":
                        break;
                    // Primària
                    case "4":
                        break;
                    //ESO/BAT/CAS
                    case "1":
                        foreach ($pla->contingut as $materies) {
                            $id_materia = neteja_apostrofs($materies['id']);
                            $codi_materia = neteja_apostrofs($materies['codi']);
                            $nom_materia = neteja_apostrofs($materies['nom']);
                            //echo ">>>>>".$id_materia." - ".$codi_materia." - ".$nom_materia."<br>";
                            $nom_materia = "(" . $codi_materia . ")" . $nom_materia;
                            // Inserció a moduls_materies_ufs
                            $sql = "INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
                            $sql .= "VALUES ('" . $id_pla . "','" . $id_materia . "','S');";
                            //echo $sql."<br>";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $id_taula_materies = extreu_id('moduls_materies_ufs', 'codi_materia', 'id_mat_uf_pla', $id_materia, $db);

                            // Inserció a la taula materies
                            $sql = "INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
                            $sql .= "VALUES ('" . $id_taula_materies . "','" . $codi_materia . "','" . $nom_materia . "');";
                            //echo $sql."<br>";
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                        break;

                    // CF LOE
                    case "2":
                        //crea_modul_fictici($id_pla,$codi_subetapa);
                        // Fem un recorregut complet i introduim tots els moduls
                        //echo "entra LOE >>".$tipus_pla;
                        foreach ($pla->contingut as $materies) {
                            if (strlen($materies['codi']) == "3") {
                                //echo "mòdul".$materies[codi]."<br>";
                                $materies['nom'] = neteja_apostrofs($materies['nom']);
                                $sql = "INSERT INTO moduls(idplans_estudis,nom_modul,codi_modul,hores_finals, horeslliuredisposicio) ";
                                $sql .= "VALUES ('" . $id_pla . "','(" . $codi_subetapa . ")" . $materies['nom'] . "','" . $materies['codi'] . "',0,0);";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                        foreach ($pla->contingut as $materies) {
                            if (strlen($materies['codi']) == "5") {
                                $codi_materia = $codi_subetapa . "_" . $materies['codi'];
                                // Introduim a la taula general de matèries
                                $sql = "INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
                                $sql .= "VALUES ('" . $id_pla . "','" . $materies['id'] . "','S');";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();

                                // Extreiem l'identificador de la mattèria
                                $id_taula_materies = extreu_id('moduls_materies_ufs', 'codi_materia', 'id_mat_uf_pla', $materies['id'], $db);

                                // Extreiem l'identificador del módul
                                //echo "UF".$materies[codi]."<br>";
                                $codi_modul = substr($materies['codi'], 0, 3);
                                $sql = "SELECT idmoduls FROM moduls WHERE (idplans_estudis='" . $id_pla . "' AND codi_modul='" . $codi_modul . "');";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();

                                // Comprovem que el módul existeix. Et pots trobar en els casos en que un módul només tingui una UF
                                //amb una UF que no té el seu módul corresponent
                                $files = $result->rowCount();
                                if ($files == 0) {
                                    $sql = "INSERT IGNORE INTO moduls(idplans_estudis,nom_modul,codi_modul,hores_finals, horeslliuredisposicio) ";
                                    $sql .= "VALUES ('" . $id_pla . "','" . $codi_modul . "_" . $nom_pla . "_No en saga','" . $codi_modul . "',0,0);";
//                                                                echo $sql."<br>";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                    //Extreiem ara el seu id per poer seguir
                                    $sql = "SELECT idmoduls FROM moduls WHERE (idplans_estudis='" . $id_pla . "' AND codi_modul='" . $codi_modul . "');";
                                    //echo $sql."<br>";
                                    $result = $db->prepare($sql);
                                    $result->execute();
                                }
                                //echo $modul_id."<br>";								
                                $modul_id_arr = $result->fetch();
                                $modul_id = $modul_id_arr['idmoduls'];


                                // Inserim la UF
                                $materies['nom'] = neteja_apostrofs($materies['nom']);
                                $sql = "INSERT IGNORE INTO unitats_formatives(idunitats_formatives,nom_uf,hores,codi_uf,data_inici,data_fi) ";
                                $sql .= "VALUES ('" . $id_taula_materies . "','" . $materies['nom'] . "',50,'" . $codi_materia . "','" . $fila['data_inici'] . "','" . $fila['data_fi'] . "');";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();

                                // Inserim el registre que relaciona el módul i la UF
                                $sql = "INSERT IGNORE INTO moduls_ufs(id_moduls,id_ufs) ";
                                $sql .= "VALUES ('" . $modul_id . "','" . $id_taula_materies . "');";
                                //echo $sql."<br>";
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                        break;
                    //CF LOGSE
                    case "3":
                        foreach ($pla->contingut as $materies) {
                            $id_materia = $materies['id'];
                            $codi_materia = $materies['codi'];
                            $nom_materia = neteja_apostrofs($materies[nom]);
                            //echo ">>>>>".$id_materia." - ".$codi_materia." - ".$nom_materia."<br>";
                            //$nom_materia="(".$codi_materia.")".$nom_materia;
                            // Inserció a moduls_materies_ufs
                            $sql = "INSERT IGNORE INTO moduls_materies_ufs(idplans_estudis,codi_materia,activat) ";
                            $sql .= "VALUES ('" . $id_pla . "','" . $id_materia . "','S');";
                            //echo $sql."<br>";
                            $result = $db->prepare($sql);
                            $result->execute();
                            $id_taula_materies = extreu_id('moduls_materies_ufs', 'codi_materia', 'id_mat_uf_pla', $id_materia, $db);

                            // Inserció a la taula materies
                            $sql = "INSERT IGNORE INTO materia(idmateria,codi_materia,nom_materia) ";
                            $sql .= "VALUES ('" . $id_taula_materies . "','" . $codi_materia . "','" . $nom_materia . "');";
                            //echo $sql."<br>";
                            $result = $db->prepare($sql);
                            $result->execute();
                        }
                        break;
                }
            }
        }
    }

// Hem de crear les relacions grups-matèria

    neteja('alumnes_grup_materia', $db);
    neteja('prof_agrupament', $db);
    neteja('grups_materies', $db);

    $sql2 = "SELECT data_inici,data_fi FROM periodes_escolars WHERE actual='S'";
//echo $sql2."<br>";
    $result2 = $db->prepare($sql2);
    $result2 -> execute();
    $fila2 = $result2->fetch();


    $resultatconsulta = simplexml_load_file($exportsagaxml);
    if (!$resultatconsulta) {
        echo "Carrega fallida";
    } else {
        $sql = "SELECT idgrups,codi_grup FROM grups;";
        $result = $db->prepare($sql);
        $result->execute();

        // NOTA: En aquest fitxer no es fa res amb els grups que no tenen maties assignades 
        // Ja s'ha fet en el pas de creació de grups

        foreach ($result->fetchAll() as $fila) {
            foreach ($resultatconsulta->grups->grup as $grup) {
                if ($grup['id'] == $fila[1]) {

                    foreach ($grup->continguts->contingut as $mat_prof) {
                        //echo "<br>".$grup[id]." --> ".$mat_prof[professor]." --> ".$mat_prof[contingut];
                        // Esbrino l'id de la materia de cada contingut
                        $id_materia = extreu_id('moduls_materies_ufs', 'codi_materia', 'id_mat_uf_pla', $mat_prof['contingut'],$db);
                        //echo $mat_prof[contingut].">>> ".$id_materia."<br>";
                        //amb aquest id_materia puc posar-hi el contingut a la taula grup_materia
                        // Si no conté cap informació significa que és un módul i no una UF
                        if ($id_materia != "") {

                            // Comprovem que aquest binomi no ha estat ja introduit					
                            $sql = "SELECT idgrups_materies FROM grups_materies ";
                            $sql .= "WHERE (id_grups='" . $fila[0] . "' AND id_mat_uf_pla='" . $id_materia . "');";
                            $result2 = $db->prepare($sql);
                            $result2->execute();
                            $idgrup_materia_arr = $result2->fetch();
                            $idgrup_materia = $idgrup_materia_arr['idgrups_materies'];

                            if ($idgrup_materia == "") {
                                $sql = "INSERT IGNORE grups_materies(id_grups,id_mat_uf_pla,data_inici,data_fi) ";
                                $sql .= "VALUES ('" . $fila[0] . "','" . $id_materia . "','" . $fila2['data_inici'] . "','" . $fila2['data_fi'] . "');";
                                //echo $sql;
                                $result = $db->prepare($sql);
                                $result->execute();

                                // Extrec l'id del grup ue acabo d'introduir					
                                $sql = "SELECT idgrups_materies FROM grups_materies ";
                                $sql .= "WHERE (id_grups='" . $fila[0] . "' AND id_mat_uf_pla='" . $id_materia . "');";
                                $result = $db->prepare($sql);
                                $result->execute();
                                $idgrup_materia_arr = $result2->fetch();
                                $idgrup_materia = $idgrup_materia_arr['idgrups_materies'];
                                //echo $idgrup_materia."<br>";
                                $id_professor = extreu_id('contacte_professor', 'valor', 'id_professor', $mat_prof['professor'], $db);
//                                                                echo $id_professor."<br>";
                                // Amb aquest id de grup matería puc introduir professor_agrupament amb l'd del professor i el del grup materia
                                $sql = "INSERT prof_agrupament(idprofessors,idagrups_materies) ";
                                $sql .= "VALUES ('" . $id_professor . "','" . $idgrup_materia . "');";
                                $result = $db->prepare($sql);
                                $result->execute();
                            }
                        }
                    }//foreach de materia_professor
                }// if de cerca del grup
            }//Foreach de grups
        } // While del select
    }// Else principal         

    introduir_fase('materies', 1, $db);
//    mysql_close($conexion);
    die("<script>location.href = './menu.php'</script>");
    ?>
</body>
