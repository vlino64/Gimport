<?php
   session_start();
   require_once('../bbdd/connect.php');
   require_once('../func/constants.php');
   require_once('../func/generic.php');
   require_once('../func/seguretat.php');
   $db->exec("set names utf8");
   
   $strNoCache = "";
?>        
    <div id="dlg_main" class="easyui-panel" style="height:auto;">
               
    <table id="tt" class="easyui-treegrid" title="Sessions on s'ha passat llista" style="height:540px;" 
            data-options="
		toolbar: '#toolbar',
		url: './inf_passa_llista/inf_passa_llista_getdata.php',
                method: 'get',
                rownumbers: false,
                idField: 'id',
                treeField: 'data'
	">
        <thead>  
            <tr>
		<th field="data" width="140" sortable="true">Data</th>
                <th field="grup" width="140" sortable="true">Grup</th>
                <th field="materia" width="420" sortable="true">Mat&egrave;ria</th>
                <th data-options="field:'passa_llista',width:50,formatter:formatPassaLlista">&nbsp;</th>
            </tr>  
        </thead>  
    </table> 
         
    <div id="toolbar" style="padding:5px;height:auto"> 
          <form id="ff" name="ff" method="post">
          <div id="datesDiv" style="">
          &nbsp;&nbsp;Desde <input id="data_inici" name="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
          Fins a <input id="data_fi" name="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
          </div>
          <br />    
          
          <div id="profDiv" style="float:left;margin-top:-15px;margin-left:10px;">
            Professor
            <input id="nomProfessor" name="nomProfessor" size="40" />
            <input type="hidden" id="idProfessor" name="idProfessor" />
          </div>
          
          &nbsp;<a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()" style="margin-top:5px;"></a>
          <br />
          </form>
    </div>

    </div>
    
    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <script type="text/javascript">  
        var url;
	var editIndex    = undefined;
	var nou_registre = 0;
	var today        = new Date();
	
        var options_prof = {
                url: "./prmod/prof_getdata.php",
                getValue: "Valor",

                list: {
                    match: {
                        enabled: true
                    },
                                
                    onSelectItemEvent: function() {
                        var value = $("#nomProfessor").getSelectedItemData().id_professor;
                        $("#idProfessor").val(value).trigger("change");
                    }
                }
        };

        $("#nomProfessor").easyAutocomplete(options_prof);
	
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
        
	function doSearch(){
            var nomProfessor = $('#nomProfessor').val();
            var idProfessor  = $('#idProfessor').val();   
            var data_inici = $('#data_inici').datebox('getValue');
            var data_fi    = $('#data_fi').datebox('getValue');
                   
            if (nomProfessor.trim()=='') {
                idProfessor = 0;
            }

            $('#tt').treegrid('load', {  
                data_inici: data_inici,
                data_fi   : data_fi,
                idprofessor  : idProfessor
            });
            
            $('#tt').treegrid({  
              url:'./inf_passa_llista/inf_passa_llista_getdata.php?'});
   
	}
        
        function formatPassaLlista(value){
            if (value == 'S'){
                var s = '<img src="./images/task_complete.png" width=24>' ;
                return s;
            } 
            else if (value == 'G'){
                var s = '<img src="./images/icons/icon_validate.png" width=24>' ;
                return s;
            }
            else {
                return '';
            }
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