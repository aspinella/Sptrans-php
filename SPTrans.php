<?php
//---------------URLS fixas
define('urlSp','http://api.olhovivo.sptrans.com.br/v0');
define('autenticar','/Login/Autenticar?token=');
define('urlBusca', '/Linha/Buscar?termosBusca=');
define('urlDetalhes', '/Linha/CarregarDetalhes?codigoLinha=');
define('urlParadas', '/Parada/Buscar?termosBusca=');
define('urlParadasLinha','/Parada/BuscarParadasPorLinha?codigoLinha=');
define('urlParadasCorredor','/Parada/BuscarParadasPorCorredor?codigoCorredor=');
define('urlCorredor','/Corredor');
define('urlPosicaoLinha','/Posicao?codigoLinha=');
define('urlPrevisaoParada1','/Previsao?codigoParada=');
define('urlPrevisaoParada2','&codigoLinha=');
define('urlPrevisaoLinha','/Previsao/Linha?codigoLinha=');
define('urlPrevisaoParadaOn','/Previsao/Parada?codigoParada=');
define('urlSptrans','http://olhovivo.sptrans.com.br/#sp?cat=ParadaLinha&c=');
//--------------------------

function cookieSecao(){
//retorna cookie necessario nas etapas seguintes
	$spToken = '<INSERT_YOUR_TOKEN_FROM_SPTRANS>';
	$url = urlSp.autenticar.$spToken; $data = "";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE,0);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	return $server_output;
}
function do_get_request($url,$cookie){
//faz GET para obter resultados de buscas e afins
	$ch = curl_init();//  open connection
    curl_setopt($ch, CURLOPT_URL, $url);//  set the url
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($ch, CURLOPT_VERBOSE,0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//  To display result of curl
    $result = curl_exec($ch);//  execute post
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpCode!=200){
    	//Serviço encontra-se fora do ar
    	exit(0);
    }
    curl_close($ch);
	return $result;

}
function arrumaCookie($cookie){
//arruma o cookie recebido, para ser usado nas funções de busca
	return substr($cookie, strpos($cookie, 'Set-Cookie:')+ strlen('Set-Cookie:'));
}
function buscaOnibus($busca){
	/*
	 [0]=> { ["CodigoLinha"]=> int(466) ["Circular"]=> bool(false) ["Letreiro"]=> string(4) "978L" ["Sentido"]=> int(1) ["Tipo"]=> int(10) ["DenominacaoTPTS"]=> string(20) "TERM.PRINCESA ISABEL" ["DenominacaoTSTP"]=> string(20) "T.T.V.N.CACHOEIRINHA" ["Informacoes"]=> NULL }
	 [1]=> { ["CodigoLinha"]=> int(33234) ["Circular"]=> bool(false) ["Letreiro"]=> string(4) "978L" ["Sentido"]=> int(2) ["Tipo"]=> int(10) ["DenominacaoTPTS"]=> string(20) "TERM.PRINCESA ISABEL" ["DenominacaoTSTP"]=> string(20) "T.T.V.N.CACHOEIRINHA" ["Informacoes"]=> NULL}
*/
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($busca);
	return json_decode(do_get_request(urlSp.urlBusca.$data,$ck),true);
}
function carregarDetalhes($codLinha){
//Retorna as informações cadastrais de uma determinada linha. Caso o parâmetro seja omitido são retornados os dados de todas as linhas do sistema.
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codLinha);
	return json_decode(do_get_request(urlSp.urlDetalhes.$data,$ck),true);
}
function procuraParadas($termo){
//Realiza uma busca fonética das paradas de ônibus do sistema com base no parâmetro informado. A consulta é realizada no nome da parada e também no seu endereço de localização.
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($termo);
	return json_decode(do_get_request(urlSp.urlParadas.$data,$ck),true);
}
function paradasLinha($codLinha){
//retorna todas as paradas de uma linha específica
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codLinha);
	return json_decode(do_get_request(urlSp.urlParadasLinha.$data,$ck),true);
}
function paradasCorredor($codCorredor){
//retorna todas as paradas em um corredor
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codCorredor);
	return json_decode(do_get_request(urlSp.urlParadasCorredor.$data,$ck),true);
}
function corredores(){
//retorna todos os corredores inteligentes de SP
	$ck = arrumaCookie(cookieSecao());
	return json_decode(do_get_request(urlSp.urlCorredor,$ck),true);
}
function posicaoLinha($codLinha){
//Retorna uma lista com todos os veículos de uma determinada linha com suas devidas posições lat / long
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codLinha);
	return json_decode(do_get_request(urlSp.urlPosicaoLinha.$data,$ck),true);
}
function previsaoParada($codParada, $codLinha){
//Retorna uma lista com a previsão de chegada dos veículos da linha informada que atende ao ponto de parada informado.
	$ck = arrumaCookie(cookieSecao());
	$data1 = urlencode($codParada);
	$data2 = urlencode($codLinha);
	return json_decode(do_get_request(urlSp.urlPrevisaoParada1.$data1.urlPrevisaoParada2.$data2,$ck),true);
}
function previsaoPontosParada($codLinha){
//Retorna uma lista com a previsão de chegada de cada um dos veículos da linha informada em todos os pontos de parada aos quais que ela atende.
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codLinha);
	return json_decode(do_get_request(urlSp.urlPrevisaoLinha.$data,$ck),true);
}
function previsaoOnibusParada($codParada){
//Retorna uma lista com a previsão de chegada dos veículos de cada uma das linhas que atendem ao ponto de parada informado.
	$ck = arrumaCookie(cookieSecao());
	$data = urlencode($codParada);
	return json_decode(do_get_request(urlSp.urlPrevisaoParadaOn.$data,$ck),true);
}
//-------Funçoes para tratamento do JSON
function retornoBuscaQtd($jsonB){
//Verifica se contem mais de uma linha diferente nos onibus buscados
	$arr = $jsonB;
	$len = count($arr);
	$num=0;
	if(is_array($arr) && @array_key_exists('Tipo',@$arr[0])){
		$str1=$arr[0]['Letreiro'].$arr[0]['Tipo'];
		for($j=1;$j<$len;$j++){
			$str2=$arr[$j]['Letreiro'].$arr[$j]['Tipo'];
			if($str1!=$str2){
				$num++;
			}
		}
		if($num>1){
			return $num;//possui mais de uma linha	
		}else{
			return 1;//possui somente uma linha	
		}
	}else{
		return 0;
	}
}
function posicaoLinhaArrumado($codLinha){
/*
[0] => 12:17 
[1] => Array ( 
	[0] => Array ( [p] => 11102 [a] => [py] => -23.49612 [px] => -46.677134 ) 
	[1] => Array ( [p] => 11097 [a] => [py] => -23.52945325 [px] => -46.66081775 ) 	
	[2] => Array ( [p] => 11100 [a] => [py] => -23.535142 [px] => -46.644700375 ) 
	[3] => Array ( [p] => 11999 [a] => 1 [py] => -23.511407125 [px] => -46.666252625 ) 
) 
*/
	$arr_aux = posicaoLinha($codLinha);
	$qtdCarros = count($arr_aux['vs']);
	$carrosID=null;
	$a[] = null;
	$lat[] = null;
	$long[] = null;
	foreach($arr_aux['vs'] as $value){
		@$carrosID[] = $value['p'];
		@$a[] = $value['a'];
		@$lat[] = $value['py'];
		@$long[] = $value['px'];
	}
	$retorno = array('hora'=> $arr_aux['hr'], 'qtdCarros'=> $qtdCarros, 'carrosID'=> $carrosID, 'lat'=> $lat,'long'=> $long, 'a'=>$a);
	return $retorno;
}
function retornoLinkSptrans($codLinha,$letreiroLinha,$defStr){
	//http://olhovivo.sptrans.com.br/#sp?cat=ParadaLinha&c=133234&l=978L-10&d=T.T.V.N.CACHOEIRINHA
	return urlSptrans.$codLinha."&l=".$letreiroLinha."&d=".$defStr;
}