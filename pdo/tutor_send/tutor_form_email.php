<div class="easyui-panel" title="Correu" style="padding-left:30px;width:505px; height:550px; filter:alpha(opacity=75);-moz-opacity:.75;opacity:.75;">
    
<form id="frm_email" enctype="application/x-www-form-urlencoded" method="post">

<label>Persones a enviar correu electr&ograve;nic</label><br />
<textarea readonly class="easyui-validatebox" name="destinataris" id="destinataris" class="form-text" rows="10" cols="50">
</textarea>


<input class="easyui-validatebox" type="hidden" name="from" id="from" value="INS" class="form-text">
<input class="easyui-validatebox" type="hidden" name="when" id="when" value="2013-08-30 12:20:11" class="form-text">
<input type="hidden" name="command" value="Sms_Send_Bulk" class="form-text" id="command">


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
<textarea class="easyui-validatebox" name="contingut" id="contingut" class="form-text" rows="12" cols="50" style="overflow:hidden">
</textarea>
<br />

<input style="font-size:24px; background-color:#FF0000; color:#FFFFFF" class="enviar" type="button" value="Enviar Correu" onClick="javascript:enviaCorreu()" />
      
</form>

</div>

<div id="resultEmailDiv" style="position:absolute; left:540px; width:470px; color: #CC0000; font:16px bolder Geneva, Arial, Helvetica, sans-serif;">
 
</div>

<script type="text/javascript">
    function enviaCorreu() {
		$('#frm_email').form('submit',{  
                url: './tutor_send/tutor_envia_email.php', 
                onSubmit: function(){  
                    return $(this).form('validate');  
                },  
                success: function(result){
		    $('#resultEmailDiv').html(result);
                }  
            });  
	}
	
	function limitText(limitField, limitCount, limitNum) {
		if (limitField.value.length > limitNum) {
			limitField.value = limitField.value.substring(0, limitNum);
		} else {
			limitCount.value = limitNum - limitField.value.length;
		}
	}
</script>