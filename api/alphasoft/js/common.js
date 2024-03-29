/**
 * Controle do Plugin JQuery Layout
 * By Marcelo Santos - Alpha Soft
 * Build in 17/12/2014
 * 
 * Commum a todos
 * 
 * API
 * @link http://layout.jquery-dev.com/
 */

var IS_TRACE		=	false;
var IS_EXCEPTION	=	false;
var TIME_CHECK_USER_CONECT		=	1;


function WRSChangeTheme()
{
	TRACE('Mudando o Thema na função WRSChangeTheme  no file:wrs_main.php');
	var theme	=	$(this).val();
	var param	=	{'theme':theme};
	
	runCall(param,'WRS_MAIN','WRS_MAIN','updateTheme');

	var host 	= 	$('#themeHost').attr('host');
		host	=	str_replace('{host}',theme,host); 

	var host_css	=	$('.changeStyleCSS').attr('host');
		host_css	=	str_replace('{host}',theme,host_css); 
	
		$('#themeHost').attr('href',host);
		
		$('.changeStyleCSS').attr('href',host_css);
		
	//$("#switch_style").attr("href", id + ".css"); 
	TRACE('Finalizaou WRSChangeTheme');	
}


function WRSChangeIdioma()
{
	TRACE('Mudando o Idioma na função WRSChangeIdioma  no file:wrs_main.php');
	var idioma	=	$(this).val();
	$("#fakeloader").show();
	var param	=	{'idioma':idioma};
	console.log('change');
	runCall(param,'WRS_MAIN','WRS_MAIN','updateIdioma',function(data){ location.reload();});
	//$("#switch_style").attr("href", id + ".css"); 
	TRACE('Finalizou WRSChangeIdioma');	
}



function filter_mouse_hover_details()
{
	_ONLY('filter_mouse_hover_details');
	if($('.tooltip_info_wrs_panel_details').length>0)
	{
		if($('.tooltip_info_wrs_panel_details').is(':hidden')==false)
		{
			$('.tooltip_info_wrs_panel').trigger('mouseout').trigger('mouseover');
		}
	}
	
}

//HERE
//@link http://www.telerik.com/forums/grid-filtering-in-javascript---not-equal-to-null
function setKNewFilter()
{
	_ONLY('setKNewFilter');
	
	var grid = $('#A162').data('kendoGrid');
		$filter = new Array();
   	$filter.push({ field: "C002", operator: "neq", value: 15 });
	grid.dataSource.filter($filter);
}


/**
 * 	Index do FilterFindType
 */
function rFFTIndex(label)
{
	_ONLY('rFFTIndex');
	var _tmp	=	{};
	for(var line in label) _tmp[label[line].field]	=	label[line].type;
	return _tmp;
}


/**
 * Recursividade para pegar apenas os types das coluns
 */
function recursiveFilterFindType(types,deep)
{

	for(var line in deep)
	{
		if(typeof deep[line]=='object')
		{
			deep[line]	=	recursiveFilterFindType(types,deep[line]);
		}

		if(typeof deep[line].field!='undefined')
		{
			var _type		=	'number';
			
			try{
				if(types[deep[line].field].wrsParam.FORMAT_STRING	=="Percent")
					{
						_type	=	'percent';
					}
			}catch(e){
				console.warn(' exception');
			}
			
				deep[line]['type']	=	_type;//types[deep[line].field];
		}
	}
	return deep;	
}




/**
 * Obtendo os atributos do filtro KendoUi para Salvar
 * 
 * Chamando a função 
 * 
 * json_encode(getFiltersKendoUiSave($('#A162').data('kendoGrid').headerIndex.field,json_decode(json_encode($('#A162').data('kendoGrid').dataSource.filter()))))
 * 
 * @param types
 * @param deep
 * @returns array
 */
function getFiltersKendoUiSave(types,deep)
{
	_ONLY('getFiltersKendoUiSave');
	for(var line in deep)
	{
		
		if(typeof deep[line]=='object')
		{
			deep[line]	=	getFiltersKendoUiSave(types,deep[line]);
		}
		
		if(typeof deep[line]['type']!='undefined') delete deep[line]['type'];

		if(typeof deep[line].field!='undefined')
		{
			var _field	=	deep[line].field;
			
			deep[line]['field_old']	=	_field;
			
			try
			{
				deep[line].field	=	types[deep[line].field].LEVEL_DRILL;
			}catch(e){
				delete deep[line];
			}
		}
	}
	
	
	return deep;	
}


function merge_all(first,second)
{
	var _first	=	first;
	
	for(var line in second)
	{
		_first.push(second[line]);
	}
	
	return _first;
}

/*
 * QUery
 */

function makeFiltersKendoUiQuery(types,deep,logic)
{
	_ONLY('getFiltersKendoUiSave');
	
	var _filter				=	[];
	var optionsFilter		=	$('body').WrsGlobal('getCM','getOPerationsFilter');
	var options				=	['%c','#v'];
	var clean_filter		=	$('body').WrsGlobal('getCM','cleanMakeFiltersKendoUiQuery');
	
	var _logic	=	' and ';
	
	if(!isEmpty(logic)) _logic	=	logic;
	
	for(var line in deep)
	{
		var _op		=	{};
		
		if(deep[line].type=='percent')
		{
			_op		=	optionsFilter.number;
		}else{
			_op		=	optionsFilter[deep[line].type];
		}
		
				
		if(typeof deep[line]=='object')
		{
			
			var _make	=	makeFiltersKendoUiQuery(types,deep[line],deep[line].logic);
			
			var _data	=	implode(_span(_logic.toUpperCase(),'#8a2d24'),_make);
			
			if(!isEmpty(_data))
			{
				_filter.push(_data);
			}
			
		}
		
		if(typeof deep[line].field!='undefined')
		{
				deep[line].field	=	types[deep[line].field].LEVEL_DRILL;
				var _br				=	'';
				
				if(in_array(deep[line].field,clean_filter))
				{
					deep[line].field	=	'';
				}
				else
				{
					if(clean_filter.length>=1)
					{
						_br	=	'<br>';
					}
					
					clean_filter.push(deep[line].field);
					
					$('body').WrsGlobal('setCM',{cleanMakeFiltersKendoUiQuery:clean_filter});
				}
				
			var _query		=	 _br+str_replace(options,[deep[line].field, _span(deep[line].value,'#03be80') ],_op[deep[line].operator])+'  ';
			
				_filter.push(_query);
		}
		
	}
	
 
	return _filter;	
}

function _span(string,color)
{
	return '<span style="color:'+color+'"><strong>'+string+'</strong></span>';
}


/**
 * Save in History
 * @param report
 * @param kendoUiFilters
 * @returns
 * 
 */
function changeFiltersKendoUi(report,kendoUiFilters)
{
	_ONLY('changeFiltersKendoUi');
	var kendoUI		=	 $('#'+report).data('kendoGrid');
	
	if(kendoUI.headerIndex==undefined || kendoUI.headerIndex=='undefined')
	{
		kendoUI.headerIndex	=	WRSHeaderIndex(kendoUI);
	}

	var filters		=	json_encode(getFiltersKendoUiSave(kendoUI.headerIndex.field,kendoUiFilters));
	var history_data=	$('#'+report+'Main').find('.wrs_history_report li:first-child a');
	
	$('.'+report).wrsAbaData('setWrsData',{REPORT_FILTER:filters});
	
	
	var history						=	json_decode(base64_decode(history_data.attr('json')));

		if(!isEmpty(history))
		{
			history['REPORT_FILTER']	=	filters;
			
			history_data.attr('json',base64_encode(json_encode(history)))
		
		}

		
	return filters;
}

function findFieldLevelDrill(param,field)
{
	for(var line in param)
	{
		if(param[line].LEVEL_DRILL==field) return line;
	}
	
	return null;
}

/**
 * Converte o json para o atributo de elemento
 * 
 *  setFiltersKendoUiDecode($('#A162').data('kendoGrid').headerIndex.field,json)
 *  
 *  
 *  
 *	setFiltersKendoUiDecode($('#A162').data('kendoGrid').headerIndex.field,json_decode('{"filters":[{"field":"[Ano][2010][Semestre][201102][Dolar]","operator":"eq","value":"1"},{"field":"[Ano][2010][Semestre][201102][Dolar]","operator":"eq","value":"2"},{"logic":"and","filters":[{"field":"[Ano][2011][Semestre][201102][Dolar]","operator":"eq","value":"2"},{"field":"[Ano][2011][Semestre][201102][Dolar]","operator":"eq","value":"3"}]},{"logic":"and","filters":[{"field":"[Ano][2011][Semestre][201108][Dolar]","operator":"eq","value":"4"},{"field":"[Ano][2011][Semestre][201108][Dolar]","operator":"eq","value":"5"}]}],"logic":"and"}'))
 *  
 *  
 *  $('#A162').data('kendoGrid').dataSource.filter(setFiltersKendoUiDecode($('#A162').data('kendoGrid').headerIndex.field,json_decode('{"filters":[{"field":"[Ano][2010][Semestre][201102][Dolar]","operator":"eq","value":"1"},{"field":"[Ano][2010][Semestre][201102][Dolar]","operator":"eq","value":"2"},{"logic":"and","filters":[{"field":"[Ano][2011][Semestre][201102][Dolar]","operator":"eq","value":"2"},{"field":"[Ano][2011][Semestre][201102][Dolar]","operator":"eq","value":"3"}]},{"logic":"and","filters":[{"field":"[Ano][2011][Semestre][201108][Dolar]","operator":"eq","value":"4"},{"field":"[Ano][2011][Semestre][201108][Dolar]","operator":"eq","value":"5"}]}],"logic":"and"}')))
 *  
 * @param types
 * @param deep
 * @returns {___anonymous2497_2498}
 */
function setFiltersKendoUiDecode(types,deep)
{

	for(var line in deep)
	{
		
		if(typeof deep[line]=='object')
		{
			deep[line]	=	setFiltersKendoUiDecode(types,deep[line]);
		}

		if(typeof deep[line].field!='undefined')
		{
			
			var _field		=	 findFieldLevelDrill(types,deep[line].field)
			var _filter_old	=	deep[line].field;
			
			if(_field!=null){
				deep[line].field			=	_field;
			}else{
				delete deep[line];
			}			
		}
	}
	
	return deep;	
}



function wrs_logout()
 {
	
	_ONLY('wrs_logout');


	var aba_data	=	abas_to_save()
	
	if(aba_data.type==true)
	{
		var godbay = confirm(LNG('NOT_CLOSE_WINDOW_LOGOUT')+aba_data.data);
	
		if(godbay==true)
			{
				SYSTEM_OFF	=	true;
			}else{
				return false;
			}
	}
	
	
	
	 $('.spinner6').width(400).prepend(LNG('LOGOUT')).resize();
		$("#fakeloader").show();
		
	
		setTimeout(function(){
			window.location	=	"run.php?file=WRS_MAIN&class=WRS_MAIN&event=logout";
		},250);
		
 }


function foreach(array,type){


		
		TRACE('---foreach--------');
		
		if(!is_array(array))TRACE('STRING::|::'+array);
		for(obj in array)
		{
			if(empty(type)){
				TRACE_DEBUG(obj+'   ||   '+array[obj]);
			}else{
				
				TRACE_DEBUG(obj+':');
			}
		}
		
			IS_TRACE	=	false;
}



var words_restrict	=	
{
		other			: 	'*Others*',
		filter_negado	:	"{EXCEPT({LEVEL_FULL}.Members,{{LEVEL_FULL}.[(All)],{DATA}})}", //{LEVEL_FULL} {DATA}
		virgula_db		:	"(_,_)",
		pipe_db			:	"(_|_)"	,
		negacao			:	"~",
		simples			:	"*"

};

function quebra_carachteres_especiais()
{
	var caracter	='"!@#$%¨&*()|<>:?}^{`/*-+.\,.;/]~[´=-]}"'+"'";
	var _tmp_array	=	[];
	
	for(var i=0; i<caracter.length;i++)
		{
		
			_tmp_array.push(caracter[i]);
		}
	return _tmp_array;
	
}

function retiraAcento(palavra){
    var com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~';  
    var sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC     ';
    for (l in palavra){
        for (l2 in com_acento){
            if (palavra[l] == com_acento[l2]){
                palavra=palavra.replace(palavra[l],sem_acento[l2]);
    		}
        }
    }
    return palavra;
}

var global_especial_caracteres		=	quebra_carachteres_especiais();



//'|',',',"\",'/','*','=','.',':','?','}','{',')','(','>','<','!','@','#','$','%','¨','&','|','<','>',':','?','^','}','Ç','`','{','\',',','.',';','/',']','~','[','´','-','+'

//funcao recursiva para encontrar o pai
function find_parent_class(object,_class)
{
	if(object.hasClass(_class)==true) return object;
	
	return find_parent_class(object.parent(),_class);
}



var RAND_TOKEN	=	js_rand(0,9999999999999);


	
if(IS_TRACE)
	{
			$('body').append('<div class="WRS_TRACE"></div>');
			$('.WRS_TRACE').dblclick(function() {$(this).html('');});
	}
	

/**
 * Gerenciamento de perfil
 * @param perfilTest
 * @returns
 */
function getArrPerfUser(perfilTest)
{
	return in_array(perfilTest,PERFIL_ID_USER);
}

/*
 * TODO:Não utilizado apenas para clone de exemplos modo debug apenas
 * ele é executado pelo console
 */
function clone_jj(report)
{
	var _jj				=	getJsonDecodeBase64(jj);
	var _report			=	_jj.kendoUi.REPORT_ID;
	var _Report_atual	=	$('.WRS_ABA .'+report).attr('id-aba');
	console.log('_jj',_jj);
	$('.WRS_ABA .'+report).data('wrs_aba_data',_jj).attr('id-aba',_report).addClass(_report);
}



// substitui o LNG trocando os %s por s se existirem
function LNG_s(str,_char,_recursiva)
{
	var char 		= _char==undefined?'s':_char;
	var recursiva 	= _recursiva==undefined?false:true;
	var string 		= recursiva?str:LNG(str);
	if(string.indexOf('%s')>0){
		return LNG_s(string.replace('%s',char),char,true);
	}else{
		return string;
	}
}



/*
 * Tipos de Execuções que o sistema opera para gerar uma novo Report
 */
var TYPE_RUN	=	{
						direct				:	'RunFiltro',
						options				:	'Options',
						reorden_column		:	'OrdenaColuna',
						drildown			:	'DrillDown',
						linha				:	'DrillLinha',
						coluna_header		:	'DrillColuna',//Não modificar sem antes também modificar na lib pois é por esse nome que não insiro no histórico
						coluna_header_line	:	'DrillLinhaHeader',
						linha_header		:	'DrillHeaderData',
						data				:	'DrillValue',
						layout				:	'Layout'
							
					};

var ABA_TAG_NAME		=	'.WRSAbas ul';
var SYSTEM_OFF			=	false; //Não é preciso validar se existe modificação isso se deve a erro interno de banco


function abas_to_save()
{
	var _save			=	 false;
	var aba_data		=	 '';
	
	$('.WRS_ABA').find('a').each(function(){
		if($(this).find('.wrs_is_change_aba').length>=1)
		{
			aba_data		+=	"\n"+$(this).find('.title').html();
			report_id		=	$(this).attr('id-aba');
			_save			=	true;
		}
		
	});
	
	
	return {type:_save, data:aba_data};
	
}





function not_close_save_info()
{

	//http://www.codigosnaweb.com/forum/viewtopic.php?t=5465
	//http://stackoverflow.com/questions/1889404/jquery-ui-dialog-onbeforeunload
	$(window).bind('beforeunload', function() {
			
		 
		var aba_data		=	 abas_to_save();
		var report_id		=	0;
		var kendoUi			=	get_aba_active_kendoUi();

		if(SYSTEM_OFF==true) {
			SYSTEM_OFF	=	 false;
			return null;
		}
		
		 
		

		if(aba_data.data=='') return null;
		
		//Ativa a aba
		if(kendoUi['REPORT_ID']!=report_id)
		{
			$('.'+report_id).trigger('click'); //Active Aba
		}
		
	  var message = LNG('NOT_CLOSE_WINDOW')+aba_data.data;
	  

	  
	  return message;
	});
	
}

var wrsCookies = {
	  getItem: function (sKey) {
	    if (!sKey) { return null; }
	    return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
	  },
	  setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
	    if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
	    var sExpires = "";
	    if (vEnd) {
	      switch (vEnd.constructor) {
	        case Number:
	          sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
	          break;
	        case String:
	          sExpires = "; expires=" + vEnd;
	          break;
	        case Date:
	          sExpires = "; expires=" + vEnd.toUTCString();
	          break;
	      }
	    }
	    document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
	    return true;
	  },
	  removeItem: function (sKey, sPath, sDomain) {
	    if (!this.hasItem(sKey)) { return false; }
	    document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "");
	    return true;
	  },
	  hasItem: function (sKey) {
	    if (!sKey) { return false; }
	    return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
	  },
	  keys: function () {
	    var aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
	    for (var nLen = aKeys.length, nIdx = 0; nIdx < nLen; nIdx++) { aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]); }
	    return aKeys;
	  }
	};


function WRS_CONSOLE(){
	if(IS_TRACE){
		//console.log(arguments);
	}
}



function changeTypeRun(IDGrid,typeRun)
{
	var report_id		=	str_replace('#','.',IDGrid);
	
		$(report_id).wrsAbaData('setKendoUi',{TYPE_RUN:typeRun});
}


//http://stackoverflow.com/questions/4994201/is-object-empty
function isEmpty(obj,ttype) {

	
	
    // null and undefined are "empty"
    if(obj == null) return true;

    if(obj == undefined) return true;
    
    if(obj=='') return true;
    
    if(obj=='undefined') return true;
    

    
    // Assume if it has a length property with a non-zero value
    // that that property is correct.

    if(typeof obj!='object')
    {
	    if (obj.length > 0)    return false;
	    if (obj.length === 0)  return false;
	    
	    return false;
    }
    
    
    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) 
    {
        if (hasOwnProperty.call(obj, key)) return false;
        
        if(key!=null || key!=undefined) return false;
    	
    	if(obj[key]!=null || obj[key]!=undefined) return false;
    	
    }

    return true;
}


function chIClass(input)
{
		return str_replace('#','.',input);
}

function getEIClass(inputEvent)
{
	return '.'+inputEvent.attr('id');
	
}

function _trim(input)
{
	return $.trim(input);
}


function include_js(file)
{
	var script	=	$('<script/>',{type:"text/javascript",src:"api/alphasoft/js/"+file+".js?"+RAND_TOKEN});
		$('head').append(script);

}


function setOption(event,value,_selected)
{
	var _option	=	"<option value='{value}' {selected} >{label}</option>";
	var replace	=	['{value}','{label}','{selected}'];
	
	for(lineValue in value)
		{
			var _tag	=	'';
			
			if(_selected==value[lineValue]){
				_tag	=	'selected';
			}
			
			event.append(str_replace(replace,[lineValue,value[lineValue],_tag],  _option));
		}	
}

function empty_wrs_defaults(input)
{
		switch(input)
		{
			case 'e30=' 	 :  return null; break;
			case undefined   : return null; break;
			case ''         :  return null; break;
			default  : return input; break;
		}
		
		return input;
}

function setOptionRadio(event,value,_selected)
{
	var rand_name = Math.floor((Math.random() * 1000) + 1);
	var _option	=	"<span><input type='radio' id='{rand}' name='"+rand_name+"' value='{value}' {selected} ><label for='{rand}'>{label}</label></span>";
	var replace	=	['{value}','{label}','{selected}','{rand}'];
	var elements = []; // inverter a ordem dos objetos, pois não da pra reordenar diretamente sem ser array
	for(lineValue in value)
		{
			var _tag	=	'';
			var _rand = 'radio_'+(Math.floor((Math.random() * 1000) + 1));			
			if(_selected==value[lineValue]){
				_tag	=	'checked';
			}
			elements.push($(str_replace(replace,[lineValue,value[lineValue],_tag,_rand],  _option)));
		}	
	event.append(elements.reverse());	
}



function array_length(array)
{
	var cc	=	0;
	
	for(var array_0 in array)
		{
			cc++;
		}
	
	return cc;
}


function implode_wrs(arrayInput)
{
	
	var _tmp		=	[];
	
	
	for(var lineInput in arrayInput)
	{
		_tmp.push(arrayInput[lineInput]);
	}
	
	return _tmp.join(',');
}

function array_join(_param,__param)
{
	var _param_back	=	 [];
	
	
	for(line in _param)
		{
			_param_back[_param_back.length]=	_param[line];
		}
	
	for(line in __param)
	{
		_param_back[_param_back.length]=	__param[line];
	}
	return _param_back;
}

function array_find_data(array,value)
{

	for(obj in array)
		{
			if(obj==value) return true;
		}
	
	return false;
}


function exist_in_array(array,value)
{
	if(array.length==0) return false; 
	for(obj in array)
		{
			if(array[obj]==value) return true;
		}
	
	return false;
}

function qtd_frozen_eq(kendoUiID,index)
{
	return $(kendoUiID).find('.k-grid-header-locked tr:eq('+index+')').find('th').length;
}



function array_remove_value(_param,array_remove)
{
	var _array_tmp	= [];
 
	
	for(array_remove_value_o in _param)
		{
			if(exist_in_array(array_remove,_param[array_remove_value_o])==false)
			{
				_array_tmp[_array_tmp.length]	=	_param[array_remove_value_o];
			}
		}	
	
	return _array_tmp;
}


function sleep(milliseconds)
{
	  var start = new Date().getTime();
	  
	  for (var i = 0; i < 1e7; i++)
	  {
		    if ((new Date().getTime() - start) > milliseconds)
		    {
		      break;
		    }
	  }
	  
}




function WRS_PANEL_DRAG()
{
	$('.wrs_panel_center_body').show();
	$('.container_panel_relatorio').hide();
}

function addJsonEncodeToElement(array,element)
{
	element.attr('json',base64_encode(json_encode(array,true)));
}



function activeToGetAllFilters()
{
	var _filter_hide_string		=	$('.wrs_panel_filter_icon').attr('filter_hide');
		_filter_hide			=	 _filter_hide_string=='true'	? true : false;

	if(!_filter_hide)
	{
			$('.wrs_panel_filter_icon').attr('filter_hide','false').trigger('click');
	}
	
	return _filter_hide;
}


function activeToGetAllFiltersRecover(_filter_hide)
{
	if(!_filter_hide)
	{
		$('.wrs_panel_filter_icon').trigger('click');
	}
	
}

function wrs_base64encode(inputArray)
{
	
	return base64_encode(inputArray);
}

function base64_json(inputArray)
{
	return base64_encode(json_encode(inputArray));
}


function base64_json_decode(inputArray)
{
	return json_decode(base64_decode(inputArray));
}



function cleanJsonEncodeToElement(element)
{
	element.attr('json','');
}


function fwrs_array_change_value(_array,value,valueChange)
{
	var tmp	=	_array;
	
	for(x in _array)
		{
			
			if(value==_array[x])
			{
				tmp[x]	=	valueChange;
			}
		}
		
		return tmp;
}

function fwrs_array_val_not_empty(_array)
{
	var tmp	=	[];
	
	for(x in _array)
		{
			if(!empty(_array[x])){
				tmp[x]=	_array[x];
			}
		}
	return tmp;
}

function getJsonEncodeToElement(element)
{
	return $.parseJSON(base64_decode(element.attr('json')));
}

function getDataMetricas(element)
{
	return element.data('wrs-data');
}



/*
 * Decodifica base 64 para json e para array
 */
function getJsonDecodeBase64(json)
{
	
	if(empty(json) || json=='null') return json;
	return $.parseJSON(base64_decode(json));
}


function filter_array_convert(input)
{
	if(empty(input)) return [];
	
	var input_array	=	 explode(',',input);
	var tmp_input	=	[];

	for(var lineInput in input_array)
		{
			tmp_input[tmp_input.length]		=	'__'+replace_attr(input_array[lineInput]);
		}
	return tmp_input;
}


function filter_TMP_to_array(input)
{
	if(empty(input)) return [];
	
	var tmp_input	=	[];
	
	
	for(var lineInput in input)
		{
			var inputData		=	input[lineInput];
			var _filter			=	explode(',',inputData['data']);
				_filter			=	empty(_filter) ? '' : _filter;
				
				tmp_input[tmp_input.length]		=	[inputData['class'],'',_filter];				
		}
	
	return tmp_input;
}


function filter_configure_window()
{
	TRACE('START:filter_configure_window');
	var filter_h	=	$('.wrs_panel_filter_icon').attr('filter_hide'); 
	var label		=	'false';
		$('.WRS_DRAG_DROP_RECEIVER_FILTER').show();
		$('.WRS_DRAG_DROP_FILTER_CONTAINER').hide();
	

		if(filter_h=='true')
		{
			label	=	 'false';
		}
		
		$('.wrs_panel_filter_icon').attr('filter_hide',label).trigger('click'); 
	
		TRACE('END::filter_configure_window');
}

/**
 * @Link http://stackoverflow.com/questions/1822598/getting-url-hash-location-and-using-it-in-jquery/1822617#1822617
 * @returns
 */
function url_hash()
{
	var hash = window.location.hash;
	return hash;
}

function setLoading(obj)
{
	obj.html('<div class="wrs_loading"><img src="./imagens/wrs_loading.gif"/></div>');
}

function WRS_PANEL_RELATORIO()
{
	$('.wrs_panel_center_body').hide();
	$('.container_panel_relatorio').show();
}



function replace_attr(value)
{
	return md5(value);
	/*
	var replace		=	['[',']','.','%'    ,'&',' ',',','(',')','{','}','/','#'];
	var sub			=	['' ,'' ,'' ,'_por_','' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ,'' ];
	return str_replace(replace,sub,value);	
	*/
}



function WRS_CONFIRM(_text,_type,_callback,_extraButtonParam,_extraType,_extraForceButtons,_extraParams)
{
	var _title				=	"ALERT_TITLE_INFO";
	var _is_type			=	_type;
	var extraButtonParam	=	_extraButtonParam==undefined?false:_extraButtonParam;
	var extraType			=	_extraType==undefined?'confirm':_extraType;
	var hide_btn_no			=	 false;
	
		switch(_type)
		{
			case 'info' 	: _title	=	LNG('ALERT_TITLE_INFO') ;break;
			case 'error' 	: _title	=	LNG('ALERT_TITLE_ERRO') ;break;
			case 'success' 	: _title	=	LNG('ALERT_TITLE_SUCCESS') ;break;
			case 'warning' 	: _title	=	LNG('ALERT_TITLE_WARNING') ;break;
		}
		
		var buttons		=	{ok:LNG('BTN_CONFIRM'),yes:LNG('BTN_SIM'),cancel:LNG('BTN_NAO')};
		
		if(typeof _extraButtonParam =='object')
			{
				buttons			=	extraButtonParam?merge_objeto(buttons,extraButtonParam):buttons;
			}else{
				
				if(_extraButtonParam==true)
					{
						hide_btn_no=true;
					}
			}
		
		
		var _modal		=	{
									type  		: 	extraType,
									wrs_type	:	_type,
									title		:	_title,
									buttonText 	: 	buttons,
									closeClick 	: 	false, //Close Modal on click near the box
									text  		: 	_text,
									callback	: 	function(result){ _callback(result); }
							};

		
		if(_extraForceButtons!=undefined)
		{
			_modal			=	merge_objeto(_modal,{buttons 	: _extraForceButtons});
		}
		
		if(_extraParams!=undefined)
		{
			_modal			=	merge_objeto(_modal,_extraParams);
		}
		
		modal(_modal);
		
		
		$('#modal-window .modal-buttons a:first-child').show();
		
		if(hide_btn_no==true)
		{
			$('#modal-window .modal-buttons a:first-child').hide();
		}
}


function WRS_ALERT(_text,_type,_callback)
{
	var _title		=	"ALERT_TITLE_INFO";
	var _is_type	=	_type;
		
	switch(_type)
	{
		case 'info' 	: _title	=	LNG('ALERT_TITLE_INFO') ;break;
		case 'error' 	: _title	=	LNG('ALERT_TITLE_ERRO') ;break;
		case 'success' 	: _title	=	LNG('ALERT_TITLE_SUCCESS') ;break;
		case 'warning' 	: _title	=	LNG('ALERT_TITLE_WARNING') ;break;
	}
	
	var _modal = {
						type  		: 	_type,
						title		:	_title,
						buttonText 	: 	{ok:LNG('BTN_CONFIRM'),yes:LNG('BTN_SIM'),cancel:LNG('BTN_NAO')},
						closeClick 	: 	false, //Close Modal on click near the box
						text  		: 	_text
				 };
	
	if(_callback!=undefined){
		_modal			=	merge_objeto(_modal,{callback	: 	function(result){ _callback(result); }});
	}
		
	modal(_modal);
}

/**
 * Apenas para uso rápido não oficial
 */
function TRACE_DEBUG(value)
{

	
	if(empty($('.WRS_TRACE').html()))
	{
		$('body').append('<div class="WRS_TRACE"></div>');
		$('.WRS_TRACE').dblclick(function() {$(this).html('');});
	}
	
	$('.WRS_TRACE').append('<div>'+value+'</div>');
	
}

function _START(input)
{
	TRACE('_START::'+input);
}

function _ONLY(input)
{
	TRACE('_ONLY::'+input);
}

function _END(input)
{
	//TRACE('_END::'+input);
}

/**
 * @Link http://stackoverflow.com/questions/10535782/how-can-i-convert-a-date-in-epoch-to-y-m-d-his-in-javascript
 * @param value
 * @returns {Boolean}
 */
function TRACE(value)
{
	if(!IS_TRACE) return false;
	

	var date 		= new Date();
	var hours 		= date.getHours();
	var minutes 	= date.getMinutes();
	var seconds 	= date.getSeconds();
	var milliSecond	=	date.getMilliseconds();
	
	var prefix		=	hours+':'+minutes+':'+seconds+':'+milliSecond+' | ';
	console.log(prefix+value);
	//$('.WRS_TRACE').append('<div>'+value+'</div>');
}


function SetElementDataWrs(element)
{
	_START('SetElementDataWrs');
	$(element).find('li').each(function()
			{
				var _wrs_data	=	$(this).attr('wrs-data');
				
					if(_wrs_data!=undefined && _wrs_data!='undefined')
					{
						_wrs_data	=	 jQuery.parseJSON(_wrs_data);
						$(this).removeAttr('wrs-data').data('wrs-data',_wrs_data);
					}
			});
	
	_END('SetElementDataWrs');
}

function fwrs_error(msg)
{
	return fwrs_alert(msg,'alert-danger');
}


function fwrs_success(msg)
{
	return fwrs_alert(msg,'alert-success');
}

function fwrs_warning(msg)
{
	return fwrs_alert(msg,'alert-warning');
}

function fwrs_alert(msg,type)
{
	return '<div class="alert '+type+'">'+msg+'</div>';
}







/**
 * Fazendo merge no objetos do javascript
 * @param objFirst
 * @param objSecond
 * @returns
 */
function merge_objeto(objFirst,objSecond)
{

	return $.extend({}, objFirst,objSecond);		
}

 

/**
 * passando apenas parametros
 * @param param_request
 * @param Ofile
 * @param Oclass
 * @param Oevent
 */
function runCall(param_request,Ofile,Oclass,Oevent,funCallBack,typeAlert,typeData)
{
	//No typeData pode se informar se é json ou não
	
	var param	=	{'class':Oclass,'file':Ofile,'event':Oevent};
		param	=	merge_objeto(param,param_request);

	$('body').WrsGlobal('setCM',{isDesconected:countTimeCheck()});
		
	TRACE('Enviando parametro para o run.php mas sem esperar resposta file:common.js');	
	
	$.post('run.php',param,funCallBack,typeData).fail(function() {
			switch(typeAlert)
			{
				case 'modal' :  WRS_ALERT(LNG('ERRO_FILE_PROCCESS'),'error'); break;
				case 'log'	 :	console.error(LNG('ERRO_FILE_PROCCESS')); break;
				default		 : $('.mensagens').html(fwrs_error(LNG('ERRO_FILE_PROCCESS'))); break;
			}
				
	  });
	//console.error('runCall');
	TRACE('runCall finalizado');
}


function countTimeCheck()
{
	var __time		=	parseInt(date('i'))+TIME_CHECK_USER_CONECT;
	return mktime (date('H') , __time , date('s') ,date('m'),date('d'),date('Y'));
}

function getDataDisconectUser(data)
{
	_ONLY('getDataDisconectUser');
	
	
	if(data.data.isUserConnect==false)
	{
		SYSTEM_OFF	=	true;
		$('#fakeloader').show();
		window.location='desconected.php?charset='+LNG('IDIOMA');
	}
}

function isDesconected()
{
	
	var _isDesconected	=	$('body').WrsGlobal('getCM','isDesconected');
	
	if(isEmpty(_isDesconected))
	{
		$('body').WrsGlobal('setCM',{isDesconected:countTimeCheck()});
	}
	
	_ONLY('isDesconected:: userLoged 60 seconds');
	
	if(_isDesconected<=mktime())
	{
			var _file	=	'WRS_LOGIN';
		var _class	=	'WRS_LOGIN';
		var _event	=	'isUserConnect';

		var _param_request	=	{};
		
		//Remove o report ID
		runCall(_param_request,_file,_class,_event,getDataDisconectUser,'log');
		
		
		//Ajusta novamente a validação
		$('body').WrsGlobal('setCM',{isDesconected:countTimeCheck()});
	}
	
	setTimeout(isDesconected,1000*60);//60 Segundos
	
	
}





function getDateTime(){
	
		var objToday = new Date(),
			weekday = LNG('DIAS'),
			dayOfWeek = weekday[objToday.getDay()],
			dayOfMonth = today + (objToday.getDate() < 10) ? '0' + objToday.getDate() : objToday.getDate(),
			months = LNG('MES'),
			curMonth = months[objToday.getMonth()],
			curYear = objToday.getFullYear(),
			curHour = objToday.getHours() < 10 ? "0" + objToday.getHours() : objToday.getHours(),
			curMinute = objToday.getMinutes() < 10 ? "0" + objToday.getMinutes() : objToday.getMinutes(),
			curSeconds = objToday.getSeconds() < 10 ? "0" + objToday.getSeconds() : objToday.getSeconds()
		var today = curHour + ":" + curMinute + ":" + curSeconds + " - " + dayOfWeek + ", " + dayOfMonth + LNG('DE') + curMonth + LNG('DE') + curYear;
	
		/*
	if(curSeconds % 59 == 0){
		verificaSessaoAtiva()
	}*/
	
	$('.WRS_TIME').html(today);
}



function setWRSTime()
{
	setInterval(getDateTime,1000);
}




/*
 * 
 * Construção do Botão direito
 * 
 */



function removeKey(inputArray, line)
{
	var _tmp		=	[];
	
	    for(var lineArray in inputArray) 
	    {
	        if(lineArray != line) 
	        {
	        	_tmp[lineArray]	=	inputArray[lineArray];
	        }
	    }
    
    return _tmp;
}


function buttonClickRightRelatorios(nameObject,itemsElement)
{
	$(nameObject).each(function(){
		$(this).removeClass('context-menu-active');
	});
	
	
	
	 $.contextMenu({
	        selector: nameObject, 
	        className: 'contex-menu-title',
	        //callback: function(key, options) {},
	        items: itemsElement
	    });
	  $('.contex-menu-title').attr('data-menutitle', LNG('TITLE_CONTEX_MENU'));
}

function wrs_contex_menu_options_panel_atributos()
{
	//A variável droppableOptionsOl vem do preenchimento da wrs_panel.js
	
	var itemsElement	=	{};
	
	itemsElement[0]	=	{name:LNG('ATTRIBUTOS_FILTRO')	,icon : 'add', callback:function(){ send_filters_to_painel( 'DIRECT', $(this),$('.sortable_filtro'));  }};
	itemsElement[1]	=	{name:LNG('ATTRIBUTOS_COLUNA')	,icon : 'add', callback:function(){ send_filters_to_painel( 'DIRECT', $(this),$('.sortable_coluna')); }};
	itemsElement[2]	=	{name:LNG('ATTRIBUTOS_LINHA')	,icon : 'add', callback:function(){ send_filters_to_painel( 'DIRECT', $(this),$('.sortable_linha')); }};
	itemsElement[3]	=	'---------';
	itemsElement[4]	=	{name:LNG('BTN_SAIR'),icon : 'quit' , callback:function(){}};
	
	buttonClickRightRelatorios('.wrs_options_panel_atributo li',itemsElement);
}

function send_filters_to_painel(type,obj,painel){
	var itens_selecionados = obj.parents('div.wrs_panel_options').find('li.ui-draggable.ui-state-focus');
	if(itens_selecionados.length>0){
		var item_ja_existe=[];
		var _painel_find = painel;
		if(painel.hasClass('sortable_coluna') || painel.hasClass('sortable_linha')){
			_painel_find = $('.sortable_coluna, .sortable_linha');
		}
		itens_selecionados.each(function(){
			var visibleitem = $(this).is(':visible');
			if(_painel_find.find('li.'+$(this).attr('tag-class')).length<=0){
				if(!visibleitem) $(this).show();
				DROP_EVENT(type, $(this).removeClass('ui-state-focus'),painel);
			}else{
				item_ja_existe.push($(this).text());
			}
			if(!visibleitem) $(this).hide();
		});
		limpa_botoes_selecionados_filtros(obj.parents('div.wrs_panel_options'));
		if(item_ja_existe.length>0){
			var _s 		= item_ja_existe.length>1?'s':'';
			var _ns 	= item_ja_existe.length>1?'ns':'m';
			var _m 		= item_ja_existe.length>1?'m':'';
			var _ram 	= item_ja_existe.length>1?'ram':'i';
			var text 	= LNG('FILTER_CONTEX_MENU_SELECTION_ITEM_EXISTS').replace('%s',_s).replace('%ns',_ns).replace('%m',_m).replace('%ram',_ram).replace('%s',_s);
			for(var item in item_ja_existe){
				text   += "<br/> - "+item_ja_existe[item];
			}
			WRS_ALERT(text,'warning');
		}
	}else{
		DROP_EVENT(type, obj, painel);
	}	
}

function wrs_contex_menu_options_panel_metrica()
{
	var itemsElement	=	{};
	
	itemsElement[0]	=	{name:LNG('ATTRIBUTOS_METRICA')	,icon : 'add', callback:function(){ send_filters_to_painel('DIRECT',$(this),$('.sortable_metrica')); }};
	itemsElement[1]	=	'---------';
	itemsElement[2]	=	{name:LNG('BTN_SAIR'),icon : 'quit' , callback:function(){}};
	
	buttonClickRightRelatorios('.wrs_options_panel_metrica li',itemsElement);
}



function add_filtro_simples_composto_btn(type)
{
	_ONLY('add_filtro_simples_composto');
	var btn_simple		=	'<span  class="icon_atributo glyphicon glyphicon-play-circle"></span>';
	var btn_composto		=	'<span  class="icon_atributo"></span>';
	
	if(type)
		return btn_simple;
	
	return btn_composto;
}





function check_filter_simple_composto()
{
	_ONLY('check_filter_simple_composto');
}





function add_filtro_simples_composto(type_load)
{
	_ONLY('add_filtro_simples_composto');
	

	$('.sortable_filtro li').each(function(){
		
		 var class_is		=	$(this).attr('class');
		 var sc_load		=	$(this).attr('sc_load');
		 var aba_active		=	get_aba_active_object();
		 var tag_class		=	$(this).attr('tag-class');
		 var val_attr		=	null;
		 
		 //Aplicando o icone simples
		 

		 //Limpando todos os filtros
		 //aba_active.wrsAbaData('setNewFilter',{tag:tag_class,data:null,remove:true});
		 
		 if(type_load==true)
			 {
			 	var simples_Kendo	=		aba_active.wrsAbaData('getFilterNegado',tag_class);
			 		
			 	//console.log('simples_Kendo.simples',simples_Kendo.simples);
			 	if(typeof simples_Kendo.simples!="undefined")
			 		{
			 			if(simples_Kendo.simples==true) 
			 			{
			 				val_attr	=	sc_load='simples';
			 			}
			 		}
			}
		 
		 
		 if(!empty(sc_load))
			 {
			 	$(this).attr('atributo',sc_load);
			 	$(this).attr('sc_load','');
			 }
		 
		 

		 if(class_is!='placeholder')
		 {
			
			
			 var atributo	=	$(this).attr('atributo');
			$(this).find('.icon_atributo').remove();
			
			if(val_attr!=null)atributo=val_attr;
			

			var class_tag			=	'.wrs_panel_options .'+tag_class;
			var json				=	$(class_tag).data('wrs-data');
				
			var simple_data			=	{val: false, leve_full:json.LEVEL_FULL,  'tag_class':tag_class};

			if(atributo=='simples')
			{
				aba_active.wrsAbaData('delFilterNegado',tag_class);
				
				simple_data.val	=	true;
				
				$(this).prepend(add_filtro_simples_composto_btn(true));
				
			}
			else
			{
				$(this).prepend(add_filtro_simples_composto_btn(false));
				
				
			}
			
			aba_active.wrsAbaData('setFilterSimples',simple_data);
		 }
	});
}






function sortable_attr_simples_composto()
{
	var itemsElement	=	{};
	

	itemsElement[0]	=	{name:LNG('ATTRIBUTOS_SIMPLES')	,	icon : 'add', callback:function(){ $(this).attr('atributo','simples'); add_filtro_simples_composto(); }};
	itemsElement[1]	=	{name:LNG('ATTRIBUTOS_COMPOSTO'),	icon : 'add', callback:function(){ $(this).attr('atributo','composto');add_filtro_simples_composto(); }};
	itemsElement[2]	=	'---------';
	itemsElement[3]	=	{name:LNG('BTN_SAIR'),icon : 'quit' , callback:function(){}};
	
	buttonClickRightRelatorios('.sortable_filtro li',itemsElement);
	
	add_filtro_simples_composto(true);

}



function WRSformatString(cellValue) 
{
	return '<div class="wrs_control_td_jqgrid ui-state-default" wrsclass="no_jgrid_config" >'+cellValue+'</div>';
}
				

/**
 * Formata os campos Numericos da GRid
 * @param cellValue
 * @param options
 * @param rowObject
 * @returns
 */
function WRSformatNumber(cellValue, options, rowObject)
{
			/*
			 * Campos esperados
			 * wrs_data.MEASURE
			 * wrs_data.FORMAT_STRING
			 */	
			var wrs_data	=	options.colModel['wrs_data'];
	
			//Configura para o Negrito
			if(strpos(cellValue,'<strong>')!==false){
				return '<strong>'+formataValue(wrs_data.MEASURE,wrs_data.FORMAT_STRING,cellValue,1)+'</strong>';
			}
			
			
			return formataValue(wrs_data.MEASURE,wrs_data.FORMAT_STRING,cellValue,1);
			

						
}
			
/**
 * Faz o ajuste do Backgroud do Frozen
 */
function WRSFrozenTheme()
{
	$('.wrs_control_td_jqgrid').each(function(){
		$(this).parent().addClass($(this).attr('wrsclass'));
	});
}
			

/**
 * 
 * Exemplos de resize
 * @link http://stackoverflow.com/questions/875225/resize-jqgrid-when-browser-is-resized
 * 
 * @param object
 */
function WRSGridLoadComplete(object)
{
	var padding		=	parseInt($('.ui-layout-center').css('padding-top').replace('px'))*2;
	var abaHeight	=	$('.WRS_ABA').outerHeight();
	
	var width		=	$('.ui-layout-center').width()-padding;
	var height		=	$('.ui-layout-center').height()-(padding+(abaHeight*3));
	
//	$(object).setGridWidth(width);
	
	$(object).jqGrid("setGridWidth", "100%");
	
	//$(object).setGridHeight(height);
}


 
/*
 * COntruindo o resize da Grid Simples
 * TODO: Tenho que sincronizar essa informação com o WRSWindowGridEventTools
 */
function resizeGridSimple()
{
	var hasClass	=	 empty($(".wrsGrid").html()) ? false : true;
	if(hasClass)//Verificando se a classe existe
	{
		
		var telerikGrid 	= 	$('.wrsGrid').data('kendoGrid');
		var IDName			=	'#'+telerikGrid.element.attr('id');
		
		var grid			=	getElementsWrsKendoUi($('.wrsGrid'));
		var window_grid		=	grid['WINDOW'];
		

		//$(IDName+"NAV .wrs_tools_options_window").find("a[wrs-data="+window_grid+"]").trigger('click');
		
		/*
		
		var padding			=	parseInt($('.wrs_nav_relatorio').css('padding-top').replace('px'))*2;
		var abaHeight		=	$('.wrs_nav_relatorio').outerHeight();
		var paddingCenter	=	parseInt($('.ui-layout-center').css('padding-bottom').replace('px'));
		var width			=	$('.ui-layout-center').width()-padding;
		var height			=	$('.ui-layout-center').height()-((padding+(abaHeight))+paddingCenter);
		
		$(IDName).height(height);	*/
		//telerikGrid.resize();
		resize_common();
	}
} 


/*
 * TODO: Verificar se está correto as informações
 */
function merge_filter_data(input,inputMerge)
{

	var _tmp_merge		=	[];
		_tmp_merge		=	inputMerge;



	for(var lineInputMerge in input)
		{
			var _key	=	String(lineInputMerge);
			
				if(!empty(input[_key]))
				{
					
						_tmp_merge[_key]		=	input[_key];
				}
		}

	return _tmp_merge;
	
}

function array_concat(first_input,second_input)
{
	var call_back	=	first_input;
	
	for(var lineSecond in second_input)
		{
			call_back[lineSecond]=	second_input[lineSecond];
		}

	return call_back;
}

/**
 * 
 * Está vinculado a formataValue
 * 
 */
function sumarizaValor(valor)
{
	var _casa_decimal 	= 	'.';
	var _milhar 		= 	',';
	var _value 			= 	valor;
	var _value_limit	=	0;
	var _WORD			=	'';
	
	if(LNG('IDIOMA')== 'POR')
	{
		_casa_decimal 	= ',';
		_milhar 		= '.';
	}
	
	
	if(valor>1000000000000000)
	{
		_value_limit	=	16;
		_WORD			=	'T';
	}
	
	else if(valor>1000000000000)
	{
		_value_limit	=	12;
		_WORD			=	'G';
	}
	
	else if(valor>1000000000)
	{
		_value_limit	=	8;
		_WORD			=	'M';
	}
	
	else if(valor>1000000)
	{
		_value_limit	=	4;
		_WORD			=	'K';
	}
	
	
	if(_value>200 || _value<-200)
	{
		_value 	= number_format(_value,0,'',_milhar);
	}
	
	return  substr(_value,0,strlen(_value)-_value_limit)+_WORD;
	
}



/**
 * Formatação das String da GRID
 * @param MEASURE_NAME
 * @param formatacao
 * @param valor
 * @param sumariza
 * @returns
 */
function formataValue(MEASURE_NAME,formatacao,valor,sumariza,notTAG,label)
{
	
	switch(formatacao)
	{
		case 'Percent' 	:
							{

									var _label				= 	(label!=undefined && label.trim()!='')?label:'';
									var controle_metricas	=	false;
									
									var _valor				=	valor;									
									var seta				=	null;
									_valor				=	parseFloat($.trim(_valor.replace(',','.').replace("%","")));

									try{
										controle_metricas=(_label.trim().toLowerCase().substr(0,5)=='cresc' || _label.trim().toLowerCase().substr(0,4)=='evol');
									}catch(e){if(IS_EXCEPTION) console.warn(' exception');
									controle_metricas	=	false;
									}
									

									//if(strpos(MEASURE_NAME,'Cresc.')!==false || strpos(MEASURE_NAME,'Evol.')!==false)	
									if(controle_metricas)
									{
										if(_valor>0){
											seta = " <img src='imagens/setinha_verde.png' width='9' height='9'/>";
										}
										else if(_valor<0)
										{
											seta = " <img src='imagens/setinha_vermelha.png' width='9' height='9'/>";
										}else if(_valor=='0.00' || _valor=='0' || _valor=="0.0")
										{
											seta = " <img src='imagens/yellow_square.png' width='9' height='9'/>";
										}
										else
										{
											seta = "";
										}
										if(!empty(notTAG))  return valor;
										 
										/*
										 * TODO:IMplementar a barra adicional para funcionar
										 */
//										seta	=	'';
										return [valor,seta];
										
									}
			
							}
		break;
	}
	
	

		return valor;
	
	
	
}

/*
 * cria um replace semelhante ao do javascript, mas utilizando arrays
 */
String.prototype.replaceComArray = function (find, replace) {
    var replaceString = this;
    for (var i = 0; i < find.length; i++) {
        // global replacement
        var pos = replaceString.indexOf(find[i]);
        while (pos > -1) {
            replaceString = replaceString.replace(find[i], replace[i]);
            pos = replaceString.indexOf(find[i]);
        }
    }
    return replaceString;
};

/*
 * bloqueia caracteres contidos em um array - por default remove aspas simples e duplas
 */
function bloqueia_chars(obj,chars){
	var _chars 			= chars==undefined?["'",'"']:chars;
	var r=[]; for(i in _chars) r.push(''); // gera um array espelho vazio com a qtde proporcional
	var _chars_verso 	= r;
	obj.unbind('keypress').bind('keypress', function (event) {
	    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
	    if (_chars.indexOf(key)>-1) {
	       event.preventDefault();
	       return false;
	    }
	}).val(obj.val().replaceComArray(_chars,_chars_verso)).on('blur',function(){
		bloqueia_chars($(this));
	});
}

function trata_campos_senha(){
	$('.show_pass_field').unbind('change').bind('change',function(){ 
		var cpo_senha = $(this).parents('div:first').find('div.form-control-wrs input.input_type_password');
		var novo_cpo  = cpo_senha.clone();
		novo_cpo.prop('type',($(this).prop('checked')?'text':'password')).insertAfter(cpo_senha).prev().remove();
		bloqueia_chars(novo_cpo);
	});
}

function trata_campos_int(){
	$('.input_type_int_only').keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });	
}



/*
 * Ativando configurações para o Tema
 */
function addKendoUiColorJQueryGrid()
{
	 _START('addKendoUiColorJQueryGrid');
	 
	 
	//Pegando  as especificação das cores do DRAG and DROP para poder inserir no Tabele do Kendo Ui
	var div_color	=	$('.ui-state-default, .ui-state-focus');
	var color		=	div_color.css('border-color');
	var colorFont	=	div_color.css('color');
	
	var ui_state_active =	$('.ui-state-active').css('color');
	
	var style	=	'';
		style	+=	"<style>.table_border,.border_color tr td {border-color : "+color+" !important;}";
		style	+=	".bold{color:"+colorFont+" !important;}";
		style	+=	".colorSelect{color:"+ui_state_active+" !important;}";
		style	+='</style>';
	$(style).appendTo( "head" );
	
	 _END('addKendoUiColorJQueryGrid');
}


/*
 * abre modal para controle de definicao ou expiracao de senha
 */
function abre_modal_alterar_senha(){

	_START('abre_modal_alterar_senha');
	var funCallBack	=	function(data)
	{
		_START('callbackabre_modal_alterar_senha');
		
			$('.modal-content-grid').html(data);
			$('.grid_alterar_senha #extraValues').val('');
			wrs_window_grid_events_tools({btn:btn_window_grid_event_admin});
			
		_END('callbackabre_modal_alterar_senha');	
	};
	
	$('#myModal').modal('show');

	grid_window_modal(
				{	
					'wrs_type_grid'	:		'form',
					'form_event'	:		'changePassword',
					'extraValues'	: 		'self'
				},
				null,
				funCallBack);
	_END('abre_modal_alterar_senha');	
	
}


/**
 * screenShot
 * 
 * @link http://stackoverflow.com/questions/17672020/html2canvas-save-image-doesnt-work
 * 
 * @Link http://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js
 * @link http://stackoverflow.com/questions/24009869/google-map-screen-capture-is-not-working-for-marker-and-marker-cluster-using-htm
 */
function screenShot() {
	
	//Confere os chart para poder fazer as conversões
	screenShotChart();
	
    html2canvas(document.body, {
   	 useCORS: true,
   	logging	: false,
    	onrendered	: function(canvas) 
    					{
				           var img 				= 	canvas.toDataURL("image/png");
				           var output 			= 	base64_encode(img);
				           var _param_request	=	{};
				           var report_wrs		=	get_aba_active_kendoUi();
				           var _title			=	str_replace(global_especial_caracteres,'',str_replace(' ','_',retiraAcento(report_wrs.TITLE_ABA)))+date('_Y_m_d_H_i_s');
				           var _param_request	= 	{image:output,nameFile:'screenShot'+_title};                  
				         	//Remove o report ID
					   		runCall(_param_request,'IMAGE','IMAGE','screenShot',function(data){
					   			
					   			$('.html2canvasImageSite').remove();
					   			$('svg').show();
					   			callModalScreenShot(_param_request.nameFile);
					   		},'modal');
					   		
				   		}
   });
}    

function get_form_info_screenShot()
{
 
	var name_base		=	$('.title_cube').html();
	var infoKendo		=	get_aba_active_kendoUi();
	
		$('#mailSubject').val('WRS - '+name_base+' - '+infoKendo.TITLE_ABA);
	
		body	=	sprintf(LNG('ScreenShotMailBodyHtml'),infoKendo.TITLE_ABA,name_base,tagFilterWRS(false,'b'),getLogin('USER_DESC'));
	
	$('#bodyMail').html(body);
}

//Enviando email screen Shot
//@link http://pt.stackoverflow.com/questions/81870/qual-express%C3%A3o-regular-posso-utilizar-para-remover-espa%C3%A7os-duplos-em-javascript
//@link http://www.rogeralmeida.com.br/blog/jquery/validacao-de-email-utilizando-expressoes-regulares-e-jquery
function sendMailScreenShot()
{
	var request			=	{
								messsage	:	$('#bodyMail').summernote('code'),
								subject		:	$('#mailSubject').val(),
								to			:	str_replace([' ',','],';',$('#mailTo').val().replace(/( ){2,}/g, '$1')),
								user_id		:	getLogin('USER_ID')
							};
	
	 
	
	//validando emails
	var check_mail		=	explode(';',request.to);
	var mails_error		=	null;
	var mails_correct	=	[]; 
	
	
	if(isEmpty($.trim($('#mailTo').val())))
	{
		 WRS_ALERT(LNG('ScreenShotMailTO'),'warning');
		 return false;
	}
	
	if(isEmpty($.trim(request.subject)))
	{
		 WRS_ALERT(LNG('ScreenShotMailSUBJECT'),'warning');
		 return false;
	}
	

	if(isEmpty($.trim(request.messsage)) || $.trim(request.messsage)=='<p><br></p>')
	{
		 WRS_ALERT(LNG('ScreenShotMailMESSENGER'),'warning');
		 return false;
	}
	
	
	
	 for(var lineMail in check_mail)
	 {
     
		if(isEmpty($.trim(check_mail[lineMail]))) continue;
		 
        	var filtro = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        
        if(!filtro.test(check_mail[lineMail]))
        {
        	if(mails_error==null) mails_error='';
        	mails_error	+=	check_mail[lineMail]+'<br>';
        }else{
        	mails_correct.push(check_mail[lineMail]);
        }
        
	 }
	 
	 
	 if(mails_error!=null)
		 {
			 WRS_ALERT(sprintf(LNG('ScreenShotMailWrong'),mails_error),'warning');
			 return false;
		 }
	 
	 
	 	request.to	=	mails_correct;
	 
	 	
		var _file	=	'WRS_REPORT';
		var _class	=	'WRS_REPORT';
		var _event	=	'screenshot';


		var mail_send_callback	=	 function(data)
		{
			$('.btn-modal-cancel').click();
			
			if(data.data.send==true){
				//send
				 WRS_ALERT(LNG('ScreenShotMailSendSuccess'),'success');
			}else{
				WRS_ALERT(sprintf(LNG('ScreenShotMailSendError'),data.data.error),'error');
				//not send
			}
			
			
		}
		//HERE
		$('.body_data_mail').html(LNG('ScreenShotMailSend'));
		$('.btn_window_grid_event,.btn-left-anexo-css').remove();
		//Remove o report ID
		runCall(request,_file,_class,_event,mail_send_callback,'modal');
		
		
     
}

function screenShotSendMail()
{
	
	
	var html		=	'<div class="progress wrs_generate_csv_zip">'+
	  '<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span></span>'+
	 '</div>'+
	'</div>';

	
	if($('.wrs_generate_csv_zip').length<=0){
		$('body').append(html);
	}
	
	$('.wrs_generate_csv_zip span').html(LNG('ScreenShotGenerate'));
	
	screenShot() ;
	
}

function callModalScreenShot(nameImage)
{
	var funCallBack	=	function(data)
	{
		_START('screenShotSendMail');
		
			$('.modal-content-grid').html(data);
		_END('screenShotSendMail');	
	};
	
	$('#myModal').modal('show');
	$('.wrs_generate_csv_zip').remove();
	//modal enviando email
	grid_window_modal(
				{'nameImage':nameImage},
				'screenShotSendMail',
				funCallBack);
}

function screenShotChart()
{
	var container_chart	=	$('#'+get_aba_active_kendoUi().REPORT_ID+'Elements .chart');
	var multiple_chart	=	$('#'+get_aba_active_kendoUi().REPORT_ID+'Elements .wrs_multiple_chart');
	var gauge			=	$('#'+get_aba_active_kendoUi().REPORT_ID+'Elements .gauge_chart');
	var dataChart		=	 'kendoChart';
	var isGauge			=	false;
	
	if(multiple_chart.length>=1){
		container_chart	=	multiple_chart;
	}
	
	if(gauge.length>=1){
		container_chart	=	gauge;
		dataChart		=	'kendoRadialGauge';
		isGauge			=	true;
	}
	
	//
		
	
	container_chart.each(function(){
		var that		=	 $(this);
		
		

		if(isGauge==true)
		{
			if($(this).attr('data-role')==="lineargauge")
			{
				dataChart	=	'kendoLinearGauge';
			}
		}
			
			
		var chart	=	that.data(dataChart);
		
			chart.exportImage().done(function(data) {
				that.find('svg').hide();
				that.prepend('<img class="html2canvasImageSite" src="'+data+'"/>');
				
			});
			
	});
	
}

function confereTabelaManageParam(tabela){
	
	var param	=	{'class':'ATT_WRS_LOG','file':'ATT_WRS_LOG','event':'confereTabelaManageParam','tabela':tabela};
	
	$.ajax({
		method: "POST",
		url: "run.php",
		async: false,
		data: param
	}).done(function( tabela_param ) {
		tabela = tabela_param;
	});

	return tabela;
}

function getLogin(id)
{
	var login 	=	$('body').WrsGlobal('getPHP','login');
	var get		=	null;
	
	try{
		get	=	login[id];
	}catch(e){
		get	=	 null;
	}
	
	return get;
	
	
}


//CheckPassword
$(function(){
	
	
	if(typeof NOT_CHECK_LOGIN=='undefined'){
		isDesconected();
	}
	});
