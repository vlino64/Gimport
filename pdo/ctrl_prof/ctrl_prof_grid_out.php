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
    <table id="dg" class="easyui-datagrid" title="Professorat marxa aviat del centre" style="height:540px;" 
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './ctrl_prof/ctrl_prof_getdata_out.php'
	">    
        <thead>  
            <tr>
                <th field="professor" width="440" sortable="true">Professor</th>
                <th field="hora_entrada" width="100" sortable="true" align="center">Hora entrada</th>
                <th field="hora_sortida" width="100" sortable="true" align="center">Hora sortida</th>
                <th field="hora_registre_sortida" width="120" sortable="true" align="center">Registre Sortida</th>
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto">  
         <form id="fmRS" method="post">    
        <strong>Dia&nbsp;</strong><input id="data_sortida" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;&nbsp;
        <strong>Registre sortida</strong>&nbsp;
        <input id="tipus_registre" name="tipus_registre" type="radio" value="PRIMER" checked="checked" />&nbsp;Primer&nbsp;
        <input id="tipus_registre" name="tipus_registre" type="radio" value="DARRER" />&nbsp;Darrer&nbsp;&nbsp;
        
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a>
        &nbsp;&nbsp;&nbsp;
        <img src="./images/block_green.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Fet registre sortida
        </form>
    </div>
    </div>
    
    <script type="text/javascript">  
        var url;
		var theDate;
		var theDay;
		
		$('#data_sortida').datebox({
			onSelect: function(date){
				theDate = new Date(date);
				theDay  = theDate.getDay();				
			}
		});
		
		$(function(){  
            $('#dg').datagrid({  
				rowStyler:function(index,row){
				    if (row.registre_sortida=='S'){
						return 'background-color:#a1d88b;color:#009a49;font-weight:bold;';
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
			if ($('#data_sortida').datebox('getValue') == '') {
				var f = new Date();
				var y = f.getFullYear();  
           		var m = f.getMonth()+1;  
            	var d = f.getDate();  
				data = d+'-'+m+'-'+y;
			}
			else {
				data = $('#data_sortida').datebox('getValue');
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
	</script>