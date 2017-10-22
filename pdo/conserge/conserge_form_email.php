<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
?>

<div class="easyui-panel" title="Correu" style="padding-left:30px;width:505px; height:550px; filter:alpha(opacity=75);-moz-opacity:.75;opacity:.75;">
    
<form id="frm_email" enctype="application/x-www-form-urlencoded" method="post">

<label>Persones a enviar correu electr&ograve;nic</label><br />

<textarea readonly class="easyui-validatebox" name="destinataris" id="destinataris" class="form-text" rows="12" cols="50">
</textarea>

<label>Plantilles de missatges predefinides</label><br />
<input id="textos_sms" class="easyui-combobox" data-options="
        valueField: 'descripcio',
        textField: 'nom',
        url: './textos_sms/textos_sms_getdata.php',
        onSelect: function(rec){
            $('#contingut').val($('#textos_sms').combobox('getValue'));
        }">

<br />

<label>Missatge&nbsp;</label><br />
<textarea class="easyui-validatebox" name="contingut" id="contingut" class="form-text" rows="8" cols="50" style="overflow:auto">
</textarea>
<br />

<input style="font-size:24px; background-color:#FF0000; color:#FFFFFF" class="enviar" type="button" value="Enviar Correu" onClick="javascript:enviaCorreu()" />
      
</form>

</div>

<div id="resultDiv" style="position:absolute; left:540px; width:470px; color: #CC0000; font:16px bolder Geneva, Arial, Helvetica, sans-serif;">
 
</div>

<script type="text/javascript">
    function enviaCorreu() {
		$('#frm_email').form('submit',{  
                url: './conserge/conserge_envia_email.php', 
                onSubmit: function(){  
                    return $(this).form('validate');  
                },  
                success: function(result){
		   $('#resultDiv').html(result);
                }  
            });  
	}
</script>