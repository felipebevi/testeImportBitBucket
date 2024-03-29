<?php

/**
 * 
 *  Criando a estrutura do Windows Grid
 *  
 */

includeCLASS('Form');
includeCLASS('KendoUi');

class WindowGrid extends FORM
{
	
	
	/**
	 *  
	 * Contem os botões do sistema
	 * @var string
	 */
	private $button		=	NULL;
	
	/**
	 * Exceptions
	 * @var WRS_REPORT
	 */
	private $exception	=	NULL;
	
	
	/**
	 * 
	 * @var WindowGrid
	 */
	
	
	private function extendException($param,$event)
	{
		/*
		 *
		 * Obtendo o evento para extender a classe que contem a regra da estrutura
		 *
		 */
//form_event
		if(is_array($param) && array_key_exists('exception', $param) && is_array($param['exception']))
		{
			$this->exception	=	 $param['exception'];
			includeCLASS($this->exception['file']);
		
			$class		=	$this->exception['class'];
			$obj		=	 new $class();
			$cube_s		=	 fwrs_request('cube_s');	
			$obj->set_conn($this->get_conn());
			$obj->run($event,$param,$cube_s);
			
			
			$this->exception	=	$obj;
		}
		
	}
	
	/**
	 * Recebe todos os Eventos iniciais
	 * Enter description here ...
	 */
	public function run()
	{
		//Apenas informações dos cubos	
		$param				=	array();
		$event				=	fwrs_request('event');
		$event_grid_inside	=	fwrs_request('event_grid_inside');
		$param['title_menu']=	'';
		$param['table']		=	'';
		
		$wrs_type_grid		=	fwrs_request('wrs_type_grid');
		
		/*
		 * Eventos do Banco
		 * @var WRS_MANAGE_PARAM
		 */
		$this->manage_param	= new WRS_MANAGE_PARAM();
		
		/*
		 * Envio de E-mail
		 */
		if($event=='screenShotSendMail')
		{
			$this->screenShotSendMail();
			return false;
		}
		
		
		/*
		 *	Executando as ROWS das GRIDS
		 */
		if(!empty($event_grid_inside))
		{	
			if($event_grid_inside=='grid')
			{
				return $this->runGrid();
			}
		}

		
		if(empty($event))
		{
			$param['title']	=	LNG('ERROR_TITLE');
			$param['html']	=	fwrs_error(LNG('ERROR_NOT_EVENT'));
			$param['button']= 	array();
		}else{		
			if($this->manage_param->load($event))
			{
				$form_event		=	fwrs_request('form_event');
				
				//CHamando o Eventos
				$param			=	$this->manage_param->getMetodoTabela($event);
				$p_request		=	fwrs_request('param_request');

				if(is_array($p_request) && count($p_request)>0)
					foreach($p_request as $k=>$v)
						$param[$k]=$v;
					
								
				$LocalEvents	=	 $this;
				
				/*
				 * extend 
				 */

				if(array_key_exists('extend', $param) && count($param['extend'])>0)
				{
					$extend			=	 $param['extend'];
					includeCLASS($extend['file']);
					$class			=	$extend['class'];					
					$objExtend		=	new $class();
					$objExtend->SetObject($this);
					$LocalEvents	=	$objExtend;				
				}
				
				switch($form_event)
				{
					case 'remove' 				: 	$param	=	$LocalEvents->delete($param) ; break;
					case 'update'				:	$param	=	$LocalEvents->update($param) ; break;
					case 'new'					:	$param	=	$LocalEvents->insert($param); break;
					case 'import'				:	$param	=	$LocalEvents->import($param); break;
					case 'export'				:	$param	=	$LocalEvents->export($param); break;
					case 'exportResults'		:	$param	=	$LocalEvents->exportResults($param); break;
					case 'changePassword'		:	$param	=	$LocalEvents->changePassword($param); break;
					case 'changePassUser'		:	$param	=	$LocalEvents->changePassUser($param); break;
					case 'form_exception'		:	{
													$param			=	$this->build_grid_form($param);
													$LocalEvents->build_grid_form_exception($param); // felipeb 20160310 - NAO SEI o motivo da classe nao retornar o $param completo, já fiz tudo que é teste possível mas não há mais tempo para investigação, acrescentei em uma variavel da classe e resgato logo abaixo se ela existir para substituir apenas o HTML do param
													break;
												}
					case 'back'				:	{
													$_REQUEST['wrs_type_grid'] = $wrs_type_grid = 'list';
												}
					default					:	$param	=	$this->build_grid_form($param);
				}

				if(isset($LocalEvents->html_param_form_exception) && $LocalEvents->html_param_form_exception!=''){
					$param['html'] = $LocalEvents->html_param_form_exception;
				}
				
				$this->extendException($param,$wrs_type_grid);
				
				
			}else{
				$param['title']	=	LNG('ERROR_TITLE');
				$param['html']	=	fwrs_error(LNG('ERROR_EVENT_NOT_FOUND'));
				$param['button']= 	array();
			}	
		}
/*
		if(!empty($this->exception))
		{
			$param['html']		=	$this->exception->change_html($param['html']);
		}
*/
		//Aplicando a conversão das TAGS para aplicar no HTML
		$TPL_TITLE				=		!array_key_exists('title', $param)?'':$param['title'];
		$TPL_HTML				=		!array_key_exists('html', $param)?'':$param['html'];

		$TPL_DATA				=		(isset($param['data']))?$param['data']:'';
		$TPL_COMPLEMENT_TITLE	=		!array_key_exists('title_menu', $param)?'':$param['title_menu'];
		
		
		if(array_key_exists('button', $param)){
			if($wrs_type_grid=='form'){
					unset($param['button']['new']); 
					unset($param['button']['remove']); 	// se o usuario estiver no formulario para alteracao, ele so pode salvar ou apagar algo (sem botao de novo)
					if(is_array($param['button']) && array_key_exists('export',$param['button']) && $form_event!='export'){
						unset($param['button']['export']); // no formulario de alteracao nao tem de aparecer botoes de import/export se existirem
					}
					if(is_array($param['button']) && array_key_exists('import',$param['button']) && $form_event!='import'){
						unset($param['button']['import']);
					}
					if(is_array($param['button']) && array_key_exists('changePassword',$param['button']) && $form_event!='changePassword'){
						unset($param['button']['changePassword']);
					}
			}else{
				unset($param['button']['update']);	// caso contrario, so pode criar Novo ou apagar vários (sem botao de salvar-update)
				unset($param['button']['back']);	// caso contrario, so pode criar Novo ou apagar vários (sem botao de salvar-update)
			}
		}
						
		$this->setButton($param['button'],(!array_key_exists('table', $param)?'':$param['table']),((is_array($param) && array_key_exists('button_force_label',$param) && $param['button_force_label'])?true:false),((is_array($param) && array_key_exists('button_icon',$param) && is_array($param['button_icon']) && count($param['button_icon'])>0)?$param['button_icon']:false),((is_array($param) && array_key_exists('button_type',$param) && is_array($param['button_type']) && count($param['button_type'])>0)?$param['button_type']:false));
				
		$TPL_BUTTON				=		$this->getButton();
				
		if(!fwrs_request('return_html')){
			//Chamando o Template
			include PATH_TEMPLATE.'windows_grid.php';
		}else{
			echo $TPL_HTML;
		}
		
	}
	
	
	
	public function screenShotSendMail()
	{
		//Apenas informações dos cubos
		includeCLASS('IMAGE');
		$IMAGE	=	 new IMAGE();
		
		$pathScreenShot		=	 $IMAGE->getPathScreenShot();
		
		include PATH_TEMPLATE.'screenShotSendMail.php';
		
		
	}
	
	
	public function insert($options)
	{
		//WRS_DEBUG_QUERY('insert','oo.log');
		$param	=	 $this->build_grid_form($options);
		
		$param['html']			=	'<pre>INSERT windowgrid</pre>'.$param['html'];
		
		return $param;
		
		
	}

	public function import($options)
	{
		//WRS_DEBUG_QUERY('import');
		$param	=	 $this->build_grid_form($options);
		$param['html']			=	'<pre>IMPORT windowgrid</pre>'.$param['html'];
		return $param;
	}
	
	public function export($options)
	{
		//WRS_DEBUG_QUERY('import');
		$param	=	 $this->build_grid_form($options);
		$param['html']			=	'<pre>EXPORT windowgrid</pre>'.$param['html'];
		return $param;
	}
	
	public function update($options)
	{
		//WRS_DEBUG_QUERY('update','oo.log');
		$param	=	 $this->build_grid_form($options);
		$param['html']			=	'<pre>UPDATE windowgrid</pre>'.$param['html'];
		return $param;
	}
	
	public function delete($options)
	{
		//WRS_DEBUG_QUERY('delete','oo.log');
		$param	=	 $this->build_grid_form($options);
		$param['html']			=	'<pre>DELETE windowgrid</pre>'.$param['html'];
		return $param;
	}
	
	/**
	 * Criando os Botões e seus eventos
	 * 
	 * Na variável $button irá receber o array com os elementos dos botões e seus eventos
	 * 
	 * @param array $button
	 * 
	 * 
	 */
	public function setButton($button,$table,$label_force=false,$button_icon=false,$button_type=false)
	{
		$button_merge			=	array('new','update','remove');
		$map_buttons			=	array();
		
		// BOTAO GENERICO DINAMICO - o que tiver a mais na manage_param será construído neste laço
		if(is_array($button)){
			foreach($button as $action_bt=>$label_bt){
				if(!in_array($action_bt, $button_merge)){
					$btn_type = is_array($button_type) && array_key_exists($action_bt, $button_type)?$button_type[$action_bt]:'btn-info';
					$map_buttons[$action_bt]		=	'<button type="button"  {complement} table="'.$table.'"	 action_type="'.$action_bt.'" 		class="btn btn_extra '.(($action_bt=='back')?'btn-default':$btn_type.' btn-color-write').' 	btn_window_grid_event">	<i class="'.(($button_icon && array_key_exists($action_bt, $button_icon))		?$button_icon[$action_bt]:'glyphicon glyphicon-plus color_write')								.'"></i> '	.$label_bt.	'</button>';
				}					
			}
		}
		
		$map_buttons['new']		=	'<button type="button"  {complement} table="'.$table.'"	 action_type="new" 		class="btn btn-color-write btn-success 	btn_window_grid_event">	<i class="'.(($button_icon && array_key_exists('new', $button_icon))		?$button_icon['new']:'fa fa-floppy-o')								.'"></i> '	.LNG('BTN_SAVE').	'</button>';
		$map_buttons['update']	=	'<button type="button" 	{complement} table="'.$table.'"	 action_type="update"  	class="btn btn-color-write btn-info		btn_window_grid_event">	<i class="'.(($button_icon && array_key_exists('update', $button_icon))		?$button_icon['update']:'fa fa-pencil-square-o')					.'"></i> '	.LNG('BTN_UPDATE').	'</button>';
		$map_buttons['remove']	=	'<button type="button" 	{complement} table="'.$table.'"	 action_type="remove" 	class="btn btn-color-write btn-danger	btn_window_grid_event">	<i class="'.(($button_icon && array_key_exists('remove', $button_icon))		?$button_icon['remove']:'glyphicon glyphicon-trash color_write')	.'"></i> '	.LNG('BTN_REMOVE').	'</button>';
		$map_buttons['out']		=	'<button type="button" 															class="btn btn-default" 	data-dismiss="modal">				<i class="glyphicon glyphicon-off"></i> '				.LNG('BTN_CLOSE').	'</button>';
		
		if(is_array($button)){
			foreach($button as $label => $_btn)
			{
				if(isset($map_buttons[$label]))
				{
					$value_btn		=	$_btn;
					$partes_btn		=	explode("</i>",$map_buttons[$label]);
					if($label_force){
						$this->button.=str_replace('{complement}', '', $partes_btn[0]."</i>".$value_btn."</button>");
					}else{
						$this->button.=str_replace('{complement}', $value_btn, $map_buttons[$label]);
					}
				}
			}
		}
		$this->button.=$map_buttons['out'];	      
	}
	
	
	
	
	public function getButton()
	{
		return $this->button;		
	}
	
	
	public function build_grid_form($_param)
	{
		//Visão que o sistema solicita
		$wrs_type_grid		=	fwrs_request('wrs_type_grid');
		$param				=	$_param;
		/*
		 * Tipos de Visão
		 */
		$visao	=	array(	'icon_big'		=>true,
							'icon_middle'	=>true,
							'icon_small'	=>true,
							'list'			=>true,
							'details'		=>true,
							'date'			=>true
						);
						
		$exec_vision	=	'';				
		/*
		 * Acções passadas por cada field 
		 */				
		$actions_fiels		= array(	'primary'		=>true,
										'title'			=>true,
										'type'			=>true,
										'length'		=>true,
										'format'		=>true,
										'format_save'	=>true,
										'grid'			=>true,
										'list'			=>true,
										'order'			=>true,
										'order_type'	=>true);


		//Formulário
		if($wrs_type_grid=='form')
		{
			$current_event			=	fwrs_request('form_event');
			$param					=	 $this->create_form($param,$visao,$actions_fiels,$current_event);
			$param['title_menu']	=	$this->navMenu($param['table']);
			return $param;
		}
		
		/*
		 * 
		 * Verifica se o Evento do Tipo da Ggrid Existe
		 * 
		 */

		if(!isset($visao[$wrs_type_grid]))
		{
				$param['title']	=	LNG('ERROR_TITLE');
				$param['html']	=	fwrs_error(LNG('ERROR_EVENT_NOT_FOUND'));
				return $param;
		}
		
		$exec_vision	=	$wrs_type_grid;

		switch($exec_vision)
		{
			case	'icon_big'		: 	return $this->vision_icon($param,$actions_fiels,$exec_vision); break;
			case	'icon_middle'	:	return $this->vision_icon($param,$actions_fiels,$exec_vision); break;
			case	'icon_small'	:	return $this->vision_icon($param,$actions_fiels,$exec_vision); break;
			case	'list'			:	return $this->vision_grid($param,$actions_fiels,$exec_vision); break;
			case	'details'		:	return $this->vision_grid($param,$actions_fiels,$exec_vision); break;
			case	'date'			:	return $this->vision_grid($param,$actions_fiels,$exec_vision); break;
			
		}
		
		
	}
	
	

	
	/**
	 * 
	 * 
	 * @param query $query
	 * @param array $param
	 * @param array $actions_fiels
	 */
	private function vision_icon($_param,$actions_fiels,$exec_vision)
	{
		$param		=	$_param;
		$html		=	'';
		$data		=	array();
		
		//Extend o Evento
		$this->extendException($param,'runGrid');		
		
		
		$page_current	=	fwrs_request('page_current');
		$page_current	=	 empty($page_current) ? 1 : $page_current;
		
		$page_size		=	fwrs_request('page_size');
		$page_size		=  	empty($page_size) ? 25 : $page_size;
		//$page_size		=	10;
		
		if(!array_key_exists('order_by', $param['order'])){
			$param['order']['order_by']='';
		}
		if(!array_key_exists('order_type', $param['order'])){
			$param['order']['order_type']='';
		}
		
		if(!empty($this->exception))
		{
		
			
			$query			=	$this->exception->change_query_exception($param['table'], $param['order']['order_by'], $param['order']['order_type'], $page_current, $page_size);
			
		}else{
		
			$query			=	$this->manage_param->select($param['field'], $param['table'], $param['order']['order_by'], $param['order']['order_type'], $page_current, $page_size);
		
		}
		
		
		if(empty($query))
		{
			$param['title']	=	LNG('WARNING_TITLE_RESULT_NOT_FOUND');
			$param['html']	=	fwrs_warning(LNG('WARNING_BODY_RESULT_NOT_FOUND'));
			return $param;
		}
		
		
		$query			= 	$this->query($query);
		
		$num_rows		=	0;
	
		/*
		 * Verificando se existe resultado
		 */
		if(!$this->num_rows($query))
		{
				$param['title']	=	LNG('WARNING_TITLE_RESULT_NOT_FOUND');
				$param['html']	=	fwrs_warning(LNG('WARNING_BODY_RESULT_NOT_FOUND'));
				return $param;
		}
		
		
		//$TAG_DIV
		//$TAG_ROWS
		include PATH_TEMPLATE.'grid_'.$exec_vision.'.php';
		
		
		$search	=	array('{TPL_DESC}','{TPL_ICON}','{primary}','{table}');
		
		$TAG_ROWS	=		<<<EOF
			<div class="row body_grid_window">
							{TAG_ROWS}
			</div>
EOF;
		
		while($rows = $this->fetch_array($query))
		{
			$rows_get		=	array();
			$tag_html		=	NULL;
			$primary_key	=	NULL;
			
			$num_rows		=	$rows['ROWS_TOTAL'];
		
			$data[]			=	$rows;

			foreach($param['field'] as $label =>$field)
			{
				//Pegando o ID primary
				if(array_key_exists('primary', $field))
				{
					$primary_key			=	 array();
					$primary_key[$label] 	=	$rows[$label];
					$primary_key			=	 base64_encode(json_encode($primary_key,true));
				}
			
				
				//Apenas informações do list é para apresentar
				if(
						(array_key_exists('list',$field) && $field['list'] && (!array_key_exists('label_icon_custom',$param) || !$param['label_icon_custom'])) ||
						(array_key_exists('label_'.$exec_vision, $field) && $field['label_'.$exec_vision])
				){
					//Fazendo as correções de formatação
					if(!empty($field['type_convert']))
 					{
						$rows_get[]	=	$this->type_convert($field['type_convert'],$rows[$label],$field);						
					}else{
 						$rows_get[]	=	$rows[$label];
 					}
				}
				
			}
			
			$replace	=	array(	implode(' - ',$rows_get),
									$param['icon'],
									$primary_key,
									$param['table']
								);
																
			$tag_html	=	str_replace($search, $replace, $TAG_DIV);
			
			$html.=$tag_html;
		}
		
		$param['visao_atual']	=	$exec_vision;
		$data['param_original'] = 	$param;
		
		$param['title_menu']	=	$this->navMenu($param['table']);
		$param['html']			=	str_replace('{TAG_ROWS}', $html, $TAG_ROWS).$this->pagination($param,$num_rows,$page_size,$page_current,$exec_vision);
		$param['data']			=	json_encode($data);
		return $param;
	}
	
	
	
	private function checkbox_exist()
	{
		return array(
				'title' 	=> 	"<input type='checkbox' class='checkline'>",
				'list' 		=> 	1,
				'basic'		=> 	1,
				'grid' 		=> 	1,
				'field' 	=> 	'checkbox_linha',//chamada muito importante 
				'width' 	=> 	25,
				'filterable'=>	false
				/*,'template'	=>	'#=checkbox_linha#'*/
		);
	}
	
	
	private function vision_grid($_param,$actions_fiels,$exec_vision)
	{
		$data					=	array();
		$param					=	$_param;
		$param['title_menu']	=	$this->navMenu($param['table']);
		$columns				=	array();
		$primary_key			=	'';
		if(array_key_exists('checkbox', $param) && $param['checkbox']){
				$columns[]				=	$this->checkbox_exist();
		}
		
		
			foreach($param['field'] as $label =>$field)
			{
					//Pegando o ID primary
					if(isset($field['primary']))
					{
						$primary_key			=	 array();
						$primary_key[$label] 	=	$field;
					}
					$_tmp_column			=	$field;
					$_tmp_column['field']	=	$label;
					
					$_tmp_column['filterable'] = (array_key_exists('filterable',$field) && !$field['filterable']) ?false:true;
					
					/*
					 * TODO: Analisar o porquê do KendoUi não respeitar o tamanho das colunas enviadas para o script.  A coluna está sendo tratada corretamente de acordo com seu conteúdo e sua exceção (use_auto_width), porém o KendoUi parece espremer as colunas pela quantidade existente quando há muitas colunas a serem exibidas (tabela de usuarios por exmeplo)
					 */
					if(!isset($_tmp_column['width']) && (!array_key_exists('use_auto_width', $param) || $param['use_auto_width']))
					{
						$_tmp_column['width']	=	 (strlen($field['title'])*13)+10;
					}
							
					
					// novo tratamento para exibicao das colunas de acordo com o configurado na libQuery
					if(
						(
							($exec_vision=='list' && (
							!isset($field['grid']) ||
							!isset($field['basic']) ||
							!isset($field['list'])
							)) || // se a visao for list e nao tiver parametro, mostra
							($exec_vision=='list' 		&& isset($field['grid']) 	&& $field['grid']=='1') || // se a visao for list e tiver o parametro list, mostra
							($exec_vision=='details' 	&& isset($field['list']) 	&& $field['list']=='1') || // se a visao for details e tiver o parametro details (list), mostra
							($exec_vision=='date' 		&& isset($field['basic']) 	&& $field['basic']=='1')   // se a visao for date(lista basica) e tiver o parametro lista basica (basic), mostra
						)
					){
								$columns[]				=	$_tmp_column;					
					}
					
			 
					
			}
		 	
			

		
		$kendoUI					=	new KendoUi();
		$param['filterable'] 		= 	'true';
		
		$data['param_original'] 	= 	$param;
		$param['data']				=	json_encode($data);
		
		$param['html']				=	$kendoUI->grid_window($columns, $param);
		return $param;		
	}
	
	
	private function runGrid()
	{
		
		header('Content-Type: application/json');
		$request 			=	json_decode(file_get_contents('php://input'),true);
		$page				=	$request['page'];
		$take				=	$request['take'];
		$skip				=	$request['skip'];
		$column_filters		=	array_key_exists('filters', $request)?$request['filters']:'';
		$sort				=	isset($request['sort']) ? $request['sort'] : array();
		$pageSize			=	$request['pageSize'];
		$table				=	 fwrs_request('table');
		$type_convert		=	NULL;	 
		$param				=	$this->manage_param->getMetodoTabela($table);
		$is_icon			=	false;
		$is_select			=	NULL;
		$checkbox_exist		=	false;
		$checkbox_val		=	'';

		//Extend o Evento
		$this->extendException($param,'runGrid');
		if(array_key_exists('checkbox', $param))
		{
			if($param['checkbox'])
			{
				$checkbox_val						=	$this->checkbox_exist();
				$check_vale[$checkbox_val['field']]	=	$checkbox_val;
				$param_tmp							=	array_merge($check_vale,$param['field']);
				$param['field']						=	$param_tmp;
				$checkbox_exist						=	true;
			}
		}
		
		if(!count($sort))
		{
				$sort				=	array('field'=>null,'dir'=>null);
				$sort['field']		=	$param['order']['order_by'];
				$sort['dir']		=	$param['order']['order_type'];
		}
		else
		{
			$sort	=	$sort[0];
		}
		

		$where=array();

		// excecao para usuarios NAO MST ou ADM, não visualizarem usuarios maiores que eles proprios
		// felipeb 20160226
		$perfil_logado 		= trim(WRS::INFO_SSAS_LOGIN('PERFIL_ID'));
		if($perfil_logado!='MST' || $perfil_logado=='ADM'){
			$CUSTOMER_ID 			= WRS::CUSTOMER_ID();
			if($table=='ATT_WRS_USER'){
				$where[]="PERFIL_ID != ''MST'' and CUSTOMER_ID = ".$CUSTOMER_ID;
			}
			if($table=='ATT_WRS_CUSTOMER'){
				$where[]="CUSTOMER_ID = ".$CUSTOMER_ID;
			}
			if($table=='REL_WRS_CUBE_USER'){
				$where[]="CUSTOMER_ID = ".$CUSTOMER_ID;
			}
			if(WRS_MANAGE_PARAM::confereTabelaCadastroRetorno($table)=='ATT_WRS_LOG'){
				$where[]="CUSTOMER_ID = ".$CUSTOMER_ID;
			}
		}
		
		$kendoUI			=	new KendoUi();
		$where_filters  	= 	$kendoUI->filtersToWhere($column_filters);
		
		if($where_filters!=''){
			$where[] = $where_filters;
		}
		
		$where = implode(" AND ",$where);
		
		if(!empty($this->exception))
		{
			$query	=	$this->exception->change_query_exception($table, $sort['field'], $sort['dir'], $request['page'], $request['pageSize'],$where);
		}else{				
			//		unset($_columns['WRS_ICON']);
			$query	=	$this->manage_param->select('*', $table, $sort['field'], $sort['dir'], $request['page'], $request['pageSize'],$where);
		}
		
		$query	=	 $this->query($query);
		
		if(isset($param['field']['WRS_ICON']))
		{
			$is_icon	=	 true;
		}
		
		
		
		
		foreach($param['field'] as $label => $tools)
		{
			if(isset($tools['type_convert']))
			{
				$type_convert[$label]	=	$tools;
			}
			
			
			if(
					isset($tools['is_select']) && 										// alem de validar se ele é is_select  
					(!array_key_exists('list', $tools) || $tools['list']==true) && 		// deve validar para validar se o campo que é is_select realmente tem de ser executado em uma grid, sendo que o SQL será executado para cada linha.  Este comando deve ser atualizado para ser trazido direto no SQL e acrescentar um field a mais com o parametro grid_only - felipeb 20160321
					(!array_key_exists('basic', $tools) || $tools['basic']==true) && 
					(!array_key_exists('grid', $tools) || $tools['grid']==true)
			)
			{
					$is_select[$label]	=	$tools['is_select'];
			}
			
			
		}
		

		 
		
		
		$num_rows	=	NULL;
		
		while($rows	=	 $this->fetch_array($query))
		{
			
			
			$rows_tmp		=	$rows;
			$num_rows		=	(!array_key_exists('ROWS_TOTAL', $rows))?$this->num_rows($query):$rows['ROWS_TOTAL'];
			
			if($is_icon){
				$rows_tmp['WRS_ICON']	=	'<img width="50" src="imagens/icons/'.$param['icon'].'">';
			}
	
			
		
			//Fazendo as correções de formatação
			if(!empty($type_convert))
			{
				foreach($type_convert as $label =>$tools)
				{
						$rows_tmp[$label]	=	 $this->type_convert($tools['type_convert'],$rows_tmp[$label],$tools);
				}
			}

			//Iniciando o seletc
			if(!empty($is_select))
			{
				foreach($is_select as $sel_label =>$sel_value)
				{
					
					
					if($sel_label=='checkbox_linha')
					{
						echo '<input type="checkbox"/>';
						continue;
					}
					
					if(is_array($sel_value))
					{

						$rows_tmp[$sel_label]=	@$sel_value[$rows_tmp[$sel_label]];						
					}else{
						$param_select	=	$this->manage_param->getMetodoTabela($sel_value);
						
						$where_box		=	$param_select['primary']."=''".$rows_tmp[$sel_label]."''";

						
						if(!empty($this->exception))
						{
								
							$query_box			=	$this->exception->change_query_exception($param_select['field'], $param_select['table'], $param_select['order']['order_by'], $param_select['order']['order_type'], 1, 1);
								
						}else{
						
							$query_box			=	$this->manage_param->select($param_select['field'], $param_select['table'], $param_select['order']['order_by'], $param_select['order']['order_type'], 1, 1,$where_box);
							
						}
						
						
						
						$query_box			=	$this->query($query_box);//,true,false);
						$html_option		=	'';
						if($this->num_rows($query_box))
						{
							$row_box	=	 $this->fetch_array($query_box);

							if(isset($param['field'][$param_select['primary']]['select_fields_in_table']) && is_array($param['field'][$param_select['primary']]['select_fields_in_table']))
							{
							
								foreach($param['field'][$param_select['primary']]['select_fields_in_table'] as $label)
								{
									$html_option[]	=	$row_box[$label];
								}
							
							}else{
								
								foreach($param_select['field'] as $param_select_label =>$param_select_value)
									{
										if(isset($param_select_value['select']))
										{
											$html_option[]	=	$row_box[$param_select_label];
										}
									}
									
							}
							
							$rows_tmp[$sel_label]		=	is_array($html_option) ?  implode(' - ',$html_option) : '';
						}
					
					}
				}
			}
			//Ens select
			
			if(array_key_exists($sort['field'], $rows_tmp))			
				$rows_tmp[$sort['field']]	=	'<b>'.$rows_tmp[$sort['field']].'</b>';
			
			//Inserindo o checkbox field
			if($checkbox_exist)
			{
				$rows_tmp[$checkbox_val['field']]	=	$checkbox_val['title'];
			}
			
			$resultGrid[]	=	 $rows_tmp;	
		}


		$result				=	 array();
		$result['total']	=	$num_rows;
		$result['data']		=	@$resultGrid;
			
		echo json_encode($result);
		
		
	}
	
	
	
	
	
	
	private function pagination($_param,$num_rows,$page_size,$page_current,$wrs_type_grid)
	{
		
		$param		=	$_param;

		
		$size_page		=	ceil($num_rows/$page_size);

		
		$param['num_rows']		=	$num_rows;
		$param['page_size']		=	$page_size;
		$param['page_current']	=	$page_current;
		$param['size_page']		=	$size_page;
		$param['wrs_type_grid']	=	$wrs_type_grid;
		 
		$of			=	LNG('OF');
		$page		=	LNG('PAGE');
		$json		=	base64_encode(json_encode($param,true));
		
		$option_select	=	 array(10,25,50,100,500);
		
		$option_html	=	NULL;
		foreach($option_select as $value)
		{
			$option_html.=fwrs_option($value, $value,$page_size);
		}
		
		
		$pagination	=	<<<EOF
		
		<div class="row wrs_grid_window_pagination form-inline" json="{$json}">
			<div class="form-group ">
					  	<button type="button" rel="first" class="btn  btn-default"><span class="glyphicon glyphicon-step-backward"></span></button>
						<button type="button" rel="back" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></button>
					 	
						<button type="button" 	class="btn  btn-link" disabled> {$page}</button>
						<input 	type="text"  	placeholder="1" value="{$page_current}"  class="form-control">
					  	<button type="button" 	class="btn  btn-link" disabled> {$of} {$size_page}</button>
					  	
					 	<button type="button" rel="next"	class="btn  btn-default"><span class="glyphicon glyphicon-chevron-right"></span></button>
					  	<button type="button" rel="last"	class="btn  btn-default"><span class="glyphicon glyphicon-step-forward"></span></button>
					  
					   <select class="wrs_pagination_gerid_window form-control">
					    	{$option_html}
					  </select>
  			</div>
		</div>
		<script>$('.wrs_grid_window_pagination').WrsGridWindowPagination();</script>
EOF;

	return $pagination;
	
	}
	
	
	 
	
	
	
	
	
	
	
	private function navMenu($table)
	{
		$TITLE_GRID_WINDOW_MENU_MANAGER					=	LNG('TITLE_GRID_WINDOW_MENU_MANAGER');
		$TITLE_GRID_WINDOW_MENU_LIST					=	LNG('TITLE_GRID_WINDOW_MENU_LIST');
		$TITLE_GRID_WINDOW_MENU_DETAILS					=	LNG('TITLE_GRID_WINDOW_MENU_DETAILS');
		$TITLE_GRID_WINDOW_MENU_BIG_ICON				=	LNG('TITLE_GRID_WINDOW_MENU_BIG_ICON');
		$TITLE_GRID_WINDOW_MENU_MEDIUM_ICON				=	LNG('TITLE_GRID_WINDOW_MENU_MEDIUM_ICON');
		$TITLE_GRID_WINDOW_MENU_SMALL_ICON				=	LNG('TITLE_GRID_WINDOW_MENU_SMALL_ICON');
		$TITLE_GRID_WINDOW_MENU_LIST_INFO				=	LNG('TITLE_GRID_WINDOW_MENU_LIST_INFO');

		$permite_menu_visao_tabela_class				=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,null,true);
		$permite_menu_visao_tabela_list_class			=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'list',true);
		$permite_menu_visao_tabela_date_class			=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'date',true);
		$permite_menu_visao_tabela_details_class		=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'details',true);
		$permite_menu_visao_tabela_icon_big_class		=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'icon_big',true);
		$permite_menu_visao_tabela_icon_middle_class	=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'icon_middle',true);
		$permite_menu_visao_tabela_icon_small_class		=	WRS_MANAGE_PARAM::confereVisaoDisponivel($table,'icon_small',true);
		
 		$navMemnu	=	<<<EOF
									  <ul class="nav navbar-nav navbar-right wrs_grid_window_custum_tools_menu {$permite_menu_visao_tabela_class}">
								            <li class="dropdown liin">
								              <a id="drop1" href="#" class="dropdown-toggle liin" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="true">
								                <i class="fa fa-eye"></i> {$TITLE_GRID_WINDOW_MENU_MANAGER}
								                <span class="caret"></span>
								              </a>
								              <ul class="dropdown-menu wrs_grid_window_event" role="menu" aria-labelledby="drop1">
								                <li role="presentation" class="{$permite_menu_visao_tabela_list_class}">		<a role="menuitem" rel="list"			tabindex="-1" table="{$table}" href="#"><i class="fa fa-table"></i> {$TITLE_GRID_WINDOW_MENU_LIST}</a></li>
												<li role="presentation" class="{$permite_menu_visao_tabela_date_class}">		<a role="menuitem" rel="date"			tabindex="-1" table="{$table}" href="#"><i class="fa fa-bars"></i> {$TITLE_GRID_WINDOW_MENU_LIST_INFO}</a></li>
								                <li role="presentation" class="{$permite_menu_visao_tabela_details_class}">		<a role="menuitem" rel="details"		tabindex="-1" table="{$table}" href="#"><i class="fa fa-list"></i> {$TITLE_GRID_WINDOW_MENU_DETAILS}</a></li>
								                <li role="presentation" class="{$permite_menu_visao_tabela_icon_big_class}">	<a role="menuitem" rel="icon_big" 		tabindex="-1" table="{$table}" href="#"><i class="fa fa-th-large"></i> {$TITLE_GRID_WINDOW_MENU_BIG_ICON}</a></li>
								                <li role="presentation" class="{$permite_menu_visao_tabela_icon_middle_class}">	<a role="menuitem" rel="icon_middle" 	tabindex="-1" table="{$table}" href="#"><i class="fa fa-th"></i> {$TITLE_GRID_WINDOW_MENU_MEDIUM_ICON}</a></li>
								                <li role="presentation" class="{$permite_menu_visao_tabela_icon_small_class}">	<a role="menuitem" rel="icon_small" 	tabindex="-1" table="{$table}" href="#"><i class="fa fa-ellipsis-h"></i> {$TITLE_GRID_WINDOW_MENU_SMALL_ICON}</a></li>
								               </ul>
								            </li>
								         </ul>
EOF;

		
	return $navMemnu;
		
		
	}
	
	
	
}

?>