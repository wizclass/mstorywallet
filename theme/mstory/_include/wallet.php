<?


if($_GET['debug']) $debug = 1;
include_once(G5_THEME_PATH."/_include/coin_price.php");
/*부분서비스점검*/
$sql = " select * from maintenance";
$nw = sql_fetch($sql);
$nw_with = $nw['nw_with'];

/*날짜선택 기본값 지정 : 3개월전~ 오늘*/
if (empty($fr_date)) {$fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 month"));}
if (empty($to_date)) {$to_date =  date("Y-m-d", strtotime(date("Y-m-d")));}

/* 시세업데이트 시간*/
/* if($is_date){
	$last_rate_time = last_exchange_rate_time();
	echo "exchage last : ".$last_rate_time."<br>";
	echo "exchage next : ".$next_rate_time."<br>";
} */



// 회원 자산, 보너스 정보 esgc
$total_deposit = $member['mb_deposit_point'] + $member['mb_deposit_calc']; 
$balance = $member['mb_balance'];
$amt = $member['mb_shift_amt'];


// 회원 자산, 보너스 정보 eth
$balance_eth = $member['mb_balance_eth'];
$calc_eth = $member['mb_calc_eth'];
$amt_eth = $member['mb_amt_eth'];

$total_token_balance = shift_coin($total_deposit + $balance - $amt,BONUS_NUMBER_POINT);
$total_eth_balance = shift_coin($balance_eth + $calc_eth - $amt_eth);

$shift_balance = shift_auto($balance,ASSETS_CURENCY);
$shift_balance_eth = shift_auto($balance_eth);
$shift_total_token_balance = shift_auto($total_token_balance,ASSETS_CURENCY);
$shift_total_eth_balance = shift_auto($total_eth_balance);
$total_token_balance_krw = shift_auto($coin['esgc_krw'] * $total_token_balance,ASSETS_CURENCY);
$total_eth_balance_krw = shift_auto($coin['eth_krw'] * $total_eth_balance,ASSETS_CURENCY);

$mb_level_array = ['일반회원','정회원','정회원S'];
$mb_lvl = $member['mb_level'];

function coin_price($income){
	global $g5;

	$currency_sql = " SELECT * from {$g5['coin_price']} where symbol = '{$income}' ";
	$result = sql_fetch($currency_sql);

	if($result['manual_use'] == 1){
		return $result['manual_cost'];
	}else{
		return $result['current_cost'];
	}
}


function coin_prices($income,$category = 'cost'){
	global $g5,$usdt_rate;

	$currency_sql = " SELECT * from {$g5['coin_price']} where symbol LIKE '{$income}' OR name LIKE '{$income}' ";
	$result = sql_fetch($currency_sql);

	$symbol = strtoupper($result['symbol']);

	if($result['changepricedaily'] > 0){
		$daily =  "<span class='font_red'>▲".number_format(str_replace("-","",$result['changepricedaily']),2)."% </span>";
	}else{
		$daily = "<span class='font_blue'>▼".number_format($result['changepricedaily'],2)."% </span>";
	}

	if($result['manual_use'] == 1){
		$cost =  $result['manual_cost'];
	}else{
		$cost = $result['current_cost'];
	}

	$dollor = $cost*$usdt_rate;
	$chart = $result['chart'];
	$icon = $result['icon'];

	if($category == 'daily'){
		return $daily;
	}else if($category == 'symbol'){
		return $symbol;
	}else if($category == 'dollor'){
		return 	$dollor;
	}else if($category == 'chart'){
		return $chart;
	}else if($category == 'daily'){
		return $daily;
	}else if($category == 'icon'){
		return $icon;
	}else if($category == 'all'){
		return array($symbol,$cost,$dollor,$daily,$chart,$icon);
	}else{
		return $cost;
	}
}

//회원 레벨 
// $member_level_array = array('일반회원','정회원','센터','인정회원','정회원','','','','','관리자','슈퍼관리자');


// $user_icon ="<span class='user_icon lv0'><i class='ri-vip-crown-line'></i></span>";

// 기본 회원가입시 0 LEVEL
// $user_level = $member_level_array[$member['mb_level']];

// if ($member['mb_level'] == 1) {
// 	$user_icon = "<span class='user_icon lv1'><i class='ri-vip-crown-line'></i></span>";
// } else if ($member['mb_level'] == 2) {
// 	$user_icon = "<span class='user_icon lv2'><i class='ri-team-fill'></i></span>";
// } else if ($member['mb_level'] == 3) {
// 	$user_icon = "<span class='user_icon lv3'><i class='ri-community-line'></i></span>";
// } else if ($member['mb_level'] == 4) {
// 	$user_icon = "<span class='user_icon lv4'><i class='ri-building-2-line'></i></span>";
// } else if ($member['mb_level'] == 5) {
// 	$user_icon = "<span class='user_icon lv5'><i class='ri-government-line'></i></span>";
// }else if($member['mb_level'] >8){
// 	$user_level = $member_level_array[$member['mb_level']];
// 	$user_icon = "<span class='user_icon lv9'><i class='ri-user-settings-line'></i></span>";
// }else{
// 	$user_icon = "<span class='user_icon lv0'><i class='ri-vip-crown-line'></i></span>";
// }


// function user_icon($id, $func)
// {
// 	global $g5, $member_level_array;

// 	$mb_sql = "SELECT * from g5_member WHERE mb_id = '{$id}' ";
// 	$result = sql_fetch($mb_sql);
// 	$mb_level = $result['mb_level'];
// 	$user_icon = "<span class='user_icon lv0'><i class='ri-vip-crown-line'></i></span>";
// 	$user_level = $member_level_array[$mb_level];

// 	if ($mb_level > 0) {
// 		$user_icon = "<span class='user_icon lv1' title='".$member_level_array[1]."'><i class='ri-vip-crown-line'></i></span>";
// 	}
// 	if ($mb_level == 2) {
// 		$user_icon = "<span class='user_icon lv2' title='".$member_level_array[2]."'><i class='ri-team-fill'></i></span>";
// 	}
// 	if ($mb_level == 3) {
// 		$user_icon = "<span class='user_icon lv3' title='".$member_level_array[3]."'><i class='ri-community-line'></i></span>";
// 	}
// 	if ($mb_level == 4) {
// 		$user_icon = "<span class='user_icon lv4' title='".$member_level_array[4]."'><i class='ri-building-2-line'></i></span>";
// 	}
// 	if ($mb_level == 5) {
// 		$user_icon = "<span class='user_icon lv5' title='".$member_level_array[5]."'><i class='ri-government-line'></i></span>";
// 	}
// 	if ($mb_level > 8) {
// 		$user_icon = "<span class='user_icon lv9' title='".$member_level_array[9]."'><i class='ri-user-settings-line'></i></span>";
// 	}


// 	if ($func == 'icon') {
// 		return $user_icon;
// 	} else {
// 		return $user_level;
// 	}
// }

/* 회원직급(grade)*/
function user_grade($id){
	global $g5;

	$mb_sql = "SELECT * from g5_member WHERE mb_id = '{$id}' ";
	$result = sql_fetch($mb_sql);
	return $result['grade'];
}


// 닉네임검사
function get_name($id){
	global $g5;
	$mb_sql = "SELECT mb_nick from g5_member WHERE mb_id = '{$id}' ";
	$result = sql_fetch($mb_sql);

	if($result && $result['mb_nick'] != ''){return $result['mb_nick'];}else{return ' - ';}
}

// 이름 + 닉네임
function express_nick_name($mb_id){
	$mb = sql_fetch("SELECT mb_nick,mb_name FROM g5_member WHERE mb_id = '{$mb_id}' ");
	
	if($mb) {
		return $mb['mb_name']."[".$mb['mb_nick']."]";
	}else{
		return '';
	}
}


// 보너스 수당-한계 퍼센트
function bonus_per($mb_id ='',$mb_balance='', $mb_limit = ''){
	global $member,$limited_per;

	if($mb_id == ''){
		if($member['mb_save_point'] != 0 && $member['mb_balance'] !=0 && $limited_per != 0){
			$bonus_per = ($member['mb_balance']/($member['mb_save_point'] * $limited_per));
		}else{
			$bonus_per = 0;
		}
	}else{
		if($mb_limit != 0 && $mb_balance !=0 && $limited_per != 0){
			$bonus_per = ($mb_balance/($mb_limit * $limited_per));
		}else{
			$bonus_per = 0;
		}
	}
	return round($bonus_per);
}




// 출금-업스테어 가능 잔고 계산시
/* $ava_sql = "select sum(mb_account + mb_calc + mb_amt + mb_balance) as total from g5_member where mb_id = '".$member['mb_id']."'";
$ava_total = sql_fetch($ava_sql);
$ava_balance = $ava_total['total'];
$ava_balance_num = Number_format($ava_balance, 2); // 콤마 포함 소수점 2자리까지 */


// 입출금 설정정보
function wallet_config($func){
	global $g5;
	
	$wallet_sql = "SELECT * FROM {$g5['wallet_config']} WHERE used = 1 AND function  = '{$func}' ";
	$walelt_result = sql_fetch($wallet_sql);
	return $walelt_result;
	/* 
	$walelt_result = sql_query($wallet_sql);
	$wallet_config = [];
	while( $wc = sql_fetch_array($walelt_result) ){
		array_push($wallet_config,$wc);
	} */
}





// 환율변환시 (입력통화, 비율, 출력할통화)
function shift_price($income,$val = 1, $outcome){
	$in_price = coin_price($income);
	$out_price = coin_price($outcome);
	
	return $in_price * $val / $out_price;
}


// 예치금/수당 퍼센트
function bonus_state($mb_id){
	global $limited_per;

	$math_percent_sql = "select sum(mb_balance / mb_deposit_point) * {$limited_per} as percent from g5_member where mb_id ='{$mb_id}' ";
	$math_percent = sql_fetch($math_percent_sql)['percent'];
	if($debug) echo "BONUS PERCENT :".$math_percent;
	return $math_percent;
}


function division_count($val){
	if($val < 0){
		return 0;
	}else{
		return number_format($val);
	}
}


/*레퍼러 하부매출*/
function refferer_habu_sales($mb_id,$category=''){
	global $member;

	$where = $category.'recom_bonus_noo';

	$referrer_sql = "select day,noo from {$where} where mb_id ='{$mb_id}' ORDER BY day desc limit 1";
	$referrer_result = sql_fetch($referrer_sql);
	$referrer_sales = $referrer_result['noo'];

	if($referrer_sales > 0){
		$referrer_sales = $referrer_sales;

		/*KHAN 본인매출 제외*/
		// $referrer_sales = $referrer_sales - $member['mb_save_point'];

	}else{
		$referrer_sales = 0;
	}
	return $referrer_sales;
}

/*레퍼러 LEG 하부매출*/
function refferer_habu_sales_power($mb_id){
	$max_recom_sql = "SELECT mb_id,MAX(noo) as big FROM recom_bonus_noo AS A WHERE A.mb_id IN (select mb_id FROM g5_member WHERE mb_recommend = '{$mb_id}' )";
	// $max_recom_result = sql_query($max_recom_sql);
	$max_recom = sql_fetch($max_recom_sql);

	$max_recom_point = $max_recom['big'];
	
	if($max_recom_point > 0){
		$max_recom_point = $max_recom_point;
	}else{
		$max_recom_point = 0;
	}
	return $max_recom_point;
}



/*스폰서(후원) 하부매출*/ 
function sponsor_habu_sales($mb_id){
	$b_recomm_sql = "select mb_id as b_recomm from g5_member where mb_brecommend='".$mb_id."' and mb_brecommend_type='L'";
	$b_recomm_res = sql_fetch($b_recomm_sql);
	$b_recomm = $b_recomm_res['b_recomm'];
	if($b_recomm){
		$left_noo_sql = "select noo from bnoo2 where mb_id ='{$b_recomm}' order by day desc limit 0 ,1";
		$left_noo_result = sql_fetch($left_noo_sql);
		$left_noo = $left_noo_result['noo'];
	}


	$b_recomm2_sql = "select mb_id as b_recomm2 from g5_member where mb_brecommend='".$mb_id."' and mb_brecommend_type='R'";
	$b_recomm2_res = sql_fetch($b_recomm2_sql );
	$b_recomm2 = $b_recomm2_res['b_recomm2'];
	if($b_recomm2){
		$right_noo_sql = "select noo from bnoo2 where mb_id ='{$b_recomm2}' order by day desc limit 0 ,1";
		$right_noo_result = sql_fetch($right_noo_sql);
		$right_noo = $right_noo_result['noo'];
	}

	$sponsor_sales_sum = $left_noo + $right_noo;

	if($sponsor_sales_sum > 0){
		$sponsor_sales = Number_format($sponsor_sales_sum);
	}else{
		$sponsor_sales = 0;
	}

	return $sponsor_sales;
}


/*국가코드*/
$nation_name=array('Japan'=>81,'Republic of Korea'=>82,'Vietnam'=>84,'China'=>86,'Indonesia'=>62,'Philippines'=>63,'Thailand'=>66);

// 회원가입시
function get_member_nation_select($name, $selected=0, $key="")
{
    global $g5,$nation_name;

	$str = "\n<select id=\"{$name}\" name=\"{$name}\"";
	$str .= ">\n";

	foreach($nation_name as $key => $value){
		$str .= '<option value="'.$value.'"';
		echo $value." | ".$selected."<br>";
        if ($value == $selected)
            $str .= ' selected="selected"';
        $str .= ">0".$value." - {$key}</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

// 프로필
function get_member_nation($value)
{
    global $g5,$nation_name;
	$key = array_search($value, $nation_name);
	return $key;
}

// 시세 마지막 업데이트 시간
function last_exchange_rate_time(){
	$sql = "SELECT * FROM m3cron_log ORDER BY DATETIME desc limit 1";
	$result = sql_fetch($sql);
	$last_time = $result['datetime'];
	return $last_time;
}

// 시세 다음 업데이트 시간
function next_exchange_rate_time(){
	$sql = "SELECT * FROM m3cron_log ORDER BY DATETIME desc limit 1";
	$result = sql_fetch($sql);
	$last_time = $result['datetime'];
	$next_time = date("Y-m-d h:i:s a", strtotime(date($last_time)."+24 hour"));
	return $next_time;
}

// 마이닝상품 만료일 계산
function expire_date($start){
	$expire_date = date("Y-m-d", strtotime($start."+3 year"));
	return $expire_date;
}

// 원 표시
function shift_kor($val){
	return Number_format($val, 0);
}

// 달러 표시
function shift_doller($val){
	return Number_format($val, 2);
}

// 달러 , ETH 코인 표시
// function shift_auto($val,$type = 'ETH'){
// 	if($type == 'ETH'){
// 		$decimal = COIN_NUMBER_POINT;
// 	}else if($type == 'KRW'){
// 		$decimal = ASSETS_NUMBER_POINT;
// 	}else{
// 		$decimal = BONUS_NUMBER_POINT;
// 	}
// 	return clean_number_format($val,$decimal);
// }


function shift_auto_zero($val,$coin = ASSETS_CURENCY){
	if($val == 0 || $val ==''){
		return '-';
	}else{
		if($coin == '$'){
			return shift_doller($val);
		
		}else if($coin == '원'){
			return shift_kor($val);
		}else{
			return shift_coin($val);
		}
	}
}

/*숫자표시*/
function shift_number($val){
	return preg_replace("/[^0-9].*/s","",$val);
}

/*콤마제거숫자표시*/
function conv_number($val) {
	$number = (int)str_replace(',', '', $val);
	return $number;
}

// 코인 소수점 특정자리수 버림 계산값 
function calculate_math($val,$point){
	$cal1 = $val * (POW(10, $point+1));
	$cal2 = round($cal1);
	$cal3 = $cal2 / (POW(10, $point+1));
	return $cal3;
}

// 퍼센트 정수
function percent_value($value){
	global $mining_hash;
	
	if($value < 1){
		$return  = '-';
	}else{
		$return  = sprintf('%0.2f', ($value/100) );
		$return .= " ".side_exp($mining_hash[0]);
	} 
	return $return;
}

// 퍼센트 숫자값
function percent_value_number($val,$rate){
	
	if($val > 0 && $rate > 0){
		$percent_value = Number_format($val/$rate);
	}else{
		$percent_value = 0;
	}
	return $percent_value;
}

/*날짜형식 변환 - 오늘기준 시간만표시*/
function timeshift($time){
	$today_year = date("Y-m-d");
	$target_year = date("Y-m-d",strtotime($time));
	
	if($today_year == $target_year){
		return date("H:i:s",strtotime($time));	
	}else{
		return date("Y-m-d",strtotime($time));
	}
}

/*날짜형식 변환 2 - 날짜만/시간포함 */
function timeshift2($time,$func=1){
	if($func == 1){
		return date("Y-m-d",strtotime($time));	
	}else{
		return date("Y-m-d H:i:s",strtotime($time));
	}
}

/*날짜형식 변환 3 - 날짜순서역순*/
function timeshift3($time){
	return date("d/m/Y ",strtotime($time));
}

// 아이디 별표시
function secure_id($mb_id){
	$strim = substr($mb_id, 0, 3) . "***";
	echo $strim;
}


function nav_active($val){
		global $stx;
		if($val == $stx) echo "active";
		if(!$stx && $val='all') echo "active";
}

function string_explode($val,$dived_value = 'member'){
	$stringArray = explode($dived_value,$val);
	$string1= "<span class='tx1'>".$stringArray[0]."</span>";
	$string2 = "<span class='tx2'>".$stringArray[1]."</span>";
	return $string1.$string2;
}

/* function Number_explode($val){
	$stringArray = explode(".",$val);
	$string1= $stringArray[0].".";
	$string2 = "<string class='demical'>".$stringArray[1]."</string>";
	return $string1.$string2;
} */

// 요청결과표시
function string_shift_code($val){
	switch ($val) {
		case "0" :
			echo "요청";
			break;
		case "1" :
			echo "승인";
			break;
		case "2" :
			echo "대기";
			break;
		case "3" :
			echo "불가";
			break;
		case "4" :
			echo "취소";
			break;
		default :
			echo "요청";
	}
}
// 사용중 아이템(패키지)

function get_shop_item($it_name=null, $it_id=null){
	$array = array();
	$sql = "SELECT * FROM g5_shop_item";
	$sql .= " WHERE it_use = 1 ";

	if($it_name != null){
		$it_name = strtoupper($it_name);
		$sql .= "AND it_name='{$it_name}' ";
	}
	
	if($it_id != null){
		$sql .= "AND it_id='{$it_id}'";
	}

	$sql .= "ORDER BY it_order ASC";
	$result = sql_query($sql);

	while($row = sql_fetch_array($result)){
		array_push($array,$row);
	}

	return $array;
}

function get_shop_item_type($type="ESGC"){
	$array = array();
	$sql = "SELECT * FROM g5_shop_item WHERE it_brand = '{$type}' ORDER BY it_order ASC";
	$result = sql_query($sql);

	while($row = sql_fetch_array($result)){
		array_push($array,$row);
	}

	return $array;
}



// 아이템 그룹 내 구매패키지정보
function ordered_items($mb_id, $table=null){

	$item = get_shop_item($table);
	$upgrade_array = array();

	for($i = 1; $i < count($item); $i++){

		if($table != null){
			$name_lower = $table;
		}else{
			$name_lower = strtolower($item[$i]['it_maker']);
		}
	
		$sql = "SELECT * FROM package_".$name_lower." WHERE mb_id = '{$mb_id}' AND promote = 0";
		$result = sql_query($sql);

		$result_num = sql_num_rows($result);

		if($result_num > 0){
		for($j = 0; $j < $result_num; $j++){
			$row = sql_fetch_array($result);

			$order_sql = "SELECT * FROM g5_shop_order WHERE od_id = '{$row['od_id']}'";
			$order_row = sql_fetch($order_sql);

			array_push($upgrade_array, array(
				"it_id" => $item[$i]['it_id'],
				"it_name" => $item[$i]['it_name'],
				"it_price" => $item[$i]['it_price'],
                "it_cust_price" => $item[$i]['it_cust_price'],
                "it_point" => $item[$i]['it_point'],
				"it_maker" => $item[$i]['it_maker'],
				"it_brand" => $item[$i]['it_brand'],
				"od_name" => $item[$i]['it_maker'],
				"it_supply_point" => $item[$i]['it_supply_point'],
				"it_option_subject" => $item[$i]['it_option_subject'],
				"it_supply_subject" => $item[$i]['it_supply_subject'],
				"od_cart_price" => $order_row['od_cart_price'],
				"upstair" => $order_row['upstair'],
				"pv" => $order_row['pv'],
				"od_time" => $order_row['od_time'],
				"od_settle_case" => $order_row['od_settle_case'],
				"row" => $row
			));
		}
		}
		
	}
	return $upgrade_array;
}




// 보유 최상위 패키지
function max_item_level_array($mb_id,$func='name'){
    $oreder_result = array_column(ordered_items($mb_id),'it_name');

    if(count($oreder_result) > 0){
        $name = max($oreder_result);
		$key = substr($name,1,1);
    }else{
        $key = 0;
		$name = '-';
    }
	
	
	if($func =='name'){
		return $name;
	}else{
		return $key;
	}
}


// 특수설정
function week_jewel(){
	global $member;
	
	if($member['mb_week_dividend'] == 1){
		echo '보석수령';
	}else{
		echo '-';
	}
}

function kyc_cert($person_cert){
	if($person_cert == 1){
		$return = "<span class='cert_icon'><img src='".G5_THEME_URL."/_images/okay_icon.png'></span>";
	}else if($person_cert == 2){
		$return = "<span class='cert_icon'><img src='".G5_THEME_URL."/_images/x_icon.png'></span>";
	}else{
		$return ='';
	}
	return $return;
}

function rank_name($val){
	if($val < 4){
		$rank_name = '';
	}else if($val == 4){
		$rank_name = '메가';
	}else if($val == 5){
		$rank_name = '기가';
	}else if($val == 6){
		$rank_name = '테가';
	}else if($val == 7){
		$rank_name = '제타';
	}
}


function retrun_tx_func($tx,$coin){
	if(strpos($tx,'@')){
		return $tx;
	}else{
		if(strtolower($coin) == 'eth' ){
			return "<a href='https://etherscan.io/address/".$tx."' target='_blank' style='text-decoration:underline'>".short_code($tx)."</a>";
		}else if(strtolower($coin) =='esgc'){
			return "<a href='https://etherscan.io/address/".$tx."#tokentxns' target='_blank' style='text-decoration:underline'>".short_code($tx)."</a>";
		}else{
			return short_code($tx, 10);
		}
	}
}

/* function retrun_fil_addr_func($tx,$coin){
	if(strtolower($coin) == 'eth'){
		return "<a href='https://etherscan.io/address/".$tx."' target='_blank' style='text-decoration:underline'>".$tx."</a>";
	}else if(strtolower($coin) =='fil'){
		return "<a href ='https://filfox.info/ko/address/".$tx."' target='_blank' style='text-decoration:underline'>".$tx."</a>";
	}else if (strtolower($coin) == 'etc'){
		return "<a href ='https://blockscout.com/etc/mainnet/address/".$tx."' target='_blank' style='text-decoration:underline'>".$tx."</a>";
	}else{
		return $tx;
	}
} */

function short_code($string, $char = 10)
{
	if(mb_strlen($string) < 10) {
		return $string;
	} else {
		return substr($string, 0, $char) . " ... " . substr($string, -$char);	
	}
}

function get_my_staking($id,$_limit=""){
	global $g5;

	$limit = $_limit != "" ? "limit 0,{$_limit}" : "";

	$shop_order_sql = "select * from {$g5['g5_shop_order_table']} where mb_id = '{$id}' order by od_time desc {$limit}";
	return sql_query($shop_order_sql);
}

// array_column 5.4 대응
if( !function_exists( 'array_column' ) ):
    
    function array_column( array $input, $column_key, $index_key = null ) {
    
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
endif;
