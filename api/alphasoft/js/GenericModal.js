/**
 * modal generica
 * felipebevi 20150817
 */

function get_inputs_val(form)
{
	_START('get_inputs_val');
	var param		=	[];
	
	form.find('input[type=text]').each(function(){
		param[$(this).attr('name')]	=	$(this).val();
	});
	
	
	form.find('input[type=checkbox]').each(function(){		
		if($(this).is(':checked'))
		{
			param[$(this).attr('name')]	=	$(this).val();
		}else{
			param[$(this).attr('name')]	=	false;
		}
	});
	

	
	form.find('select').each(function(){
		
		var _option	=	[];
		
		$(this).find('option').each(function(){
			_option.push($(this).val());
		});
		
		param[$(this).attr('name')]	=	_option;
		
		
	});
	
	_END('get_inputs_val');
	return param;
	
}

$.fn.extend({
	/*
	 * extensao para criar modal generica
	 */
	modalGeneric: function (options) {
		
		_START('modalGeneric');
		var janela_modal 		= 	$('#myModalGenericConfig');
		var janela_modal_body 	= 	$('#myModalGenericConfig .modal-body');
		var janela_title		=	$('#myModalGenericConfig  #myModalGenericConfigLabel');
		var _dataName			=	'WRSGeneridModal';
		var optionsDefault		= {
										'file'									:	null, 
										'classe'								:	null,
										'event'									:	'default',
										'title'									:	null,
										'bt_salvar'								:	true,
										'bt_atualizar'							:	false,
										'bt_apagar'								:	false,
										'bt_salvar_extra_action_validator'		:	null,
										'bt_atualizar_extra_action_validator'	:	null,
										'bt_apagar_extra_action_validator'		:	null,
										'bt_cancelar'							:	true,
										'returnModal'							:	false,
										'extraParam'							:	null
									};
		
		var  _options	=	 $.extend( {}, optionsDefault, options );						
		
		var _callback = function(data)
			{	
				_ONLY('modalGeneric::_callback');
				janela_modal_body.html(data);
			}
			
		var _runCalll	=	 function(_options)
			{
				_START('modalGeneric::_runCalll');
				_options	=	$.extend( {}, _options, _options.extraParam );
				janela_modal_body.html('Carregando...');				
				runCall(_options,_options.file,_options.classe,_options.event,_callback,'modal');
				_END('modalGeneric::_runCalll');
			}
			
		var bt_salvar	=	 function()
			{
				_START('modalGeneric::bt_salvar');
				var o_param			=	janela_modal.data(_dataName);
					o_param.event	=	'save';
					
				var  _param			=	 $.extend( {}, o_param, get_inputs_val(janela_modal_body) );

				var _continue=true;
				
				if(_options.bt_salvar_extra_action_validator!=null){
					_continue = _options.bt_salvar_extra_action_validator(_param);
				}
				
				if(_continue){
					_runCalll(_param);	
				}
				_END('modalGeneric::bt_salvar');			
			}	
		
		var bt_atualizar	=	 function()
			{
				_START('modalGeneric::bt_atualizar');
				var o_param			=	janela_modal.data(_dataName);
					o_param.event	=	'atualizar';
				var  _param			=	 $.extend( {}, o_param, get_inputs_val(janela_modal_body) );
				
				var _continue=true;
				
				if(_options.bt_atualizar_extra_action_validator!=null){
					_continue = _options.bt_atualizar_extra_action_validator(_param);
				}
				
				if(_continue){
					_runCalll(_param);	
				}
				
				_END('modalGeneric::bt_atualizar');
				
			}
		
		var bt_apagar	=	 function()
			{
				_START('modalGeneric::bt_apagar');
				var o_param			=	janela_modal.data(_dataName);
					o_param.event	=	'apagar';
				var  _param			=	 $.extend( {}, o_param, get_inputs_val(janela_modal_body) );
				
				var _continue=true;
				
				if(_options.bt_apagar_extra_action_validator!=null){
					_continue = _options.bt_apagar_extra_action_validator(_param);
				}
				
				if(_continue){
					_runCalll(_param);	
				}
				_END('modalGeneric::bt_apagar');
			}
	
		
			
			janela_modal.data(_dataName,_options);		
						
			janela_modal.find('.bt-salvar').toggle(_options.bt_salvar);
			janela_modal.find('.bt-atualizar').toggle(_options.bt_atualizar);
			janela_modal.find('.bt-apagar').toggle(_options.bt_apagar);
			janela_modal.find('.bt-cancelar').toggle(_options.bt_cancelar);
			janela_title.html(_options.title);
					
			janela_modal.modal('show');
			
			janela_modal.find('.bt-salvar').unbind('click').click(bt_salvar);
			janela_modal.find('.bt-atualizar').unbind('click').click(bt_atualizar);
			janela_modal.find('.bt-apagar').unbind('click').click(bt_apagar);
						
			_runCalll(_options);
		
			_END('modalGeneric');
		
		if(_options.returnModal)	
			return janela_modal;
		else
			return this;
	}

});

function getValuesWindow(){
	_ONLY('getValuesWindow');
	return {'dados':'array'};
}


function carrega_report_generic_modal(arg)
{
	_ONLY('carrega_report_generic_modal:Empty');
	console.log('ARG',arg);
}