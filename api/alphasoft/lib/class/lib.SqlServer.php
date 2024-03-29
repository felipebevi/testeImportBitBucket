<?php 

/**
 * CLASS PARA CONEXÃO COM O SQL SERVER
 * 
 * Documento Exclusivo AlphaSoft
 * @author Marcelo Santos
 */
class SQL_SERVER
{
	private $conn =	false;
	
	/**
	 * Define a conexão do banco
	 *
	 * @param sqlsrv_connect $conn
	 */
	public function set_conn($conn)
	{
		$this->conn	= $conn;
	}
	
	
	public function isConnect($that)
	{
		includeCLASS('WRS_LOGIN');
		$login	=	 new WRS_LOGIN();
		$login->set_conn($this->get_conn());
		$login->isUserConnect(false);
	}
	
	/**
	 * Retorna a Conexão do banco
	 *
	 * @return sqlsrv_connect
	 */
	public function get_conn()
	{
		return $this->conn;
	}
	
	/**
	 * Conexão de Banco Apenas executado quando não chamado a conexão mas deve ser chamado apenas para o define 
	 *
	 * WARNING: Apenas use quando souber o que está fazendo
	 */
	public function runConnection()
	{
		$connectionInfo = array('Database'=>BANCO,'UID'=>USUARIO,'PWD'=>SENHA,
								'APP'=>'WRS','WSID'=>'WEBServer',
								'CharacterSet'=>CODE,
								'ConnectionPooling'=>0,
								'MultipleActiveResultSets'=>true,
								'TransactionIsolation'=>SQLSRV_TXN_READ_UNCOMMITTED,
								'LoginTimeout'=>1800);
								//'TraceFile'=>'D;\WEB\TEMP\WRS_SQL_TRACE.TXT',
								//'TraceOn'=>true);
		$conn = sqlsrv_connect( SERVIDOR, $connectionInfo );

		if($conn === false ) {
			echo "Could not connect.\n<pre>";
			die( print_r( sqlsrv_errors(), true));
			exit();
		}

		$this->set_conn($conn);
	}

	/**
	 * Retorna a execução da Query
	 *
	 * @param string $query
	 * @return sqlsrv_query;
	 */
	public function query($query,$make_login=true,$log=true)
	{
		//session_write_close();//Para a escrita da Sessão
		if(IS_WRS_DEBUG && $log)
		{
			if($make_login) $this->query_debug($query);
		}
		
		$_query	= sqlsrv_query($this->get_conn(),$query);		
	/*
		if(IS_WRS_DEBUG){
			if($make_login) $this->query_debug('DONE::'.$query);
		}*/

		//session_regenerate_id(); //Retoma a escrita da sessão
		if(!$_query) { $this->query_debug('ERROR:::: '.$query.PHP_EOL.'result::'.print_r($_query,true).PHP_EOL.'typeError::'.print_r(sqlsrv_errors(),true)); return false; }
		
		return $_query;
	}
	
	/**
	 * retorna a execução da consulta no SQL Server
	 *
	 * @param sqlsrv_query $query
	 * @return sqlsrv_fetch_array
	 */	
	public function fetch_array($query)
	{
		return sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
	}

	/**
	 * Retorna se foi encontrado linha na select
	 *
	 * @param sqlsrv_query $query
	 * @return sqlsrv_num_rows
	 */
	public function num_rows($query)
	{
		if(!@$query) return false;
		
		return sqlsrv_has_rows($query)  === true ? true : false;
	}
	
	/**
	 * Cria o arquivo e insere conteúdo na última linha do arquivo
	 *
	 * @param string $file_path
	 * @param string $data
	 */
	public static function query_debug($data)
	{
		$file_path	=	 PATH_VAR.DIRECTORY_SEPARATOR;
		
		//Verificando se a pasta existe;
		if(!file_exists($file_path)){
			mkdir($file_path, 0777, true);
		}
		
		//Caminho do debug por nome do usuario		
		$file_path_by_user	=	$file_path.date('Y_m_d_').WRS_DEBUG_QUERY_FILE_NAME;
		
		//Verifica se o arquivo existe e se existir insere o debug nesse diretório
		if(file_exists($file_path_by_user)){
			$file_path	=	$file_path_by_user;
		}else{
			$file_path.=date('Y_m_d_').WRS_DEBUG_QUERY_FILE_NAME;
		}
		
		if(file_exists($file_path)){
			self::file_limit_size($file_path);
		}
		
		$identify		=	' - [ login:'.WRS::LOGIN().' | IP_REMOTO:'.$_SERVER['REMOTE_ADDR'].' ] ';
			
	//	$_data			=	date('H:i:s d-m-Y').' '.$data.PHP_EOL;
	//	$_data			=	date('H:i:s d-m-Y').' | '.$data.PHP_EOL;
		$_data			=	PHP_EOL.'-- '.date('H:i:s d-m-Y').$identify.PHP_EOL.$data.PHP_EOL;
		$_data			.=	str_repeat("-", 50).PHP_EOL;
		
		$handle 		= 	fopen($file_path,"a+");
		fwrite($handle,utf8_decode($_data));
		fclose($handle);
	}
	
	/**
	 * Converte o tamanho do arquivo em bytes
	 *
	 * @param unknown $bytes
	 * @param int $decimals
	 */
	private static function filesize2bytes($bytes, $decimals = 2) 
	{
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		
		return array(sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)), @$sz[$factor]);
	}
	
	/**
	 * Verifica se  o arquivo de LOG está na capacidade máxima
	 *
	 * @param unknown $file_path
	 */
	private static function file_limit_size($file_path)
	{
		$file_size = self::filesize2bytes(filesize ($file_path));
		
		//Verifica de o arquivo é em M e é maior que 2MB
		if($file_size[1]=='M' && $file_size[0]>2)
		{
			//Se o arquivo existir Removeo
			if(file_exists($file_path.'_1')){
				unlink($file_path.'_1');
			}		
				
			rename($file_path, $file_path.'_1');
		}
	}
	
	
	public function logout($tag=false)
	{
		includeQUERY('WRS_LOGIN');
		//Fazendo o Logout
		$this->cleanTMP();
		$this->query(QUERY_LOGIN::LOGOUT_SSAS(WRS::LOGIN_ID()));
		session_destroy();
	
		if(!$tag) header('Location: login.php');
	
	}
	
	
	/**
	 * Verificando se o usuário está registrado no sistema
	 *
	 * @param boolean $tag
	 * @return array
	 */
	public function isUserConnect($tag=false)
	{
		$login_id 	= 	WRS::GET_LOGIN_ID();
		$param		=	 array('data'=>array('isUserConnect'=>false));
	
		//Foi chamado pelo Java script
		if($tag)	header('Content-Type: application/json');
	
		if(!empty($login_id))
		{
			if($this->num_rows($this->query(QUERY_LOGIN::GET_SSAS_LOGIN($login_id))))
				$param['data']['isUserConnect']	=	 true;
			else
				$param['data']['isUserConnect']	=	 false;
		}
	
		if($tag)	return json_encode($param,true);
	
		if($param['data']['isUserConnect']==false)
		{
			$this->logout(true);
			if(!$tag) header('Location: desconected.php?charset='.LNG('IDIOMA'));
				
		}
	}
	
	
	
	public function cleanTMP()
	{
			$ini			=	WRS_INI::tmp();
			$_tables		=	json_decode($ini['TABLES'],true);
		
			foreach($_tables as $info_tables)
			{
				$param			=	array('(company)','(user)');
				
				$info_tables['table']		=	 str_replace(	$param, 
																array(WRS::CUSTOMER_ID(),WRS::USER_ID()), 
																$info_tables['table']
															);
				
				$table	=	$info_tables['table'];
				//$this->query("IF OBJECT_ID('dbo.".$table."', 'U') IS NOT NULL DROP TABLE ".$table);
				$this->query("EXEC DROP_TABLE '".$table."'");
			
			}
	}	
}

?>