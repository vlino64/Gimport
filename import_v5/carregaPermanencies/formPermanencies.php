<?php
/* ---------------------------------------------------------------
 * Aplicatiu: programa d'importació de dades a gassist
 * Fitxer:index.php
 * Autor: Víctor Lino
 * Descripció: Pàgina de selecció d'opcions
 * Pre condi.:
 * Post cond.:
 * 
  ---------------------------------------------------------------- */
require_once('../../pdo/bbdd/connect.php');
include("../funcions/funcions_generals.php");

session_start();
//Check whether the session variable SESS_MEMBER is present or not
if ((!isset($_SESSION['SESS_MEMBER'])) || ($_SESSION['SESS_MEMBER'] != "access_ok")) {
    header("location: ../login/access-denied.php");
    exit();
}
?>

<html>
    <head>
        <title>Menú Saga</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <?php
        print("<form method=\"post\" action=\"./actPermanencies.php\" enctype=\"multipart/form-data\" id=\"profform\">");

        print("<table align = 'center'>");
        print("<tr><td colspan=\"3\"><h2>Emparellament professor/torn</h2><br>");
        print("</td></tr>");
        print("<tr align=\"center\" bgcolor=\"orange\" ><td>Nom i Cognoms</td><td></td><td>Torn per assignar permanències</td></tr>");

        $pos = 1;

        $sql = "SELECT A.idprofessors AS idprof, B.Valor AS valor "
                . "FROM professors A, contacte_professor B "
                . "WHERE A.idprofessors = B.id_professor AND "
                . "A.activat = 'S' AND "
                . "B.id_tipus_contacte = 1;";
        //echo "<br>".$sql;
        $result = $db->prepare($sql);
        $result->execute();
        foreach ($result->fetchAll() as $fila) {
            print("<tr><td><input type=\"text\" name=\"nomProfessor" . $pos . "\" value=\"" . $fila['valor'] . "\" READONLY></td>");
            print("<td><input type=\"text\" name=\"idProfessor" . $pos . "\" value=\"" . $fila['idprof'] . "\" HIDDEN ></td>");
            print("<td><select name=\"id_torn_" . $pos . "\" ");
            print(">");
            print("<option value=\"0\">No es crearà</option>");
            $sql2 = "SELECT idtorn AS id,nom_torn AS nom_torn FROM torn WHERE 1 ORDER BY nom_torn; ";
            $result2 = $db->prepare($sql2);
            $result2->execute();
            foreach ($result2->fetchAll() as $fila2) {
                print("<option value=\"" . $fila2['id'] . "\" ");
                print(">" . $fila2['nom_torn'] . "</option>");
            }
            print("</select></td>");
            print("</tr> ");
            $pos++;
        }

        $pos--;
        if ($pos != 0) {
            print("<tr><td align=\"center\" colspan=\"7\"><input name=\"boton\" type=\"submit\" id=\"boton\" value=\"Enviar\"></td></tr>");
            print("<tr><td align=\"center\" colspan=\"7\"><input type=\"text\" name=\"recompte\" value=\"" . $pos . "\" HIDDEN ></td></tr>");
            print("</table>");
            print("</form>");
        } else {

            $page = "../import/index.php";
            $sec = "0";
            header("Refresh: $sec; url=$page");
        }
        ?>








    </body>





