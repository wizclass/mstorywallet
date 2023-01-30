<?php
include_once(G5_PATH.'/util/purchase_proc.php');

if($debug){
	$mb_id = 'test3';
	$mb_no = 4;
	$_POST['od_settle_case'] = "ESGC";
	$_POST['od_cart_price'] = 5000;
	$od_tno = 2022111101;
}

$od_tno = isset($_POST['od_tno']) ? $_POST['od_tno'] : false;

if(!$od_tno){
	echo json_encode(array("code"=>"300","msg"=>"잘못된 접근입니다.")); 
	exit;
}

$item_sql = "select *, count(*) as cnt from {$g5['g5_shop_item_table']} where it_id = '{$od_tno}'";
$item_row = sql_fetch($item_sql);

if($item_row['cnt'] <= 0){
	echo json_encode(array("code"=>"300","msg"=>"존재하지 않는 상품입니다.")); 
	exit;
}

$od_cart_price = isset($_POST['od_cart_price']) ? shift_coin($_POST['od_cart_price'],COIN_NUMBER_POINT) : false;

if(!$od_cart_price){
	echo json_encode(array("code"=>"300","msg"=>"스테이킹 수량이 올바르지 않습니다.")); 
	exit;
}

$od_settle_case = $item_row['it_brand'];

// if($od_settle_case == "ETH"){
	    // $krw = "eth_krw";
	    // $total_balance = $total_eth_balance; 
	    // $column = "mb_calc_eth = mb_calc_eth";
// }else{
	$krw = "esgc_krw";
	$total_balance = $total_token_balance;
	$column = "mb_deposit_calc = mb_deposit_calc";
// }
	
$ASSETS_CURENCY = ASSETS_CURENCY;
	
if($od_cart_price < $item_row['it_price']){
    echo json_encode(array("code"=>"300","msg"=>"최소 스테이킹 {$ASSETS_CURENCY} 수량은 {$item_row['it_price']} 입니다.")); 
	exit;
}

if($total_balance < $od_cart_price){
    echo json_encode(array("code"=>"300","msg"=>"보유하신 {$ASSETS_CURENCY} 수량이 부족합니다.")); 
	exit;
}

$od_tax_mny = $item_row['it_supply_point'];
$pay_end = $item_row['it_point'] * 12;
$od_name = $item_row['it_option_subject'];

$today = date('Y-m-d');
$od_hope_date = date('d') < 29 ? date("y-m-d", strtotime("+1 month", strtotime($today))) :  date("y-m-01", strtotime("first day of +2 month", strtotime($today)));

$val = substr($pack_maker,1,1);

$od_tax_flag = $od_cart_price * ($item_row['it_cust_price'] * 0.01);  //수수료 부분 일단 임시
$_od_cart_price = $od_cart_price - $od_tax_flag;  //수수료 부분 일단 임시

$od_cash = shift_coin($_od_cart_price * $coin[$krw],ASSETS_NUMBER_POINT);
$upstair = shift_coin(($od_tax_mny * 0.01) * $_od_cart_price, BONUS_NUMBER_POINT);
$pv = shift_coin($upstair / $pay_end, BONUS_NUMBER_POINT);

$expiry_date = ($item_row['it_point'] * 12) - 1;
$od_invoce_time = date("Y-m-d",strtotime("+{$expiry_date} month", strtotime($od_hope_date)));

$od_id = date("YmdHis",time()) . sprintf('%02d', rand(0, 99));

$sql = "insert g5_shop_order set
		od_id = {$od_id}, 
		mb_id = '{$mb_id}', 
		mb_no = {$mb_no}, 
		od_tax_flag = {$od_tax_flag},
		od_cart_price = {$_od_cart_price}, 
		od_cash = {$od_cash}, 
		upstair = {$upstair}, 
		pv = {$pv}, 
		pay_count = 0,
		pay_acc = 0,
		pay_acc_eth = 0,
		pay_end = {$pay_end},
		od_tax_mny = {$od_tax_mny},
		od_hope_date = '{$od_hope_date}', 
		od_name = '{$od_name}', 
		od_tno = {$od_tno}, 
		od_time = now(),
		od_date = curdate(),
		od_memo = '{$mb_name}',
		od_app_no = '{$item_row['ca_id3']}',
		od_settle_case = '{$od_settle_case}',
		od_receipt_time = now(), 
		od_status = '{$admin}스테이킹',
		od_invoice_time = '{$od_invoce_time}'";

if($debug){
	$rst = 1;
	echo "구매내역 Invoice 생성<br>";
	echo $sql."<br><br>"; 
}else{
	$rst = sql_query($sql);
}

$logic = purchase_package($mb_id, $od_tno,0);

if($rst && $logic){

	$sql = "update g5_member set {$column} - {$od_cart_price} where mb_id = '{$mb_id}'";

	if($debug){
		echo "회원 금액 반영<br>";
		echo $sql."<br>";
	}else{
		$sql_result = sql_query($sql);
		ob_end_clean();
		
		if($sql_result){
			echo (json_encode(array("code" => "200", "msg" => "[스테이킹 시작일 : {$od_hope_date}] {$today}일 {$_od_cart_price} {$ASSETS_CURENCY} 스테이킹을 하였습니다."),JSON_UNESCAPED_UNICODE));
		}
	}
}else{
	ob_end_clean();
	echo (json_encode(array("code"=>"500","msg" => "죄송합니다. [{$_od_cart_price} {$od_settle_case}] 스테이킹을 시도중에 문제가 발생하였습니다. 문제가 지속되면 관리자에 문의해주세요."),JSON_UNESCAPED_UNICODE));
}

?>

<?if($debug){?>
<style>
    .red{color:red;font-size:16px;font-weight:900}
    .blue{color:blue;font-size:16px;font-weight:900}
    .title {font-weight:900}
    code{text-decoration: italic;color:green;display:block}
    .box{background:#f5f5f5;border:1px solid #ddd;padding:20px;}
</style>
<?}?>
