<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $idgrups = $_REQUEST['idgrups'];
?>        
    <div id="alumnesDiv" style="float:left">    
    <table id="dg_email" class="easyui-datagrid" title="Alumnes de <?=getGrup($db,$idgrups)["nom"]?>" style=" margin-right:5px;width:500px;height:530px"
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#tb',
		url: './tutor/tutor_alum_getdata.php?idgrups=<?=$idgrups?>'	
            ">    
        <thead>  
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="Valor" width="400" sortable="true">Alumne</th>
            </tr>  
        </thead>  
    </table>
    </div>
    
    <?php include_once('../tutor_send/tutor_form_email.php'); ?>
    
    <div id="tb" style="padding:5px;height:auto">
        <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="getSelections()">Accepta destinataris</a>
    </div>
    
    <script type="text/javascript">  
        var url;
		
	$('#dg_email').datagrid({singleSelect:(this.value==1)})
		
	$(function(){  
            $('#dg_email').datagrid({  
				rowStyler:function(index,row){
				    if (row.id_tipus_incidencia==<?=TIPUS_FALTA_ALUMNE_ABSENCIA?>){
						return 'background-color:whitesmoke;color:#be0f34;font-weight:bold;';
					}
					if (row.id_tipus_incidencia==<?=TIPUS_FALTA_ALUMNE_RETARD?>){
						return 'background-color:whitesmoke;color:#ada410;font-weight:bold;';
					}
					if (row.id_tipus_incidencia==<?=TIPUS_FALTA_ALUMNE_SEGUIMENT?>){
						return 'background-color:whitesmoke;color:#002596;font-weight:bold;';
					}
					if (row.id_tipus_incidencia==<?=TIPUS_FALTA_ALUMNE_JUSTIFICADA?>){
						return 'background-color:#a1d88b;color:#009a49;font-weight:bold;';
					}
				}  
            });  
        });
		
		function getSelections(){
			var ss = []; var ssm = [];
			var rows = $('#dg_email').datagrid('getSelections');
			for(var i=0; i<rows.length; i++){
			var row = rows[i];
			  ss.push(row.idalumnes);
			  ssm = ssm + (row.Valor+'\n');
			}
                       
			url = './tutor_send/tutor_set_target_email.php?idalumnes='+ss;
			
			$.post(url,{},function(result){  
            if (result.success){  
			   
            } else {  
               $.messager.show({     
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
			   
             },'json');
			 $.messager.alert('Informaci&oacute;','Destinataris inclosos correctament.Ja pots redactar el missatge o b&egrave; triar-lo predefinit i enviar.');
			 
			 $('#frm_email').form('load',{
				destinataris:ssm,
			 });
			
		}

		function doReloadCorreu(idgrups,nomgrup){
			url = './tutor_send/tutor_email.php?idgrups='+idgrups;
			$('#dlg_fm_email').dialog('refresh', url);
		}
		
		function enviarCorreu(idgrups,nomgrup){
		    var rows = $('#dg_email').datagrid('getSelections');
			if (rows) {  
			    var ss = [];
			    for(var i=0; i<rows.length; i++){
					var row = rows[i];
					ss.push(row.idalumnes);
				}
				url = './tutor_send/tutor_form_email.php?idalumnes='+ss;
				$('#dlg_fm_email').dialog('open').dialog('setTitle','Enviar Correu grup '+nomgrup);
				$('#dlg_fm_email').dialog('refresh', url);
			}
		}

	</script>