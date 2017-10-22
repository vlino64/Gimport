<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $idprofessors  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0;
   $strNoCache    = "";
?> 
	
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:550px;">
    <table id="dg" class="easyui-datagrid" title="Mat&egrave;ries amb automatr&iacute;cula" style="height:548px;"
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './mat_automat/mat_automat_getdata.php',
		onClickRow: onClickRow
	">
	<thead>
            <tr>
                <th field="materia" width="400" sortable="true">Mat&egrave;ria</th>
                <th field="grup" width="250" sortable="true">Grup</th>
                <th data-options="field:'automatricula',width:100,align:'center',
                formatter:function(value,row){                
                            return row.automatricula;
                       }, 
                editor:{type:'checkbox',options:{on:'S',off:''}}
                ">Automatr&iacute;cula</th>
                <th data-options="field:'contrasenya',width:180,align:'left',editor:{type:'validatebox',options:{required:false}}">Contrasenya<br />automatr&iacute;cula</th>
            </tr>  
        </thead>  
    </table>  
    
    <div id="toolbar" style="padding:5px;height:auto">  
        <a id="horari_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-tip',plain:true" onclick="verHorario()">Veure horari grup</a>
        &nbsp;&nbsp;
        <a id="add_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-save',plain:true" onclick="accept()">Acceptar canvis</a>
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
                <a href="#" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="javascript:imprimirHorario()">Imprimir</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_hor').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
      
    <script type="text/javascript">  
        var url;
	var editIndex = undefined;
	var url;
   	var nou_registre = 0;
		
	$(function(){  
            $('#dg').datagrid({             
		rowStyler:function(index,row){
                	if (row.automatricula=='S'){
                        	return 'background-color:whitesmoke;color:navy;font-weight:bold;';
			}
		}  
            });  
        });
				
	function verHorario(){  
            var row     = $('#dg').datagrid('getSelected');
            var idgrups = row.idgrups;
			
            url = './hor/hor_see.php?idgrups='+idgrups;
			
            $('#dlg_hor').dialog('open').dialog('setTitle','Horari');
            $('#dlg_hor').dialog('refresh', url);
        }
		
	function imprimirHorario(){
            var row     = $('#dg').datagrid('getSelected');
            var idgrups = row.idgrups;
			
	    url = './hor/hor_print.php?idgrups='+idgrups+'&curs=<?=$_SESSION['curs_escolar']?>&cursliteral=<?=$_SESSION['curs_escolar_literal']?>';
	    $('#fitxer_pdf').attr('src', url);
        }
				
	function onClickRow(index){
			if (editIndex != index){
				if (endEditing()){
					$('#dg').datagrid('selectRow', index)
							.datagrid('beginEdit', index);
					editIndex = index;
				} else {
					$('#dg').datagrid('selectRow', editIndex);
				}
			}
	}
		
	function endEditing(){
			if (editIndex == undefined){return true}			
			if ($('#dg').datagrid('validateRow', editIndex)){
				$('#dg').datagrid('endEdit', editIndex);
				id      = $('#dg').datagrid('getRows')[editIndex]['id_mat_uf_pla'];
				idgrups = $('#dg').datagrid('getRows')[editIndex]['idgrups'];
				url     = './mat_automat/mat_automat_edita.php?id='+id+'&idgrups='+idgrups;
				
				afterEdit(url,
						  $('#dg').datagrid('getRows')[editIndex]['automatricula'],
						  $('#dg').datagrid('getRows')[editIndex]['contrasenya']);
				
				editIndex = undefined;
				return true;
			} else {
				return false;
			}
	}
		
		
	function accept(){			
			if (endEditing()){
				$('#dg_mat').datagrid('acceptChanges');
				var row_p = $('#dg').datagrid('getSelected');
										
				if (nou_registre) { 
					url = './mat_automat/mat_automat_nou.php';
					nou_registre = 0;
				}
				else {
					url = './mat_automat/mat_automat_edita.php?id='+row_p.id_mat_uf_pla;
				}

				saveItem(url,row_p);
			}
	}
				
	function saveItem(url,row_p){ 			
	
			$.post(url,{automatricula:row_p.automatricula,contrasenya:row_p.contrasenya},function(result){  
                        if (result.success){  
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
	
            $.post(url,{automatricula:field1,contrasenya:field2},function(result){  
            if (result.success){  
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
            margin-bottom:1px;  
        }  
        .fitem label{  
            display: inline-table;
            width:120px;  
        }  
    </style>