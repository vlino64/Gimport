<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
?>    
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:600px;">   
    <table id="dg" class="easyui-datagrid" title="Control assist&egrave;ncia general" style="width:auto;height:598px;" 
		data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './assist_adm_al/assist_adm_al_getdata.php',
		onClickRow: onClickRow
    ">    
        <thead>
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="dia_hora" width="100">Franja hor&agrave;ria</th>
                <th field="nom_materia" width="350">Mat&egrave;ria</th>
                <th field="grup" width="300">Grup</th>
                <th field="descripcio" width="180">Espai centre</th>
            </tr>
        </thead>
    </table>
    
    <div id="toolbar" style="padding:5px;height:auto">  
        Dia&nbsp;<input id="data" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
                
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
        Alumne&nbsp;<input id="nomAlumne" name="nomAlumne" size="60" />
        <input type="hidden" id="idAlumne" name="idAlumne" />
        <a id="gestio_faltes_alumne" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true"  plain="true" onclick="gestioFaltesAlumne()">
            Gesti&oacute; Assist&egrave;ncia Alumne</a>
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
        <a id="btnFalta" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="gestioIncident(<?=TIPUS_FALTA_ALUMNE_ABSENCIA?>,1)">Falta</a>
        <a id="btnRetard" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="gestioIncident(<?=TIPUS_FALTA_ALUMNE_RETARD?>,1)">Retard</a>
        <a id="btnRetard" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-tip',plain:true" onclick="gestioIncident(<?=TIPUS_FALTA_ALUMNE_SEGUIMENT?>,1)">Seguiment</a>
        <a id="btnJustificar" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="gestioIncident(<?=TIPUS_FALTA_ALUMNE_JUSTIFICADA?>,1)">Justificar</a>
        &nbsp;&nbsp;
        <a id="btnTreure" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="gestioAssistencia(0,0)">Treure incid&egrave;ncies</a>
    </div>
    </div>
    
    <script type="text/javascript">  
                var url;
		var editIndex = undefined;
		var nou_registre = 0;
		var idgrups;
		var nom_grup;
                
                $('#dg').datagrid({singleSelect:(this.value==1)})
                
                var options_alum = {
                        url: "./almat_tree/alum_getdata.php",

                        getValue: "alumne",

                        list: {
                                match: {
                                        enabled: true
                                },
                                
                                onSelectItemEvent: function() {
                                        var value = $("#nomAlumne").getSelectedItemData().id_alumne;
                                        $("#idAlumne").val(value).trigger("change");
                                }
                        }
                };

                $("#nomAlumne").easyAutocomplete(options_alum);
		
                function gestioFaltesAlumne(){
			$('#dg').datagrid('load',{ 
				idalumne : $('#idAlumne').val(),
                                data     : $('#data').datebox('getValue')
     			});
		} 
		
		function onClickRow(index){
		}
	
		function reject(){
		    $('#dg').datagrid('rejectChanges');
		    editIndex = undefined;
		    
		}
		
		function gestioAssistencia(tipus_incidencia,afegir){
		  var rows_mat   = $('#dg').datagrid('getSelections');
		  var rows_fh    = $('#dg').datagrid('getSelections');
                  var data       = $('#data').datebox('getValue');
		  
                  var ss_alum = [];
		  ss_alum.push($('#idAlumne').val());
                  
		  if (rows_mat){ 
			   var ss_mat = [];
			   var ss_fh  = [];
			   for(var i=0; i<rows_mat.length; i++){
					var row = rows_mat[i];
					ss_mat.push(row.idgrups_materies);
					ss_fh.push(row.idfranges_horaries);
			   }
			   url = './assist_adm/assist_adm_edita.php';
			   
                    $.messager.confirm('Confirmar','Introdu&iuml;m aquestes dades?',function(r){  
                    if (r){  
                        $.post(url,{
				data:data,
				id_tipus_incidencia:tipus_incidencia,
				afegir:afegir,
				idalumnes:ss_alum,
				idfranges_horaries:ss_fh,
				idgrups_materies:ss_mat},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Introducci&oacute; de faltes efectuada correctament!','info');
                            } else { 
				$.messager.alert('Error','Introducci&oacute; de faltes efectuada erroniament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json');  
                    }  
                    });  
                  }
		}
		
		function gestioIncident(tipus_incidencia,afegir){
		  var rows_mat   = $('#dg').datagrid('getSelections');
		  var rows_fh    = $('#dg').datagrid('getSelections');
                  var data       = $('#data').datebox('getValue');
		  
                  var ss_alum = [];
		  ss_alum.push($('#idAlumne').val());
                  
		  if (rows_mat){
			   var ss_mat = [];
			   var ss_fh  = [];
			   for(var i=0; i<rows_mat.length; i++){
				var row = rows_mat[i];
				ss_mat.push(row.idgrups_materies);
				ss_fh.push(row.idfranges_horaries);
			   }
			   url = './assist_adm/assist_adm_edita.php';
			   
		    $.messager.prompt('Gesti&oacute; incid&egrave;ncies', 'Sisplau, introdueixi el comentari per la incid&egrave;ncia', function(r){
                    if (r){  
                        $.post(url,{
				data:data,
				id_tipus_incidencia:tipus_incidencia,
				afegir:afegir,
				comentari:r,
				idalumnes:ss_alum,
				idfranges_horaries:ss_fh,
				idgrups_materies:ss_mat},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Introducci&oacute; d\' incid&egrave;ncies efectuada correctament!','info');
                            } else { 
				$.messager.alert('Error','Introducci&oacute; d\' incid&egrave;ncies efectuada erroniament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json');  
                    }  
                    });  
		  }
		}
		
		function myformatter(date){  
                    var y = date.getFullYear();  
                    var m = date.getMonth()+1;  
                    var d = date.getDate();  
                    return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
                }  
                function myparser(s){  
                    if (!s) return new Date();  
                    var ss = (s.split('-'));  
                    var y = parseInt(ss[0],10);  
                    var m = parseInt(ss[1],10);  
                    var d = parseInt(ss[2],10);  
                    if (!isNaN(y) && !isNaN(m) && !isNaN(d)){  
                        return new Date(d,m-1,y);  
                    } else {  
                        return new Date();  
                    }  
                }
		
		
	</script>
        
    <style type="text/css">  
        #fm{  
            margin:0;  
            padding:10px 30px;  
        }  
        .ftitle{  
            font-size:14px;  
            font-weight:bold;  
            padding:5px 0;  
            margin-bottom:10px;  
            border-bottom:1px solid #ccc;  
        }  
        .fitem{  
            margin-bottom:5px;  
        }  
        .fitem label{  
            display:inline-block;  
            width:80px;  
        }  
    </style>