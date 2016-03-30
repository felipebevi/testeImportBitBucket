
<?PHP
header('Content-Type: text/html; charset=utf-8');

 class WRS{
 	
 	public static function VERSION(){}
 }

include('POR.lng');

header('Content-Type: text/html; charset=utf-8');



function is_html($string)
{
	return preg_match("/<[^<]+>/",$string,$m) != 0;
}



function isArray($value,$prince)
{
	
	
	
	
	

	
	
	if(is_array($value))
	{
		return '<span class="notranslate">array(</span>'.$prince.implode($prince."<span class='notranslate'>,</span>".$prince,$value).$prince.'<span class="notranslate">)</span>';
	}
	
	
	
	$replace		=	 array('%s',
								'"',
								htmlspecialchars('<p>'),
								htmlspecialchars('</p>'),
								htmlspecialchars('<br>'),
								htmlspecialchars('<strong>'),
								htmlspecialchars('</strong>'),
								htmlspecialchars('<b>'),
								htmlspecialchars('</b>'),
								'#NOMECAMPO#',
								'#VAL#',
								'#DEFAULT_LAYOUT#',
								'#NAME_FILE#',
								htmlspecialchars("<img src='imagens/wrs_loading.gif'/>")
								
	);
	$rr				=	array(
								' <span class="notranslate">%s</span> ', 
								"'",
								'<span class="notranslate">'.htmlspecialchars('<p>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('</p>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('<br>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('<strong>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('</strong>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('<b>').'</span>',
								'<span class="notranslate">'.htmlspecialchars('</b>').'</span>',
								'<span class="notranslate">#NOMECAMPO#</span>',
								'<span class="notranslate">#VAL#</span>',
								'<span class="notranslate">#DEFAULT_LAYOUT#</span>',
								'<span class="notranslate">#NAME_FILE#</span>',
								"<span class='notranslate'>".htmlspecialchars("<img src='imagens/wrs_loading.gif'/>")."</span>"
	);
	
	
	
	if(!is_html($value)) return $prince.str_replace($replace, $rr, htmlspecialchars($value)).$prince;
	
	
	
		$eof	=	htmlentities('<<<EOF');
	
	
		echo '<span class="notranslate">'.$eof.'</span><br>'.str_replace($replace, $rr, htmlspecialchars($value)).PHP_EOL.'<span class="notranslate"><br>EOF</span>';
	
}


 
foreach($language as $label =>$value)
{
	
	

	$replace		=		'"';
	
	$prince			=	'<span class="notranslate">'.$replace.'</span>';
	
	echo '<span class="notranslate"><b>$language["'.$label.'"]=</b></span>';
	echo isArray($value,$prince);
	echo '<span class="notranslate">;<br></span>';
	echo PHP_EOL.PHP_EOL;
	
}


?>

