<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$cognoms = isset($_REQUEST['cognoms']) ? $_REQUEST['cognoms'] : '' ;
?>    
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">
    <table id="dg" class="easyui-datagrid" style="height:540px;" title="Manteniment d'alumnes"
        data-options="
		singleSelect: true,
                pagination: true,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './alum/alum_getdata.php',
                sortName:'ca.Valor',
                sortOrder:'asc',
		onClickRow: onClickRow
		"> 
        <thead>  
            <tr>
                <!--<th data-options="field:'ck',checkbox:true"></th>-->
                <th data-options="field:'acces_alumne',width:20,styler:cellStyler_alumne,
                	formatter:function(value,row){
                            return '';
			}
                "></th>
                <th data-options="field:'acces_familia',width:20,styler:cellStyler_familia,
                	formatter:function(value,row){
			    return '';
			}
                "></th>
                <th data-options="field:'codi_alumnes_saga',width:120">ID</th>
                <th field="Valor" width="700" sortable="true">Nom</th>
                <!--<th field="grup" width="200" sortable="true">Grup</th>-->
            </tr>  
        </thead>  
    </table>  
    
    <div id="toolbar" style="padding:5px;height:auto">  
    <div style="margin-bottom:5px">  
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newItem()">Nou</a>
        <a id="activa_button" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" disabled="true" onclick="activa('S')">Activa</a>  
        <a id="desactiva_button" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" disabled="true" onclick="activa('N')">Desactiva</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyUser()">Esborra</a>
        Cognoms: <input id="cognoms" name="cognoms" class="easyui-validatebox" style="width:180px" value="<?=$cognoms?>">
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a>

        <img src="./images/line.png" height="1" width="100%" align="absmiddle" />&nbsp; 
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="pujarFoto()">Pujar foto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="esborrarFoto()">Esborrar foto</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="canviContrasenya()">Contrasenya alumne</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="canviContrasenyaFamilia()">Dades acc&egrave;s families</a>
        <br />
        <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="gestioGermans()">
        <img src="./images/group.png" height="16" align="absmiddle" />&nbsp;Gesti&oacute; germans</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="habilitarFamilies()">
        <img src="./images/keys.png" height="16" align="absmiddle" />&nbsp;Habilitar accés totes les families</a>
        &nbsp;
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="llistatContrasenyesFamilies()">Llistat contrasenyes families</a>
        
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" /> 
        <img src="./images/block_yellow.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Acc&eacute;s alumne&nbsp;
        <img src="./images/block_red.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Acc&eacute;s familia&nbsp;
        <img src="./images/block_blue.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Acc&eacute;s familia majors d'edat
        <br />
        <a id="activa_a_alumne" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" disabled="true" onclick="dona_acces('alumne')">Donar acc&eacute;s alumne</a>
        <a id="desactiva_a_alumne" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" disabled="true" onclick="treure_acces('alumne')">Treure acc&eacute;s alumne</a>
        
        <a id="activa_a_familia" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" disabled="true" onclick="dona_acces('familia')">Donar acc&eacute;s familia</a>
        <a id="desactiva_a_familia" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" disabled="true" onclick="treure_acces('familia')">Treure acc&eacute;s familia</a>
    </div>  
    </div>
    </div>
    
    <div id="dlg_contrasenya" class="easyui-dialog" style="width:450px;height:200px;"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_contrasenya-buttons">
            <div class="ftitle">Canvi contrasenya</div>
        	<form id="fm" method="post" novalidate>
            <div class="fitem">
                <label style="width:150px;">Nova contrasenya:</label>
                <input id="contrasenya_1" name="contrasenya_1" class="easyui-validatebox" type="password" data-options="required:true,validType:'length[3,20]'">
            </div>
            <div class="fitem">
                <label style="width:150px;">Repeteixi contrasenya:</label>
                <input id="contrasenya_2" name="contrasenya_2" class="easyui-validatebox" type="password" data-options="required:true,validType:'length[3,20]'">
            </div>
        	</form>
    </div>
    
    <div id="dlg_alum_nou" class="easyui-dialog" style="width:720px;height:540px;"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_alum_nou-buttons">
            <form id="fm_alum_nou" method="post" novalidate>
            
            <div class="fitem"><label>Codi SAGA:</label> 
            <input name="codi_alumnes_saga" class="easyui-numberbox validatebox-text" size="55"></div>
            <hr>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Nom complet</strong></label> 
            <input name="elem<?=TIPUS_nom_complet?>" class="easyui-validatebox validatebox-text" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Identificador</strong></label> 
            <input name="elem<?=TIPUS_iden_ref?>" class="easyui-validatebox validatebox-text" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Nom</strong></label> 
            <input name="elem<?=TIPUS_nom_alumne?>" class="easyui-validatebox validatebox-text" size="55"></div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>1r Cog. Alumne</strong></label> 
            <input name="elem<?=TIPUS_cognom1_alumne?>" class="easyui-validatebox validatebox-text" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>2n Cog. Alumne</strong></label> 
            <input name="elem<?=TIPUS_cognom2_alumne?>" class="easyui-validatebox validatebox-text" size="55"></div>
           
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Email</strong></label> 
            <input name="elem<?=TIPUS_email?>" class="easyui-validatebox validatebox-text" size="55"></div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Data de naixement</strong></label> 
            <input name="elem<?=TIPUS_data_naixement?>" class="easyui-validatebox validatebox-text" size="55">
            <br>&nbsp;Format: DD/MM/AAAA</div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Login</strong></label> 
            <input name="elem<?=TIPUS_login?>" class="easyui-validatebox validatebox-text" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Contrasenya</strong></label> 
            <input name="elem<?=TIPUS_contrasenya?>" type="password" class="easyui-validatebox" size="55"></div>

            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Nom pare/tutor</strong></label> 
            <input name="elem<?=TIPUS_nom_pare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>1r Cog. pare</strong></label> 
            <input name="elem<?=TIPUS_cognom1_pare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>2n Cog. pare</strong></label> 
            <input name="elem<?=TIPUS_cognom2_pare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Email tutor 1</strong></label> 
            <input name="elem<?=TIPUS_email1?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Mòbil sms</strong></label> 
            <input name="elem<?=TIPUS_mobil_sms?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Nom mare/tutora</strong></label> 
            <input name="elem<?=TIPUS_nom_mare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>1r Cog. mare</strong></label> 
            <input name="elem<?=TIPUS_cognom1_mare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>2n Cog. mare</strong></label> 
            <input name="elem<?=TIPUS_cognom2_mare?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Email tutor 2</strong></label> 
            <input name="elem<?=TIPUS_email2?>" class="easyui-validatebox" size="55"></div>
            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Mòbil sms tutor 2</strong></label> 
            <input name="elem<?=TIPUS_mobil_sms2?>" class="easyui-validatebox" size="55"></div>

            
            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Adreça</strong></label> 
            <input name="elem<?=TIPUS_adreca?>" class="easyui-validatebox validatebox-text" size="55"></div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Nom del municipi</strong></label> 
            <input name="elem<?=TIPUS_nom_municipi?>" class="easyui-validatebox validatebox-text" size="55"></div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Codi postal</strong></label> 
            <input name="elem<?=TIPUS_codi_postal?>" class="easyui-validatebox validatebox-text" size="55"></div>

            <div class="fitem" style="border-bottom:1px dashed #CCC; padding-bottom:1px; margin-bottom:1px; ">
            <label><strong>Telèfon</strong></label> 
            <input name="elem<?=TIPUS_telefon?>" class="easyui-validatebox validatebox-text" size="55"></div>
            
            </form>
    </div>
        
    <div id="dlg_alum_nou-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveAlumNou()">Acceptar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_alum_nou').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div> 

    <div id="dlg_contrasenya-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveContrasenya()">Acceptar</a>
        	    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_contrasenya').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_contrasenya_familia" class="easyui-dialog" style="width:500px;height:480px;"  
            closed="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_contrasenya_familia-buttons">
        	<br />
        	<form id="fm_familia" method="post" novalidate>
            
            <fieldset style="border:1px dashed #CCC;">
            <legend><h2>Dades tutor 1</h2></legend>
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Login</label>
                <input id="login_tutor_1" name="login_tutor_1" class="easyui-validatebox" type="text" data-options="required:true,validType:'length[3,50]'">
            </div>
            <br />
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Nova contrasenya</label>
                <input id="contrasenya_1_tutor_1" name="contrasenya_1_tutor_1" class="easyui-validatebox" type="password" data-options="required:true,validType:'length[3,20]'">
            </div>
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Repeteixi contrasenya</label>
                <input id="contrasenya_2_tutor_1" name="contrasenya_2_tutor_1" class="easyui-validatebox" type="password" data-options="required:true,validType:'length[3,20]'">
            </div>
            </fieldset>
            
            <br />
            <div class="fitem">
                <label style="width:180px;">&nbsp;&nbsp;&nbsp;<strong>Afegim un segon tutor?</strong></label>
                <input id="afegir_tutor_2" name="afegir_tutor_2" type="checkbox" value="1" onClick="cbChanged(this);">
            </div>
            <br />
            
            <div id="tutor2Div">
            <fieldset style="border:1px dashed #CCC;">
            <legend><h2>Dades tutor 2</h2></legend>
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Login</label>
                <input id="login_tutor_2" name="login_tutor_2" class="easyui-validatebox" type="text" data-options="validType:'length[3,50]'">
            </div>
            <br />
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Nova contrasenya</label>
                <input id="contrasenya_1_tutor_2" name="contrasenya_1_tutor_2" class="easyui-validatebox" type="password" data-options="validType:'length[3,20]'">
            </div>
            <div class="fitem" style="padding-left:10px;">
                <label style="width:150px; text-align:right;">Repeteixi contrasenya</label>
                <input id="contrasenya_2_tutor_2" name="contrasenya_2_tutor_2" class="easyui-validatebox" type="password" data-options="validType:'length[3,20]'">
            </div>
            </fieldset>
            </div>
            
        	</form>
    </div>
        
    <div id="dlg_contrasenya_familia-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveContrasenyaFamilia()">Acceptar</a>
        			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_contrasenya_familia').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_fitxa" class="easyui-dialog" style="width:900px;height:650px;"  
            closed="true" collapsible="true" resizable="true" modal="true" maximizable="true" buttons="#dlg_fitxa-toolbar">
    </div>
        
    <div id="dlg_fitxa-toolbar">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="#" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="saveItem(<?php echo $_REQUEST['index'];?>)">Guardar</a>
                    <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="$('#dlg_fitxa').dialog('close')">Tancar</a>  
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_upload" class="easyui-dialog" style="width:900px;height:550px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" maximizable="true" toolbar="#dlg_upload-toolbar">
    </div>
        
    <div id="dlg_upload-toolbar">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_upload').dialog('refresh')">Recarregar</a>
                    <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:tancarFoto()">Tancar</a>  
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_cf" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" maximizable="true" toolbar="#dlg_cf-toolbar">  
    </div>
    
    <div id="dlg_cf-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="javascript:$('#dlg_cf').dialog('refresh')">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="javascript:imprimirContrasenyesFamilies()">Imprimir</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_cf').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <div id="dlg_acces_families" class="easyui-dialog" style="width:800px;height:600px;"  
            closed="true" collapsible="true" resizable="true" modal="true" maximizable="true" buttons="#dlg_acces_families-toolbar">  
    </div>
    
    <div id="dlg_acces_families-toolbar">  
    <table cellpadding="0" cellspacing="0" style="width:100%">  
        <tr>  
            <td>
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_acces_families').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <div id="dlg_ge" class="easyui-dialog" style="width:875px;height:450px;padding:5px 5px" modal="true" closed="true">
        <table id="dg_ge" class="easyui-datagrid" title="" style="width:850px;height:405px"
                data-options="
                    iconCls: 'icon-edit',
                    singleSelect: true,
                    url:'./alum/alum_getgermans.php',
                    pagination: false,
                    rownumbers: true, 
                    toolbar: '#tb_ge_toolbar'
                ">
            <thead>
                <tr>
                    <th data-options="field:'ck1',checkbox:true"></th>
                    <th field="Valor" width="500">Alumne</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div id="tb_ge_toolbar" style="height:auto">
	<input id="nomAlumne" name="nomAlumne" size="60" />
        <input type="hidden" id="idAlumne" name="idAlumne" />
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true" onclick="accioGerma('ADD')">Afegir germ&agrave;</a>
        &nbsp;&nbsp;
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-remove',plain:true" onclick="accioGerma('DEL')">Treure germ&agrave;</a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-redo',plain:true" onclick="tancarGermans()">Tancar</a>
    </div>
      
    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <script type="text/javascript">  
                var url;
		
		//$('#dg').datagrid({singleSelect:(this.value==1)})
                document.getElementById("nomAlumne").value.innerHTML='';
                document.getElementById("idAlumne").value.innerHTML='';
                
		var options_alum = {
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

                $("#nomAlumne").easyAutocomplete(options_alum);
                
		$('#dg').datagrid({
				view: detailview,
				detailFormatter:function(index,row){
					return '<div class="ddv"></div>';
				},
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
					ddv.panel({
					border:false,
					cache:true,
					href:'./alum/alum_contacte.php?index='+index+'&idalumnes='+row.id_alumne,
					onLoad:function(){
					$('#dg').datagrid('fixDetailRowHeight',index);
					$('#dg').datagrid('selectRow',index);
					$('#dg').datagrid('getRowDetail',index).find('form').form('load',row);
				}
				});
					$('#dg').datagrid('fixDetailRowHeight',index);
				}
		});
		
		$(function(){  
            $('#dg').datagrid({             
				rowStyler:function(index,row){
					if (row.activat=='N'){
						return 'background-color:whitesmoke;color:#CCC;';
					}
				}  
            });  
        });
		
	$("#tutor2Div").hide();
		
	function cbChanged(checkboxElem) {
	  if (checkboxElem.checked) {
		$("#tutor2Div").show();
	  } else {
		$("#tutor2Div").hide();
	  }
	}


        function cellStyler_alumne(value,row,index){
            if (value == 'S'){
                return 'background-color:#ffcb00;color:white;';
            }
        }
		
	function cellStyler_familia(value,row,index){
            if (value == 'S'){
                return 'background-color:#a70e11;color:white;';
            }
            else if (value == 'F'){
                return 'background-color:#6eaff2;color:white;';
            }
        }
		
	function habilitarFamilies(){ 
	   url = './alum/acces_totes_families.php';
		
	   $.messager.confirm('Confirmar','Habilitem l\'acc&eacute;s a totes les families?',function(r){  
           if (r){ 
			$('#dlg_acces_families').dialog('open').dialog('setTitle','Acc&eacute;s families');
			$('#dlg_acces_families').dialog('refresh', url);
			$.post(url,function(result){ },'json');
                 }  
           });  
	}
				
	function llistatContrasenyesFamilies(){  
            url = './alum/contrasenyes_families_see.php';
            $('#dlg_cf').dialog('open').dialog('setTitle','Dades acc&eacute;s families');
            $('#dlg_cf').dialog('refresh', url);
        }
		
		function imprimirContrasenyesFamilies(){
		    url = './alum/contrasenyes_families_print.php';
		    $('#fitxer_pdf').attr('src', url);
                }
		
		function onClickRow(index){ 
			var row = $('#dg').datagrid('getSelected');
			if (row.activat=='S'){
				$('#activa_button').linkbutton('disable');
				$('#desactiva_button').linkbutton('enable');
				$('#desactiva_a_alumne').linkbutton('enable');
				$('#desactiva_a_familia').linkbutton('enable');
                                $('#activa_a_alumne').linkbutton('enable');
				$('#activa_a_familia').linkbutton('enable');
			}
			if (row.activat=='N'){
				$('#activa_button').linkbutton('enable');
				$('#desactiva_button').linkbutton('disable');
				$('#desactiva_a_alumne').linkbutton('disable');
				$('#desactiva_a_familia').linkbutton('disable');
                                $('#activa_a_alumne').linkbutton('disable');
				$('#activa_a_familia').linkbutton('disable');
			}
		}
			
        function doSearch(){
			var s = 1;  
			$('#dg').datagrid('load',{  
			    s : s,
				cognoms: $('#cognoms').val()  
			});  
		}
		
		function pujarFoto(){ 
		    var row = $('#dg').datagrid('getSelected');
            if (row){
				url = './alum/alum_upload_photo.php?idalumnes='+row.id_alumne;
				$('#dlg_upload').dialog('open').dialog('setTitle','Pujar foto');
				$('#dlg_upload').dialog('refresh', url);
			}
		}
		
		function esborrarFoto(){ 
		    $.messager.confirm('Confirmar','Esborrem aquesta foto?',function(r){
				var row = $('#dg').datagrid('getSelected');
				if (row){
					url = './alum/esborra_foto.php?id='+row.id_alumne;
					$.post(url,{},function(result){  
					if (result.success){ 
						$.messager.alert('Informaci&oacute;','Foto esborrada correctament!','info');
						$('#dg').datagrid('reload');
					} else { 
						$.messager.show({     
						title: 'Error',  
						msg: result.msg  
						});  
						}  
					 },'json');
				}
			});
		}
		
                function saveAlumNou(){
		    $('#fm_alum_nou').form('submit',{
                        url: url,
                        onSubmit: function(){
                            return $(this).form('validate');
                        },
                        success: function(result){                           
                            var result = eval('('+result+')');
                            if (result.msg){
                                $.messager.show({
                                    title: 'Error',
                                    msg: result.msg
                                });
                            } else {
                                $.messager.alert('Informaci&oacute;','Alumne donat d\'alta correctament!','info');
                                $('#dlg_alum_nou').dialog('close');
                                $('#dg').datagrid('reload');
                            }
                        }
                    });
                }
                
		function tancarFoto(){ 
			$('#dlg_upload').dialog('close');
			open1('./alum/alum_grid.php',this);
		}
		
		function canviContrasenya(){
                        var row = $('#dg').datagrid('getSelected');
			$('#dlg_contrasenya').dialog('open').dialog('setTitle','Dades usuari');
                        $('#fm').form('clear');
                        url = './alum/alum_update_passwd.php?id='+row.id_alumne;
                }
		
		function saveContrasenya(){
                        var contrasenya_1 = $('#contrasenya_1').val();
			var contrasenya_2 = $('#contrasenya_2').val();
			if (contrasenya_1!=contrasenya_2) {
				 $.messager.alert('Error','Les contrasenyes no coincideixen! Sisplau, revisa-les.','error');
				return false;
			}
			
			$('#fm').form('submit',{
                        url: url,
                        onSubmit: function(){
                            return $(this).form('validate');
                        },
                        success: function(result){
					var result = eval('('+result+')');
                        if (result.msg){
                            $.messager.show({
                                title: 'Error',
                                msg: result.msg
                            });
                        } else {
			    $.messager.alert('Informaci&oacute;','Contrasenya actualitzada correctament!','info');
                            $('#dlg_contrasenya').dialog('close');
			    $('#dg').datagrid('reload');
                        }
                        }
                    });
                }
		
		function canviContrasenyaFamilia(){				
			var row = $('#dg').datagrid('getSelected');
			if (row) {
				url = './alum/familia_load.php?id='+row.id_alumne;
				$('#fm_familia').form('clear');
				$('#fm_familia').form('load',url);
				$('#dlg_contrasenya_familia').dialog('open').dialog('setTitle','Dades connexi&oacute; familia');
				url = './alum/familia_update_passwd.php?id='+row.id_alumne;
			}
                }
		
		function saveContrasenyaFamilia(){
			var contrasenya_1_tutor_1 = $('#contrasenya_1_tutor_1').val();
			var contrasenya_2_tutor_1 = $('#contrasenya_2_tutor_1').val();
				
			if (contrasenya_1_tutor_1!=contrasenya_2_tutor_1) {
				$.messager.alert('Error','Les contrasenyes del tutor 1 no coincideixen! Sisplau, revisa-les.','error');
				return false;
			}
			
			if($("#afegir_tutor_2").is(':checked')) {
				var contrasenya_1_tutor_2 = $('#contrasenya_1_tutor_2').val();
				var contrasenya_2_tutor_2 = $('#contrasenya_2_tutor_2').val();
				
				if (contrasenya_1_tutor_2!=contrasenya_2_tutor_2) {
					$.messager.alert('Error','Les contrasenyes del tutor 2 no coincideixen! Sisplau, revisa-les.','error');
					return false;
				}
			}
			
			$('#fm_familia').form('submit',{
                        url: url,
                        onSubmit: function(){
                            return $(this).form('validate');
                        },
                        success: function(result){
                            var result = eval('('+result+')');
                            if (result.msg){
                                $.messager.show({
                                    title: 'Error',
                                    msg: result.msg
                                });
                            } else {
                                $.messager.alert('Informaci&oacute;','Dades de connexi&oacute; actualitzades correctament!','info');
                                $('#dlg_contrasenya_familia').dialog('close');
                                $('#dg').datagrid('reload');
                            }
                        }
                    });
                }
		
		function treure_acces(element){ 
		    var row = $('#dg').datagrid('getSelected');
                    if (row){
				$.messager.confirm('Confirmar','Est&aacute;s segur de que vols treure l\'acc&eacute;s?',function(r){  
				if (r){
					if (element=='alumne') {
						url = './alum/alum_t_ac_alum.php?idalumnes='+row.id_alumne;
					}
					if (element=='familia') {
						url = './alum/alum_t_ac_fami.php?idalumnes='+row.id_alumne;
					}
					$.post(url,{},function(result){  
					if (result.success){ 
						$.messager.alert('Informaci&oacute;','Acc&eacute;s tret correctament!','info');
						$('#dg').datagrid('reload');
					} else { 
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
                
                function dona_acces(element){ 
		    var row = $('#dg').datagrid('getSelected');
                    if (row){
				$.messager.confirm('Confirmar','Est&aacute;s segur de que vols donar l\'acc&eacute;s?',function(r){  
				if (r){
					if (element=='alumne') {
						url = './alum/alum_d_ac_alum.php?idalumnes='+row.id_alumne;
					}
					if (element=='familia') {
						url = './alum/alum_d_ac_fami.php?idalumnes='+row.id_alumne;
					}
					$.post(url,{},function(result){  
					if (result.success){ 
						$.messager.alert('Informaci&oacute;','Acc&eacute;s donat correctament!','info');
						$('#dg').datagrid('reload');
					} else { 
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
		
		function gestioGermans(){		    
			var row = $('#dg').datagrid('getSelected');
						
			if (row) {
				$('#dg_ge').datagrid('load',{  
					idalumnes : row.id_alumne
				});
				$('#dlg_ge').dialog('open').dialog('setTitle','Rel.laci&oacute; de germans');
			}
                }
		
		function accioGerma(accio){		    
			var id_germa = 0;
			var row      = $('#dg').datagrid('getSelected');
			var row_ge   = $('#dg_ge').datagrid('getSelected');
			
			if (accio == 'ADD') {
				id_germa = $('#idAlumne').val();
			}
			else if (accio == 'DEL') {
				id_germa = row_ge.idalumnes;
			}
			
			url = './alum/germa_edita.php';
			
			if (row) {
                            $.post(url,{
				action:accio,
				idalumnes:row.id_alumne,
				id_germa:id_germa},function(result){  
                            if (result.success){  
                                $.messager.alert('Informaci&oacute;','Germ&agrave; gestionat correctament!','info');
				$('#dg_ge').datagrid('reload');
                            } else { 
				$.messager.alert('Error','Acci&oacute; Germ&agrave; gestionat err&ograve;niament!','error');
								 
                                $.messager.show({  
                                    title: 'Error',  
                                    msg: result.msg  
                                });  
                            }  
                        },'json');
			}
        }
		
	function tancarGermans() {
		javascript:$('#dlg_ge').dialog('close');
	}
		
	function saveItem(index){
		var row = $('#dg').datagrid('getRows')[index];
		var url = row.isNewRecord ? './alum/alum_nou.php' : './alum/alum_edita.php?id='+row.id_alumne;
			
		$('#dg').datagrid('getRowDetail',index).find('form').form('submit',{
			url: url,
			onSubmit: function(){
				return $(this).form('validate');
			},
			success: function(data){
				data = eval('('+data+')');
                                data.isNewRecord = false;
				$('#dg').datagrid('collapseRow',index);
				$('#dg').datagrid('updateRow',{
					index: index,
					row: data
				});
			}
		});
			
		if (url=='./alum/alum_nou.php') {
			cognoms = $('#cognoms').val();
			open1('./alum/alum_grid.php?cognoms='+cognoms,this);
			//doSearch();
		}
	}
		
	function cancelItem(index){
            var row = $('#dg').datagrid('getRows')[index];
            if (row.isNewRecord){
		$('#dg').datagrid('deleteRow',index);
            } else {
		$('#dg').datagrid('collapseRow',index);
            }
	}
		
	function newItem(){
            $('#fm_alum_nou').form('clear');
            $('#dlg_alum_nou').dialog('open').dialog('setTitle','Alumne nou');
            url = './alum/alum_nou.php';
            
            /*$('#dg').datagrid('appendRow',{isNewRecord:true});
            var index = $('#dg').datagrid('getRows').length - 1;
            $('#dg').datagrid('expandRow', index);
            $('#dg').datagrid('selectRow', index);*/
	}
		
        function destroyUser(){  
            var row = $('#dg').datagrid('getSelected');  
            if (row){  
                $.messager.confirm('Confirmar','Estás segur de que vols esborrar aquest alumne?',function(r){  
                    if (r){ 
                        $.post('./alum/alum_esborra.php',{id:row.id_alumne},function(result){  
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
		
		function activa(op){  
            var row = $('#dg').datagrid('getSelected');  
            if (row){  
                $.messager.confirm('Confirmar','Procedim?',function(r){  
                    if (r){  
                        $.post('./alum/alum_desactiva.php',{op:op,id:row.id_alumne},function(result){  
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
    </script>
    
    <style type="text/css">  
        #fm{  
            margin:0;  
            padding:10px 30px;  
        }  
        #fm_alum_nou{  
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