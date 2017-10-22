<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   if (isset($_SESSION['sortida'])) {
   	unset($_SESSION['sortida']);
   }
   
   $strNoCache = "";
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:550px;">   
    <table id="dg" class="easyui-datagrid" title="Sortides enregistrades" style="height:548px;" 
	data-options="
		singleSelect: true,
                pagination: true,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './sortides/sortides_getdata.php',
		onClickRow: onClickRow
	">    
        <thead>  
            <tr>
                <th data-options="field:'data_inici',width:90,align:'left',editor:{type:'datebox',options:{formatter:myformatter,parser:myparser}}">Data anada</th>
                <th data-options="field:'hora_inici',width:75,align:'left',editor:{type:'validatebox',options:{precision:0}}">H. anada</th>
                <th data-options="field:'data_fi',width:90,align:'left',editor:{type:'datebox',options:{formatter:myformatter,parser:myparser}}">Data tornada</th>
                <th data-options="field:'hora_fi',width:75,align:'left',editor:{type:'validatebox',options:{precision:0}}">H. tornada</th>
                <th sortable="true" data-options="field:'lloc',width:150,align:'left',editor:{type:'validatebox',validType:'length[0,55]',required:true}">Lloc</th>
                <th sortable="true" data-options="field:'descripcio',width:450,editor:{type:'textarea',validType:'length[0,256]'}">Descripci&oacute;</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">  
        <a id="gestio_professors" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true,disabled:true"  plain="true" onclick="gestioProfessors()">Professors</a>&nbsp; 
        <a id="gestio_alumnes" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-tip',plain:true,disabled:true" onclick="gestioAlumnes()">Alumnes</a>
        &nbsp;&nbsp;
        <a id="accept_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true,disabled:true" onclick="accept()">Acceptar</a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a id="tancar_sortida" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-undo',plain:true,disabled:true" onclick="tancarSortida()">Tancar sortida</a>
    </div>
    </div>
    
    <div id="dlg_prof" class="easyui-dialog" style="width:725px;height:500px;padding:5px 5px" modal="true" closed="true">
        <table id="dg_prof" class="easyui-datagrid" title="Professors" style="width:700px;height:405px" 
                data-options="
                    iconCls: 'icon-edit',
                    singleSelect: true,
                    url:'./sortides/plan_sortides_getdetail.php',
                    pagination: false,
                    rownumbers: true, 
                    toolbar: '#tb_prof',
                    onClickRow: onClickRow_prof
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
    
    <div id="tb_prof" style="height:auto">
		<a id="btnAppend" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append_prof()">Nou</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="destroyItem_prof()">Esborrar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept_prof()">Acceptar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="tancar_prof()">Tancar</a>
    </div>
    
    <div id="dlg_alum" class="easyui-dialog" style="width:885px;height:550px;padding:5px 5px" modal="true" closed="true">
        <table id="dg_alum" class="easyui-datagrid" title="Alumnes" style="width:auto;height:auto"
                data-options="
                    iconCls: 'icon-edit',
                    singleSelect: true,
                    url:'./sortides/plan_sortides_getalumnes.php',
                    pagination: false,
                    rownumbers: true,
                    toolbar: '#tb_alum',
                    onClickRow: onClickRow_alum 
                ">
            <thead>
                <tr>
                    <th data-options="field:'grup',width:250">Grup</th>                         
                    <!--<th data-options="field:'alumne',width:450">Alumne</th>-->
                    <th data-options="field:'id_alumne',width:480,
                            formatter:function(value,row){
								return row.alumne;
							},
							editor:{
								type:'combogrid',
								options:{
									idField: 'id_alumne', 
                                    valueField:'id_alumne',
									textField:'alumne',
                                    mode:'remote',
									url:'./sortides/alum_getdata.php',
									required:false,
                                    columns:[[
                                        {field:'alumne',title:'alumne',width:420}
                                    ]]
								}
                            }">Alumne</th>
                    </tr>
            </thead>
        </table>
    </div>
    
    <div id="tb_alum" style="height:auto">
		<a id="btnAppend" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append_alum()">Nou</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="destroyItem_alum()">Esborrar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept_alum()">Acceptar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="tancar_alum()">Tancar</a>
    </div>
    
    <script type="text/javascript">  
        var url;
		var editIndex         = undefined;
		var editIndex_prof    = undefined;
		var editIndex_alum    = undefined;
		var nou_registre      = 0;
		var nou_registre_prof = 0;
		var nou_registre_alum = 0;
		
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
								
	$(function(){  
            $('#dg').datagrid({  
				rowStyler:function(index,row){
					if (row.tancada=='S'){
						return 'background-color:whitesmoke;color:#aaa;';
					}
				}   
            });  
        });
		
	$(function(){  
            $('#dg_prof').datagrid({             
				rowStyler:function(index,row){
					if (row.responsable=='S'){
						return 'background-color:whitesmoke;color:blue;font-weight:bold;';
					}
				}  
            });  
        });
		
	function gestioProfessors(){  
            var row = $('#dg').datagrid('getSelected');
			
            if (row){  		
				$('#dlg_prof').dialog('open').dialog('setTitle','Professors sortida');
				$('#dg_prof').datagrid('load',{ 
					idsortides: row.idsortides
     			});
            }  
        }
		
	function gestioAlumnes(){  
            var row = $('#dg').datagrid('getSelected');
			
            if (row){
				$('#dlg_alum').dialog('open').dialog('setTitle','Alumnes sortida');
				$('#dg_alum').datagrid('load',{ 
					sortida: row.idsortides
     			});
            }  
        }
		
		function tancarSortida(){ 
		  var row = $('#dg').datagrid('getSelected');
		  
		  if (row){ 
			   url = './sortides/sortides_tancar.php';
			   
			   $.messager.confirm('Confirmar','Tanquem aquesta sortida?',function(r){  
                    if (r){  
                        $.post(url,{
								idsortides:row.idsortides},function(result){  
                            if (result.success){  
                                   $.messager.alert('Informaci&oacute;','Sortida tancada correctament!','info');
								   $('#dg').datagrid('reload');
								   editIndex = undefined;
								   $('#gestio_professors').linkbutton('disable');
								   $('#gestio_alumnes').linkbutton('disable');
								   $('#tancar_sortida').linkbutton('disable');
								   $('#accept_button').linkbutton('disable');
                            } else { 
							    $.messager.alert('Error','Sortida tancada erroniament!','error');
								 
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
		
		function onClickRow(index){
			var row = $('#dg').datagrid('getSelected');
			
			if (row.tancada=='N') {
				if (editIndex != index){
					if (endEditing()){
						$('#dg').datagrid('selectRow', index)
								.datagrid('beginEdit', index);
						editIndex = index;
					} else {
						$('#dg').datagrid('selectRow', editIndex);
					}
				}
				$('#gestio_professors').linkbutton('enable');
				$('#gestio_alumnes').linkbutton('enable');
				$('#tancar_sortida').linkbutton('enable');
				$('#accept_button').linkbutton('enable');
			}
		}
		
		function onClickRow_prof(index){
			if (editIndex_prof != index){
					if (endEditing_prof()){
						$('#dg_prof').datagrid('selectRow', index)
								     .datagrid('beginEdit', index);
						editIndex_prof = index;
					} else {
						$('#dg_prof').datagrid('selectRow', editIndex_prof);
					}
			}
		}
		
		function onClickRow_alum(index){
			if (editIndex_alum != index){
					if (endEditing_alum()){
						$('#dg_alum').datagrid('selectRow', index)
								     .datagrid('beginEdit', index);
						editIndex_alum = index;
					} else {
						$('#dg_alum').datagrid('selectRow', editIndex_alum);
					}
			}
		}
	
		function endEditing(){
			if (editIndex == undefined){return true}
			if ($('#dg').datagrid('validateRow', editIndex)){
				$('#dg').datagrid('acceptChanges');
				$('#dg').datagrid('endEdit', editIndex);
				
				if (nou_registre) { 
					url = './sortides/sortides_nou.php';
					nou_registre = 0;
				}
				else {
					url = './sortides/sortides_edita.php?id='+$('#dg').datagrid('getRows')[editIndex]['idsortides'];
				}
				afterEdit(url,
						  $('#dg').datagrid('getRows')[editIndex]['data_inici'],
						  $('#dg').datagrid('getRows')[editIndex]['data_fi'],
						  $('#dg').datagrid('getRows')[editIndex]['hora_inici'],
						  $('#dg').datagrid('getRows')[editIndex]['hora_fi'],
						  $('#dg').datagrid('getRows')[editIndex]['lloc'],
						  $('#dg').datagrid('getRows')[editIndex]['descripcio']);
				
				editIndex = undefined;
				return true;
			} else {				
				return false;
			}
		}
		
		function endEditing_prof(){
			if (editIndex_prof == undefined){return true}			
			if ($('#dg_prof').datagrid('validateRow', editIndex_prof)){
				var row_p = $('#dg').datagrid('getSelected');
				var ed  = $('#dg_prof').datagrid('getEditor', {index:editIndex_prof,field:'id_professorat'});
				var val = $(ed.target).combogrid('getValue');
				var professor = $(ed.target).combogrid('getText');
											
				$('#dg_prof').datagrid('getRows')[editIndex_prof]['professor'] = professor;
				$('#dg_prof').datagrid('endEdit', editIndex_prof);
				
				if (nou_registre_prof) { 
					url = './sortides/plan_sortides_prof_nou.php';
					nou_registre_prof = 0;
				}
				else {
					url = './sortides/plan_sortides_prof_edita.php?id='+$('#dg_prof').datagrid('getRows')[editIndex_prof]['idprofessorat_sortides'];
				}
				afterEdit_prof(url,
					row_p.idsortides,
					$('#dg_prof').datagrid('getRows')[editIndex_prof]['id_professorat'],
					$('#dg_prof').datagrid('getRows')[editIndex_prof]['responsable']);
				
				editIndex_prof = undefined;
				return true;
			} else {
				return false;
			}
		}
		
		function endEditing_alum(){
			if (editIndex_alum == undefined){return true}			
			if ($('#dg_alum').datagrid('validateRow', editIndex_alum)){
				var row_p  = $('#dg').datagrid('getSelected');
				var ed     = $('#dg_alum').datagrid('getEditor', {index:editIndex_alum,field:'id_alumne'});
				var val    = $(ed.target).combogrid('getValue');
				var alumne = $(ed.target).combogrid('getText');
											
				$('#dg_alum').datagrid('getRows')[editIndex_alum]['alumne'] = alumne;
				$('#dg_alum').datagrid('endEdit', editIndex_alum);
				
				if (nou_registre_alum) { 
					url = './sortides/sortides_alum_nou.php';
					nou_registre_alum = 0;
				}
				else {
					url = './sortides/sortides_alum_edita.php?id='+$('#dg_alum').datagrid('getRows')[editIndex_alum]['idsortides_alumne'];
				}
				afterEdit_alum(url,
					row_p.idsortides,
					$('#dg_alum').datagrid('getRows')[editIndex_alum]['id_alumne']);
				
				editIndex_alum = undefined;
				return true;
			} else {
				return false;
			}
		}
		
		function append_prof(){
			if (endEditing_prof()){
				$('#dg_prof').datagrid('appendRow',{});
				nou_registre_prof = 1;
				
				editIndex_prof = $('#dg_prof').datagrid('getRows').length-1;
				$('#dg_prof').datagrid('selectRow', editIndex_prof)
						     .datagrid('beginEdit', editIndex_prof);
			}
			
		}
		
		function append_alum(){
			if (endEditing_alum()){
				$('#dg_alum').datagrid('appendRow',{});
				nou_registre_alum = 1;
				
				editIndex_alum = $('#dg_alum').datagrid('getRows').length-1;
				$('#dg_alum').datagrid('selectRow', editIndex_alum)
						     .datagrid('beginEdit', editIndex_alum);
			}
			
		}
		
		function accept(){
			if (endEditing()){
				$('#dg').datagrid('acceptChanges');
				var row_a = $('#dg').datagrid('getSelected');
										
				if (nou_registre) { 
					url = './sortides/sortides_nou.php';
					nou_registre = 0;
				}
				else {
					url = './sortides/sortides_edita.php?id='+row_a.idsortides;
				} 
				saveItem(url,row_a);
			}
		}
		
		function accept_prof(){
			if (endEditing_prof()){
				$('#dg_prof').datagrid('acceptChanges');
				var row_a = $('#dg_prof').datagrid('getSelected');
				var row_p = $('#dg').datagrid('getSelected');
										
				if (nou_registre_prof) { 
					url = './sortides/plan_sortides_prof_nou.php';
					nou_registre_prof = 0;
				}
				else {
					url = './sortides/plan_sortides_prof_edita.php?id='+row_a.idprofessorat_sortides;
				} 
				saveItem_prof(url,row_a,row_p);
			}
		}
		
		function accept_alum(){
			if (endEditing_alum()){
				$('#dg_alum').datagrid('acceptChanges');
				var row_a = $('#dg_alum').datagrid('getSelected');
				var row_p = $('#dg').datagrid('getSelected');
										
				if (nou_registre_alum) { 
					url = './sortides/sortides_alum_nou.php';
					nou_registre_alum = 0;
				}
				else {
					url = './sortides/sortides_alum_edita.php?id='+row_a.idsortides_alumne;
				} 
				saveItem_alum(url,row_a,row_p);
			}
		}
		
		function afterEdit(url,field1,field2,field3,field4,field5,field6){
			$.post(url,{data_inici:field1,data_fi:field2,hora_inici:field3,hora_fi:field4,lloc:field5,descripcio:field6},function(result){  
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
		
		function afterEdit_prof(url,field1,field2,field3){
			$.post(url,{sortida:field1,id_professorat:field2,responsable:field3},function(result){  
            if (result.success){  
               /*$('#dg_prof').datagrid('reload');
			   editIndex_prof = undefined;*/   
            } else {  
               $.messager.show({     
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
        }
		
		function afterEdit_alum(url,field1,field2){
			$.post(url,{sortida:field1,id_alumne:field2},function(result){  
            if (result.success){  
               $('#dg_alum').datagrid('reload');
			   editIndex_alum = undefined;
            } else {  
               $.messager.show({     
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
        }
		
		function saveItem(url,row_a){ 			
	
			$.post(url,{data_inici:row_a.data_inici,
						data_fi:row_a.data_fi,
						hora_inici:row_a.hora_inici,
						hora_fi:row_a.hora_fi,
						lloc:row_a.lloc,
						descripcio:row_a.descripcio},function(result){  
            if (result.success){  
               $('#dg').datagrid('reload');
			   editIndex = undefined;
			   $('#gestio_professors').linkbutton('disable');
			   $('#gestio_alumnes').linkbutton('disable');
			   $('#tancar_sortida').linkbutton('disable');
			   $('#accept_button').linkbutton('disable');
            } else {  
               $.messager.show({   
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
		  
        }
		
		function saveItem_prof(url,row_a,row_p){ 			
	
			$.post(url,{sortida:row_p.idsortides,id_professorat:row_a.id_professorat,responsable:row_a.responsable},function(result){  
            if (result.success){  
               $('#dg_prof').datagrid('reload');
			   editIndex_prof = undefined;
            } else {  
               $.messager.show({   
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
		  
        }
		
		function saveItem_alum(url,row_a,row_p){ 			
	
			$.post(url,{sortida:row_p.idsortides,id_alumne:row_a.id_alumne},function(result){  
            if (result.success){  
               $('#dg_alum').datagrid('reload');
			   editIndex_alum = undefined;
            } else {  
               $.messager.show({   
               title: 'Error',  
               msg: result.msg  
               });  
               }  
             },'json');
		  
        }
		
		function destroyItem_prof(){  
            var row_p = $('#dg').datagrid('getSelected');
			var row_a = $('#dg_prof').datagrid('getSelected'); 
            if (row_a){  
                $.messager.confirm('Confirmar','Est&aacute;s seguro de que vols eliminar aquest professor de la sortida?',function(r){  
                    if (r){  
                        $.post('./sortides/plan_sortides_prof_esborra.php',{id:row_a.id_professorat,sortida:row_p.idsortides},function(result){  
                            if (result.success){  
                                $('#dg_prof').datagrid('reload');    
								//$('#dg').datagrid('reload');
								editIndex_prof = undefined; 
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
		
		function destroyItem_alum(){  
            var row_p = $('#dg').datagrid('getSelected');
			var row_a = $('#dg_alum').datagrid('getSelected'); 
            if (row_a){  
                $.messager.confirm('Confirmar','Est&aacute;s seguro de que vols eliminar aquest alumne de la sortida?',function(r){  
                    if (r){  
                        $.post('./sortides/sortides_alum_esborra.php',{id:row_a.id_alumne,sortida:row_p.idsortides},function(result){  
                            if (result.success){  
                                $('#dg_alum').datagrid('reload');    
								//$('#dg').datagrid('reload');
								editIndex_alum = undefined; 
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
		
		function reject(){
		    $('#dg').datagrid('rejectChanges');
			editIndex = undefined;
			$('#dlg_fh').dialog('close');
		}
		
		function tancar_prof(){
		    //$('#dg').datagrid('reload');
			$('#dg_prof').datagrid('rejectChanges');
			editIndex_prof = undefined;
			editIndex      = undefined;
			$('#dlg_prof').dialog('close');
		}
		
		function tancar_alum(){
		    //$('#dg').datagrid('reload');
			$('#dg_alum').datagrid('rejectChanges');
			editIndex_alum = undefined;
			editIndex      = undefined;
			$('#dlg_alum').dialog('close');
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