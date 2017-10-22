<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
?>

    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">
    <table id="dg" class="easyui-datagrid" title="CCC Mesures" style="height:540px"
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
                <th data-options="field:'ccc_nom',width:400,editor:{type:'validatebox',options:{required:true}}
                ">Nom</th>
            </tr>  
        </thead>  
    </table>
  
    <div id="toolbar">  
    <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="append_fh()">Nou</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="javascript:destroyItem()">Esborrar</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')">Guardar</a>  
    <a href="#" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')">Cancel.lar</a>
    </div>   
    </div>
    
    <script type="text/javascript">  
		var editIndex = undefined;
		var url;
		var nou_registre = 0;
		
		$(function(){  
            $('#dg').edatagrid({  
                url: './ccc_mesures/ccc_mesures_getdata.php',  
                saveUrl: './ccc_mesures/ccc_mesures_nou.php',  
                updateUrl: './ccc_mesures/ccc_mesures_edita.php',  
                destroyUrl: './ccc_mesures/ccc_mesures_esborra.php'  
            });  
		
        });
				 		    	
		function onAfterEdit(rowIndex, rowData, changes){
			$('#dg').datagrid('reload');
		}
		
		function onClickRow(index){
		}
				
		function append(){
			if (endEditing()){
				$('#dg').datagrid('appendRow',{});
				nou_registre = 1;
				editIndex = $('#dg').datagrid('getRows').length-1;
				$('#dg').datagrid('selectRow', editIndex)
						.datagrid('beginEdit', editIndex);
			}
			
		}
		
		function append_fh(){
			javascript:$('#dg').edatagrid('addRow');
			$('#carrecs_button').linkbutton('disable');
		}
		
		function accept(){		
			if (endEditing()){				
				$('#dg').datagrid('acceptChanges');
				var row_p = $('#dg').datagrid('getSelected');
										
				if (nou_registre) { 
					url = './ccc_mesures/ccc_mesures_nou.php';
					nou_registre = 0;
				}
				else {
					url = './ccc_mesures/ccc_mesures_edita.php?id='+row_p.idccc_tipus_mesura;
				}
				saveItem(url,row_p);
			}
		}
		
		function reject(){
			$('#dg').datagrid('rejectChanges');
			editIndex = undefined;
			$('#dlg').dialog('close');
		}
				
		function destroyItem(){  
            var row = $('#dg').datagrid('getSelected'); 
            if (row){  
                $.messager.confirm('Confirmar','Est&aacute;s segur de que vols esborrar aquesta mesura?',function(r){  
                    if (r){  
                        $.post('./ccc_mesures/ccc_mesures_esborra.php',{id:row.idccc_tipus_mesura},function(result){  
                            if (result.success){  
                                $('#dg').datagrid('reload');  
								editIndex = undefined;
                            } else {  
                                $.messager.show({ 
                                    title: 'Error',  
                                    msg: result.errorMsg  
                                });  
                            }  
                        },'json');  
                    }  
                });  
            }  
        }
		
		function saveItem(url,row_a,row_p){ 			
	
			$.post(url,{idfranges_horaries:row_p.idfranges_horaries,iddies_setmana:row_a.iddies_setmana},function(result){  
            if (result.success){  
               //$('#dg_mat').datagrid('reload');    
			   //$('#dg').datagrid('reload'); 
            } else {  
               $.messager.show({   
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
             },'json');
		  
        }
		
		function afterEdit(url,field1,field2){		
	
			$.post(url,{idfranges_horaries:field1,iddies_setmana:field2},function(result){  
            if (result.success){  
               //$('#dg_mat').datagrid('reload');
			   //$('#dg').datagrid('reload');    
            } else {  
               $.messager.show({     
               title: 'Error',  
               msg: result.errorMsg  
               });  
               }  
             },'json');
		  
        }
	
	</script>