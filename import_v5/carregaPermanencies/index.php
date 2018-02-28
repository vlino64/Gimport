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
        <script type="text/javascript">
        </script>
    </head>

    <body>

        <form enctype="multipart/form-data" action="./index3.php" method="post" name="fcontacto">
            <br><br><br>
            <table class="general" width="70%" align="center" bgcolor="#ffbf6d">
                <tr><td align="center"><p>
                            <b>Per poder emplenar  els horaris amb hores de permanència s'han de realitzar <br>
                                algunes operacions previes:</b><br><br>
                            1. Crea un torn nou (o més) amb una única franja que començi amb l'hora d'entrada i acabi 
                            la de sortida. O amb les <br>que tu creguis necessàries<br>
                            <font color = "grey"><sub>A tenir en compte: <br>
                                Has de crear en primer lloc el torn, després les franges horàries.<br>
                                Les hores tenen el format XX:XX:XX<br>
                                A cada franja li has d'assignar els dies<br><br>
                            </sub></font>
                            2. Comprova que el professorat que ha marxat està desactivat i que el nou està introduit 
                            en el sistema <br>( Els centres amb gestió centralitzada, recordeu fer-ho des d'aquesta 
                            gestió centralitzada).
                            <br><br>

                    </td>
                </tr>
                <tr><td align="center"><input type=button onClick="location.href = 'formPermanencies.php'" value="Ara si, carreguem permanències" ></td></tr>
            </table>
            <br>




            </body>



