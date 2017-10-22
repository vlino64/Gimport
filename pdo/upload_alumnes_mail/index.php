<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:index.php
* Autor: Víctor Lino
* Descripció: Pàgina de selecció d'opcions
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
	ini_set("session.cookie_lifetime","7200");
	ini_set("session.gc_maxlifetime","7200");
	session_start();
        if ( !isset($_SESSION['professor']) ) {
		header('Location: ../index.php');
		$idprofessors         = 0;
		$curs_escolar         = '';
		$curs_escolar_literal = '';
	}
	else 
            
             { ?> 
            <html>
            <head>
            <title>Càrrega provisional de fotos</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf8">
            <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">

            </head>

            <body>

            <form enctype="multipart/form-data" action="./carrega_mails.php" method="post" name="fcontacto">

            <table class="general" width="70%" align="center" >
                    <tr><td></td><td></td></tr>


                    <tr colspan="2" align="center">
                    <td align="center">
                            <br>
                            <?php echo "FITXER CSV DE MAILS A CARREGAR A CARREGAR<BR>"; ?>
                            <?php echo "El separador ha de ser coma i els camps entre cometes"; ?>
                            <input name="archivo" type="file" id="archivo">
                            <br><br><input name="boton" type="submit" id="boton" value="<?php echo "PUJAR"; ?>">
                            </form>
                            </table>
                    </td>
                    </tr>
            </table>
            </div>


            </body>

        <?php
	}

?>







