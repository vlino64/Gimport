<?php
  session_start();
  require_once('../bbdd/connect.php');
  require_once('../func/constants.php');
  require_once('../func/generic.php');
  require_once('../func/seguretat.php');
  $db->exec("set names utf8");
    
  if ( isset($_REQUEST['idprofessor']) && ($_REQUEST['idprofessor']==0) ) {
  	$idprofessor = 0;
  }
  else if ( isset($_REQUEST['idprofessor']) ) {
    $idprofessor = $_REQUEST['idprofessor'];
  }
  if (! isset($idprofessor)) {
    $idprofessor = 0;
  }
?>

    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">
    <table id="dg" class="easyui-datagrid" title="Gesti&oacute; alumnes grup mat&egrave;ria" style="height:540px;"
	data-options="
		singleSelect: true,
                pagination: false,
                rownumbers: true,
		toolbar: '#toolbar',
                fixed: true,
		url: './alum_class/alum_class_getdata.php',
		onClickRow: onClickRow
	">    
        <thead>  
            <tr> 
            	<th data-options="field:'ck1',checkbox:true"></th>
                <th field="Valor" width="500">Alumne</th>               
            </tr>  
        </thead>  
    </table>
  
    <div id="toolbar" style="height:auto; padding-top:7px; padding-bottom:7px;">
       
    Grup / Mat&egrave;ria<br />
    <select id="grups_materies" name="grups_materies" class="easyui-combogrid" style="width:650px" data-options="
            panelWidth: 610,
            idField: 'idagrups_materies',
            textField: 'materia',
            url: url,
            method: 'get',
            columns: [[
                {field:'grup',title:'Grup',width:170},
                {field:'materia',title:'Mat&egrave;ria',width:480}
            ]],
            fitColumns: true
    ">
    </select>
    
    <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a>
    <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
    
    Copiar alumnes de<br />
    <select id="grups_materies_desde" name="grups_materies_desde" class="easyui-combogrid" style="width:650px" data-options="
            panelWidth: 610,
            idField: 'idagrups_materies',
            textField: 'materia',
            url: url,
            method: 'get',
            columns: [[
                {field:'grup',title:'Grup',width:170},
                {field:'materia',title:'Mat&egrave;ria',width:480}
            ]],
            fitColumns: true
    ">
    </select>
    <a href="#" class="easyui-linkbutton" iconCls="icon-save" onclick="copiarAlumnes()">Copiar</a>
    <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
    
    Cercar alumne<br />
    <input id="nomAlumne" name="nomAlumne" size="60" />
    <input type="hidden" id="idAlumne" name="idAlumne" />
    
    <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
    &nbsp;
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="afegirAlumne()">Afegir alumne</a>
    &nbsp;&nbsp;
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="treureAlumnes()">Treure alumne(s)</a>
    </div>   
    </div>
    
    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <script type="text/javascript">  
		var editIndex = undefined;
		var url;
		var nou_registre = 0;
		
                var options = {
                        url: "./almat_tree/alum_getdata.php",

                        getValue: "alumne",

                        list: {
                                match: {
                                        enabled: true
                                },
                                
                                onSelectItemEvent: function() {
                                        var value = $("#nomAlumne").getSelectedItemData().id_alumne;
                                        $("#idAlumne").val(value).trigger("change");
                                }
                        }
                };

                $("#nomAlumne").easyAutocomplete(options);

		$('#grups_materies').combogrid({
			url: 'nodata.php',
		});
						
		$('#grups_materies').combogrid({
			url: './alum_class/grup_materia_getdata.php?idprofessors=<?=$idprofessor?>',	
		});
        
                $('#grups_materies_desde').combogrid({
			url: './alum_class/grup_materia_getdata.php?idprofessors=<?=$idprofessor?>',	
		});
		
		$('#dg').datagrid({singleSelect:(this.value==1)})
				
		function doSearch(){
			editIndex = undefined;

			$('#dg').datagrid('load',{  
				idgrups_materies : $('#grups_materies').combobox('getValue')
			});
		}
		
		function afegirAlumne(){
			//var id_alumne        = $('#idalumne').combogrid('getValue');
                        var id_alumne        = $('#idAlumne').val();
			var idgrups_materies = $('#grups_materies').combobox('getValue');
			url = './almat_tree/almat_tree_nou.php';
			
			if (idgrups_materies!=0 && id_alumne!=0) {
						$.post(url,{
								idgrups_materies:idgrups_materies,
								idalumnes:id_alumne},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Alumne introdu&iuml;t correctament!','info');
				$('#dg').datagrid('reload');
                            } else { 
				$.messager.alert('Error','Alumne introdu&iuml;t erroniament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json');
			}
        }
		
		function treureAlumnes(){ 
		  var idgrups_materies = $('#grups_materies').combobox('getValue');
		  var rows_al  = $('#dg').datagrid('getSelections');
		  
		  if (rows_al){ 
			   var ss_al = [];
			   for(var i=0; i<rows_al.length; i++){
					var row = rows_al[i];
					ss_al.push(row.idalumnes);
			   }
			   			   
			   url = './almat_tree/almat_tree_edita.php';
			   
			   $.messager.confirm('Confirmar','Esborrem aquests alumnes?',function(r){  
                    if (r){  
                        $.post(url,{
				idgrups_materies:idgrups_materies,
				idalumnes:ss_al},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Dades actualitzades correctament!','info');
				$('#dg').datagrid('reload');
                            } else { 
				$.messager.alert('Error','Dades actualitzades erroniament!','error');
								 
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
                
                function copiarAlumnes(){ 
		    var idgrups_materies       = $('#grups_materies').combobox('getValue');
		    var idgrups_materies_desde = $('#grups_materies_desde').combobox('getValue');			   			   
                    
                    url = './alum_class/copiar_alum.php';
			   
		    $.messager.confirm('Confirmar','Copiem aquests alumnes?',function(r){  
                    if (r){
                        $.post(url,{
				idgrups_materies:idgrups_materies,
				idgrups_materies_desde:idgrups_materies_desde},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Dades actualitzades correctament!','info');
				$('#dg').datagrid('reload');
                            } else { 
				$.messager.alert('Error','Dades actualitzades erroniament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json'); 
                    }
                    });
                }
			
	
		function onClickRow(index){
			if (editIndex != index){
				if (endEditing()){
					$('#dg').datagrid('selectRow', index)
							.datagrid('beginEdit', index);
					editIndex = index;
				} else {
					$('dg').datagrid('selectRow', editIndex);
				}
			}
		}
		
		function endEditing(){
			if (editIndex == undefined){return true}			
			if ($('#dg').datagrid('validateRow', editIndex)){
				var row = $('#dg').datagrid('getSelected');
				$('#dg').datagrid('endEdit', editIndex);
							
				editIndex = undefined;
				return true;
			} else {
				return false;
			}
		}
		
	</script>