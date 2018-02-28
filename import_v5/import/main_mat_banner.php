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
        <title>Càrrega automàtica _SAGA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            function Urgente5(obj)
            {
                if (obj.checked)
                {
                    alert('\
                    Podeu utilitzar aquesta opció si al fer els horaris \n\
                    heu aprofitat les matèries i móduls del curs \n\
                    passat i/o els canvis que heu fet en les matèries \n\
                    són mínims.\n\
                    Pot també resultar útil si heu creat móduls i Ufs \n\
                    manualment durant el curs anterior.');
                }
                return true;
            }
            function Urgente6(obj)
            {
                if (obj.checked)
                {
                    alert('\
                    Podeu utilitzar aquesta opció si al fitxer de SAGA \n\
                    els mòduls i les Ufs encara no han estat carregats \n\
                    i voleu aprofitar part de la càrrega del curs passat.\n\
                    Pot també resultar útil si heu creat móduls i Ufs \n\
                    manualment durant el curs anterior.');
                }
                return true;
            }
            function openWin() {
                window.open("");
            }
        </script>
    </head>

    <body>

        <?php
        if ((isset($_GET['retorn'])) && ($_GET['retorn'] == 'yes')) {
            ?>
            <script type="text/javascript">
                alert("     Alguna de les opcions no és correcta o manquen configuracions. \n\
            Revisa les opcions escollides. \n\
            En ocasions pot resultar útil buidar el formulari amb el botò inferior \n");
            </script>
            <?php
        }


// Netegem registres amb camps buits
        $sql = "DELETE FROM `equivalencies` WHERE grup_gp!='' AND grup_ga='' AND grup_saga='';";
        $result = $db->prepare($sql);
        $result->execute();


        if (extreu_fase('app_horaris', $db) == 5) {
            ?>

            <form enctype="multipart/form-data" action="./main_mat.php" method="post" name="selectMateries">
                <br><br><br>        
                <table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
                    <tr><td align="center"><p><h3>Selecciona una de les opcions</h3><br></td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="0" id="materies_0" />
                            Carregar les matèries, mòduls i unitats formatives del fitxer de SAGA</td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="10" id="materies_1" DISABLED /> 
                            <font color = 'grey'>Seleccionar les matèries que no volem s'esborrin i a continuació 
                            carregar la informació de SAGA</font></td></tr>                        
                    <tr><td align="center"><input type="radio" name="saga" value="1" id="materies_1"  /> 
                            Mantenir matèries, mòduls i unitats formatives del curs passat</td></tr>
                    <tr><td align="center"><input name="boton" type="submit" id="boton" value="Envia la configuració"></td></tr>
                </table>
            </form>        
            <?php
        } else {
            ?>
            <form enctype="multipart/form-data" action="./main_mat.php" method="post" name="selectMateries">
                <br><br><br>        
                <table class="general" width="70%" align="center"bgcolor="#ffbf6d" >
                    <tr><td align="center"><p><h3>Llegeix atentament i selecciona una de les opcions</h3><br></td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="2" id="materies_2" />
                            No tinc cicles formatius LOE</td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="3" id="materies_3"  /> 
                            Tinc cicles formatius LOE però vull tractar els móduls com si fossin matèries anuals. 
                            Sense tenir en compte les unitats formatives</td></tr>
                    <tr><td align="center">____________________________________________________</td></tr>                    
                    <tr><td align="center">Tinc cicles formatius LOE i ...</td></tr>                    
                    <tr><td align="center"><input type="radio" name="saga" value="4" id="materies_4"  /> 
                            vull carregar les matèries, crèdits i mòduls des del fitxer d'horaris, i les Ufs del fitxer de SAGA</td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="6" id="materies_6" onclick="Urgente6(this)"   /> 
                            vull carregar les matèries, crèdits i mòduls des del fitxer d'horaris i mantenir les Ufs carregades el curs passat</td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="5" id="materies_5" onclick="Urgente5(this)" /> 
                            vull mantenir les matèries/mòduls/UFs que ja tinc al programa d'horaris</td></tr>
                    <tr><td align="center"><input type="radio" name="saga" value="10" id="materies_1" DISABLED  /> 
                            <font color = 'grey'>Seleccionar les matèries/mòduls/UFs que no volem s'esborrin i a continuació carregar 
                            la informació del programa d'horaris </font></td></tr>     
                    <tr><td><a href="javascript:window.open('mostraUFsSaga.php','mywindowtitle','width=500,height=600')">Quines Ufs hi ha al<br> fitxer de Saga ?</a></td></tr>                    
                    <tr><td align="center"><input name="boton" type="submit" id="boton" value="Envia la configuració"></td></tr>
                </table>
            </form>             
            <?php
        }
        ?>
    </body>
</html>

