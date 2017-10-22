<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $strNoCache = "";
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">   
    <table id="dg" class="easyui-datagrid" title="Qui esta en linia" style="height:540px"
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './ass_servei/quies_enlinia_getdata.php'
	">    
        <thead>  
            <tr>
            	<!--<th field="idincidencia_alumne" width="100" sortable="true" align="center">ID</th>--> 
                <th field="hora" width="90" sortable="true" align="center">Hora</th> 
                <th field="professor" width="370" sortable="true">Professor</th>
                <th field="accions" width="120" sortable="true">Acci&oacute;</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">  
        Dia&nbsp;<input id="data" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser">
        </input> 
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()">Cercar</a>
        &nbsp;&nbsp;&nbsp;
        <a id="horari_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-tip',plain:true" onclick="verHorario()">Veure horari professor</a>
    </div>
    </div>
    
    <div id="dlg_hor_c" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" toolbar="#dlg_hor-toolbar">  
    </div>
    
    <div id="dlg_hor-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_hor_c').dialog('refresh')">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_hor_c').dialog('close')">Tancar</a>  
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
		var theDate;
		var theDay;
		
		$('#data').datebox({
			onSelect: function(date){
				theDate = new Date(date);
				theDay  = theDate.getDay();				
			}
		});
		
		$(function(){  
            $('#dg').datagrid({  
				view: detailview,  
                detailFormatter:function(index,row){
					return '<div class="ddv" style="padding:5px 0"></div>';
				},
				rowStyler:function(index,row){
					if (row.id_accio==<?=TIPUS_ACCIO_LOGIN?>){
						return 'color:blue;font-weight:bold;';
					}
					if (row.id_accio==<?=TIPUS_ACCIO_LOGOUT?>){
						return 'background-color:whitesmoke;color:#ccc';
					}
			    }, 
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
					ddv.panel({
						border:false,
						cache:false,
						href:'./ass_servei/quies_enlinia_getdetail.php?id='+row.id_professor,
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
				data : $('#data').datebox('getValue')
			});  
		} 
			
		function verHorario(){
		    var row = $('#dg').datagrid('getSelected');
            editIndex = undefined;
			
			if (row){ 			     
				url = './prmat/prmat_see.php?idprofessors='+row.id_professor;
				$('#dlg_hor_c').dialog('open').dialog('setTitle','Horari professor');
				$('#dlg_hor_c').dialog('refresh', url);
            }
        }
		
		function enviarSMS(){
			var data = $('#data').datebox('getValue');
			url = './conserge/conserge_sms.php?data='+data;
			$('#dlg_sms').dialog('open').dialog('setTitle','Enviar SMS');
			$('#dlg_sms').dialog('refresh', url);
		}
		
		function tancar() {
		    javascript:$('#dlg_sms').dialog('close');
			location.href = './home.php';
			//open1('./conserge/conserge_grid.php');
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