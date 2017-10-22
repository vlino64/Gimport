<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $fechaSegundos = time();
   $strNoCache    = "";
   $id_professor  = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">   
    <table id="dg" class="easyui-datagrid" title="Comunicaci&oacute; abs&egrave;ncia professorat" style="height:540px;"
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './abs_prof_adm/com_abs_prof_adm_getdata.php',
                onClickRow: onClickRow
	">    
        <thead>  
            <tr>
                <th data-options="field:'ck',checkbox:true"></th>
                <th field="hora" width="90" sortable="true" align="center">Hora</th> 
                <th field="grup" width="190" sortable="true">Grup</th>
                <th field="materia" width="280" sortable="true">Mat&egrave;ria</th>
                <!--<th field="espaicentre" width="120" sortable="true">Espai</th>-->
                <th data-options="field:'comentari_tasca',width:340,align:'left',editor:{type:'textarea',validType:'length[0,256]'}">Detall tasca</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">  
        Dia&nbsp;<input id="data" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        &nbsp;
        <!--Motiu:&nbsp;
        <select id="motiu_combo" class="easyui-combobox" data-options="
					width:300,
                    url:'./abs_prof/tfp_getdata.php',
					idField:'idtipus_falta_professor',
                    valueField:'idtipus_falta_professor',
					textField:'tipus_falta',
					panelHeight:'auto'
		">
        </select>-->
        <br />Professor&nbsp;
        <select id="professors" class="easyui-combogrid" style="width:430px" data-options="
            panelWidth: 430,
            idField: 'id_professor',
            textField: 'Valor',
            url: url,
            method: 'get',
            columns: [[
                {field:'Valor',title:'Professor',width:430}
            ]],
            fitColumns: true
        ">
        </select>
        <a id="horari_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true" onclick="verHorario(<?= $id_professor ?>)"><img src="./images/schedule_icon.png" height="16" align="absbottom" />&nbsp;Horari professor</a>
        <br />Comentari&nbsp;<input id="comentari" name="comentari" class="easyui-validatebox" type="text" size="70" data-options="type:'textarea',validType:'length[0,200]'">
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" />       
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()">Detallar hores d'abs&egrave;ncia</a>
        <a id="add_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:false" onclick="comunicacioAbsencia(1,<?=TIPUS_FALTA_PROFESSOR_ABSENCIA?>)">Comunicar abs&egrave;ncia</a>
        <br />Fitxers a pujar: <strong>m&agrave;xim 2 Mb</strong>.<br />Formats permesos: <strong>gif,jpeg,jpg,png,doc,docx,xls,xlsx,ppt,pptx,pdf,rar,zip,odt,odp,ods,odg</strong>.
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
    
    <script type="text/javascript">  
        var url;
		var editIndex = undefined;
		var nou_registre = 0;
		var idgrups;
		var nom_grup;
		var data;
		var theDate;
		var theDay;
		
		$('#dg').datagrid({singleSelect:(this.value==1)})
		
		$('#data').datebox({
			onSelect: function(date){
				theDate = new Date(date);
				theDay  = theDate.getDay();
			}
		});
		
		$('#professors').combogrid({
			url: './prmod/prof_getdata.php',
			onSelect: function(){
				$('#horari_button').linkbutton('enable');
				$('#ufs_button').linkbutton('enable');
			}
		});
		
		$(function(){  
            $('#dg').datagrid({  
				view: detailview,  
                detailFormatter:function(index,row){
					return '<div class="ddv"></div>';
				},
				onExpandRow: function(index,row){
				    data     = $('#data').datebox('getValue');
					var ddv  = $(this).datagrid('getRowDetail',index).find('div.ddv');
                    ddv.panel({
                        border:false,
                        cache:true,
                        href:'./abs_prof/upload_task_form.php?data='+data+'&index='+index+'&id_professor='+row.idprofessors+'&idfranges_horaries='+row.idfranges_horaries+'&idgrups='+row.idgrups,
                        onLoad:function(){
                            $('#dg').datagrid('fixDetailRowHeight',index);
                            $('#dg').datagrid('selectRow',index);
                            $('#dg').datagrid('getRowDetail',index).find('form').form('load',row);
                        }
                    });
                    $('#dg').datagrid('fixDetailRowHeight',index);
				},
				rowStyler:function(index,row){
				}  
            });  
        });
		
		function endEditing(){
			if (editIndex == undefined){return true}
			if ($('#dg').datagrid('validateRow', editIndex)){		
				$('#dg').datagrid('endEdit', editIndex);
				$('#dg').datagrid('acceptChanges');
						
				editIndex = undefined;
				return true;
			} else {				
				return false;
			}
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
		
        function doSearch(){				   
			$('#dg').datagrid('load',{  
				dia : theDay,
				idprofessors : $('#professors').combogrid('getValue')
			});
			$('#add_button').linkbutton('enable');  
		}
		
		function verHorario(id_professor){
			var idprofessors = $('#professors').combogrid('getValue');
			
			url = './prmat/prmat_see.php?idprofessors='+idprofessors;
			$('#dlg_hor').dialog('open').dialog('setTitle','Horari');
			$('#dlg_hor').dialog('refresh', url);
			
        }
		
	function myformatter(date){  
            var y = date.getFullYear();  
            var m = date.getMonth()+1;  
            var d = date.getDate();  
            return (d<10?('0'+d):d)+'-'+(m<10?('0'+m):m)+'-'+y;
        }
		
		function saveItem(index){
			var row = $('#dg').datagrid('getRows')[index];
			data    = $('#data').datebox('getValue');
			url     = './abs_prof/upload_task.php?data='+data+'&id_professor='+row.idprofessors+'&idfranges_horaries='+row.idfranges_horaries;
			
			$('#dg').datagrid('getRowDetail',index).find('form').form('submit',{
				url: url,
				onSubmit: function(){
					return $(this).form('validate');
				},
				success: function(data){
					var win = $.messager.progress({
						title:'Sisplau esperi un moment',
						msg:'Pujant fitxer ...'
					});
					setTimeout(function(){
						$.messager.progress('close');
					},3000);
					
					$('#resultPhotoDiv'+index).html(data);
					//data = eval('('+data+')');
					//$('#dg').datagrid('reload');
				}
			});
		}
		
		function comunicacioAbsencia(afegir,tipus_incident){ 
		  var rows         = $('#dg').datagrid('getSelections'); 
		  var data         = $('#data').datebox('getValue');
		  var idprofessors = $('#professors').combogrid('getValue');
		  var motiu        = tipus_incident;
		  //$('#motiu_combo').combobox('getValue');
		  var comentari    = $('#comentari').val();
		  
		  endEditing();
		  
		  if (rows){ 
			   var ss_fh   = [];
			   var ss_grup = [];
			   var ss_mat  = [];
			   var ss_com  = [];
			   for(var i=0; i<rows.length; i++){
					var row = rows[i];
					ss_fh.push(row.idfranges_horaries);
					ss_grup.push(row.idgrups);
					ss_mat.push(row.idmateria);
					ss_com.push(row.comentari_tasca);
			   }
			   
			   url = './abs_prof_adm/com_abs_prof_adm_edita.php';
			   
			   $.messager.confirm('Confirmar','Recorda seleccionar les franges hor&agrave;ries de abs&egrave;ncia. Comuniquem aquesta abs&egrave;ncia?',function(r){  
                    if (r){  
                        $.post(url,{
								data:data,
								id_tipus_incidencia:motiu,
								idprofessors:idprofessors,
								comentari:comentari,
								afegir:afegir,
								idfranges_horaries:ss_fh,
								idgrups:ss_grup,
								idmateries:ss_mat,
								comentaris_tasca:ss_com},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Introducci&oacute; de dades efectuada correctament!','info');
                            } else { 
							    $.messager.alert('Error','Introducci&oacute; de dades efectuada erroniament!','error');
								 
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