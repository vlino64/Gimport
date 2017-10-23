<?php
/*---------------------------------------------------------------
* Aplicatiu: Geisoft. Programa d'importaci� de dades
* Fitxer: login-form.php
* Autor: Jatinder(phpsense)/ Modif: Victor Lino
* Descripci�: Fitxer: Formulari de login
* Pre condi.:
* Post cond.:
----------------------------------------------------------------*/
//require_once('../../bbdd/connect.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Form</title>
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<br><br><br>
<form name="form1" method="post" action="./login-exec.php">

<table width="75%" align="center" border="0" bgcolor="#ffbf6d">
	<tr border="0"><td align="center">
	<table border="0" >
    <tr>
      <td colspan=2 align="center"><b><br>Per poder accedir a aquesta aplicaci&oacute; necessites permisos de superadministrador</b></td>
     </tr>
    <tr>
      <td width="112" align="right"><b>Login</b></td>
      <td width="188"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <td  align="right"><b>Password</b></td>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Login" /></td>
    </tr>
    </table>
	</td></tr>
</table>

</form>
<table width="75%" align="center" border="0" bgcolor="#ffbf6d">
	<tr border="0"><td align="center">
	<table border="0" >
    <tr>
      <td align="center">
   
          
          
		 <h2>Tinguem en compte :</h2><br>
      <textarea rows="10" cols="80" READONLY>
1. Aquest programa d'importació carregarà les dades de SAGA i, si es disposa de les eines, dels programes de gestió d'horaris GPuntis, GHC Peñalara I HorWin (Sevilla).

2. En primer lloc es demanarà informació del tipus de càrrega que volem fer. És vital indicar-ho correctament perquè la càrrega es faci el millor possible.

3. En aquestes pantalles inicials es demanarà la càrrega dels fitxers xml exportats des de les aplicacions.

4. A continuació ens apareixerà un menú en funció de les opcions seleccionades.

5. Aquest menú anirà canviant a mesura que anem passant fases.

6. L'aplicació anirà combinant la informació de totes dues aplicacions és per això important que estiguin les dades el més completes possibles, tant en SAGA com en el programa de generació d'horaris.

7. L'aplicació, davant d'una informació incompleta, o que no pugui gestionar, ometrà la introducció d'aquesta informació. Tampoc podrà gestionar informacions incompletes o no presents en alguns dels fitxers.
      </textarea> 
		</td>
     </tr>
    </table>
            </td></tr><tr><td></td></tr>
</table>

<br><br>
<div style="position:relative; bottom:10px; text-align:center; width: 30%; margin:0 auto; height:30px; border:0px solid grey;">
<p>GEISoft - Programa d'importaci&oacute; de dades V4.0</p>
<p>2017/18</p>
</div>
</body>

</html>
