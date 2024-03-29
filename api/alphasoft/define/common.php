<?php 

/**
 * Funções comuns para o WRS
 */
/**
 * Apenas string oara fazer as complementações do sistema
 * @var strfing
 */
define('WRS_COMPLEMENT', 'VALUE_WRS');
define('WRS_TEMP_RAND',rand(0,99999999999999));

/*
 * Funções Genericas 
 */


function PARAMETERS_SEPARATORS($key)
{
	
	
	$local = array(
			'vir'		=>'(_,_)',
			'pipe'		=>'(_|_)',
			'negacao'	=>'~',
			'simples'	=>'*'
	);
	
	
	if(array_key_exists($key, $local))
	{
		return $local[$key];
	}
	
		return false;
	

}



/**
 * 
 * Salvando tablela temporária para salvar
 * 
 * @param string $table_name
 * 
 */
function table_tmp($table_name)
{
	if(empty($table_name)) return false;
	
	//PATH_TABLE_TMP
	//DB_DIRECTORY_SEPARATOR
	
	
}


/*
 * CRiando o diretório
 */
function create_directory($path,$ds,$raiz)
{
	$_dir		=	str_replace($ds, DS, $raiz);
	$_path		=	str_replace($raiz, '', $path);
	$l_path		=	explode($ds, $_path);
	
	foreach($l_path as $val)
	{
		$_dir = $_dir.$val.DS;
		if(!is_dir($_dir))
		{
			mkdir($_dir, 0700);
		}
	}

}


/**
 * Debug 
 * @param string $texto
 * @param string $fileName
 */
function WRS_DEBUG_QUERY($texto,$_fileName='wrs_debug_query.log')
{
	if(is_array($texto)) $texto = print_r($texto,true);
	
	
	$fileName		=	$_fileName;
	$fileName		=	date('Y_m_d_').$fileName;
	
	
	$fp = fopen(PATH_VAR.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$fileName,'a');

	
	if($fileName=='WRS_DEBUG.txt')
		fwrite($fp,$texto.PHP_EOL.'---------------------------------------------------------------------------------------------'.PHP_EOL); // grava a string no arquivo. Se o arquivo não existir ele será criado
	else
		fwrite($fp,$texto.PHP_EOL); // grava a string no arquivo. Se o arquivo não existir ele será criado
	
	fclose($fp);
}

function include_js($file)
{
	echo '<script type="text/javascript" src="api/alphasoft/js/'.$file.'.js?'.RAND_TOKEN.'"></script>'.PHP_EOL;	
}
/**
 * Pegando apenas o NOme do Browser
 * @return string
 */
function wrs_get_user_browser()
{
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = '';
	if(preg_match('/MSIE/i',$u_agent))
	{
		$ub = "ie";
	}
	elseif(preg_match('/Firefox/i',$u_agent))
	{
		$ub = "firefox";
	}
	elseif(preg_match('/Safari/i',$u_agent))
	{
		$ub = "safari";
	}
	elseif(preg_match('/Chrome/i',$u_agent))
	{
		$ub = "chrome";
	}
	elseif(preg_match('/Flock/i',$u_agent))
	{
		$ub = "flock";
	}
	elseif(preg_match('/Opera/i',$u_agent))
	{
		$ub = "opera";
	}

	return $ub;
}



/**
 * Constroi um Select com base no Array passado e os parametros no implements
 * name
 * id
 * class
 * implements
 * 
 * 
 * @param array $array
 * @param string $selected_by_label
 * @param array $implements
 * @return string
 */
function select($array,$selected_by_label,$implements)
{
	$option			=	'<option value="{label}" {SELECTED} {element} >{value}</option>'.PHP_EOL;
	$option_array	=	array('{value}','{SELECTED}','{label}','{element}');
	
	$select			=	'<select name="{name}" id="{id}" class="{class}" {implements} >{option}</select>'.PHP_EOL;
	$select_array	=	array('{option}','{name}','{id}','{class}','{implements}');

	$html			=	NULL;
	$selected		=	NULL;
	
	
	foreach ($array as $label =>$_value)
	{
		$selected	=	$selected_by_label==$label ? 'SELECTED' : NULL;
		
		$value		=	$_value;
		$element	=	'';
		if(is_array($_value))
		{
			$value		=	$_value['value'];
			$element	=	$_value['element'];
		}
		
		
		$replace	=	 array($value,$selected,$label,$element);
		$html		.= str_replace($option_array, $replace, $option);
	}
	
	$replace	= array();
	$replace[]	= $html;
	$replace[]	= isset($implements['name']) 		? $implements['name'] 		: NULL;
	$replace[]	= isset($implements['id']) 			? $implements['id'] 		: NULL;
	$replace[]	= isset($implements['class']) 		? $implements['class'] 		: NULL;
	$replace[]	= isset($implements['implements']) 	? $implements['implements'] : NULL;
	$html		= str_replace($select_array, $replace, $select);
	
	return $html;
}

/*
 * Funções de Negócio
 */




/**
 * Fazendo o include do idioma
 */
$file_language	=	PATH_LANGUAGE.WRS::USER_LANGUAGE().'.lng';

if(!file_exists($file_language)){
	$file_language	=	PATH_LANGUAGE.'POR.lng';
}
//Ao incluir esse arquivo é criada a variável $language
include_once $file_language;


/**
 * Retorna todo o idioma  passado
 * @param string $label
 * @return string
 */

function LNG($string)
{
	//Recebe a variável global
	GLOBAL $language;

	if(!isset($language[$string])) return $string;

	if(is_array($language[$string])) return $language[$string];
		
	return ucfirst($language[$string]);
}

function eLNG($string)
{
	echo LNG($string);
}

// substitui o LNG trocando os %s por s se existirem
// aceita outros caracteres para buscar e substituir (para plurais de outras palavras sem que seja somente S no final)
function LNG_S($string,$char=false,$char_to_find=false,$recursiva=false)
{
	$_char 			= $char===false?'s':$char;
	$_char_to_find 	= $char_to_find===false?'%s':$char_to_find;
	$_recursiva 	= !$recursiva?false:true;
	$_string 		= $_recursiva?$string:LNG($string);
	
	if(strpos($_string,$_char_to_find)){
		return LNG_S(str_replace($_char_to_find,$_char,$_string),$_char,$_char_to_find,true);
	}else{
		return $_string;
	}
}




function LNG_JOIN($string)
{
	//Recebe a variável global 
	GLOBAL $language;
	
	$join	=	$string;
	
	foreach($string as $label => $value)
	{
		if(isset($language[$value]))
			$join[$label]	=	$language[$value];
		else
			$join[$label]	=	$value;
	}
	
	return implode(' ',$join);
}


?>