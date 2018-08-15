<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:prof-act.php
 * Autor: Víctor Lino
 * Descripció: Actualització o càrrega de professorat
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/func_grups_materies.php");
include("../funcions/funcions_generals.php");
include("../funcions/funcionsCsv.php");
ini_set("display_errors", 1);

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
    header("location: ../login/access-denied.php");
    exit();
}
?>
<html>
    <head>
        <title>Càrrega automàtica SAGA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            function marcar(source)
            {
                var patt1 = "es_CCFF_LOE";
                checkboxes = document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
                for (i = 0; i < checkboxes.length; i++) //recoremos todos los controles
                {
                    if ((checkboxes[i].type == "checkbox") && (checkboxes[i].name.indexOf(patt1) != -1))
                    {
                        checkboxes[i].checked = source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
                    }
                }
            }
        </script>
    </head>

    <body>
        <?php
        if (extreu_fase('app_horaris', $db) == 5) {
            if (($_POST['saga'] == 0) OR ( $_POST['saga'] == 1)) {
                
            } else {
                $page = "./main_mat_banner.php?retorn=yes";
                $sec = "0";
                header("Refresh: $sec; url=$page");
            }
        } else {
            if (($_POST['saga'] >= 2) AND ( $_POST['saga'] <= 6)) {
                $exporthorarixml = $_SESSION['upload_horaris'];
                if (extreu_fase('app_horaris', $db) == 4) {
                    $resultatconsulta = 1;
                } else {
                    $resultatconsulta = simplexml_load_file($exporthorarixml);
                }
            } else {
                $page = "./main_mat_banner.php?retorn=yes";
                $sec = "0";
                header("Refresh: $sec; url=$page");
            }
        }

        // No s'ha de fer res
        if ($_POST['saga'] == 1) {
            $page = "./menu.php";
            $sec = "0";
            header("Refresh: $sec; url=$page");

            // Carreguem tot com si fossin matèries    
        } else if (($_POST['saga'] == 2) OR ( $_POST['saga'] == 3)) {
            // Ja comprova a intro_mat.php si és o no segona càrrega
            $page = "./intro_mat.php";
            $sec = "0";
            header("Refresh: $sec; url=$page");
            // Carreguem des del fitxer de SAGA    
        } else if ($_POST['saga'] == 0) {
            select_plaestudis_saga();
        } else {
            $j = 0;
//            buidatge("ufs_mantenint_materies");
            // Carreguem tot de nou
            if ($_POST['saga'] == 4) {
                // Si és una segona carrega
                if (!extreu_fase('segona_carrega', $db)) {
                    buidatge("desdemateries", $db);
                }
                carrega_CCFF_de_SAGA($db);
            } else if ($_POST['saga'] == 6) {
                if (!extreu_fase('segona_carrega', $db)) {
                    buidatge("desdediesfrangesespais", $db);
                    
                }
                buidatge("materies", $db);
                $sql = "DELETE FROM equivalencies WHERE materia_saga IS NOT NULL AND materia_gp IS NOT NULL;";
                $result = $db->prepare($sql);
                $result->execute();
            }

            // Mantenim totes les matèries
            else if ($_POST['saga'] == 5) {
                buidatge("desdediesfrangesespais", $db);
                introduir_fase('materies', 1, $db);
                $page = "./menu.php";
                $sec = "0";
                header("Refresh: $sec; url=$page");
            }
            // Ara msotrem totes les matèries seleccionades per defecte de forma que
            // Haurem de desmarcar les que siguin LOE

            if (!$resultatconsulta) {
                echo "Carrega Horaris fallida";
            } else {
                echo "<br>Carregues correctes";
                print("<form method=\"post\" action=\"./DUAL_select_pla.php\" enctype=\"multipart/form-data\" id=\"profform\">");
                print("<table align=\"center\" >");
                print("<tr bgcolor=\"#ffbf6d\"><td align=\"center\" colspan=\"4\">");
                print("<h3>Selecció de matèries</h3>");
                print("<b>El primer checkbox indica si aquest element s'ha de crear. Hauries de desmarcar reunions, guardies, mòduls i matèries obsoletes.. <br>");
                print("El segon checkbox indica si aquest element és una matèria/crèdit ESO/BAT/CAS/LOGSE ");
                print("<br>Pots marcar tots o cap amb aquest checkbox i després afinar...<input type=\"checkbox\" onclick=\"marcar(this);\" >");
                print("<br>Tot el que estigui marcat en el segon checkbox es carregarà com a matèrìa/crèdit ESO/BAT/CAS/LOGSE, ");
                print("<br> la resta com a mòduls LOE. </b></td></tr> ");

                print("<tr align=\"center\" bgcolor=\"#ffbf6d\" ><td colspan=\"4\">Matèries del fitxer d'horaris</td></tr>");
                $pos = 1;
                $franges_pintades = 0;
                //GPUNTIS
                if (extreu_fase('app_horaris', $db) == 0) {
                    //echo "<br>app_horaris = 0";
                    foreach ($resultatconsulta->subjects->subject as $materia) {
                        if ($pos % 4 == 1) {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {
                                print("bgcolor=\"#ffbf6d\"");
                            }
                            print(">");
                        }
                        $nom_materia = $materia->longname;
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $codi_materia = $materia['id'];
                        $codi_materia = neteja_apostrofs($codi_materia);
                        $nom_materia = "(" . $codi_materia . ")" . $nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_" . $pos . "\" value = \"Yes\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_" . $pos . "\" value = \"Yes\" >");
                        print("<input type=\"text\" name=\"codi_mat_" . $pos . "\" value=\"" . $codi_materia . "\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_" . $pos . "\" value=\"" . $nom_materia . "\" SIZE=\"30\" READONLY></td>");
                        if ($pos % 4 == 0) {
                            print("</tr>");
                        }
                        $pos++;
                    }
                }

                //PEÑALARA    
                else if (extreu_fase('app_horaris', $db) == 1) {
                    //echo "<br>app_horaris = 1";
                    foreach ($resultatconsulta->materias->materia as $materia) {
                        if ($pos % 4 == 1) {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {
                                print("bgcolor=\"#ffbf6d\"");
                            }
                            print(">");
                        }
                        $nom_materia = $materia->nombreCompleto;
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $codi_materia = $materia->nombre;
                        $codi_materia = neteja_apostrofs($codi_materia);
                        $nom_materia = "(" . $codi_materia . ")" . $nom_materia;
                        print("<td bgcolor=\"#ffbf6d\" ><input type=\"checkbox\" name=\"crea_" . $pos . "\" value = \"Yes\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_" . $pos . "\" value = \"Yes\" >");
                        print("<input type=\"text\" name=\"codi_mat_" . $pos . "\" value=\"" . $codi_materia . "\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_" . $pos . "\" value=\"" . $nom_materia . "\" SIZE=\"30\" READONLY></td>");
                        if ($pos % 4 == 0) {
                            print("</tr>");
                        }
                        $pos++;
                    }
                }

                //kRONOWIN 
                else if (extreu_fase('app_horaris', $db) == 2) {
                    //echo "<br>app_horaris = 2";
                    foreach ($resultatconsulta->NOMASIGT->NOMASIGF as $materia) {
                        if ($pos % 4 == 1) {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {
                                print("bgcolor=\"#ffbf6d\"");
                            }
                            print(">");
                        }
                        $codi_materia = $materia['ABREV'];
                        $nom_materia = $materia['NOMBRE'];
                        $codi_materia = neteja_apostrofs($codi_materia);
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $nom_materia = "(" . $codi_materia . ")" . $nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_" . $pos . "\" CHECKED value = \"Yes\" >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_" . $pos . "\" value = \"Yes\" >");
                        print("<input type=\"text\" name=\"codi_mat_" . $pos . "\" value=\"" . $codi_materia . "\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_" . $pos . "\" value=\"" . $nom_materia . "\" SIZE=\"30\" READONLY></td>");
                        if ($pos % 4 == 0) {
                            print("</tr>");
                        }
                        $pos++;
                    }
                }

                //HORWIN
                else if (extreu_fase('app_horaris', $db) == 3) {
                    //echo "<br>app_horaris = 3";
                    foreach ($resultatconsulta->DATOS->ASIGNATURAS->ASIGNATURA as $materia) {
                        if ($pos % 4 == 1) {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {
                                print("bgcolor=\"#ffbf6d\"");
                            }
                            print(">");
                        }
                        $nom_materia = $materia['nombre'];
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $codi_materia = $materia['num_int_as'];
                        $nivell = $materia['nivel'];
                        $codi_materia = neteja_apostrofs($codi_materia);
                        $nom_materia = "(" . $nivell . ")" . $nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_" . $pos . "\" CHECKED value = \"Yes\" >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_" . $pos . "\" value = \"Yes\" >");
                        print("<input type=\"text\" name=\"codi_mat_" . $pos . "\" value=\"" . $codi_materia . "\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_" . $pos . "\" value=\"" . $nom_materia . "\" SIZE=\"30\" READONLY></td>");
                        if ($pos % 4 == 0) {
                            print("</tr>");
                        }
                        $pos++;
                    }
                }

                //ASC Horaris
                else if (extreu_fase('app_horaris', $db) == 4) {
                    $materies = extreuMateriesCsv();

                    for ($fila = 0; $fila <= count($materies) - 1; $fila++) {
                        if ($pos % 4 == 1) {
                            $franges_pintades ++;
                            print("<tr ");
                            if ($franges_pintades % 4 != 0) {
                                print("bgcolor=\"#ffbf6d\"");
                            }
                            print(">");
                        }
                        $nom_materia = $materies[$fila][0] . "-" . $materies[$fila][1];
                        $nom_materia = neteja_apostrofs($nom_materia);
                        $codi_materia = $nom_materia;
                        print("<td><input type=\"checkbox\" name=\"crea_" . $pos . "\" value = \"Yes\" CHECKED >");
                        print("<input type=\"checkbox\" name=\"es_CCFF_LOE_" . $pos . "\" value = \"Yes\" >");
                        print("<input type=\"text\" name=\"codi_mat_" . $pos . "\" value=\"" . $codi_materia . "\" SIZE=\"15\" HIDDEN>");
                        print("<input type=\"text\" name=\"nom_mat_" . $pos . "\" value=\"" . $nom_materia . "\" SIZE=\"30\" READONLY></td>");
                        if ($pos % 4 == 0) {
                            print("</tr>");
                        }
                        $pos++;
                    }
                }

                $pos--;
                print("<tr><td align=\"center\" colspan=\"4\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
                print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
                print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
                print("</table>");
                print("</form>");
            }
        }
        ?>
    </body>






