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
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:550px;">  
    <table id="dg" class="easyui-datagrid" title="Abs&egrave;ncies comunicades" style="height:548px;" 
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './abs_prof/abs_prof_getdata.php'
	">    
        <thead>  
            <tr>
                <th data-options="field:'data_incidencia',width:90,align:'left',editor:{type:'datebox',options:{formatter:myformatter,parser:myparser}}">Data</th>
                <th data-options="field:'hora',width:100,align:'center',sortable:true">Hora</th>
                <th data-options="field:'dia',width:100,align:'center',sortable:true">Dia setmana</th>
            	<!--<th sortable="true" data-options="field:'id_tipus_incidencia',width:120,
						formatter:function(value,row){
							return row.tipus_falta;
						},
						editor:{
							type:'combobox',
							options:{
								valueField:'idtipus_falta_professor',
                                textField:'tipus_falta',
                                url:'./abs_prof/tfp_getdata.php',
								required:true
							}
						}">Tipus falta</th>-->
                <th data-options="field:'comentari',width:400,sortable:true">Comentari</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">  
        Desde: <input id="data_inici_tutor" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        fins a: <input id="data_fi_tutor" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        &nbsp;
       <!--
        Motiu:&nbsp;
        <select id="motiu_combo" class="easyui-combobox" data-options="
					width:180,
                    url:'./abs_prof/tfp_getdata.php',
					idField:'idtipus_falta_professor',
                    valueField:'idtipus_falta_professor',
					textField:'tipus_falta',
					panelHeight:'auto'
		">
        </select>
        -->
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a>
        &nbsp;&nbsp;&nbsp;
        <a id="horari_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-tip',plain:true" onclick="verHorario(<?= $id_professor ?>)">Veure horari professor</a>&nbsp;
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
				
		$(function(){  
            $('#dg').datagrid({  
				view: detailview,  
                detailFormatter:function(index,row){
					return '<div class="ddv"></div>';
				},				
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
					ddv.panel({
						border:false,
						cache:false,
						href:'./abs_prof/abs_prof_getdetail.php?id='+row.idincidencia_professor,
						onLoad:function(){
							$('#dg').datagrid('fixDetailRowHeight',index);
						}
					});
					$('#dg').datagrid('fixDetailRowHeight',index);
				},
				rowStyler:function(index,row){
				}  
            });  
        });
		
        function doSearch(){ 
			$('#dg').datagrid('load',{  
				data_inici_tutor    : $('#data_inici_tutor').datebox('getValue'),
				data_fi_tutor       : $('#data_fi_tutor').datebox('getValue')
				 
			});  
			//id_tipus_incidencia : $('#motiu_combo').combobox('getValue') 
		}
		
		function verHorario(id_professor){
			url = './prmat/prmat_see.php?idprofessors='+id_professor;
			$('#dlg_hor').dialog('open').dialog('setTitle','Horari');
			$('#dlg_hor').dialog('refresh', url);
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