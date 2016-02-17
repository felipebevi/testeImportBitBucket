<?php

/**
 * Contem as querys para a construção do Administrativo
 * @author msdantas, fbevilacqua
 *
 */

class WRS_MANAGE_PARAM
{
	private $array_retorno_padrao = array(	 'title'				=>	'',
										 'button'				=>	'',
										 'field'				=>	'',
										 'table'				=>	'',
										 'order'				=>	'',
										 'icon'					=>	'',	
										 'primary'				=>	'',
										 'button_force_label'	=>	false, 		// forca o label dos icones na visualizacao de ICON de acordo com o array abaixo
						 				 'button_icon'			=>	'',			// altera o icone padrao de cada botao na WindowGrid
										 'exception'			=>	'', 		// quando houver exceptions (uma view ao inves de tabela) utilizar um array com o nome da classe em questão para esta excecao 
										 'checkbox'				=>	false,		// se existe checkbox por linha na visualizacao de grid
										 'label_icon_custom'	=>	false,		// se existir labels personalizados por visao de icone
 										 'use_auto_width'		=>	true, 		// usar o calculo de largura para a coluna de acordo com o nome do titulo da mesma
						 				 'callback_btn_events'	=>	'', 		// se as acoes padrao dos botoes será alterada, adicionar o nome da acao de callback
										 'actionSingle'			=>	'', 		// se a acao do clique simples sera alterada, adicionar o nome da acao de callback
										 'actionDouble'			=>	'',			// se a acao do clique duplo sera alterada, adicionar o nome da acao de callback
										 'aplicaClassLinhas'	=>	true,		// [false,true,ClassEspecifica] se o parametro classDataLine sera aplicado nas linhas da grid do KendoUi
						 				 'aplicaClassHeaders'	=>	false		// [false,true,ClassEspecifica] se o parametro classDataHeader sera aplicado nas headers da grid do KendoUi
		 );

	private static $array_configuracao_padrao = 	array(
			'tabela_bd'				=>	'',
			'acesso_requisicao'		=>	true,
			'acesso_via_menu'		=>	true,
			'metodo_classe_param'	=>	''
	);
	
	
	private static $array_configuracao_tabelas = 	array(
			'ATT_WRS_CUSTOMER'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_CUSTOMER_TESTE',
					'metodo_classe_param'	=>	'ATT_WRS_CUSTOMER',
					'nome_menu_LNG'			=>	'MENU_ADMIN_CUSTOMER'
			),
			'ATT_WRS_USER'					=> 	array(
					'tabela_bd'				=>	'ATT_WRS_USER',
					'metodo_classe_param'	=>	'ATT_WRS_USER',
					'nome_menu_LNG'			=>	'MENU_ADMIN_USER'
			),
			'ATT_WRS_DATABASE'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_DATABASE',
					'metodo_classe_param'	=>	'ATT_WRS_DATABASE',
					'nome_menu_LNG'			=>	'MENU_ADMIN_DATABASE'
			),
			'ATT_WRS_CUBE'					=> 	array(
					'tabela_bd'				=>	'ATT_WRS_CUBE',
					'metodo_classe_param'	=>	'ATT_WRS_CUBE',
					'nome_menu_LNG'			=>	'MENU_ADMIN_CUBE'
			),
			'ATT_WRS_HIERARCHY'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_HIERARCHY',
					'metodo_classe_param'	=>	'ATT_WRS_HIERARCHY',
					'nome_menu_LNG'			=>	'MENU_ADMIN_HIERARCHY'
			),
			'ATT_WRS_PERFIL'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_PERFIL',
					'metodo_classe_param'	=>	'ATT_WRS_PERFIL',
					'nome_menu_LNG'			=>	'MENU_ADMIN_PERFIL'
			),
			'REL_WRS_CUBE_USER'				=> 	array(
					'tabela_bd'				=>	'REL_WRS_CUBE_USER',
					'metodo_classe_param'	=>	'REL_WRS_CUBE_USER',
					'nome_menu_LNG'			=>	'MENU_ADMIN_CUBE_USER'
			),
			'ATT_WRS_REPORT'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_REPORT',
					'metodo_classe_param'	=>	'ATT_WRS_REPORT',
					'nome_menu_LNG'			=>	'MENU_ADMIN_REPORT'
			),
			'ATT_WRS_DOWNLOAD'				=> 	array(
					'tabela_bd'				=>	'ATT_WRS_DOWNLOAD',
					'metodo_classe_param'	=>	'ATT_WRS_DOWNLOAD',
					'nome_menu_LNG'			=>	'MENU_ADMIN_DOWNLOAD'
			),
			'ATT_WRS_LOG'					=> 	array(
					'tabela_bd'				=>	'ATT_WRS_LOG',
					'metodo_classe_param'	=>	'ATT_WRS_LOG',
					'nome_menu_LNG'			=>	'MENU_ADMIN_LOG'
			),
			'GET_SSAS_REPORT'				=> 	array(
					'tabela_bd'				=>	'GET_SSAS_REPORT',
					'metodo_classe_param'	=>	'GET_SSAS_REPORT',
					'nome_menu_LNG'			=>	'MENU_ADMIN_SSAS_REPORT',
					'acesso_via_menu'		=>	false
			),
			'GET_SSAS_LAYOUTS'				=> 	array(
					'tabela_bd'				=>	'GET_SSAS_LAYOUTS',
					'metodo_classe_param'	=>	'GET_SSAS_LAYOUTS',
					'nome_menu_LNG'			=>	'MENU_ADMIN_SSAS_LAYOUTS',
					'acesso_via_menu'		=>	false
			)
			
	);
	
	public static function GET_CONFIG_TABLE($tabela=NULL){
		$arr_todos = array();
		foreach(self::$array_configuracao_tabelas as $tabela_name=>$config){
			$arr_todos[$tabela_name] = array_merge(self::$array_configuracao_padrao,self::$array_configuracao_tabelas[$tabela_name]);
		}
		if($tabela==NULL){
			return $arr_todos;
		}
		if(!is_string($tabela) || !array_key_exists($tabela, $arr_todos)){
			// se for passado o nome de uma tabela diferente do padrao só porque o nome da tabela é um teste ou porque mudou,
			// procura nos nomes das tabelas configurados para retornar o objeto correto e prosseguir com o fucnionamento do sistema
			$dados_contem_tabela_parametro=array();
			foreach($arr_todos as $nome_tabela=>$dados){
				if($dados['tabela_bd']==$tabela){
					$dados_contem_tabela_parametro = $dados;
					break;
				}
			}
			if(count($dados_contem_tabela_parametro)==0){
				return false;
			}else{
				return $dados_contem_tabela_parametro;
			}
		}
		return $arr_todos[$tabela];
	}

	public function getDadosTabelaConfig($tabela=NULL){
		return self::GET_CONFIG_TABLE($tabela);
	}
	
	public function getMetodoTabela($tabela){
		$dados_tabela = $this->getDadosTabelaConfig($tabela);
		return $this->$dados_tabela['metodo_classe_param']();
	}
	
	public function load($_event)
	{
		
		$dados_tabela_evento = $this->getDadosTabelaConfig($_event);
		if($dados_tabela_evento!=false && $dados_tabela_evento['acesso_requisicao']){
			return true;
		}
				
		return false;
	}
	
	/**
	 * 
	 * Contruindo o select 
	 * 
	 * @param string $_columns
	 * @param string $table
	 * @param string $orderBy
	 * @param string $orderByPOS
	 * @param int 	 $_start
	 * @param int 	 $_end
	 */
	public static function select($columns,$table,$orderBy,$orderByPOS,$_start,$_end, $_where=NULL)
	{
		$_columns	=	$columns;
		$where		=	$_where;
		
		if(is_array($columns))
		{
			unset($_columns['WRS_ICON']);
		}
		
		if(is_array($where))
		{
			foreach($_where as $label=>$value)
			{
				$where	.=	$label.' LIKE "%'.$value.'%"';
			}
		}
		
		$columns 	= 	empty($_columns) 	?	'*' 					: 	$_columns;
		$columns	=	is_array($columns) 	?	implode(',',array_keys($columns)) 	:	$columns;
		
		return  "exec Select_Table '".$table."', '".$orderBy."', '".$orderByPOS."', '".$where."',".$_start.",".$_end;
		//return ' SELECT '.$columns.' FROM '.$table.' '.$where.' ORDER BY '.$orderBy.' '.$orderByPOS;
	}
	
	public function ATT_WRS_CUSTOMER_TESTE(){
		return $this->ATT_WRS_CUSTOMER();
	}
	
	//Classe de Cliente ADMINISTRATIVO
	public function ATT_WRS_CUSTOMER()
	{
		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_customer');
		$button['import']		=	LNG('bt_import_customer');
		
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_CUSTOMER');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'CUSTOMER_ID' ,'order_type'=>'ASC');
		$fields	=	array();
		

		$exceptions	=	array('class'=>'ATT_WRS_CUSTOMER'	,'file'=>'ATT_WRS_CUSTOMER'	, 'type'=>'');
		$extend 	= 	array('class'=>'ATT_WRS_CUSTOMER' 	,'file'=>'ATT_WRS_CUSTOMER');
		
		$fields['WRS_ICON']			=	array('title'=>'Icone'				,   'width'=>50,     'basic'=>true, 'grid'=>true, 'is_upload'=>true);
		$fields['CUSTOMER_ID']		=	array('title'=>'ID'					,   'primary'=>true, 'class'=>'hide');
		$fields['CUSTOMER_CODE']	=	array('title'=>'Código'				,	'length'=>15,    'list'=>true,	'basic'=>true,  'grid'=>true);
		$fields['CUSTOMER_DESC']	=	array('title'=>'Nome'				,	'length'=>180,   'list'=>true,	'basic'=>true , 'grid'=>true);
		$fields['CUSTOMER_GROUP']	=	array('title'=>'Corporação'			,	'length'=>180);
		$fields['CUSTOMER_STATUS']	=	array('title'=>'Status'				,	'is_select'=>array('-1'=>'Selecionar','1'=>'Ativo','0'=>'Inativo'), 'basic'=>true, 'list'=>true, 'grid'=>true);
		$fields['CUSTOMER_EXPIRY']	=	array('title'=>'Dias Expira Senha'	,	'type'=>'int');
		//Falta o UPLOAD do LOGO
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_CUSTOMER'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,

				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'CUSTOMER_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'carrega_grid_list_admin', 							// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
			);
	}
	
	
	//Classe de Usuário ADMINISTRATIVO
	public function ATT_WRS_USER()
	{
		$button		=	$button_icon	=	array();
		
		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');		
		$button['export']		=	LNG('bt_export_user');
		$button['import']		=	LNG('bt_import_user');
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_USER');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		//return $this-> ATT_WRS_CUSTOMER();
		//return $this-> ATT_WRS_DATABASE();
		//return $this-> ATT_WRS_CUBE();
		//return $this-> ATT_WRS_HIERARCHY();
		//return $this->ATT_WRS_PERFIL();
		//return $this->REL_WRS_CUBE_USER();
		//return $this->ATT_WRS_REPORT();
//		return $this->ATT_WRS_DOWNLOAD();

		$extend = 	array('class'=>'ATT_WRS_USER' ,'file'=>'ATT_WRS_USER');
		
		$order							=	array('order_by'=>'USER_ID' ,'order_type'=>'ASC');
		
		$fields							=	array();
		$fields['WRS_ICON']				=	array('title'=>'Icone'					, 'width'=>60,     'basic'=>true, 'grid'=>true, 'is_upload'=>true);
		$fields['USER_ID']				=	array('title'=>'ID'						, 'primary'=>true, 'type'=>'int', 'class'=>'hide');
		$fields['USER_CODE']			=	array('title'=>'Usuário'				, 'length'=>200,   'basic'=>true, 'list'=>true,  'grid'=>true);
		$fields['USER_PWD']				=	array('title'=>'Senha'					, 'length'=>30, 	'type'=>'password');
		$fields['USER_DESC']			=	array('title'=>'Nome'					, 'length'=>200,   'basic'=>true, 'list'=>true,  'grid'=>true);
		$fields['USER_EMAIL']			=	array('title'=>'Email'					, 'length'=>200);
		$fields['USER_TYPE']			=	array('title'=>'Cargo'					, 'length'=>80);
		$fields['USER_FILTER']			=	array('title'=>'Nível de Estrutura'		, 'length'=>80);
		$fields['USER_FILTER_VALUE']	=	array('title'=>'Filtro Nível Estrutura' , 'length'=>160);
		$fields['USER_INTERNAL']		=	array('title'=>'Internal'				, 'length'=>160);
		$fields['USER_STATUS']			=	array('title'=>'Status'					, 'is_select'=>array('-1'=>'Selecionar','1'=>'Ativo','0'=>'Inativo'), 'basic'=>true, 'list'=>true, 'grid'=>true);
		$fields['USER_FORMAT']			=	array('title'=>'Tema'					, 'is_select'=>array('-1'=>'Selecionar','azul'=>'Azul','cinza'=>'Cinza','laranja'=>'Laranja','verde'=>'Verde','vermelho'=>'Vermelho'));
		$fields['LANGUAGE_ID']			=	array('title'=>'Idioma'					, 'is_select'=>array('-1'=>'Selecionar','EN'=>'Inglês','ES'=>'Espanhol','POR'=>'Português'));
		$fields['PERFIL_ID']			=	array('title'=>'Perfil'					, 'is_select'=>'ATT_WRS_PERFIL', 'select_fields_in_table'=>array('PERFIL_ID','PERFIL_DESC'), 'length'=>255);
		$fields['CUSTOMER_ID']			=	array('title'=>'Cliente'				, 'is_select'=>'ATT_WRS_CUSTOMER', 'select_fields_in_table'=>array('CUSTOMER_CODE','CUSTOMER_DESC'),'length'=>255);

		return array(	'title'		=>	LNG('TITLE_ATT_WRS_USER'), 
						'button'	=>	$button,
						'field'		=>	$fields,
						'table'		=>	$table,
						'order'		=>	$order,
						'icon'		=>	'user.png',
						'primary'	=>	'USER_ID',
						'extend'				=>	$extend,											// NEW
						'checkbox'				=>	true, 												// NEW
						'button_icon'			=>	$button_icon,										// NEW
						'button_force_label'	=>	true, 			  									// NEW
		 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
						'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
						'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
						'exception'				=>	true, 												// NEW
						'aplicaClassLinhas'		=>	true,												// NEW
						'aplicaClassHeaders'	=>	'text-center'										// NEW
					);
		
	}
	
	
	//Classe de Database ADMINISTRATIVO
	public function ATT_WRS_DATABASE()
	{

		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_database');
		$button['import']		=	LNG('bt_import_database');
		
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_DATABASE');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'DATABASE_ID' ,'order_type'=>'ASC');
		$extend	= 	array('class'=>'ATT_WRS_DATABASE' 	,'file'=>'ATT_WRS_DATABASE');
		$fields	=	array();
		
		$fields['WRS_ICON']			=	array('title'=>'#',   			'width'=>50,     'basic'=>true, 'grid'=>true);
		$fields['DATABASE_ID']		=	array('title'=>'Código',   		'primary'=>true, 'list'=>true,  'grid'=>true);
		$fields['DATABASE_DESC']	=	array('title'=>'Nome',			'length'=>255,   'basic'=>true, 'list'=>true,  'grid'=>true);
		$fields['DATABASE_COMMENT']	=	array('title'=>'Comentários',   'length'=>255,   'list'=>true,  'grid'=>true);
		$fields['DATABASE_USER']	=	array('title'=>'Usuário',   	'length'=>30,    'grid'=>true);
		$fields['DATABASE_PWD']  	=	array('title'=>'Senha',   		'length'=>30);
		$fields['DATABASE_STATUS']	=	array('title'=>'Status',		'is_select'=>array('-1'=>'Selecionar','1'=>'Ativo','2'=>'Bloqueado','3'=>'Inativo'),      'basic'=>true, 'list'=>true, 'grid'=>true);
		$fields['DATABASE_LINK']  	=	array('title'=>'Link',   		'length'=>255);
		//IMAGEM (DATABASE_IMAGE) => Combo com as imagens que foram UPLOAD
		$fields['DATABASE_ORDER']	=	array('title'=>'Ordem',   		'type'=>'int');
		/*
		 * TODO: nao existe ATT_WRS_SERVER para buscar informacoes
		 */
		//$fields['SERVER_ID']	=	array('title'=>'Servidor',   	'is_select'=>'ATT_WRS_SERVER','list'=>true,   'grid'=>true);
		$fields['SERVER_ID']	=	array('title'=>'Servidor',   	'list'=>true,   'grid'=>true);
		$fields['CUSTOMER_ID']		=	array('title'=>'Cliente',   	'is_select'=>'ATT_WRS_CUSTOMER','list'=>true,   'grid'=>true);
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_DATABASE'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'DATABASE_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
			);
	}
	
	
	//Classe de Cubo ADMINISTRATIVO
	public function ATT_WRS_CUBE()
	{
		//Botões Language
		$button		=	$button_icon	=	array();
		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_cube');
		$button['import']		=	LNG('bt_import_cube');
		
		
	
		if(!WRS_USER::getArrPerfUser('DRG'))
		{
			$button['remove']	=	LNG('bt_remove');
		}
		

		
		//Personalizando o icone
		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';
		
		

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_CUBE');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'CUBE_ID' ,'order_type'=>'ASC');
		
		//Extend para classe que irá executar a query diferente do GeneridWindow
		$extend	= 	array('class'=>'ATT_WRS_CUBE' 	,'file'=>'ATT_WRS_CUBE');
		
		
		//Campos da table
		$fields	=	array();
		$fields['WRS_ICON']			=	array('title'=>'#',   		'width'=>50,    'basic'=>true , 'grid'=>true);
		$fields['CUBE_ID']		    =	array('title'=>'Código',   	'primary'=>true,'list'=>true,   'grid'=>true);
		$fields['CUBE_DESC']	    =	array('title'=>'Nome',		'length'=>180,  'basic'=>true,  'list'=>true,  'grid'=>true);
		$fields['CUBE_STATUS']		=	array('title'=>'Status',	'is_select'=>array('-1'=>'Selecionar','1'=>'Ativo','0'=>'Inativo'),     'basic'=>true, 'list'=>true, 'grid'=>true);
		$fields['DATABASE_ID']		=	array('title'=>'Database',  'is_select'=>'ATT_WRS_DATABASE', 'select_fields_in_table'=>array('DATABASE_ID','DATABASE_DESC'),  'length'=>180,    'list'=>true,  'grid'=>true);
		$fields['CUSTOMER_ID']		=	array('title'=>'Cliente',   'is_select'=>'ATT_WRS_CUSTOMER', 'select_fields_in_table'=>array('CUSTOMER_CODE','CUSTOMER_DESC'),  'grid'=>true,     'class'=>'hide');
		
		
		
		//Atributos de execuções e Eventos
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_CUBE'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'CUBE_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
			);
	}
	
	
	//Classe de Hierarquia ADMINISTRATIVO
	/*public function ATT_WRS_HIERARCHY()
	{
		$button	=	array('new'=>'');
		
		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_HIERARCHY');
		$table	=	$dados_tabela_evento['tabela_bd'];

		$order	=	array('order_by'=>'CUSTOMER_ID' ,'order_type'=>'ASC');
		$fields	=	array();
		
		$fields['WRS_ICON']			=	array('title'=>'#'					,   'width'=>50,    'basic'=>true , 'grid'=>true);
		$fields['CUBE_ID']		    =	array('title'=>'ID'					,   'primary'=>true,'list'=>true,   'grid'=>true);
		$fields['CUBE_DESC']	    =	array('title'=>'CUBE_DESC'		    ,	'length'=>255,  'basic'=>true,  'list'=>true,  'grid'=>true);
		$fields['CUBE_STATUS']		=	array('title'=>'CUBE_STATUS'		,	'type'=>'int',  'basic'=>true,  'list'=>true,  'grid'=>true);
		$fields['DATABASE_ID']		=	array('title'=>'DATABASE ID'	    ,   'is_select'=>'ATT_WRS_DATABASE','list'=>true,  'grid'=>true);
		$fields['CUSTOMER_ID']		=	array('title'=>'CUSTOMER ID'	    ,   'grid'=>true);
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_HIERARCHY'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'CUBE_ID'
			);
	}*/
	
	
	//Classe de Perfil ADMINISTRATIVO
	public function ATT_WRS_PERFIL()
	{
		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_profile');
		$button['import']		=	LNG('bt_import_profile');
		
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_PERFIL');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'PERFIL_ID' ,'order_type'=>'ASC');
		$extend	= 	array('class'=>'ATT_WRS_PERFIL' 	,'file'=>'ATT_WRS_PERFIL');
		$fields	=	array();
		
		$fields['WRS_ICON']			=	array('title'=>'#',   			'width'=>50,  		'basic'=>true,  'grid'=>true);
		$fields['PERFIL_ID']		=	array('title'=>'Código',   		'primary'=>true	,	'length'=>15);
		$fields['PERFIL_DESC']		=	array('title'=>'Nome',			'length'=>180,	 	'basic'=>true,  'list'=>true,  'grid'=>true);
		$fields['PERFIL_LEVEL']		=	array('title'=>'Nível'	,		'length'=>15);
		$fields['PERFIL_STATUS']	=	array('title'=>'Status',		'is_select'=>array('-1'=>'Selecionar','1'=>'Ativo','0'=>'Inativo'), 'basic'=>true, 'list'=>true, 'grid'=>true);
		//$fields['PERFIL_FLAG']		=	array('title'=>'PERFIL_STATUS',	'type'=>'int',   'list'=>true, 	'basic'=>true );
		//Marcar os checkbox do Perfil Flag
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_PROFILE'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'PERFIL_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
			);
	}
		
	
	//Classe de Relação CUBO-USUÁRIO ADMINISTRATIVO
	public function REL_WRS_CUBE_USER()
	{
		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_cube_user');
		$button['import']		=	LNG('bt_import_cube_user');
		
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('REL_WRS_CUBE_USER');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'CUBE_ID' ,'order_type'=>'ASC');
		$extend	= 	array('class'=>'REL_WRS_CUBE_USER' 	,'file'=>'REL_WRS_CUBE_USER');
		
		
		$fields							=	array();
		$fields['WRS_ICON']				=	array('title'=>'#',		'basic'=>true,	'width'=>50,	 'grid'=>true);
		$fields['CUBE_ID']				=	array('title'=>'ID', 	'primary'=>true,'type'=>'int',   'list'=>true,'type'=>'int', 'basic'=>true );
		$fields['CUBE_IDS']				=	array('title'=>'IDS',	'type'=>'int',  'list'=>true,	 'type'=>'int', 'basic'=>true,'class'=>'' );
		

		return array(	'title'		=>	LNG('TITLE_REL_WRS_CUBE_USER'), 
						'button'	=>	$button,
						'field'		=>	$fields,			
						'table'		=>	$table,
						'order'		=>	$order,
						'icon'		=>	'user.png',
						'primary'	=>	'CUBE_ID',
						'extend'				=>	$extend,											// NEW
						'checkbox'				=>	true, 												// NEW
						'button_icon'			=>	$button_icon,										// NEW
						'button_force_label'	=>	true, 			  									// NEW
		 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
						'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
						'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
						'exception'				=>	true, 												// NEW
						'aplicaClassLinhas'		=>	true,												// NEW
						'aplicaClassHeaders'	=>	'text-center'										// NEW
					);
					
					
	}
	
	/*
	//Classe de Relatórios ADMINISTRATIVO
	public function ATT_WRS_REPORT()
	{
		$button	=	array('new'=>'');
		
		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_REPORT');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'REPORT_ID' ,'order_type'=>'ASC');
		$fields	=	array();
		
		$fields['WRS_ICON']			=	array('title'=>'#',   			'basic'=>true ,  'width'=>50,'grid'=>true);
		$fields['REPORT_ID']		=	array('title'=>'ID',   			'primary'=>true, 'type'=>'int',  'class'=>'hide');
		$fields['DATABASE_ID']		=	array('title'=>'Database',		'list'=>true,	 'basic'=>true,  'grid'=>true);
		$fields['CUBE_ID']			=	array('title'=>'Cubo',			'list'=>true,	 'basic'=>true,  'grid'=>true );
		$fields['CUSTOMER_ID']		=	array('title'=>'Cliente',		'list'=>true, 	 'basic'=>true,  'grid'=>true);
		$fields['USER_ID']			=	array('title'=>'Usuário',		'list'=>true,	 'basic'=>true,  'grid'=>true);
		$fields['REPORT_DESC']		=	array('title'=>'Nome Relatório','list'=>true,	 'basic'=>true,  'grid'=>true);
		//ROWS
		//COLUMNS
		//MEASURES
		//FILTERS
		//Checkbox REPORT_SHARE
		//Checkbox REPORT_AUTOLOAD
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_REPORT'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'REPORT_ID'
			);
	}
	*/
	
	//Classe de Arquivos para Download ADMINISTRATIVO
	public function ATT_WRS_DOWNLOAD()
	{
		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');		
		$button['export']		=	LNG('bt_export_download');
		$button['import']		=	LNG('bt_import_download');
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_DOWNLOAD');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'DOWNLOAD_ID' ,'order_type'=>'ASC');
		$extend	= 	array('class'=>'ATT_WRS_DOWNLOAD' 	,'file'=>'ATT_WRS_DOWNLOAD');
		$fields	=	array();
		
		$fields['WRS_ICON']			=	array('title'=>'#',   			'width'=>50,      'basic'=>true,  'grid'=>true);
		$fields['DOWNLOAD_ID']		=	array('title'=>'ID',   			'primary'=>true,  'type'=>'int',  'class'=>'hide');
		$fields['FILE_DESC']		=	array('title'=>'FILE_DESC',		'class'=>'hide');
		$fields['FILE_NAME']		=	array('title'=>'Nome Arquivo',	'list'=>true,	  'basic'=>true,  'grid'=>true,  'class'=>'hide');
		$fields['DATABASE_ID']		=	array('title'=>'Database',		'list'=>true,	  'basic'=>true,  'grid'=>true,  'class'=>'hide');
		$fields['FILE_SIZE']		=	array('title'=>'Tamanho',		'list'=>true, 	  'basic'=>true,  'grid'=>true,  'class'=>'hide');
		//$fields['FILE_DATE']		=	array('title'=>'Data',			'list'=>true,	  'basic'=>true,  'grid'=>true,  'class'=>'hide');
		
		
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_DOWNLOAD'), 
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'DOWNLOAD_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
			);
	}
	

	//Classe de Logs ADMINISTRATIVO
	public function ATT_WRS_LOG()
	{
		$button		=	$button_icon	=	array();

		$button['back']			=	LNG('bt_back');
		$button['new']			=	LNG('bt_new');
		$button['update']		=	LNG('bt_update');
		$button['export']		=	LNG('bt_export_log');
		$button['import']		=	LNG('bt_import_log');
		
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		$button_icon['export']	=	'glyphicon glyphicon-export color_write';
		$button_icon['import']	=	'glyphicon glyphicon-import color_write';

		$dados_tabela_evento = $this->getDadosTabelaConfig('ATT_WRS_LOG');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order	=	array('order_by'=>'DATE_ID' ,'order_type'=>'DESC');
		$extend	= 	array('class'=>'ATT_WRS_LOG' 	,'file'=>'ATT_WRS_LOG');
		$fields	=	array();
	
		$fields['WRS_ICON']			=	array('title'=>'#',    		'width'=>50,	  'grid'=>true);
		$fields['TRANSACTION_ID']	=	array('title'=>'ID',   		'primary'=>true,  'list'=>true,  'grid'=>true,  'class'=>'hide');
		$fields['DATE_ID']			=	array('title'=>'Data', 		'type'=>'date',   'list'=>true,	 'grid'=>true,  'class'=>'hide');
		$fields['PROCESS']			=	array('title'=>'Processo',	'list'=>true,	  'grid'=>true,  'class'=>'hide');
		$fields['OPERATION']		=	array('title'=>'Operação',	'list'=>true, 	  'grid'=>true,  'class'=>'hide');
		$fields['MESSAGE']			=	array('title'=>'Mensagem',	'list'=>true, 	  'grid'=>true,  'class'=>'hide');
		$fields['USER_DESC']		=	array('title'=>'Usuário',	'list'=>true, 	  'grid'=>true,  'class'=>'hide');
		$fields['CUSTOMER_DESC']	=	array('title'=>'Cliente',	'list'=>true, 	  'grid'=>true,  'class'=>'hide');
	
		return array(	'title'		=>	LNG('TITLE_ATT_WRS_LOG'),
				'button'	=>	$button,
				'field'		=>	$fields,
				'table'		=>	$table,
				'order'		=>	$order,
				'icon'		=>	'user.png',
				'primary'	=>	'TRANSACTION_ID',
				'extend'				=>	$extend,											// NEW
				'checkbox'				=>	true, 												// NEW
				'button_icon'			=>	$button_icon,										// NEW
				'button_force_label'	=>	true, 			  									// NEW
 				'callback_btn_events'	=>	'callback_admin_btn_events', 						// NEW
				'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
				'actionDouble'			=>	'callback_load_admin_generic_modal',				// NEW
				'exception'				=>	true, 												// NEW
				'aplicaClassLinhas'		=>	true,												// NEW
				'aplicaClassHeaders'	=>	'text-center'										// NEW
		);
	}
	
	
	
	public function GET_SSAS_REPORT()
	{
		$dados_tabela_evento = $this->getDadosTabelaConfig('GET_SSAS_REPORT');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order		=	array();
		$fields		=	array();
		$button		=	array();
		
		$button['new']		=	'Abrir Relatório';
		if(!WRS_USER::getArrPerfUser('DRG')){
			$button['update']	=	'Abrir Layout';
			$button['remove']	=	LNG('bt_remove');
		}

		$button_icon['back']	=	'glyphicon glyphicon-chevron-left bt_voltar';
		$button_icon['new']		=	'fa fa-pencil-square-o';
		$button_icon['update']	=	'fa fa-floppy-o';
		
		/*
		 * TODO: pegar do language esses labels
		 */
		
		$exceptions	=	array('file'=>'WRS_REPORT', 'class'=>'WRS_REPORT','type'=>'');
				
		 $fields['REPORT_DESC']		=	array('title'=>'Nome', 				'width'=>250,	'classDataLine'=>'text-left',	'label_icon_small'=>true,	'label_icon_middle'=>true,	'label_icon_big'=>true,		'list'=>true,	'basic'=>true,  'grid'=>true);
		 $fields['REPORT_SHARE']	=	array('title'=>'Compartilhado', 	'width'=>100,	'classDataLine'=>'text-center', 																					'list'=>true, 	'basic'=>true, 	'grid'=>false,'is_select'=>array('-1'=>'Selecionar','1'=>'Sim','0'=>'Não'));
		 $fields['REPORT_DATE']		=	array('title'=>'Data', 				'width'=>130,	'classDataLine'=>'text-center',															'label_icon_big'=>true,		'list'=>true,	'basic'=>true,  'grid'=>true, 		'type'=>'date_object' ,'format'=>'d/m/Y H:i:s' ,'type_convert'=>true);
		 $fields['REPORT_AUTOLOAD']	=	array('title'=>'Carga', 			'width'=>80,	'classDataLine'=>'text-center', 																					'list'=>true, 	'basic'=>true, 	'grid'=>false,'is_select'=>array('-1'=>'Selecionar','1'=>'Sim','0'=>'Não'));
		 $fields['USER_DESC']		=	array('title'=>'Criador', 			'width'=>150,	'classDataLine'=>'text-left',								'label_icon_middle'=>true,	'label_icon_big'=>true,		'list'=>true,	'basic'=>true,  'grid'=>true);
		 $fields['LAYOUT_ROWS']		=	array('title'=>'Linhas', 			'width'=>200,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		 $fields['LAYOUT_COLUMNS']	=	array('title'=>'Colunas', 			'width'=>200,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		 $fields['LAYOUT_MEASURES']	=	array('title'=>'Medidas', 			'width'=>200,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		 $fields['LAYOUT_FILTERS']	=	array('title'=>'Filtros', 			'width'=>200,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		 $fields['FILTER_DESC']		=	array('title'=>'Filtrados', 		'width'=>200,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		 $fields['FILTERS_VALUES']	=	array('title'=>'Itens Filtrados',	'list'=>false,	'basic'=>false, 'grid'=>false);
		
		
		 return array(	 'title'				=>	LNG('TITLE_ATT_WRS_REPORT'),
						 'button'				=>	$button,
						 'field'				=>	$fields,
						 'table'				=>	$table,
						 'order'				=>	$order,
						 'icon'					=>	'report.png',	
						 'primary'				=>	'REPORT_ID',
						 'button_force_label'	=>	true, 			  									// NEW
						 'button_icon'			=>	$button_icon,										// NEW
						 'exception'			=>	$exceptions, 										// NEW
						 'checkbox'				=>	true, 												// NEW
						 'label_icon_custom'	=>	true, 												// NEW
 						 'use_auto_width'		=>	false, 												// NEW
		 				 'callback_btn_events'	=>	'callback_report_btn_events', 						// NEW
						 'actionSingle'			=>	'callback_check_line_generic_modal',				// NEW
						 'actionDouble'			=>	'callback_load_report_generic_modal',				// NEW
						 'aplicaClassLinhas'	=>	true,												// NEW
						 'aplicaClassHeaders'	=>	'text-center'										// NEW
		 );
		
	}
	
	
	
	
	
	public function GET_SSAS_LAYOUTS()
	{
		$dados_tabela_evento = $this->getDadosTabelaConfig('GET_SSAS_LAYOUTS');
		$table	=	$dados_tabela_evento['tabela_bd'];
		
		$order		=	array();
		$fields		=	array();
		$button		=	array();
	
		
		if(!WRS_USER::getArrPerfUser('DRG'))
		{
			$button['remove']	=	LNG('BTN_REMOVE');
		}
	
		$button_icon=	array('new'=>'fa fa-folder-open');
		/*
		 * TODO: pegar do language esses labels
		*/
	
		$exceptions	=	array('file'=>'ReportLayout', 'class'=>'ReportLayout','type'=>'');
		
	
		$fields['LAYOUT_ID']		=	array('title'=>'#'							, 	'width'=>50,		'class'=>'hide','grid'=>false );
		$fields['LAYOUT_DESC']		=	array('title'=>LNG('tpl_layout_name')		, 	'width'=>300,	'classDataLine'=>'text-center', 'list'=>true, 	'basic'=>true, 	'grid'=>false);
		$fields['LAYOUT_ALIAS']		=	array('title'=>LNG('tpl_layout_alias')		, 	'width'=>300,	'classDataLine'=>'text-center',	'label_icon_big'=>true,		'list'=>true,	'basic'=>true,  'grid'=>true, 		);
		$fields['LAYOUT_SHARE']		=	array('title'=>'Comartilhado'				, 	'width'=>50,	'classDataLine'=>'text-center', 'list'=>true, 	'basic'=>true, 	'grid'=>false,'is_select'=>array(''=>'Sem opção','1'=>'Sim','0'=>'Não'));
		$fields['LAYOUT_ROWS']		=	array('title'=>LNG('ATTRIBUTOS_LINHA')		, 	'width'=>150,	'classDataLine'=>'text-left',	'label_icon_middle'=>true,	'label_icon_big'=>true,		'list'=>true,	'basic'=>true,  'grid'=>true);
		$fields['LAYOUT_COLUMNS']	=	array('title'=>LNG('ATTRIBUTOS_COLUNA')		, 	'width'=>150,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		$fields['LAYOUT_MEASURES']	=	array('title'=>LNG('ATTRIBUTOS_METRICA')	, 	'width'=>150,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		$fields['LAYOUT_FILTERS']	=	array('title'=>LNG('ATTRIBUTOS_FILTRO')		, 	'width'=>150,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false);
		$fields['LAYOUT_DATE']		=	array('title'=>'Data'						, 	'width'=>100,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false ,'type'=>'date_object' ,'format'=>'d/m/Y H:i:s' ,'type_convert'=>true);
		$fields['LAYOUT_UPDATE']	=	array('title'=>'Update'						, 	'width'=>100,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false, 'type'=>'date_object' ,'format'=>'d/m/Y H:i:s' ,'type_convert'=>true);
		$fields['LAYOUT_FLAG']		=	array('title'=>'Flag'						, 	'width'=>60,	'classDataLine'=>'text-left',	'list'=>true,	'basic'=>false, 'grid'=>false ,'is_select'=>array(''=>'Sem opção','1'=>'Sim','0'=>'Não'));
		$fields['LAYOUT_STATUS']	=	array('title'=>'Status'						,	'list'=>false,	'basic'=>false, 'grid'=>false);
		$fields['SERVER_ID']		=	array('title'=>'Server ID'					,	'list'=>false,	'basic'=>false, 'grid'=>false);
		$fields['DATABASE_ID']		=	array('title'=>'Database ID'				,	'list'=>false,	'basic'=>false, 'grid'=>false);
		$fields['CUBE_ID']			=	array('title'=>'Cube ID'					,	'list'=>false,	'basic'=>false, 'grid'=>false);
		$fields['USER_ID']			=	array('title'=>'User ID'					,	'list'=>false,	'basic'=>false, 'grid'=>false);
	
		
		return array(	 'title'				=>	LNG('tpl_layout'),
						'button'				=>	$button,
						'field'					=>	$fields,
						'table'					=>	$table,
						'order'					=>	$order,
						'icon'					=>	'report.png',
						'primary'				=>	'REPORT_ID',
						'button_force_label'	=>	true, 			  			// NEW
						'button_icon'			=>	$button_icon,				// NEW
						'exception'				=>	$exceptions, 				// NEW
						'checkbox'				=>	true, 						// NEW
						'label_icon_custom'		=>	true, 						// NEW
						'use_auto_width'		=>	false, 						// NEW
						'callback_btn_events'	=>	null, 						// NEW
						'actionSingle'			=>	'layout_actionSingle',		// NEW
						'actionDouble'			=>	'layout_actionDouble',		// NEW
						'aplicaClassLinhas'		=>	true,						// NEW
						'aplicaClassHeaders'	=>	'text-center'				// NEW
				);
	
	}
	
	

	
}