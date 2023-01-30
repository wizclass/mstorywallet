<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

$todate = date("Y-m-d H:i:s",time());
$debug = false;

if($debug){
	$member['mb_id'] = "test";
	$member['mb_divide_date'] = "";
}

if($member['mb_id'] == ""){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"잘못된 접근입니다.")));
	return false;
}

if($member['mb_divide_date'] != ""){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"{$member['mb_id']} 님은 출금을 하실수 없습니다. 관리자에 문의주세요.")));
	return false;
}


$pin =  $_POST['pin'];

if(!isset($pin)){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"잘못된 접근입니다.")));
	return false;
}

$check_sql = "SELECT reg_tr_password,kyc_cert FROM g5_member WHERE mb_id = '{$member['mb_id']}'";

$check_row = sql_fetch($check_sql);

if(!check_password($pin,$check_row['reg_tr_password'])){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"핀번호를 확인해주세요.")));
	return false;
}

if($check_row['kyc_cert'] <= 0){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"KYC 인증 미등록/미승인 KYC인증이 미등록 또는 미승인 상태입니다.")));
	return false;
}


// 출금처리 PROCESS
$user_ip = $_SERVER['REMOTE_ADDR'];
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

/* 메일인증 - 사용안함 */
//include_once('../lib/otphp/lib/otphp.php');
//include_once(G5_LIB_PATH.'/mailer.lib.php');

$func				= trim($_POST['func']);
$mb_id			= trim($_POST['mb_id']);
$amt		= trim($_POST['amt']);
$select_coin 		= $_POST['select_coin'];

/* 원화계좌출금*/
$wallet_address = trim($_POST['wallet_address']);

if($debug){
	$mb_id = 'arcthan';
	$func = 'withdraw';
	$amt = 0.1;
	$wallet_address = "test_wallet";
	$select_coin = 'ETH';
	if($select_coin == "ETH"){
		$total_eth_balance = 1;
	}else{
		$total_token_balance = 100000;
	}
}

if($select_coin == "ETH"){
	$withdrwal_setting = wallet_config('withdrawal_eth');
	$withdrwal_total = $prev_balance = $total_eth_balance;
}else{
	$withdrwal_setting = wallet_config('withdrawal');
	$withdrwal_total = $prev_balance = $total_token_balance;
}

$fee = $withdrwal_setting['fee'];
$min_limit = $withdrwal_setting['amt_minimum'];
$max_limit = $withdrwal_setting['amt_maximum'];
$day_limit = $withdrwal_setting['day_limit'];

// // 출금가능금액 검증
if ($max_limit != 0 && ($withdrwal_total * $max_limit * 0.01) < $withdrwal_total) {
	$withdrwal_total = $withdrwal_total * ($max_limit * 0.01);
}

$fee_calc = $fee; // 수수료
$in_amt = $amt - $fee_calc; // 실제출금 차감포인트

if($in_amt <= 0){
	echo (json_encode(array("result" => "Failed", "code" => "0010","sql"=>"<span style='font-size:12px'>출금가능 수량이 수수료보다 많아야 합니다.</span>"),JSON_UNESCAPED_UNICODE)); 
	return false;
}

//출금기록 확인
$today_ready_sql = "SELECT * FROM {$g5['withdrawal']} WHERE mb_id = '{$mb_id}' AND date_format(create_dt,'%Y-%m-%d') = '{$now_date}' AND coin = '{$select_coin}'";
$today_ready = sql_query($today_ready_sql);
$today_ready_cnt = sql_num_rows($today_ready);

if($is_debug) echo "<code>일제한: ".$day_limit .' / 오늘 : '.$today_ready_cnt."<br><br>".$today_ready_sql."/ 총 필요금액".$amt_eth_cal."</code><br><br>";

// 일 요청 제한
if($day_limit != 0 && $today_ready_cnt >= $day_limit){
	echo (json_encode(array("result" => "Failed", "code" => "0010","sql"=>"<span style='font-size:12px'>일일 출금 횟수가 초과하였습니다. 하루 최대 $day_limit 회 출금 가능 합니다.</span>"),JSON_UNESCAPED_UNICODE)); 
	return false;
}
if($is_debug) echo "<code>최소: ".$min_limit .' / 최대가능금액 : '.$withdrwal_total."  (".$max_limit."%) / 현재출금가능".$total_withraw."</code><br><br>";

// 최소금액 제한 확인
if( $min_limit != 0 && $amt < $min_limit ) {
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"요청하신 출금 수량이 최소 출금 수량 보다 많아야 출금 신청이 가능합니다.")));
	return false;
}

// 최대금액 제한 확인
if( $max_limit != 0 && $amt > $withdrwal_total ) {
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"요청하신 출금 수량이 하루 최대 출금 수량 보다 많습니다.")));
	return false;
}

if($withdrwal_total < $amt){
	echo (json_encode(array("result" => "Failed", "code" => "0002","sql"=>"회원님의 코인 수량을 확인해주세요.")));
	return false;
}

$Enc_wallet_addr = Encrypt($wallet_address,$secret_key,$secret_iv);
$Enc_wallet_addr2 = Encrypt($wallet_address,$mb_id,'x');

$encrypt_sql = "INSERT person_log set mb_id = '{$mb_id}', addr_key='{$Enc_wallet_addr}', dt = '{$todate}'";
sql_query($encrypt_sql);

$addr_key = sql_fetch("SELECT no as person_key FROM person_log WHERE addr_key = '{$Enc_wallet_addr}' order by no desc limit 0,1 ")['person_key'];

//출금 처리
$proc_receipt = "insert {$g5['withdrawal']} set
mb_id ='{$mb_id}',
od_id = '{$addr_key}'
, addr = '{$Enc_wallet_addr}'
, bank_name = ''
, bank_account = ''
, account_name = ''
, account = '{$prev_balance}'
, amt ={$in_amt}
, fee = {$fee_calc}
, fee_rate = {$fee}
, amt_total = {$amt}
, coin = '{$select_coin}'
, status = '0'
, create_dt = '".$now_datetime."'
, cost = '1'
, out_amt = '{$in_amt}'
, od_type = '출금요청'
, memo = ''
, ip =  '{$user_ip}' ";



if($debug){ 
	$rst = 1;
	echo "<br>".$proc_receipt."<br><br>"; 
}else{
	$rst = sql_query($proc_receipt);
}

// 회원정보업데이트
// 출금시 선차감
if($rst){

	$columns = $select_coin == "ETH" ? "eth_my_wallet = '{$Enc_wallet_addr2}' , mb_amt_eth = mb_amt_eth + {$amt} " : "mb_wallet = '{$Enc_wallet_addr2}' , mb_shift_amt = mb_shift_amt + {$amt}";

	$amt_query = "UPDATE g5_member set 
	{$columns}
	, otp_key = ''
	where mb_id = '{$mb_id}' ";
}
 
if($debug){ 
	$amt_result = 1;
	print_R($amt_query); 
}else{ 
	$amt_result = sql_query($amt_query);
}


if($rst && $amt_result){
	echo (json_encode(array("result" => "success", "code" => "1000")));
}else{
	echo (json_encode(array("result" => "Failed", "code" => "0001","sql"=>"처리되지 않았습니다. 문제가 지속되면 관리자에게 연락주세요."),JSON_UNESCAPED_UNICODE));
}
