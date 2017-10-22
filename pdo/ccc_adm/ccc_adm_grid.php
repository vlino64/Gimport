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
   
   $fechaSegundos = time();
   $strNoCache = "";
?>        
    <div id="dlg_main" class="easyui-panel" style="width:auto;height:auto;">  
    <table id="dg" class="easyui-datagrid" title="CCC introdu&iuml;des" style="height:540px;" 
	data-options="
		singleSelect: true,
                pagination: true,
                rownumbers: true,
		toolbar: '#toolbar',
		url: './ccc_adm/ccc_adm_getdata.php',
		onClickRow: onClickRow
	">    
        <thead>  
            <tr>
                <th data-options="field:'data_ccc',width:85,align:'left',editor:{options:{formatter:myformatter,parser:myparser}}">Data</th>
                <th field="hora" width="85" sortable="true">Hora</th>
		<th field="nom_falta" width="80" sortable="true">Tipus CCC</th>
                <th field="expulsio" width="30" sortable="true">Exp</th>
                <th field="alumne" width="200" sortable="true">Alumne</th>
                <th field="professor" width="200" sortable="true">Professor</th>
                <th field="mesura" width="220" sortable="true">Sanci&oacute;</th>
                <!--<th data-options="field:'data_inici_sancio_ccc',width:85,align:'left',editor:{options:{formatter:myformatter,parser:myparser}}">Inici</th>
                <th data-options="field:'data_fi_sancio_ccc',width:85,align:'left',editor:{options:{formatter:myformatter,parser:myparser}}">Fi</th>-->
                
            </tr>  
        </thead>  
    </table> 
    
    <div id="toolbar" style="padding:5px;height:auto"> 
        Desde: <input id="data_inici" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        fins a: <input id="data_fi" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        <a href="#" class="easyui-linkbutton" iconCls="icon-search" onclick="doSearch()"></a> 
        
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" /> 
        <a id="nova_ccc" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-add',plain:true,disabled:false" onclick="novaCCC()">Nova CCC</a>
        <a id="esborrar_ccc" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-cancel',plain:true,disabled:true" onclick="esborrarCCC()">Esborrar CCC</a>
        <a id="gestio_sancio" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true,disabled:true"  plain="true" onclick="gestioSancio()">Editar CCC</a> 
        <a id="imprimir_ccc" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-print',plain:true,disabled:true" onclick="imprimirCCC()">Imprimir CCC</a>
        
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" />
        <a id="informesCCC" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-help',plain:true"  plain="true" onclick="informesCCC()">Informes</a>&nbsp;
        <a id="estadistiquesCCC" href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-help',plain:true"  plain="true" onclick="estadistiquesCCC()">Dades estad&iacute;stiques</a>        
        <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="enviarSMS()">
        <img src="./images/sms.png" height="20" align="absbottom" />&nbsp;Enviar SMS</a>&nbsp;
        <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" onclick="enviarCorreu()">
        <img src="./images/email.png" height="20" align="absbottom" />&nbsp;Enviar Correu</a>
        
        <img src="./images/line.png" height="1" width="100%" align="absmiddle" /> 
        <img src="./images/block_green.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;En curs
        <img src="./images/block_yellow.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Per comen&ccedil;ar
        <img src="./images/block_red.png" width="25" height="15" style="border:1px dashed #7da949" />&nbsp;Per establir sanci&oacute;
    </div>
    </div>
    
	<div id="dlg_sancio" class="easyui-dialog" style=" padding-left:5px; padding-top:5px; width:900px;height:600px;"  
            closed="true" maximized="true" maximizable="true" collapsible="true" resizable="true" modal="true" buttons="#dlg_sancio-buttons">
        	<form id="fm_sancio" method="post" novalidate>
            <div>  
            	<label style="font-size:16px; font-weight:bolder">Implica expulsi&oacute; de classe?</label>
            	<input id="expulsio" name="expulsio" type="checkbox" value="S">
                <label style="color:#666666">(marcar aquesta opci&oacute; si es treu l'alumne de classe)</label>
            	<br /><br />
                
                <label style="width:150px; color:#666666">Data incid&egrave;ncia</label>
                <input id="data_incident" name="data_incident" class="easyui-datebox" data-options="required: true,formatter:myformatter,parser:myparser">
            	&nbsp;&nbsp;&nbsp;
                <label style="width:150px; color:#666666">Tipus d'incid&egrave;ncia</label>
                <select id="id_falta" name="id_falta" class="easyui-combobox" data-options="
                    required: true,
                    width:120,
                    url:'./ccc_tipus/ccc_tipus_getdata.php',
                    idField:'idccc_tipus',
                    valueField:'idccc_tipus',
                    textField:'nom_falta',
                    panelHeight:'auto'
                ">
                </select>
                <br /><br />
                
                <label style="width:150px; color:#666666">Grup</label>
                <input id="idgrup" name="idgrup" class="easyui-combobox" style="width:220px" data-options="
                    required: false,
                    panelWidth: 220,
                    idField: 'idgrups',
                    textField: 'nom',
                ">
                
                <label style="width:150px; color:#666666">Mat&egrave;ria</label>
                <input id="idmateria" name="idmateria" class="easyui-combobox" style="width:350px" data-options="
                    required: false,
                    panelWidth: 350,
                    idField: 'id',
                    textField: 'nom',
                ">
                
                <label style="width:150px; color:#666666">Espai</label>
                <input id="idespais" name="idespais" class="easyui-combobox" style="width:220px" data-options="
                    required: false,
                    panelWidth: 220,
                    idField: 'idespais_centre',
                    textField: 'descripcio',
                ">
                
                <br /><br />
                <label style="width:150px; color:#666666">Motiu</label>&nbsp;  
                <select id="id_motius" name="id_motius" class="easyui-combogrid" style="width:880px" data-options="
                    required: false,
                    panelWidth: 880,
                    idField: 'idccc_motius',
                    textField: 'nom_motiu',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'nom_motiu',title:'',width:880}
                    ]],
                    fitColumns: true
                ">
                </select>
                <br /><br />
                <label style="width:150px; color:#666666">Fets que s'han produ&iuml;t</label><br /> 
                <textarea name="descripcio_detallada" style="height:210px; width:910px;"></textarea>
            </div>
            <br />
            <div class="fitem">
                <label style="width:150px; color:#666666">Tipus de sanci&oacute;</label>
                <input id="id_tipus_sancio" name="id_tipus_sancio" class="easyui-combobox" data-options="
                required: true,
                width: 450,
                valueField: 'idccc_tipus_mesura',
                textField: 'ccc_nom',
                url: './ccc_adm/mesures_getdata.php'
                ">
            </div>
            
            <div class="fitem">
                <label style="width:100px;color:#666666">Termini de la sanci&oacute;</label><br />
                <label style="color:#666666">Desde:</label><input id="data_inici_sancio" name="data_inici_sancio" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        		<label style="color:#666666">Fins a:</label><input id="data_fi_sancio" name="data_fi_sancio" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
            </div>
        	</form>
    </div>
        
    <div id="dlg_sancio-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td align="left">
                	<!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" onclick="imprimirCCC()">Imprimir</a>-->
                </td>
                <td align="right">
                    <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" iconCls="icon-ok" onclick="saveSancio()">Acceptar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" iconCls="icon-cancel" onclick="javascript:$('#dlg_sancio').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_nova_ccc" class="easyui-dialog" style=" padding-left:5px; padding-top:5px; width:900px;height:600px;"  
            closed="true" collapsible="true" resizable="true" maximized="true" maximizable="true" modal="true" buttons="#dlg_nova_ccc-buttons">
              	<form id="fm_nova_ccc" method="post" novalidate>
                <input type="hidden" name="id_tipus_sancio" value="0" />
            	<input type="hidden" name="data_inici_sancio" value="" />
            	<input type="hidden" name="data_fi_sancio" value="" />
            
            	<label style="font-size:16px; font-weight:bolder">Implica expulsi&oacute; de classe?</label>
            	<input id="expulsio" name="expulsio" type="checkbox" value="S">
            	<label style="color:#666666">(marcar aquesta opci&oacute; si es treu l'alumne de classe)</label>
            	<br /><br />
              	
                <label style="color:#666666">Data incid&egrave;ncia</label>
            	<input id="data_incident" name="data_incident" class="easyui-datebox" data-options="required: true,formatter:myformatter,parser:myparser">
            	&nbsp;&nbsp;
            	<label style="color:#666666">Tipus d'incid&egrave;ncia</label>
            	<select id="id_falta" name="id_falta" class="easyui-combobox" data-options="
					required: true,
                    width:120,
                    url:'./ccc_tipus/ccc_tipus_getdata.php',
					idField:'idccc_tipus',
                    valueField:'idccc_tipus',
					textField:'nom_falta',
					panelHeight:'auto'
                 ">
             	</select>
             	&nbsp;&nbsp;
             
            	<label style="width:150px; color:#666666">Professor</label>
                <input id="idprofessor" name="idprofessor" class="easyui-combobox" data-options="
                required: true,
                width: 350,
                valueField: 'id_professor',
                textField: 'Valor',
                url: './ccc_adm/prof_getdata.php'
                ">
                <br /><br />
                
                <label style="width:150px; color:#666666">Horari professor (si els fets s'han produ&iuml;t dintre d'alguna classe, sisplau indiqueu-la)</label><br />
                <select id="idunitats_classe" name="idunitats_classe" class="easyui-combogrid" style="width:860px" data-options="
                    required: false,
                    panelWidth: 860,
                    idField: 'idunitats_classe',
                    textField: 'nom_materia',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'dia_hora',title:'Dia/Hora',width:200},
                        {field:'nom_materia',title:'Materia',width:400},
                        {field:'grup',title:'Grup',width:190},
                        {field:'descripcio',title:'Espai',width:70}
                    ]],
                    fitColumns: true
                ">
                </select>
                <br /><br />
                
                <label style="width:150px; color:#666666">Alumne</label>
                <input id="nomAlumne" name="nomAlumne" size="60" />
                <input type="hidden" id="idalumne" name="idalumne" />
                <br />              
                
                <label style="width:150px; color:#666666">Motiu</label><br />
                <select id="id_motius_nova" name="id_motius_nova" class="easyui-combogrid" style="width:840px" data-options="
                    required: false,
                    panelWidth: 840,
                    idField: 'idccc_motius',
                    textField: 'nom_motiu',
                    url: url,
                    method: 'get',
                    columns: [[
                        {field:'nom_motiu',title:'',width:840}
                    ]],
                    fitColumns: true
                ">
                </select>
                <br /><br />
                
                <label style="width:150px; color:#666666">Fets que s'han produ&iuml;t</label><br />  
                <textarea name="descripcio_detallada" style="height:150px; width:940px;"></textarea>
         		<br /><br />
                
                <!--<label style="width:150px; color:#666666">Tipus de sanci&oacute;</label>
                <input id="id_tipus_sancio" name="id_tipus_sancio" class="easyui-combobox" data-options="
                required: true,
                width: 250,
                valueField: 'idccc_tipus_mesura',
                textField: 'ccc_nom',
                url: './ccc_adm/mesures_getdata.php'
                ">
                
                <br /><br />
                <label style="color:#666666">Termini de la sanci&oacute;</label><br />
                <label style="color:#666666">Desde:</label><input id="data_inici_sancio" name="data_inici_sancio" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
        		<label style="color:#666666">Fins a:</label><input id="data_fi_sancio" name="data_fi_sancio" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>
                -->
                
        	</form>
    </div>
        
    <div id="dlg_nova_ccc-buttons">
        <table cellpadding="0" cellspacing="0" style="width:100%">  
            <tr>  
                <td>
                    <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" iconCls="icon-ok" onclick="saveCCC()">Acceptar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" plain="true" iconCls="icon-cancel" onclick="javascript:$('#dlg_nova_ccc').dialog('close')">Cancel.lar</a>
                </td>
            </tr>  
        </table>  
    </div>
    
    <div id="dlg_inf" class="easyui-dialog" style="width:900px;height:600px;" data-options="maximized:true,maximizable:true,closed:true,resizable:true,modal:true"   
            toolbar="#dlg_inf-toolbar">
    </div>
    
    <div id="dlg_inf-toolbar">
    <table cellpadding="0" cellspacing="0" style="width:100%;">  
        <tr>  
            <td>
                <form id="fm_informes" method="post">
                Desde: <input id="data_inici_informe" name="data_inici_informe" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;
        		Fins a: <input id="data_fi_informe" name="data_fi_informe" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;<br />
				<img src="./images/line.png" height="1" width="100%" align="absmiddle" /> 
                Criteri:
                <select id="criteri" name="criteri" class="easyui-combobox">
                    <option value="CAP"></option>
                    <option value="idgrup">Grup</option>
                    <option value="idalumne">Alumne</option>
                    <option value="idprofessor">Professor</option>
                </select>
                
                &nbsp;Valor: 
                <input id="valor_criteri" name="valor_criteri" class="easyui-combobox" style="width:525px" data-options="
                	required: false,
                    panelWidth: 525,
                    idField: 'idgrups',
                    textField: 'nom',
                    url: './grma/grup_getdata.php'
                ">
               
                </form>
                <img src="./images/block_blue.png" height="1" width="100%" align="absmiddle" /> 
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="doReload()">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="javascript:imprimirInforme()">Imprimir</a>  
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_inf').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>
    
    <div id="dlg_est" class="easyui-dialog" style="width:900px;height:600px;" data-options="maximized:true,maximizable:true,closed:true,resizable:true,modal:true"   
            toolbar="#dlg_est-toolbar">
    </div>
    
    <div id="dlg_est-toolbar">
    <table cellpadding="0" cellspacing="0" style="width:100%;">  
        <tr>  
            <td>
                <form id="fm_estadistiques" method="post">
                Desde: <input id="data_inici_estad" name="data_inici_estad" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;
        		Fins a: <input id="data_fi_estad" name="data_fi_estad" class="easyui-datebox" data-options="formatter:myformatter,parser:myparser"></input>&nbsp;<br />
				<img src="./images/line.png" height="1" width="100%" align="absmiddle" /> 
                Criteri:
                <select id="criteri_estad" name="criteri_estad" class="easyui-combobox">
                    <option value="CAP"></option>
                    <option value="idgrup">Grup</option>
                    <option value="idalumne">Alumne</option>
                    <option value="idprofessor">Professor</option>
                </select>
                
                &nbsp;Valor: 
                <input id="valor_criteri_estad" name="valor_criteri_estad" class="easyui-combobox" style="width:525px" data-options="
                	required: false,
                    panelWidth: 525,
                    idField: 'idgrups',
                    textField: 'nom',
                    url: './grma/grup_getdata.php'
                ">
                
                &nbsp;Subcriteri:
                <select id="sub_criteri_estad" name="sub_criteri_estad" class="easyui-combobox">
                    <option value="idgrup"></option>
                    <option value="idgrup">Grup</option>
                    <option value="idmateria">Mat&egrave;ria</option>
                    <option value="idalumne">Alumne</option>
                    <option value="idprofessor">Professor</option>
                    <option value="id_falta">Tipus</option>
                    <option value="id_tipus_sancio">Sanci&oacute;</option>
                </select>
                
                </form>
                <img src="./images/block_blue.png" height="1" width="100%" align="absmiddle" /> 
                <a href="#" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="doReload_estad()">Recarregar</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="javascript:imprimirDadesEstad()">Imprimir</a>  
                <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="javascript:$('#dlg_est').dialog('close')">Tancar</a>  
            </td>
        </tr>  
    </table>  
    </div>

    <div id="dlg_sms" class="easyui-dialog" style="width:900px;height:600px;"  
            closed="true" collapsible="true" maximized="true" maximizable="true" resizable="true" modal="true" buttons="#dlg_sms-toolbar">  
    </div>
        
    <div id="dlg_sms-toolbar">  
         <a href="#" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="tancar()">Tancar</a>  
    </div>

    <iframe id="fitxer_pdf" scrolling="yes" frameborder="0" style="width:10px;height:10px; visibility:hidden" src=""></iframe>
    
    <script type="text/javascript">  
        var url;
		var index_combo;
		var valor_combo;
		var editIndex    = undefined;
		var nou_registre = 0;
		var today        = new Date();
		var data_inici_sancio;
		var data_fi_sancio;
		var criteri       = 0;
		
                var options_alum = {
                        url: "./almat_tree/alum_getdata.php",

                        getValue: "alumne",

                        list: {
                                match: {
                                        enabled: true
                                },
                                
                                onSelectItemEvent: function() {
                                        var value = $("#nomAlumne").getSelectedItemData().id_alumne;
                                        $("#idalumne").val(value).trigger("change");
                                }
                        }
                };

                $("#nomAlumne").easyAutocomplete(options_alum);
                
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
		
		$('#idprofessor').combobox({
			onSelect: function(date){			
				$('#idunitats_classe').combogrid({
					url:'./ccc_adm/horari_getdata.php?idprofessors='+$('#idprofessor').combobox('getValue'),
					valueField:'idunitats_classe',
					textField:'nom_materia'
				});
			}
		});
				
		$('#id_motius').combogrid({
			url:'./ccc/motius_getdata.php',
			valueField:'idccc_motius',
			textField:'nom_motiu',
			editable:false
		});
		
		$('#id_motius_nova').combogrid({
			url:'./ccc/motius_getdata.php',
			valueField:'idccc_motius',
			textField:'nom_motiu',
			editable:false
		});
		
		$('#idgrup').combobox({
			url:'./grma/grup_getdata.php',
			valueField:'idgrups',
			textField:'nom'
		});
				
		$('#idgrup').combobox({
			onSelect: function(date){			
				$('#idmateria').combobox({
					url:'./ccc_adm/mat_getdata.php?idgrup='+$('#idgrup').combobox('getValue'),
					valueField:'id_mat_uf_pla',
					textField:'nom'
				});
			}
		});
				
		$('#idespais').combobox({
			url:'./hormod/ec_getdata.php',
			valueField:'idespais_centre',
			textField:'descripcio'
		});
		

		$('#criteri').combobox({
			onSelect: function(date){	
			    criteri = $('#criteri').combobox('getValue');
				switch (criteri) {
					case 'idgrup':
						url         = './grma/grup_getdata.php' ;
						index_combo = 'idgrups' ;
						valor_combo = 'nom' ;
						break;
					case 'idmateria':
						url         = './grma/mat_getdata.php' ;
						index_combo = 'id' ;
						valor_combo = 'nom' ;
						break;
					case 'idalumne':
						url         = './ccc/alum_getdata.php' ;
						index_combo = 'id_alumne' ;
						valor_combo = 'alumne' ;
						break;
					case 'idprofessor':
						url         = './ccc_adm/prof_getdata.php' ;
						index_combo = 'id_professor' ;
						valor_combo = 'Valor' ;
						break;
					case 'id_falta':
						url         = './ccc_adm/tipus_getdata.php' ;
						index_combo = 'idccc_tipus' ;
						valor_combo = 'nom_falta' ;
						break;
					case 'id_tipus_sancio':
						url         = './ccc_adm/mesures_getdata.php' ;
						index_combo = 'idccc_tipus_mesura' ;
						valor_combo = 'ccc_nom' ;
						break;
				} 
		
				$('#valor_criteri').combobox({
					url:url,
					valueField:index_combo,
					textField:valor_combo
				});
			}
		});
		
		$('#criteri_estad').combobox({
			onSelect: function(date){	
			    criteri = $('#criteri_estad').combobox('getValue');
				switch (criteri) {
					case 'idgrup':
						url         = './grma/grup_getdata.php' ;
						index_combo = 'idgrups' ;
						valor_combo = 'nom' ;
						break;
					case 'idmateria':
						url         = './grma/mat_getdata.php' ;
						index_combo = 'id' ;
						valor_combo = 'nom' ;
						break;
					case 'idalumne':
						url         = './ccc/alum_getdata.php' ;
						index_combo = 'id_alumne' ;
						valor_combo = 'alumne' ;
						break;
					case 'idprofessor':
						url         = './ccc_adm/prof_getdata.php' ;
						index_combo = 'id_professor' ;
						valor_combo = 'Valor' ;
						break;
					case 'id_falta':
						url         = './ccc_adm/tipus_getdata.php' ;
						index_combo = 'idccc_tipus' ;
						valor_combo = 'nom_falta' ;
						break;
					case 'id_tipus_sancio':
						url         = './ccc_adm/mesures_getdata.php' ;
						index_combo = 'idccc_tipus_mesura' ;
						valor_combo = 'ccc_nom' ;
						break;
				} 
		
				$('#valor_criteri_estad').combobox({
					url:url,
					valueField:index_combo,
					textField:valor_combo
				});
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
						href:'./ccc_adm/ccc_adm_getdetail.php?id='+row.idccc_taula_principal,
						onLoad:function(){
							$('#dg').datagrid('fixDetailRowHeight',index);
						}
					});
					$('#dg').datagrid('fixDetailRowHeight',index);
				},
				rowStyler:function(index,row){
				    /*data_inici_sancio = new Date(row.data_inici_sancio);
					data_fi_sancio = new Date(row.data_fi_sancio);
					if (row.id_tipus_sancio === null) {
						return 'background-color:whitesmoke;color:#be0f34;font-weight:bold;';
					}
					if (today>data_fi_sancio){
						return 'background-color:whitesmoke;color:#666;';
					}
					if (today<data_inici_sancio){
						return 'background-color:whitesmoke;color:#af8900;font-weight:bold;';
					}
					if (today>=data_inici_sancio && today<=data_fi_sancio ){
						return 'background-color:#a1d88b;color:#009a49;font-weight:bold;';
					}*/
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
			//criteri       = $('input[name=criteri]:checked', '#fm_informes').val();
			criteri       = $('#criteri').combobox('getValue');			
			valor_criteri = $('#valor_criteri').combobox('getValue');
			
			url = './ccc_adm/ccc_adm_inf.php?criteri='+criteri+'&valor_criteri='+valor_criteri+'&data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#dlg_inf').dialog('refresh', url);
		} 
		
		function doReload_estad(){
			d_inici       = $('#data_inici_estad').datebox('getValue');
			d_fi          = $('#data_fi_estad').datebox('getValue');
			criteri       = $('#criteri_estad').combobox('getValue');
			valor_criteri = $('#valor_criteri_estad').combobox('getValue');
			sub_criteri   = $('#sub_criteri_estad').combobox('getValue');
			
			url = './ccc_adm/ccc_adm_see.php?criteri='+criteri+'&valor_criteri='+valor_criteri+'&sub_criteri='+sub_criteri+'&data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#dlg_est').dialog('refresh', url);
		} 
		
		function informesCCC(){  
			url = './ccc_adm/ccc_adm_inf.php';
			$('#dlg_inf').dialog('open').dialog('setTitle','Informe CCC');
			$('#dlg_inf').dialog('refresh', url);
    	}
		
		function estadistiquesCCC(){  
			url = './ccc_adm/ccc_adm_see.php';
			$('#dlg_est').dialog('open').dialog('setTitle','Dades estad&iacute;stiques CCC');
			$('#dlg_est').dialog('refresh', url);
    	}
	
		function imprimirInforme(){  
			d_inici  = $('#data_inici_informe').datebox('getValue');
			d_fi     = $('#data_fi_informe').datebox('getValue');
			//criteri  = $('input[name=criteri]:checked', '#fm_informes').val();
			criteri       = $('#criteri').combobox('getValue');
			valor_criteri = $('#valor_criteri').combobox('getValue');
		
			url = './ccc_adm/ccc_adm_print_inf.php?criteri='+criteri+'&valor_criteri='+valor_criteri+'&data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#fitxer_pdf').attr('src', url);
    	}
		
		function imprimirDadesEstad(){  
			d_inici  = $('#data_inici_informe_estad').datebox('getValue');
			d_fi     = $('#data_fi_informe_estad').datebox('getValue');
			//criteri  = $('input[name=criteri]:checked', '#fm_informes').val();
			criteri       = $('#criteri_estad').combobox('getValue');
			valor_criteri = $('#valor_criteri_estad').combobox('getValue');
			sub_criteri   = $('#sub_criteri_estad').combobox('getValue');
		
			url = './ccc_adm/ccc_adm_print.php?criteri='+criteri+'&valor_criteri='+valor_criteri+'&sub_criteri='+sub_criteri+'&data_inici='+d_inici+'&data_fi='+d_fi;
			
			$('#fitxer_pdf').attr('src', url);
    	}
	
		function gestioSancio(){
            var row = $('#dg').datagrid('getSelected');
            if (row){
				$('#idmateria').combobox({
					url:'./ccc_adm/mat_getdata.php?idgrup='+row.idgrup,
					valueField:'id_mat_uf_pla',
					textField:'nom'
				});
				
                $('#dlg_sancio').dialog('open').dialog('setTitle','Editar CCC');
				$('#fm_sancio').form('load','./ccc_adm/ccc_adm_load.php?id='+row.idccc_taula_principal);				
				
				url = './ccc_adm/ccc_adm_edita.php?action=UPDATE&id='+row.idccc_taula_principal;
            }
        }

        function saveSancio(){		
			$('#fm_sancio').form('submit',{
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
						
						$('#gestio_sancio').linkbutton('disable');
						$('#esborrar_ccc').linkbutton('disable');
						$('#imprimir_ccc').linkbutton('disable');
                    }
                }
            });
        }
		
		function novaCCC(){  
           $('#dlg_nova_ccc').dialog('open').dialog('setTitle','Nova CCC');
		   $('#fm_nova_ccc').form('load','./ccc_adm/ccc_adm_load.php?id=0');				
				
		   url = './ccc_adm/ccc_adm_edita.php?id=0';
        }
		
		function saveCCC(){		
			$('#fm_nova_ccc').form('submit',{
                url: url,
                onSubmit: function(){
                    return $(this).form('validate');
                },
                success: function(result){
					var result = eval('('+result+')');
                    if (result.errorMsg){
                        $.messager.show({
                            title: 'Error',
                            msg: result.errorMsg
                        });
                    } else {
                        $('#dlg_nova_ccc').dialog('close');     
                        $('#dg').datagrid('reload'); 
						editIndex = undefined;
						
						$('#gestio_sancio').linkbutton('disable');
						$('#esborrar_ccc').linkbutton('disable');
						$('#imprimir_ccc').linkbutton('disable');
                    }
                }
            });
        }
		
		function esborrarCCC(){ 
		  var row = $('#dg').datagrid('getSelected');
		  
		  if (row){ 
			   url = './ccc_adm/ccc_adm_esborra.php';
			   
			   $.messager.confirm('Confirmar','Esborrem aquesta CCC?',function(r){  
                    if (r){  
                        $.post(url,{
								id:row.idccc_taula_principal},function(result){  
                            if (result.success){  
                                   $.messager.alert('Informaci&oacute;','CCC esborrada correctament!','info');
								   $('#dg').datagrid('reload');
								   editIndex = undefined;
								   $('#gestio_sancio').linkbutton('disable');
								   $('#informe_alumne').linkbutton('disable');
								   $('#esborrar_ccc').linkbutton('disable');
								   $('#imprimir_ccc').linkbutton('disable');
                            } else { 
							    $.messager.alert('Error','CCC esborrada erroniament!','error');
								 
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
		
		function imprimirCCC(){  
			var row = $('#dg').datagrid('getSelected');
		    if (row) {
				url = './ccc_adm/ccc_adm_printCCC.php?id='+row.idccc_taula_principal;
				
				$('#fitxer_pdf').attr('src', url);
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
				$('#esborrar_ccc').linkbutton('enable');
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
		
                function enviarSMS(){
                    var data ='--';
                    url = './conserge/conserge_sms.php?adm=1&data='+data;
                    $('#dlg_sms').dialog('open').dialog('setTitle','Enviar SMS');
                    $('#dlg_sms').dialog('refresh', url);
		}
		
		function enviarCorreu(){  
                    var data ='--';
                    url = './conserge/conserge_email.php?adm=1&data='+data;
                    $('#dlg_sms').dialog('open').dialog('setTitle','Enviar Correu');
                    $('#dlg_sms').dialog('refresh', url);
		}
                
                function tancar() {
        	    javascript:$('#dlg_sms').dialog('close');
                    open1('./ccc_adm/ccc_adm_grid.php');
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