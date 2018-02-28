<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:grups_act.php
 * Autor: Víctor Lino
 * Descripció: Carrega els grups del fitxer de saga
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once("../../pdo/bbdd/connect.php");
include("../funcions/funcions_generals.php");
include("../funcions/func_grups_materies.php");
include("../funcions/func_prof_alum.php");
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
    </head>

    <body>
        <?php
        if (!isset($_POST['recompte'])) {
            $exporthorarixml = $_SESSION['upload_horaris'];
            $exportsagaxml = $_SESSION['upload_saga'];
            if ($_GET['app'] == 5)
                $arrProfessorat = extreu_professorat($exportsagaxml, $_GET['app']);
            else
                $arrProfessorat = extreu_professorat($exporthorarixml, $_GET['app']);
            // Capçalera del formulari
            print("<form method=\"post\" action=\"./act_prof_form_GEISoft.php\" enctype=\"multipart/form-data\" id=\"profform\">");
            print("<table align=\"center\">");
            print("<tr><td align=\"center\" colspan=\"3\">");
            print("<h3>Relaciona els professors que tenim a SAGA amb els del programa d'assistència</h3>");
            print("<h3>Completa els que manquin i comprova els que ja estan emparellats</h3>");
            print("<tr align=\"center\" bgcolor=\"orange\" ><td>Professorat SAGA o eina horaris</td><td>Identificador </td><td>Professorat a Gassist</td>");
            print("</tr>");
            $pos = 1;
            if ($_GET['app'] != 4) {
                if ($_GET['app'] == 5) {
                    $resultatconsulta2 = simplexml_load_file($exportsagaxml);
                } else {
                    $resultatconsulta2 = simplexml_load_file($exporthorarixml);
                }
                if (!$resultatconsulta2) { // no es carrega l'xml si es tracta d'un csv
                    echo "Carrega fallida saga >> ";
                }
            }
            // Si NO tens gestió centralitzada
            foreach ($arrProfessorat as $professor) {
                $codi = $professor[0];
                $nomComplet = $professor[1];
                if ($nomComplet == "") {
                    $nomComplet = $codi;
                }
                print("<tr>");
                print("<td><input type=\"text\" name=\"nom_" . $pos . "\" value=\"" . $nomComplet . "\" SIZE=\"40\"READONLY></td>");
                print("<td><input type=\"text\" name=\"id_prof_saga_" . $pos . "\" value=\"" . $codi . "\" SIZE=\"10\" READONLY></td>");

                // Si l'emparellament ja està fet no presentem el desplegable
                $sql = "SELECT COUNT(codi_prof_gp) AS compte FROM equivalencies WHERE codi_prof_gp='" . $codi . "';";
                $result2 = $db->prepare($sql);
                $result2->execute();
                $fila2 = $result2->fetch();
                if ($fila2['compte'] == 0) {
                    print("<td><select name=\"user_gassist_" . $pos . "\" ");
                    print(">");
                    $sql = "SELECT A.Valor AS valor,A.id_professor AS codi ";
                    $sql .= "FROM contacte_professor A, professors B ";
                    $sql .= "WHERE A.id_professor = B.idprofessors AND B.activat = 'S' ";
                    $sql .= "AND id_tipus_contacte = 1;";
                    $result = $db->prepare($sql);
                    $result->execute();
                    $files = $result->rowCount();
                    print("<option value = \"0\" >No hi ha equivalència</option>");
                    foreach ($result->fetchAll() as $fila) {
                        print("<option value=\"" . $fila['codi'] . "\" ");
                        print(">" . $fila['valor'] . "</option>");
                    }
                    print("</select></td></tr>");
                } else {
                    print("<td>Ja emparellat</td>");
                }

                $pos++;
            }
            $pos--;
            print("<tr><td align=\"center\" colspan=\"3\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\">");
            print("&nbsp&nbsp<input type=button onClick=\"location.href='./menu.php'\" value=\"Torna al menú!\" ></td></tr>");
            print("<tr><td align=\"center\" colspan=\"3\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
            print("</table>");
            print("</form>");
        } else {
            $recompte = $_POST['recompte'];
            // Carreguem els grups i el seu torn
            for ($i = 1; $i <= $recompte; $i++) {
                echo "<br>" . $_POST['user_gassist_' . $i];
                if ((isset($_POST['user_gassist_' . $i])) && ($_POST['user_gassist_' . $i] != '0' )) {
                    $nom = $_POST['nom_' . $i];
                    $id_prof_saga = $_POST['id_prof_saga_' . $i];
                    $codi_prof_gp = $_POST['user_gassist_' . $i];

                    $sql = "INSERT INTO equivalencies(prof_ga,nom_prof_gp,codi_prof_gp) VALUES ('" . $codi_prof_gp . "','" . $nom . "','" . $id_prof_saga . "');";
                    $result = $db->prepare($sql);
                    $result->execute();
                }
            }

            introduir_fase('professorat', 1, $db);
            $page = "./menu.php";
            $sec = "0";
            header("Refresh: $sec; url=$page");
        }
        ?>
    </body>
