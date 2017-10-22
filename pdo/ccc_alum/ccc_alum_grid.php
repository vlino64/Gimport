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
   
   $idprofessors  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0;
   $fechaSegundos = time();
   $strNoCache    = "?nocache=$fechaSegundos";
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">    
    <table id="dg" class="easyui-datagrid" title="CCC per aprovar" style="width:auto;height:550px"
			data-options="
				singleSelect: true,
                pagination: true,
                rownumbers: true,
				toolbar: '#toolbar',
				url: './ccc_alum/ccc_alum_getdata.php',
				onClickRow: onClickRow
			">    
        <thead>  
            <tr>
                <th data-options="field:'data_ccc',width:90,align:'left',editor:{options:{formatter:myformatter,parser:myparser}}">Data</th>
                <th field="alumne" width="810" sortable="true">Alumne</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto"> 
        Desde: <input id="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        fins a: <input id="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a> 
        &nbsp;&nbsp;
        <!--<a id="esborrar_ccc" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-cancel',plain:true,disabled:true" onclick="esborrarCCC()">Esborrar CCC</a>-->
        <a id="gestio_sancio" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true,disabled:true"  plain="true" onclick="gestioCCC()">Aprovar CCC</a> 
    </div>
    </div>
    
	<div id="dlg_sancio" class="easyui-dialog" style=" padding-left:5px; padding-top:5px; width:800px;height:500px;"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_sancio-buttons">
        	<form id="fm_ccc" method="post" novalidate>
            <div>            
                <label style="width:150px; color:#666666">Data incid&egrave;ncia</label>
            	<input id="data_incident" name="data_incident" class="easyui-datebox" data-options="required: true,formatter:myformatter,parser:myparser">
                <br /><br />

                <label style="width:150px; color:#666666">Fets produ&iuml;ts segons l'alumne</label>  
                <textarea readonly name="descripcio_detallada" style="height:155px; width:770px;"></textarea>
                <br /><br />

                <label style="width:150px; color:#666666">Fets produ&iuml;ts segons el professor</label>  
                <textarea name="descripcio_detallada_prof" style="height:155px; width:770px;"></textarea>
            </div>            
        	</form>
    </div>
        
    <div id="dlg_sancio-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="aprovarCCC()">Acceptar</a>
        			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_sancio').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div>

    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <script type="text/javascript">  
        var url;
		var editIndex    = undefined;
		var nou_registre = 0;
		var today        = new Date();
		var data_inici_sancio;
		var data_fi_sancio;
		var idprofessors = <?= $idprofessors?>;
		
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
				view: detailview,
				detailFormatter:function(index,row){
					return '<div class="ddv" style="padding:5px 0"></div>';
				},
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
					ddv.panel({
						border:false,
						cache:false,
						href:'./ccc_alum/ccc_alum_getdetail.php?id='+row.idccc_alumne_principal,
						onLoad:function(){
							$('#dg').datagrid('fixDetailRowHeight',index);
						}
					});
					$('#dg').datagrid('fixDetailRowHeight',index);
				}
            });  
        });
		
		function doSearch(){ 
			$('#dg').datagrid('load',{  
				data_inici: $('#data_inici').datebox('getValue'),
				data_fi   : $('#data_fi').datebox('getValue')  
			});  
		} 
		
		function doReload(){
			d_inici       = $('#data_inici_informe').datebox('getValue');
			d_fi          = $('#data_fi_informe').datebox('getValue');
			criteri       = $('#criteri').combobox('getValue');			
			valor_criteri = $('#valor_criteri').combobox('getValue');
			
			url = './ccc_adm/ccc_adm_see.php?criteri='+criteri+'&valor_criteri='+valor_criteri+'&data_inici='+d_inici+'&data_fi='+d_fi;
			$('#dlg_inf').dialog('refresh', url);
		} 
		
		
		function gestioCCC(){  
            var row = $('#dg').datagrid('getSelected');
            if (row){
                $('#dlg_sancio').dialog('open').dialog('setTitle','Aprovar CCC');
				$('#fm_ccc').form('load','./ccc_alum/ccc_alum_load.php?id='+row.idccc_alumne_principal);				
				
				url = './ccc_alum/ccc_alum_aprova.php?id='+row.idccc_alumne_principal;
            }
        }

        function aprovarCCC(){		
			$('#fm_ccc').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
					var result = eval('('+result+')');
                    if (result.errorMsg){
                        $.messager.show({
                            title: 'Error',
                            msg: result.msg
                        });
                    } else {
                        $('#dlg_sancio').dialog('close');     
                        $('#dg').datagrid('reload'); 
						editIndex = undefined;
                    }
                }
            });
        }
		
		function esborrarCCC(){ 
		  var row = $('#dg').datagrid('getSelected');
		  
		  if (row){ 
			   url = './ccc_adm/ccc_alum_esborra.php';
			   
			   $.messager.confirm('Confirmar','Esborrem aquesta CCC?',function(r){  
                    if (r){  
                        $.post(url,{
								id:row.idccc_alumne_principal},function(result){  
                            if (result.success){  
                                   $.messager.alert('Informaci&oacute;','CCC esborrada correctament!','info');
								   $('#dg').datagrid('reload');
								   editIndex = undefined;
                            } else { 
							    $.messager.alert('Error','CCC esborrada err&ograve;niament!','error');
								 
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
				
				if (editIndex != index){
					if (endEditing()){
						$('#dg').datagrid('selectRow', index)
								.datagrid('beginEdit', index);
						editIndex = index;
					} else {
						$('#dg').datagrid('selectRow', editIndex);
					}
				}
				$('#gestio_sancio').linkbutton('enable');
				$('#imprimir_ccc').linkbutton('enable');
				
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
				
		function reject(){
		    $('#dg').datagrid('rejectChanges');
			editIndex = undefined;
			$('#dlg_fh').dialog('close');
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