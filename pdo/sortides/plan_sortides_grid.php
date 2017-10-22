<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $strNoCache    = "";
   $id_professor  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;"> 
    <table id="dg" class="easyui-datagrid" title="Planificaci&oacute; sortides" 
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './sortides/plan_sortides_getdata.php'
	">    
        <thead>  
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="Valor" width="520" sortable="true">Alumne</th>                
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto"> 
    <form id="ff" action="./sortides/plan_sortides_edita.php" method="post">
    <input type="hidden" name="idprofessors" value="<?=$id_professor?>" />
    <table cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td>Dia&nbsp;anada</td>
        <td>Hora&nbsp;anada</td>
        <td>Dia&nbsp;tornada</td>
        <td>Hora&nbsp;tornada</td>
        <td>Lloc</td>
        </tr>
        
        <tr>
        <td><input id="data_anada" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;</td>
        <td><input id="hora_anada" class="easyui-timespinner" data-options="min:'08:00',max:'23:00'" style="width:70px;"></input>&nbsp;</td>
        <td><input id="data_tornada" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;</td>
        <td><input id="hora_tornada" class="easyui-timespinner" data-options="min:'08:00',max:'23:00'" style="width:70px;"></input>&nbsp;&nbsp;&nbsp;</td>
        <td><input id="lloc" name="lloc" class="easyui-validatebox" type="text" size="23" data-options="required: true,type:'textarea',validType:'length[0,55]'"></td>
        </tr>
     </table>

     <table cellpadding="0" cellspacing="0">   
        <tr>
        <td>Descripci&oacute;</td>
        <td>Tancada</td>
        <td></td>
        </tr>
        
        <tr>
        <td><textarea id="descripcio" name="descripcio" rows="3" cols="55"></textarea></td>
        <td valign="top">
        <input id="tancada" name="tancada" type="radio" class="easyui-validatebox" value="S" />&nbsp;S&iacute;<br />
        <input id="tancada" name="tancada" type="radio" class="easyui-validatebox" value="N" checked="checked" />&nbsp;No
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td valign="top">
        <a id="add_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-reload',plain:true" onclick="planificarSortida(1,<?=$id_professor?>)">Planificar sortida</a><br />
        <a id="add_prof_button" href="javascript:void(0)" class="easyui-linkbutton" disabled="true" data-options="iconCls:'icon-add',plain:true" onclick="Professors(<?=$id_professor?>)">Professors inclosos</a></td>
        </tr>
     </table>
     
     <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
     
     <table cellpadding="0" cellspacing="0">  
        <tr>
        <td>Grups</td>
        <td>&nbsp;</td>
        </tr>
        
        <tr>
        <td>
        <select id="grups" class="easyui-combogrid" style="width:350px" data-options="
            panelWidth: 350,
            idField: 'idgrups',
            textField: 'nom',
            url: url,
            disabled:true,
            method: 'get',
            columns: [[
                {field:'nom',title:'Grup',width:300}
            ]],
            fitColumns: true
        ">
        </select>&nbsp;
        </td>
        <td><a id="search_alum_button" href="#" class="easyui-linkbutton" disabled="true" iconCls="icon-search" onclick="doSearch()">Alumnes del grup</a>&nbsp;</td></tr>

        <tr>
        <td>
        <a id="add_alum_button" href="javascript:void(0)" class="easyui-linkbutton" disabled="true" data-options="iconCls:'icon-add',plain:true" onclick="gestioAlumnes(1)">Afegir alumnes</a>
        <a id="del_alum_button" href="javascript:void(0)" class="easyui-linkbutton" disabled="true" data-options="iconCls:'icon-remove',plain:true" onclick="gestioAlumnes(0)">Treure alumnes</a>
        </td>
        <td><a id="info_alum_button" href="javascript:void(0)" class="easyui-linkbutton" disabled="true" data-options="iconCls:'icon-help',plain:true" onclick="veureAlumnes()">Alumnes inclosos</a></td>
        </tr>   
    </table>
    
    </form>
    </div>
    </div>
    
	<div id="dlg_hor" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" toolbar="#dlg_hor-toolbar">  
    </div>
    
    <div id="dlg_hor-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_hor').dialog('refresh')">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_hor').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <div id="tb" style="height:auto">
		<a id="btnAppend" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Nou</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="destroyItem()">Esborrar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept()">Acceptar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="reject()">Tancar</a>
    </div>
    
    <div id="dlg" class="easyui-dialog" style="width:725px;height:500px;padding:5px 5px" modal="true" closed="true" buttons="#dlg-buttons">
        <table id="dg_mat" class="easyui-datagrid" title="Professors" style="width:700px;height:455px"
                data-options="
                    iconCls: 'icon-edit',
                    singleSelect: true,
                    url:'./sortides/plan_sortides_getdetail.php',
                    pagination: false,
                    rownumbers: true, 
                    toolbar: '#tb',
                    onClickRow: onClickRow
                ">
            <thead>
                <tr>
                    <th data-options="field:'id_professorat',width:550,
                            formatter:function(value,row){
								return row.professor;
							},
							editor:{
								type:'combogrid',
								options:{
									idField: 'id_professor', 
                                    valueField:'id_professor',
									textField:'professor',
                                    mode:'remote',
									url:'./sortides/prof_getdata.php',
									required:false,
                                    columns:[[
                                        {field:'professor',title:'professor',width:530}
                                    ]]
								}
                            }">Professor</th>
                            
                    <th data-options="field:'responsable',width:100,align:'center',
                    formatter:function(value,row){
                                 if (value==0) {
                                    valor = '';
                                 }
                                 else {
                                    valor = 'S';
                                 }
                                 return valor;
                           }, 
                    editor:{type:'checkbox',options:{on:'S',off:''}}
                    ">Responsable</th>
                    </tr>
            </thead>
        </table>
    </div>
    
    <div id="tb_alum" style="height:auto">
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="$('#dlg_alum').dialog('close');">Tancar</a>
    </div>
    
    <div id="dlg_alum" class="easyui-dialog" style="width:845px;height:550px;padding:5px 5px" modal="false" closed="true" buttons="#tb_alum">
        <table id="dg_alum" class="easyui-datagrid" title="Alumnes" style="width:auto;height:auto"
                data-options="
                    iconCls: 'icon-edit',
                    singleSelect: true,
                    url:'./sortides/plan_sortides_getalumnes.php',
                    pagination: false,
                    rownumbers: true 
                ">
            <thead>
                <tr>
                    <th data-options="field:'grup',width:250">Grup</th>                            
                    <th data-options="field:'alumne',width:450">Alumne</th>
                    </tr>
            </thead>
        </table>
    </div>
    
    <script type="text/javascript">  
        var url;
		var editIndex = undefined;
		var nou_registre = 0;
		var idgrups;
		var nom_grup;
		var data;
		var theDate;
		var theDay;
		
		$.extend($.fn.datagrid.defaults.editors, {
		combogrid: {
				init: function(container, options){
					var input = $('<input type="text" class="datagrid-editable-input">').appendTo(container); 
					input.combogrid(options);
					return input;
				},
				destroy: function(target){
					$(target).combogrid('destroy');
				},
				getValue: function(target){
					return $(target).combogrid('getValue');
				},
				setValue: function(target, value){
					$(target).combogrid('setValue', value);
				},
				resize: function(target, width){
					$(target).combogrid('resize',width);
				}
			}
		});
		
		$('#dg').datagrid({singleSelect:(this.value==1)});
		
		$(function(){  
            $('#dg_mat').datagrid({             
				rowStyler:function(index,row){
					if (row.responsable=='S'){
						return 'background-color:whitesmoke;color:blue;font-weight:bold;';
					}
				}  
            });  
        });
		
		$('#grups').combogrid({
			url: './sortides/grup_getdata.php',
			onSelect: function(){
			}	
		});
		
        function doSearch(){				   
			$('#dg').datagrid('load',{  
				idgrups   : $('#grups').combogrid('getValue')
			});
		}
		
		function Professors(id_professor){  
				$('#dlg').dialog('open').dialog('setTitle','Professors sortida');
				$('#dg_mat').datagrid('load',{ 
     			});
        }
						
		function planificarSortida(afegir,profesor){ 
		  var data_anada   = $('#data_anada').datebox('getValue');
		  var hora_anada   = $('#hora_anada').val();
		  var data_tornada = $('#data_tornada').datebox('getValue');
		  var hora_tornada = $('#hora_tornada').val();
		  var idprofessors = profesor;
		  var lloc         = $('#lloc').val();
		  var descripcio   = $('#descripcio').val();
		  var tancada      = $("input[name='tancada']:checked").val();
		  
		  url = './sortides/plan_sortides_edita.php';
	   
		  $.messager.confirm('Confirmar','Planifiquem aquesta sortida?',function(r){  
             
		  if (r){
					    if ($(ff).form('validate')) {  
                        $.post(url,{
								data_anada:data_anada,
								hora_anada:hora_anada,
								data_tornada:data_tornada,
								hora_tornada:hora_tornada,
								idprofessors:idprofessors,
								lloc:lloc,
								descripcio:descripcio,
								tancada:tancada,
								afegir:afegir},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Introducci&oacute; de dades efectuada correctament!','info');
								$('#search_alum_button').linkbutton('enable');
								$('#add_alum_button').linkbutton('enable');
								$('#del_alum_button').linkbutton('enable');
								$('#info_alum_button').linkbutton('enable');
								$('#add_prof_button').linkbutton('enable');
								$('#grups').combo('enable');
								$('#add_button').linkbutton('disable');
                            } else { 
							    $.messager.alert('Error','Introducci&oacute; de dades efectuada erroniament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json');  
						}
                    }  
           });	
		}
		
		function onClickRow(index){
			if (editIndex != index){
				if (endEditing()){
					$('#dg_mat').datagrid('selectRow', index)
							    .datagrid('beginEdit', index);
					editIndex = index;
				} else {
					$('dg_mat').datagrid('selectRow', editIndex);
				}
			}
		}
		
		function endEditing(){
			if (editIndex == undefined){return true}			
			if ($('#dg_mat').datagrid('validateRow', editIndex)){
				var ed  = $('#dg_mat').datagrid('getEditor', {index:editIndex,field:'id_professorat'});
				var val = $(ed.target).combogrid('getValue');
				var professor = $(ed.target).combogrid('getText');
											
				$('#dg_mat').datagrid('getRows')[editIndex]['professor'] = professor;
				$('#dg_mat').datagrid('endEdit', editIndex);
				
				if (nou_registre) { 
					url = './sortides/plan_sortides_prof_nou.php';
					nou_registre = 0;
				}
				else {
					url = './sortides/plan_sortides_prof_edita.php?id='+$('#dg_mat').datagrid('getRows')[editIndex]['idprofessorat_sortides'];
				}
				afterEdit(url,
					$('#dg_mat').datagrid('getRows')[editIndex]['id_professorat'],
					$('#dg_mat').datagrid('getRows')[editIndex]['responsable']);
				
				editIndex = undefined;
				return true;
			} else {
				return false;
			}
		}
		
		function append(){
			if (endEditing()){
				$('#dg_mat').datagrid('appendRow',{});
				nou_registre = 1;
				
				editIndex = $('#dg_mat').datagrid('getRows').length-1;
				$('#dg_mat').datagrid('selectRow', editIndex)
						    .datagrid('beginEdit', editIndex);
			}
			
		}
		
		function accept(){
			if (endEditing()){
				$('#dg_mat').datagrid('acceptChanges');
				var row_a = $('#dg_mat').datagrid('getSelected');
										
				if (nou_registre) { 
					url = './sortides/plan_sortides_prof_nou.php';
					nou_registre = 0;
				}
				else {
					url = './sortides/plan_sortides_prof_edita.php?id='+row_a.idprofessorat_sortides;
				} 
				saveItem(url,row_a);
			}
		}
		
		function reject(){
			$('#dg_mat').datagrid('rejectChanges');
			editIndex = undefined;
			$('#dlg').dialog('close');
		}
		
		function destroyItem(){  
            var row_a = $('#dg_mat').datagrid('getSelected'); 
            if (row_a){  
                $.messager.confirm('Confirmar','Est&aacute;s seguro de que vols eliminar aquest professor?',function(r){  
                    if (r){  
                        $.post('./sortides/plan_sortides_prof_esborra.php',{id:row_a.id_professorat},function(result){  
                            if (result.success){  
                                $('#dg_mat').datagrid('reload');    
								//$('#dg').datagrid('reload');
								editIndex = undefined; 
                            } else {  
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
		
		function saveItem(url,row_a){ 			
	
			$.post(url,{id_professorat:row_a.id_professorat,responsable:row_a.responsable},function(result){  
            if (result.success){  
               //$('#dg_mat').datagrid('reload');     
			   //$('#dg').datagrid('reload'); 
            } else {  
               $.messager.show({   
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
		  
        }
		
		function afterEdit(url,field1,field2){
			$.post(url,{id_professorat:field1,responsable:field2},function(result){  
            if (result.success){  
               //$('#dg_mat').datagrid('reload');
			   //$('#dg').datagrid('reload');    
            } else {  
               $.messager.show({     
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
        }
		
		function veureAlumnes(){  
			$('#dlg_alum').dialog('open').dialog('setTitle','Alumnes sortida');
			$('#dg_alum').datagrid('load',{ });
        }
		
		function gestioAlumnes(afegir){ 
		  var rows_alum  = $('#dg').datagrid('getSelections');
		  
		  if (rows_alum){ 
			   var ss_alum = [];
			   for(var i=0; i<rows_alum.length; i++){
					var row = rows_alum[i];
					ss_alum.push(row.idalumnes);
			   }
			   url = './sortides/plan_sortides_alum_edita.php';
			   
			   $.messager.confirm('Confirmar','Actualitzem aquestes dades?',function(r){  
                    if (r){  
                        $.post(url,{
								idalumnes:ss_alum,
								afegir:afegir},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Actualitzaci&oacute; efectuada correctament!','info');
								//$('#dg').datagrid('reload');
								$('#dg_alum').datagrid('reload');
                            } else { 
							    $.messager.alert('Error','Actualitzaci&oacute; efectuada erroniament!','error');
								 
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
        form{
            margin:0;
            padding:0;
        }
        .dv-table td{
            border:0;
        }
        .dv-table input{
            border:1px solid #ccc;
        }
    </style>