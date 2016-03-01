<?php
/**
 * Contem Metodos Genericos para a area Administrativa
 * @author fbevilacqua
 * interface com funcoes para a area administrativa
 * felipebevi.com.br 20160118
 */
includeQuery('ATT_WRS_AdminInterfaces');

class WRS_AdminInterface  extends WRS_BASE
{

	public $classname = NULL;
	public $OBJECT	= NULL;
	private $manage_param = NULL;
	public $temp_folder;
	private $queryClass = NULL;
	
	public function __construct(){
		$this->queryClass = new QUERY_WRS_ADMIN();
	}

	// atualizar o atributo data dentro da variavel param para que se mantenha nas WindowGrids navegadas,
	// possibilitando ler o atributo "param_original" de qualquer tela navegada, o qual por sua vez são
	// os atributos da tabela em questão definidos no WRS_MANAGE_PARAM
	public function RefreshDataAttrInParam($_param){
		$param = $_param;
		$data=array();
		$param['visao_atual']	=	'list';
		$data['param_original'] = 	$param;
		$param['data']	=	json_encode($data);
		return $param;
	}
	
	public function retornaMsgAcaoTelaAdmin($boolStatus,$msg,$tabela_cadastro='',$query=''){
 		$typeMsg = $boolStatus?'success':'error'; 		
		$callback='function(){}';
 		if($tabela_cadastro!=''){
 			$callback="function(){ $('.menu_cadastro[tabela=".WRS_MANAGE_PARAM::confereTabelaCadastroRetorno($tabela_cadastro)."]').trigger('click'); }";
 		}
		$msg = addslashes($msg);
 		$JS=<<<HTML
	 		$('#myModal').modal('hide'); // #myModalGenericConfig
			WRS_ALERT('{$msg}','{$typeMsg}',{$callback}); 
HTML;
 		if($query!=''){
 			$JS.="WRS_CONSOLE(".json_encode(array('query'=>$query),1).")";
 		}
 		echo fwrs_javascript($JS); 		
 		exit();
	}
	
	/**
	 * Confere se existe um diretorio completo, porém somente a partir da segunda posicao, uma vez que considero que a primeira é sempre a base do sistema e/ou o diretorio inicial, Ex.: C:/ ou /var/, por isso nunca vou criar o primeiro diretorio passado
	 * @param string $dir - deve ser passado o diretorio COMPLETO, desde a raiz
	 */
	public function confere_criacao_diretorio($dir,$testa_outro_replace=false){
		$dir = $testa_outro_replace?$dir:str_replace("/","\\",$dir);
		if(!is_dir($dir)){
			$diretorios = explode("\\",$dir);
			if(count($diretorios)>1){
				$dir_atual = $diretorios[0];
				for($p=1;$p<count($diretorios);$p++){
					if(trim($diretorios[$p])!=''){
						$diretorio = $dir_atual.DS.$diretorios[$p];
						if(!is_dir($diretorio)){
							mkdir($diretorio, 0777, true);
						}
						$dir_atual.=DS.$diretorios[$p];
					}
				}
			}else{
				$this->confere_criacao_diretorio(str_replace("\\",'/',$dir),true);
			}
		}
	}
	
	public function SetObject($Object)
	{
		$this->OBJECT=$Object;
		$this->temp_folder = PATH_FILE.$this->classname.DS.'exportFiles'.DS;//sys_get_temp_dir().DIRECTORY_SEPARATOR;
		$this->confere_criacao_diretorio($this->temp_folder);
		
		// funcoes JS para tratamento dos formularios administrativos
		$scr_field_bloq = <<<HTML
		<script>
			$('input[type=text], input[type=password]').each(function(){ bloqueia_chars($(this)); });
			trata_campos_senha();
			trata_campos_int();
		</script>
HTML;
		echo $scr_field_bloq;
	}
	
	public function downloadLink($_registros,$_chavePrimaria,$_param){

		$compacta 		= $_param['efetua_compactacao']; // bool
		$regIds			= $_registros;
		$primary		= $_chavePrimaria;
		$nome_diretorio	= $_param['nome_diretorio'];			
		$WRS_DEFINE		= WRS_INI::WRS_DEFINE();
		$nome_diretorio	= $WRS_DEFINE['WRS_DIRNAME_EXPORT'].$nome_diretorio;
		$this->confere_criacao_diretorio($nome_diretorio);
		
		$_param['tabela_export'] = WRS_MANAGE_PARAM::confereTabelaCadastroRetorno($_param['tabela_export']);
		$return_export 	= $this->queryClass->getQueryExportarTabela(array(
																'tabela'	=> WRS_MANAGE_PARAM::getAtributoTabelaConfig($_param['tabela_export'],'tabela_bd'),
																'colunas'	=> WRS_MANAGE_PARAM::getAtributoTabelaConfig($_param['tabela_export'],'colunas_import_export'),
																'filtros'	=> (is_array($regIds) && count($regIds)>0)?$primary.' in ('.implode(',',$regIds).')':'',
																'separador' => $_param['caracter_separacao'],
																'diretorio'	=> $nome_diretorio
													));
		
		$query_export 	= $return_export['query'];
		$file_export 	= $return_export['file'];
		$file_original 	= $return_export['file_ori'];
		
		$this->set_conn($this->OBJECT->get_conn());
		$query			= 	$this->query($query_export);
		
		
		/*
		 * Verificando se existe resultado
		*/
		$processo=false;
		if(!$this->num_rows($query))
		{
			return false;
		}else{		
			$rows 	= $this->fetch_array($query);
			
			$output	= $rows['output'];

			if(substr($output,0,6)=='SUCESS'){
				if($compacta){
					$file_export_inv	= str_replace('/','\\',$file_export); // troca barras normais por invertidas para processo no SQL server/ windows
					$new_file_export	= substr($file_export,0,-4).'.ZIP';
					$new_file_export_inv= str_replace('/','\\',$new_file_export); // troca barras normais por invertidas para processo no SQL server/ windows
					$return_compact 	= $this->queryClass->getQueryCompreessFile('ZIP', $file_export_inv, $new_file_export_inv);
					$query			= 	$this->query($return_compact);
					if(!$this->num_rows($query))
					{
						return false;
					}else{
						$rows 	= $this->fetch_array($query);
						$output	= $rows['output'];
					
						if(substr($output,0,10)=='Compressed'){
							$processo=true;
							$file_export = $new_file_export;
						}
						
					}
					
				}else{
					$processo=true;
				}			
			}			
		}
		
		if($processo){
					
			$url = "run.php?file=".$this->classname."&class=".$this->classname."&event=downloadFile&fileDownload=".$file_export."&nameFileUser=".$file_original;
			return $url;
			
		}else{
			return false;
		}
			
	}
	
	public function downloadFile(){

		$temp_filename_zip 	= trim($_REQUEST['fileDownload']); // somente a variavel chama zip, pode ser outro arquivo...
		$name_file_user 	= trim($_REQUEST['nameFileUser']); // variavel para definir um  nome de arquivo diferente do original para o usuario final
		
		$this->SetObject(NULL);
	
		if(is_file($temp_filename_zip)){ // $this->temp_folder.
			
			// se existe nome alternativo para o usuario final, utiliza eeste nome
			$nome_arq = (($name_file_user!='')?$name_file_user:basename($temp_filename_zip));
			
			// confere se a extensao do nome definido pro arquivo do usuario final é a mesma do arquivo original.  Se nao for, aplica a extensao correta
			if(strtoupper(substr($temp_filename_zip,-4)) != strtoupper(substr($nome_arq,-4))){
				$nome_arq = substr($nome_arq,0,-4).substr($temp_filename_zip,-4);
			}
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".$nome_arq."\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($temp_filename_zip)); // $this->temp_folder.
			ob_clean();
			flush();
			ignore_user_abort(true);
			if(readfile($temp_filename_zip)) // $this->temp_folder.
			{
				$this->apagarArquivoGerado($temp_filename_zip); // $this->temp_folder.
				//if(is_file()){
					
				//}
			}
			exit();
			
			
				
		}else{
			return false;
		}
	
	}
	
	public function apagarArquivoGerado($filename){ // str ou array
		if(is_array($filename) && count($filename)>0){
			foreach($filename as $file){
				$this->apagarArquivoGerado($file);
			}
		}else{
			if(is_file($this->temp_folder.$filename)){
				unlink($this->temp_folder.$filename);
			}else if(is_file($filename)){
				unlink($filename);
			}
		}
	}
	
	public function gerarArquivoZIPExport($filename,$arrFiles){
		if(is_array($arrFiles)){
	
			$zip = new ZipArchive();
			if ($zip->open($this->temp_folder.$filename, ZIPARCHIVE::CREATE)!==TRUE) {
				exit('forbidden to create file: '.$this->temp_folder.$filename);
			}
	
			foreach($arrFiles as $file){
				$file = $this->temp_folder.$file;
				if(is_file($file)){
					$zip->addFile($file, basename($file) );
				}
			}
			$zip->status;
			$zip->close();
				
		}else{
			return false;
		}
	}
	
	public function gerarArquivoCSVExport($nome,$conteudo){
		$fp = fopen($this->temp_folder.$nome, 'w');
		if(!is_array($conteudo)) $conteudo = array($conteudo);
		foreach ($conteudo as $linha) {
			fputcsv($fp, $linha);
		}
		fclose($fp);
	}
		
	public function importarDadosEmMassa($name,$_request_original=NULL){
		
		// bool se ja enviou um arquivo (true) ou esta na primeira tela (false)
		$form_send			= (is_array($_request_original) && array_key_exists('envio_de_arquivo', $_request_original) && $_request_original['envio_de_arquivo']==1);
			
		$event_form			= WRS_MANAGE_PARAM::confereTabelaCadastroRetorno($_request_original['event']);	
		
		$upload_dir_key 	= 	$name;
	
		$WRS_DEFINE	= WRS_INI::WRS_DEFINE();
		$diretorio 	= $WRS_DEFINE['WRS_DIRNAME_IMPORT'];

		$name_file_import 		= WRS_MANAGE_PARAM::getAtributoTabelaConfig($event_form,'nome_arquivo_import');
		$columns_description 	= WRS_MANAGE_PARAM::getAtributoTabelaConfig($event_form,'colunas_descricao');
		$columns_description	= (!$columns_description || $columns_description=='')?'':$columns_description;
		if($name_file_import!=''){
			$avisos = str_replace('#NAME_FILE#',$name_file_import,LNG('ADMIN_AVISO_IMPORT'));
			$avisos = str_replace('#DEFAULT_LAYOUT#',$columns_description,$avisos);
		}


		$msg_erro_tamanho 					= LNG('msg_maxFileSize');
		$msg_erro_tipo 						= LNG('msg_acceptFileTypes');
		$msg_erro_nao_existe_file_in_zip	= LNG('msg_nao_existe_file_in_zip').$parameter_upload['nome_obrigatorio_zip'];
		$msg_nome_obrigatorio_necessario	= LNG('msg_nome_obrigatorio_necessario').$parameter_upload['nome_obrigatorio_zip'];
		
		$extra_params		=	array(
				'upload_dir'				=>  $diretorio,
				'autoUpload'				=> 	true,
				'maxFileSize'				=> 	10000000, // 10 MB,
				'maxNumberOfFiles'			=> 	1,
				'botao_selecionar'			=>	true,
				'botao_enviar'				=>	false,
				'botao_cancelar'			=>	false,
				'botao_apagar'				=>	false,
				'barra_status'				=>	false,
				'image_versions' 			=>  false,
				'restricaoCsvZip'			=>  true,
				'nome_obrigatorio_zip' 		=> 	$name_file_import,
				'valida_nome_dentro_zip' 	=> 	true,
				'valida_nome_obrigatorio' 	=> 	true,
				'accept_file_types'			=>  "/(zip)|(csv)$/i",
				'messages'					=>  array(
													'maxFileSize'					=> $msg_erro_tamanho,
													'acceptFileTypes'				=> $msg_erro_tipo,
													'nao_existe_arquivo_zip'		=> $msg_erro_nao_existe_file_in_zip,
													'nome_obrigatorio_necessario'	=> $msg_nome_obrigatorio_necessario
												)
            	//,'print_response' 	=>  false
		);
			
		
		include PATH_TEMPLATE.'import_file_window.php';
		$arquivos_existentes = $form_send?$upload->listFiles():$upload->removeAllFiles();
				
		$colunas_import_export = WRS_MANAGE_PARAM::getAtributoTabelaConfig($event_form,'colunas_import_export');
		
		$output_geral='';
		if($form_send && is_array($arquivos_existentes) && count($arquivos_existentes)>0){
			$caracter_delimitador	 = ($_request_original['caracter_d']!='ponto_virgula')?'virgula':'ponto_virgula';
			$tipo_importacao		 = ($_request_original['tipo_importacao']!='atualizar')?'remover':'atualizar';
			$colunas				 = ($colunas_import_export)?$colunas_import_export:'';
			$campo_id				 = $_request_original['campo_id'];
			$cont = 0;
			$msgs_erros = $msgs_erros_detalhes = array();
			foreach($arquivos_existentes as $arq){
				$nome_arquivo=false;
				if(strtoupper(substr($arq,-3))=='ZIP'){
			
					$file_zip_to_import	= str_replace('/','\\',$arq); // troca barras normais por invertidas para processo no SQL server/ windows
					$file_to_unzip		= str_replace('/','\\',$diretorio.$upload_dir_key.$name_file_import); // troca barras normais por invertidas para processo no SQL server/ windows
					$return_compact 	= $this->queryClass->getQueryCompreessFile('UNZIP', $file_zip_to_import, $file_to_unzip);

					$this->set_conn($this->OBJECT->get_conn());
					$query			= 	$this->query($return_compact);
					
					if(!$this->num_rows($query))
					{
						$msgs_erros[]="<b>".$arq."</b> ".LNG('file_not_unziped');						
					}else{
						$rows 	= $this->fetch_array($query);
						if(array_key_exists('output', $rows)){
							$output	= $rows['output'];
							
							if(substr($output,0,9)=='Extracted'){
								$nome_arquivo = $diretorio.$upload_dir_key.$name_file_import;
								$output_geral.=addslashes($output)."<br>";
							}else{
								$msgs_erros[]=$output;
							}
						}

						if(array_key_exists('ERROR_MESSAGE', $rows) && $rows['ERROR_MESSAGE']!=''){
							$msgs_erros_detalhes[]=$rows['ERROR_MESSAGE'];
						}
						if(array_key_exists('REMOVE_MESSAGE', $rows) && $rows['REMOVE_MESSAGE']!=''){
							$msgs_erros_detalhes[]=$rows['REMOVE_MESSAGE'];
						}
			
					}								
					
					
				}else{
					$nome_arquivo = $arq;
				}
				
				if($nome_arquivo){
					
					// verifica se existem campos KEY para formar a chave para a tabela em importacao
					$primary = $campo_id;
					if(array_key_exists('_param', $_request_original)){
						$obj = $_request_original['_param'];
						$keys = array();
						foreach($obj['field'] as $atributo=>$dados_campo){
							if(array_key_exists('key', $dados_campo)){
								$keys[]=$atributo;
							}
						}
						unset($obj);
						if(count($keys)>0){
							$primary = implode(',',$keys);
						}
					}

					$ret = $this->trataDadosImportados($nome_arquivo,array(
																	'delimitador'		=>$caracter_delimitador,
																	'tipo_importacao'	=>$tipo_importacao,
																	'tabela_import'		=>WRS_MANAGE_PARAM::getAtributoTabelaConfig($event_form,'tabela_bd'),
																	'campo_id'			=>$primary,
																	'colunas'			=>$colunas
															));

					if(is_array($ret)){

						if(array_key_exists('output', $ret)){
							$output	= $ret['output'];
							if(substr($output,0,6)!='SUCESS'){								
								$msgs_erros[]=$output;
							}else{
								$output_geral.=addslashes($output)."<br>";
							}
						}
						
						if(array_key_exists('ERROR_MESSAGE', $ret) && $ret['ERROR_MESSAGE']!=''){
							$msgs_erros_detalhes[]=$ret['ERROR_MESSAGE'];
						}
						if(array_key_exists('REMOVE_MESSAGE', $ret) && $ret['REMOVE_MESSAGE']!=''){
							$msgs_erros_detalhes[]=$ret['REMOVE_MESSAGE'];
						}
							
					}else{
						$msgs_erros[]="<b>".$arq."</b> ".LNG('file_not_validated');
					}
				
				}else{
					$msgs_erros[]="<b>".$arq."</b> ".LNG('file_not_validated');
				}
			}
	
			$mensagem_success		= LNG('upload_files_success_file');
			$tipo_mensagem_success	= 'success';				
			$tipo_mensagem_error	= 'error';
				
			if(count($msgs_erros)>0){
				$mensagem			= '<b>'.LNG('IMPORT_EXPORT_MESSAGES_STATUS').'</b><br>'.implode('<br>',$msgs_erros);
				if(count($msgs_erros_detalhes)>0){
					$mensagem			.= '<br><b>'.LNG('IMPORT_EXPORT_MESSAGES_DETAIL').'</b><br>'.implode('<br>',$msgs_erros_detalhes);
				}
				$tipo_mensagem 		= $tipo_mensagem_error;
			}else{
				$mensagem 			= $mensagem_success;
				$mensagem			.= '<br><b>'.LNG('IMPORT_EXPORT_MESSAGES_STATUS').'</b><br>'.$output_geral;
				if(count($msgs_erros_detalhes)>0){
					$mensagem			.= '<br><b>'.LNG('IMPORT_EXPORT_MESSAGES_DETAIL').'</b><br>'.implode('<br>',$msgs_erros_detalhes);
				}
				$tipo_mensagem 		= $tipo_mensagem_success;
			}
			
			$upload->removeAllFiles();
				
			return str_replace(array('{MENSAGEM}','{TIPOMENSAGEM}'),array($mensagem,$tipo_mensagem),$HTML_OK);
		}else{
			$upload->removeAllFiles();
			return $HTML_UPLOAD;
		}
	}
	
	
	public function trataDadosImportados($nameFile,$options){
		$delimitador 			= $options['delimitador']=='virgula'?',':(($options['delimitador']=='ponto_virgula')?';':'\t');
		$tipoImport	 			= $options['tipo_importacao']=='atualizar'?1:0;
		$tabelaImport	 		= $options['tabela_import'];
		$colunas	 			= $options['colunas'];
		$campo_id	 			= $options['campo_id'];
		$nome_apenas_arquivo 	= basename($nameFile);
		$query_import			= $this->queryClass->getQueryImportarTabela(array(
											'tabela'		=>$tabelaImport,
											'campo_id'		=>$campo_id,
											'colunas'		=>$colunas,
											'filtros'		=>'',
											'separador'		=>$delimitador,
											'diretorio'		=>$nameFile,
											'tipoImport'	=>$tipoImport
								));

		$this->set_conn($this->OBJECT->get_conn());
		$query			= 	$this->query($query_import);
		
		/*
		 * Verificando se existe resultado
		*/		
		if(!$this->num_rows($query))
		{
			return false;
		}else{
			return $this->fetch_array($query);
		}		
		
		return false;
		
	}
	
	
	
}

?>