<?php
function get_coins_price(){
	$result = array();
	$url_list = array(
		'https://api.upbit.com/v1/ticker?markets=KRW-ETH&markets=USDT-ETH',
		'https://api.probit.com/api/exchange/v1/ticker?market_ids=ESGC-USDT'
		);

	$data = multi_curl($url_list);
	
	$eth_krw = $data[0][0]['trade_price'];
	$usdt_eth = $data[0][1]['trade_price'];
	$usdt_esgc = $data[1]['data'][0]['last'];

	$result['usdt_krw'] = $eth_krw / $usdt_eth;
	$result['usdt_eth'] = $usdt_eth;
	$result['usdt_esgc'] = $usdt_esgc;
	$result['esgc_eth'] = shift_coin($usdt_esgc / $usdt_eth);
	$result['esgc_krw'] = $result['esgc_eth'] * $eth_krw;
	$result['eth_krw'] = $eth_krw;

	return $result;
}

function multi_curl($url){
	$ch = array();
	$response = array();
	$curl_init = curl_multi_init();
	foreach($url as $key => $value){
		$ch[$key] = curl_init($value);
		curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);
		curl_multi_add_handle($curl_init,$ch[$key]);
	}
	
	do {
		curl_multi_exec($curl_init, $running);
		curl_multi_select($curl_init);
	} while ($running > 0);
	
	foreach(array_keys($ch) as $key){
		$response[$key] = json_decode(curl_multi_getcontent($ch[$key]),true); 
		curl_multi_remove_handle($curl_init, $ch[$key]);
	}
	
	curl_multi_close($curl_init);
	return $response;
}

// 코인 표시
function shift_coin($val, $decimal = COIN_NUMBER_POINT){
	$_num = (int)str_pad("1",$decimal+1,"0",STR_PAD_RIGHT);
	return floor($val*$_num)/$_num;
}

function clean_number_format($val, $decimal = COIN_NUMBER_POINT){
	$_decimal = $decimal <= 0 ? 1 : $decimal;
	$_num = number_format(shift_coin($val,$decimal), $_decimal);
    $_num = rtrim($_num, 0);
    $_num= rtrim($_num, '.');

    return $_num;
}

function shift_auto($val,$type = 'ETH'){
	if($type == 'ETH'){
		$decimal = COIN_NUMBER_POINT;
	}else if($type == 'KRW'){
		$decimal = ASSETS_NUMBER_POINT;
	}else{
		$decimal = BONUS_NUMBER_POINT;
	}
	return clean_number_format($val,$decimal);
}

$coin = get_coins_price();
?>