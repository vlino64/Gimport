<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
?>
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">
    <table id="dg" class="easyui-datagrid" title="CCC Tipus" style="height:540px"
			data-options="
				singleSelect: true,
                pagination: true,
                rownumbers: true,
				toolbar: '#toolbar',
                onClickRow: onClickRow,
                onAfterEdit: onAfterEdit 
			">
        <thead>  
            <tr>  
                        
                <th data-options="field:'nom_falta',width:180,editor:{type:'validatebox',options:{required:true}}
                ">Nom</th>
                
                <th data-options="field:'valor',width:60,align:'left',editor:{type:'numberbox',options:{precision:0}}">
                Valor</th>
                
                <th data-options="field:'limit_acumulacio_comunicacio',width:80,align:'left',editor:{type:'numberbox',options:{precision:0}}">
                L&iacute;mit<br /> acumulaci&oacute;</th>
                
                <th data-options="field:'comentari',width:350,editor:{type:'textarea',validType:'length[0,160]'}">Comentari</th>
               
            </tr>  
        </thead>  
    </table>
  
    <div id="toolbar">  
    <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="append_fh()">Nou</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="javascript:destroyItem()">Esborrar</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')">Guardar</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')">Cancel.lar</a>
        
    <a id="carrecs_button" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" disabled="true" onclick="gestionCarrecs()">Comunicaci&oacute; c&agrave;rrecs</a>  
    </div>   
    </div>
    
    <div id="tb" style="height:auto">
		<a id="btnAppend" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="append()">Nou</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="destroyItemCarrec()">Esborrar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept()">Acceptar</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="reject()">Tancar</a>
    </div>
    
    <div id="dlg" class="easyui-dialog" style="width:450px;height:350px;padding:5px 5px" modal="true" closed="true" buttons="#dlg-buttons">
        <table id="dg_mat" class="easyui-datagrid" title="Comunicaci&oacute; c&agrave;rrecs" style="width:425px;height:305px"
                data-options="
                    singleSelect: true,
                    url:'./ccc_tipus/ccc_tipus_getdetail.php',
                    pagination: false,
                    rownumbers: true,
                    toolbar: '#tb',
                    onClickRow: onClickRow_mat
                ">
            <thead>
                <tr>
                    <!--<th data-options="field:'id_dies_franges',width:50">ID</th>-->
                    <th data-options="field:'id_carrec',width:250,
                            formatter:function(value,row){
								return row.nom_carrec;
							},
							editor:{
								type:'combobox',
								options:{
                                    valueField:'idcarrecs',
									textField:'nom_carrec',
									url:'./ccc_tipus/carrecs_getdata.php',
									required:false
								}
                            }">C&agrave;rrec</th>
                </tr>
            </thead>
        </table>
    </div> 
      
    <script type="text/javascript">  
	var editIndex = undefined;
	var url;
	var nou_registre = 0;
		
	$(function(){  
            $('#dg').edatagrid({  
                url: './ccc_tipus/ccc_tipus_getdata.php',  
                saveUrl: './ccc_tipus/ccc_tipus_nou.php',  
                updateUrl: './ccc_tipus/ccc_tipus_edita.php',  
                destroyUrl: './ccc_tipus/ccc_tipus_esborra.php'  
            });  
		
        });
				 		
		$('#dg').datagrid({  
			view: detailview,  
			detailFormatter:function(index,row){  
				return '<div style="padding:2px"><table id="ddv-' + index + '"></table></div>';  
			},  
			onExpandRow: function(index,row){  
				$('#ddv-'+index).datagrid({  
					url:'./ccc_tipus/ccc_tipus_getdetail.php?id='+row.idccc_tipus,  
					fitColumns:false,  
					rownumbers:true,  
					loadMsg:'c&agrave;rrecs ...',  
					height:'auto',
					columns:[[  
						{field:'nom_carrec',title:'C&agrave;rrec',width:400},  
					]],
					onResize:function(){  
						$('#dg').datagrid('fixDetailRowHeight',index);  
					},  
					onLoadSuccess:function(){  
						setTimeout(function(){  
							$('#dg').datagrid('fixDetailRowHeight',index);  
						},0);  
					}  
				});  
				$('#dg').datagrid('fixDetailRowHeight',index);  
			}  
		});
   				
		function gestionCarrecs(){  
                    var row = $('#dg').datagrid('getSelected');
			
                    if (row){  
                                        $('#dlg').dialog('open').dialog('setTitle',row.nom_falta);
                                        $('#dg_mat').datagrid('load',{ 
                                                id: row.idccc_tipus  
                                });
                    }  
                }		
    	
		function onAfterEdit(rowIndex, rowData, changes){
			$('#dg').datagrid('reload');
			$('#carrecs_button').linkbutton('disable');
		}
		
		function onClickRow(index){
			$('#carrecs_button').linkbutton('enable');
		}
		
		function onClickRow_mat(index){
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
			    var row_p = $('#dg').datagrid('getSelected');
				var ed = $('#dg_mat').datagrid('getEditor', {index:editIndex,field:'id_carrec'});
				var nom_carrec = $(ed.target).combobox('getText');				
				$('#dg_mat').datagrid('getRows')[editIndex]['nom_carrec'] = nom_carrec;
				$('#dg_mat').datagrid('endEdit', editIndex);
				
				if (nou_registre) { 
					url = './ccc_tipus/ccc_carrec_nou.php';
					nou_registre = 0;
				}
				else {
					url = './ccc_tipus/ccc_carrec_edita.php?id='+$('#dg_mat').datagrid('getRows')[editIndex]['idccc_tipus_comunicacio_carrec'];
				}
				
				afterEdit(url,
				  row_p.idccc_tipus,
				  $('#dg_mat').datagrid('getRows')[editIndex]['id_carrec']);
				
				editIndex = undefined;
				return true;
			} else {
				return false;
			}
		}
		
		function append_fh(){
			javascript:$('#dg').edatagrid('addRow');
			$('#carrecs_button').linkbutton('disable');
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
				var row_p = $('#dg').datagrid('getSelected');
				var row_a = $('#dg_mat').datagrid('getSelected');
										
				if (nou_registre) { 
					url = './ccc_tipus/ccc_carrec_nou.php';
					nou_registre = 0;
				}
				else {
					url = './ccc_tipus/ccc_carrec_edita.php?id='+row_a.idccc_tipus_comunicacio_carrec;
				}
				saveItem(url,row_a,row_p);
			}
		}
		
		function reject(){
			$('#dg_mat').datagrid('rejectChanges');
			editIndex = undefined;
			$('#dlg').dialog('close');
		}
		
	function destroyItem(){  
            var row = $('#dg').datagrid('getSelected'); 
            if (row){  
                $.messager.confirm('Confirmar','Est&aacute;s segur de que vols esborrar aquesta falta?',function(r){  
                    if (r){  
                        $.post('./ccc_tipus/ccc_tipus_esborra.php',{id:row.idccc_tipus},function(result){  
                            if (result.success){  
                                $('#dg').datagrid('reload');   
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
		
	function destroyItemCarrec(){  
            var row = $('#dg_mat').datagrid('getSelected'); 
            if (row){  
                $.messager.confirm('Confirmar','Est&aacute;s segur de que vols esborrar aquest c&agrave;rrec?',function(r){  
                    if (r){  
                        $.post('./ccc_tipus/ccc_carrec_esborra.php',{id:row.idccc_tipus_comunicacio_carrec},function(result){  
                            if (result.success){  
                                $('#dg_mat').datagrid('reload'); 
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
		
	function saveItem(url,row_a,row_p){ 			
	
            $.post(url,{id_tipus:row_p.idccc_tipus,id_carrec:row_a.id_carrec},function(result){  
            if (result.success){
               editIndex = undefined; 
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
	
            $.post(url,{id_tipus:field1,id_carrec:field2},function(result){  
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
	
	</script>