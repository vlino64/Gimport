<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$curs            = getCursActual($db)["idperiodes_escolars"];
$idalumnes       = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0;
?>   
    <div id="head_ccc" style="background:whitesmoke; width:100%; height:25px; padding-top:6px;">
    	<h2>&nbsp;&nbsp;Nova CCC</h2>
    </div>
    <br />  
    <form id="fm_nova_ccc" method="post" novalidate>
    <input type="hidden" name="idalumne" value="<?=$idalumnes?>" />
                  	
    <label style="color:#333">Data incid&egrave;ncia</label>
    <input id="data_incident" name="data_incident" class="easyui-datebox" data-options="required: true,formatter:myformatter,parser:myparser">
    &nbsp;&nbsp;
              
   	<label style="width:150px; color:#333">Professor</label>
    <input id="idprofessor" name="idprofessor" class="easyui-combobox" data-options="
                required: true,
                width: 350,
                valueField: 'id_professor',
                textField: 'Valor',
                url: './ccc_adm/prof_getdata.php'
    ">
    <br /><br />
                
    <label style="width:150px; color:#333">Horari professor (si els fets s'han produ&iuml;t dintre d'alguna classe, si us plau indiqueu-la)</label><br />
    <select id="idunitats_classe" name="idunitats_classe" class="easyui-combogrid" style="width:760px" data-options="
                    required: false,
                    panelWidth: 760,
                    idField: 'idunitats_classe',
                    textField: 'nom_materia',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'dia_hora',title:'Dia/Hora',width:200},
                        {field:'nom_materia',title:'Materia',width:400},
                        {field:'grup',title:'Grup',width:190},
                        {field:'descripcio',title:'Espai',width:70}
                    ]],
                    fitColumns: true
      ">
      </select>
      <br /><br />       
                                
      <label style="width:150px; color:#333">Fets que s'han produ&iuml;t</label><br />  
      <textarea name="descripcio_detallada" style="height:150px; width:800px;"></textarea>
      <br /><br />
                                
      </form>
        
      <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td bgcolor="whitesmoke" align="right">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveCCC()">Acceptar</a>
        			&nbsp;&nbsp;
                </td>
            </tr>  
      </table>
    
    <script type="text/javascript">  		
		var url;
		
	    $('#data_incident').datebox({
			onSelect: function(date){
				data_incident = $('#data_incident').datebox('getValue');
				id_professor  = $('#idprofessor').combobox('getValue');
				
				$('#idunitats_classe').combogrid({
					url:'./alum_automat/horari_getdata.php?idprofessors='+id_professor+'&data='+data_incident,
					valueField:'idunitats_classe',
					textField:'nom_materia'
				});
			}
		});
		
		$('#idprofessor').combobox({
			onSelect: function(date){
				data_incident = $('#data_incident').datebox('getValue');
				id_professor  = $('#idprofessor').combobox('getValue');
				
				$('#idunitats_classe').combogrid({
					url:'./alum_automat/horari_getdata.php?idprofessors='+id_professor+'&data='+data_incident,
					valueField:'idunitats_classe',
					textField:'nom_materia'
				});
			}
		});
		
		function saveCCC(){		
			url = './alum_automat/alum_automat_edita_ccc.php';
			
			$('#fm_nova_ccc').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
					var result = eval('('+result+')');
                    $.messager.alert('Informaci&oacute;','Nova CCC enregistrada correctament!','info');
					$('#fm_nova_ccc').form('clear');
                }
            });
        }
		</script>