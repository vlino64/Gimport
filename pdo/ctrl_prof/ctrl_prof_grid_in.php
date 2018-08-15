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
    <table id="dg" class="easyui-datagrid" title="Control Registre Professorat" style="height:540px;" 
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './ctrl_prof/ctrl_prof_getdata_in.php'
	">    
        <thead>  
            <tr>
                <th field="professor" width="440" sortable="true">Professor</th>
                <th field="hora_entrada" width="100" sortable="true" align="center">Hora entrada</th>
                <th field="hora_sortida" width="100" sortable="true" align="center">Hora sortida</th>
                <th field="hora_registre_entrada" width="120" sortable="true" align="center" data-options="styler:cellStyler_entrada">Registre Entrada</th>
                <th field="hora_registre_sortida" width="120" sortable="true" align="center" data-options="styler:cellStyler_sortida">Registre Sortida</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">
    	<form id="fmRE" method="post">    
        <strong>Dia&nbsp;</strong><input id="data_entrada" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;&nbsp;
        <strong>Registre entrada</strong>&nbsp;
        <input id="tipus_registre" name="tipus_registre" type="radio" value="PRIMER" checked="checked" />&nbsp;Primer&nbsp;
        <input id="tipus_registre" name="tipus_registre" type="radio" value="DARRER" />&nbsp;Darrer&nbsp;&nbsp;
        
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a>
        &nbsp;&nbsp;
        <a id="assist_button" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-print',plain:true" onclick="Informe()">Informe</a>
        </form>
    </div>
    </div>
    
    <div id="dlg_inf" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" collapsible="true" maximized="true" maximizable="true" resizable="true" modal="true" toolbar="#dlg_inf-toolbar">
	</div>
    
    <div id="dlg_inf-toolbar">
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <form id="fmINF" method="post">
                Desde: <input id="data_inici_inf" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser,required:true"></input>&nbsp;
        	Fins a: <input id="data_fi_inf" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser,required:true"></input>&nbsp;
		Per professor: 
                <select id="c_professor" class="easyui-combobox" name="state" style="width:400px;">
                    <option value="0">Tots els professors ...</option>
                    <?php
			  $rsProfessors = getProfessorsActius($db,TIPUS_nom_complet);
                          foreach($rsProfessors->fetchAll() as $row) {
			  	echo "<option value='".$row["idprofessors"]."'>".$row["Valor"]."</option>";
			  }

			  if (isset($rsProfessors)) {
                                //mysql_free_result($rsProfessors);
			  }
                    ?>
                </select>
                
                <a href="#" onclick="imprimirPDF()">
                <img src="./images/icons/icon_pdf.png" height="32"/></a>
                </form>
                
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="doReload()">Recarregar</a>  
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_inf').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    
    <script type="text/javascript">  
        var url;
	var theDate;
	var theDay;
		
	$('#data_entrada').datebox({
			onSelect: function(date){
				theDate = new Date(date);
				theDay  = theDate.getDay();				
			}
	});
		
	$(function(){  
            $('#dg').datagrid({  
				rowStyler:function(index,row){
					if (row.registre_entrada=='N'){
						return 'background-color:whitesmoke;color:#AAA;';
					}
					if (row.registre_sortida=='N'){
						return 'background-color:whitesmoke;color:#AAA;';
					}
				    if (row.registre_entrada=='S'){
						//return 'background-color:#a1d88b;color:#009a49;font-weight:bold;';
					}
				}  
            });  
        });
		
		/*$(document).ready(function(){
			setInterval(function() {
				$("#dlg_main").load("./ctrl_prof/ctrl_prof_grid_in.php");
			}, 5000);
		});*/
		
        function doSearch(){ 
			var data;
			if ($('#data_entrada').datebox('getValue') == '') {
				var f = new Date();
				var y = f.getFullYear();  
                                var m = f.getMonth()+1;  
                                var d = f.getDate();  
				data = d+'-'+m+'-'+y;
			}
			else {
				data = $('#data_entrada').datebox('getValue');
			}
			
			$('#dg').datagrid('load',{  
				data : data,
				tipus_registre : $('input[name=tipus_registre]:checked', '#fmRE').val()
			});  
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
		
		function cellStyler_entrada(value,row,index){
			if (row.hora_registre_entrada != '00:00') {
				if (row.hora_entrada < row.hora_registre_entrada){
					return 'background-color:red;color:#FFF;font-weight:bold;';
				}
                                if (row.hora_entrada >= row.hora_registre_entrada){
					return 'background-color:green;color:#FFF;font-weight:bold;';
				}
			}
        }
		
		function cellStyler_sortida(value,row,index){
			if (row.hora_registre_sortida != '00:00') {
				if (row.hora_sortida > row.hora_registre_sortida){
					return 'background-color:red;color:#FFF;font-weight:bold;';
				}
                                if (row.hora_sortida <= row.hora_registre_sortida){
					return 'background-color:green;color:#FFF;font-weight:bold;';
				}
			}
        }
		
		function Informe(){
			url = './ctrl_prof/ctrl_prof_reg_see.php';
			$('#dlg_inf').dialog('open').dialog('setTitle','Informe control registre professorat');
			//$('#dlg_inf').dialog('refresh', url);
		}
		
		function imprimirPDF(){  
			d_inici     = $('#data_inici_inf').datebox('getValue');
			d_fi        = $('#data_fi_inf').datebox('getValue');
			c_professor = $('#c_professor').combobox('getValue');
			
			url = './ctrl_prof/ctrl_prof_reg_print.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_professor='+c_professor;
			
			$('#fitxer_pdf').attr('src', url);		
		}
		
		function imprimirWord(idgrups,idmateria){  
			d_inici  = $('#data_inici_inf').datebox('getValue');
			d_fi     = $('#data_fi_inf').datebox('getValue');
			//c_alumne = $('#c_alumne').combobox('getValue');
			
			url = './assist/assist_print_word.php?data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#fitxer_pdf').attr('src', url);		
		}
		
		function imprimirExcel(idgrups,idmateria){  
			d_inici  = $('#data_inici_inf').datebox('getValue');
			d_fi     = $('#data_fi_inf').datebox('getValue');
			//c_alumne = $('#c_alumne').combobox('getValue');
			
			url = './assist/assist_print_excel.php?data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#fitxer_pdf').attr('src', url);		
		}
		
		function doReload(){
			d_inici  = $('#data_inici_inf').datebox('getValue');
			d_fi     = $('#data_fi_inf').datebox('getValue');
			c_professor = $('#c_professor').combobox('getValue');
                        
                        if(d_inici!='' && d_fi!='') {
                            url = './ctrl_prof/ctrl_prof_reg_see.php?data_inici='+d_inici+'&data_fi='+d_fi+'&c_professor='+c_professor;
                            $('#dlg_inf').dialog('refresh', url);
                        }
                        else {
                            $.messager.alert('Avís','Si us plau introdueixi data d\'inici i data fi');
                        }
		}
		
	</script>