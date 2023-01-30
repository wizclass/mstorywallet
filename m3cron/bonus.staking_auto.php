<?php
/*
웹크론이 아닌 리눅스 크론으로 돌고있습니다.
*/
define('ASSETS_NUMBER_POINT',0); // 원 단위
define('BONUS_NUMBER_POINT',0); // ESGC 단위
define('COIN_NUMBER_POINT',8); // 이더 단위

$absolute_path = "/var/www/html/esgcwallet";
// include_once("{$absolute_path}/logcompany.php");
include_once("{$absolute_path}/theme/esgc/_include/coin_price.php");

$host_name = 'localhost';
$user_name = 'root';
$user_pwd = 'wizclass235689!@';
$database = 'esgcwallet';
$conn = mysqli_connect($host_name,$user_name,$user_pwd,$database);


$debug = false;

$bonus_day = date('Y-m-d');
$code = "staking";

$pre_sql = "select m.mb_id, m.mb_level, s.* from g5_member m join g5_shop_order s on m.mb_no = s.mb_no where 
s.od_hope_date = curdate() and 
s.pay_count < s.pay_end and 
s.od_refund_price <= 0";

if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$pre_result = mysqli_query($conn,$pre_sql);
$result_cnt = mysqli_num_rows($pre_result);

ob_start();

echo "<span class ='title' style='font-size:20px;'>스테이킹 정산</span><br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 스테이킹 발생수 : ".$result_cnt." </span><br><br>";
echo "<div class='btn' onclick=bonus_url('".$code."')>돌아가기</div>";

?>



<html><body>
<header>정산시작</header>    
<div>
<?

excute();

function excute(){
    global $pre_result,$result_cnt,$debug,$coin,$bonus_day,$conn;

    if($result_cnt <= 0){
        echo "<div style='display: flex;justify-content: center;color: firebrick;'>당일 정산 하실 스테이킹 명단이 존재 하지 않습니다.</div>";    
        return false;
    }

    $token_list = array(); // 아이디 별로 토큰 스테이킹 저장
    $eth_list = array();// 아이디 별로 이더 스테이킹 저장
    
    $pay_token_count = array(); // 아이디 별로 토큰 스테이킹 몇번 지급받았는지 저장 
    $pay_eth_count = array(); // 아이디 별로 이더 스테이킹 몇번 지급 받았는지 저장

    $plus_token_paid_list = array(); // 토큰이 얼마큼 지불되었는지 텍스트로 보여주기 위함
    $plus_eth_paid_list = array(); // 이더가 얼마큼 지불되었는지 텍스트로 보여주기 위함
    $refund_token_list = array(); // 스테이킹 만기가되어 정산된 esgc 와 명단
    $refund_eth_list = array(); // 스테이킹 만기가되어 정산된  eth 와 명단
    $pay_eth_list = ""; // 이더 지급받은 회원 화면에 출력
    $pay_token_list = ""; // 토큰 지급받은 회원 화면에 출력

    $history_value_sql = ""; // soodang_minig 테이블에 적재할 sql value 부분

    $update_shop_sql = "update g5_shop_order set ";
    $token_shop_column_sql = ""; // 토큰 지급받은 회원들 업데이트 (g5_shop_order 으로 들어갈 sql) 
    $eth_shop_column_sql = ""; // 이더 지급받은 회원들 업데이트 (g5_shop_order 으로 들어갈 sql) 
    $shop_pay_count_column_sql = ""; // 지급 받은 스테이킹 +1 해서 재저장
    $shop_next_pay_date_column_sql = ""; // 다음 스테이킹 받는 날
    $shop_where_sql = "where no in(";

    $update_sql = "update g5_member set ";
    $token_column_sql = ""; // 토큰 지급받은 회원들 업데이트 (g5_member 로 들어갈 sql)
    $eth_column_sql = ""; // 이더 지급받은 회원들 업데이트 (g5_member 로 들어갈 sql)
    $vip_column_sql = ""; // 이더로 스테이킹을 받는사람은 강제로 특별회원으로 등업할 sql
    $where_sql = "where mb_id in(";

    //구매 내역 돌면서 이더,토큰으로 나누어서 정산 시작
    //g5_shop_order, soodang_mining 테이블에 들어갈 쿼리문 작성
    for($i=0; $i < $row = mysqli_fetch_array($pre_result); $i++){

        $pay_calc = $row['pv'];
        $_rate = $row['od_tax_mny'];
        $rate = $_rate * 0.01;
        $refund_member = "";

        $shop_where_sql .= "{$row['no']},";

        if($shop_pay_count_column_sql == ""){$shop_pay_count_column_sql .= ", pay_count = case no ";} 
        $shop_pay_count_column_sql .= "when {$row['no']} then pay_count + 1 ";

        if($shop_next_pay_date_column_sql == ""){$shop_next_pay_date_column_sql .= ", od_hope_date = case no ";} 
        $shop_next_pay_date_column_sql .= "when {$row['no']} then date_add( od_hope_date , interval 1 month) ";

        $pay_count = $row['pay_count'] + 1;

        // if($pay_count == $row['pay_end']){

        //     $refund_member  = "[ 스테이킹 만기 : {$row['od_cart_price']} {$row['od_settle_case']} 반환 ]";

        //     if($row['od_settle_case'] == "ETH"){
        //         $eth_list[$row['mb_id']] += $row['od_cart_price'];
        //         $refund_eth_list[$row['mb_id']] .= $refund_member;

        //     }else{
        //         $token_list[$row['mb_id']] += $row['od_cart_price'];
        //         $refund_token_list[$row['mb_id']] .= $refund_member;
        //     }
            
        // }

        if($row['od_settle_case'] == "ETH"){
            // $pay_calc = $row['od_settle_case'] == "ETH" ? $pay_calc : $pay_calc * $coin['esgc_eth'];
            $pay_calc *= $coin['esgc_eth'];
            $pay_calc = shift_coin($pay_calc);
            $eth_list[$row['mb_id']] += $pay_calc;
            $plus_eth_paid_list[$row['mb_id']] .= " + {$pay_calc} ETH";  
            $total_paid = $row['pay_acc_eth'] + $pay_calc;
            $pay_eth_count[$row['mb_id']] += 1;
            $rec = "Bonus by {$_rate}%({$row['od_cart_price']} {$row['od_settle_case']}) - ETH :: {$pay_calc}";
            $rec_adm = "{$rec} / {$total_paid}( {$pay_count} / {$row['pay_end']} )";  
            $type = "ETH";

            if($vip_column_sql == ""){$vip_column_sql .= ", mb_level = case mb_id ";} 
            $vip_column_sql .= "when '{$row['mb_id']}' then 2 ";

            if($eth_shop_column_sql == ""){$eth_shop_column_sql .= ", pay_acc_eth = case no ";}
            $eth_shop_column_sql .= "when {$row['no']} then pay_acc_eth + {$pay_calc} ";

        }else{
            $token_list[$row['mb_id']] += $pay_calc;
            $pay_token_count[$row['mb_id']] += 1;
            $total_paid = $row['pay_acc'] + $pay_calc;
            $plus_token_paid_list[$row['mb_id']] .= " + {$pay_calc} ESGC";  
            $rec = "Bonus by {$_rate}%({$row['od_cart_price']} ESGC) - ESGC :: {$pay_calc}";
            $rec_adm = "{$rec} / {$total_paid} ( {$pay_count} / {$row['pay_end']} )";  
            $type = "ESGC";
            if($token_shop_column_sql == ""){$token_shop_column_sql .= ", pay_acc = case no ";}
            $token_shop_column_sql .= "when {$row['no']} then pay_acc + {$pay_calc} ";
        }

        $history_value_sql .= "(curdate(), '{$row['od_name']}', '{$row['mb_id']}', '{$row['od_memo']}', {$pay_calc}, '$type', {$rate}, '{$rec} {$refund_member}', '{$rec_adm} {$refund_member}', now(), {$row['od_id']}, 0),";
    }

    // 정산된 이더를 g5_member 테이블에 +해줄 쿼리문 작성 및 스테이킹 지급내역 화면출력
    $_eth_times = count($pay_eth_count) > 0;
    foreach($eth_list as $key => $value){
        if($eth_column_sql == ""){$eth_column_sql .= ", mb_balance_eth = case mb_id ";}
        $eth_column_sql .= "when '{$key}' then mb_balance_eth + {$value} ";
        $where_sql .= "'{$key}',";
        $eth_times = $_eth_times ? (array_key_exists($key, $pay_eth_count) ? "( {$pay_eth_count[$key]} Times )" : "") : "";
        $pay_eth_list .= "<br><br><span class='title block' style='font-size:30px;background:wheat;'>{$key} {$eth_times}</span><br><span class=blue> ▶▶ 스테이킹 지급 : {$value} ETH</span><br><span>{$plus_eth_paid_list[$key]}<br>{$refund_eth_list[$key]}</span>";
    }

    // 정산된 토큰을 g5_member 테이블에 +해줄 쿼리문 작성 및 스테이킹 지급내역 화면출력
    $_token_times = count($pay_token_count) > 0;
    foreach($token_list as $key => $value){
        if($token_column_sql == ""){$token_column_sql .= ", mb_balance = case mb_id ";}
        $token_column_sql .= "when '{$key}' then mb_balance + {$value} ";
        $where_sql .= "'{$key}',";
        $token_times = $_token_times ? (array_key_exists($key, $pay_token_count) ? "( {$pay_token_count[$key]} Times )" : "") : "";
        $pay_token_list .= "<br><br><span class='title block' style='font-size:30px;'>{$key} {$token_times}</span><br><span class=blue> ▶▶ 스테이킹 지급 : {$value} ESGC</span><br><span>{$plus_token_paid_list[$key]}<br>{$refund_token_list[$key]}</span>";
    }

    $vip_column_sql .= $vip_column_sql != "" ? "else mb_level end " : "";
    $eth_column_sql .= $eth_column_sql != "" ? "else mb_balance_eth end " : "";
    $token_column_sql .= $token_column_sql != "" ? "else mb_balance end " : "";
    $column_sql = ltrim($eth_column_sql.$token_column_sql.$vip_column_sql,",");
    $where_sql = substr($where_sql,0,-1).")";
    $update_sql .= $column_sql.$where_sql;


    $shop_pay_count_column_sql .= $shop_pay_count_column_sql != "" ? "else pay_count end " : "";
    $shop_next_pay_date_column_sql .= $shop_next_pay_date_column_sql != "" ? "else od_hope_date end " : "";
    $eth_shop_column_sql .= $eth_shop_column_sql != "" ? "else pay_acc_eth end " : "";
    $token_shop_column_sql .= $token_shop_column_sql != "" ? "else pay_acc end " : "";

    $shop_where_sql = substr($shop_where_sql,0,-1).")";
    $shop_column_sql = ltrim($shop_pay_count_column_sql.$shop_next_pay_date_column_sql.$eth_shop_column_sql.$token_shop_column_sql,",");

    $update_shop_sql .= $shop_column_sql.$shop_where_sql;
    
    if($column_sql != ""){
        $pay_token_member_count = count($token_list);
        $pay_eth_member_count = count($eth_list);
        echo "<span class='title block' style='font-size:30px;'>ESGC 스테이킹 발생자 : {$pay_token_member_count}</span>{$pay_token_list}<br><br>";
        echo "<span class='title block' style='font-size:30px;background:wheat;'>ETH 스테이킹 발생자 : {$pay_eth_member_count}</span>{$pay_eth_list}";

        $history_value_sql = rtrim($history_value_sql,",");
        $history_sql = "insert into soodang_mining(`day`,`allowance_name`,`mb_id`,`mb_name`,`mining`,`currency`,`rate`,`rec`,`rec_adm`,`datetime`,`shop_order_id`,`overcharge`)
                        values {$history_value_sql}";

        if($debug){
            echo "<br><br><code>";
            print_R($update_sql);
            echo "</code>";
            echo "<br><br><code>";
            print_R($update_shop_sql);
            echo "</code>";
            echo "<br><br><code>";
            print_R($history_sql);
            echo "</code>";
        }else{

            // 로그히스토리 테이블->오더히스토리 테이블->멤버테이블 
            // mysql 에러 발생시 위험도가 낮은 순서대로 디비저장
            $result = mysqli_query($conn,$history_sql) ;
            if($result){
                $result = mysqli_query($conn,$update_shop_sql); 
                if($result){
                    mysqli_query($conn,$update_sql); 
                    if(!$result){echo "<span>스테이킹 정산에 문제가 발생하였습니다. :: g5_member</span>";}
                }else{echo "<span>스테이킹 정산에 문제가 발생하였습니다. :: g5_shop_order</span>"; }
            }else{echo "<span>스테이킹 정산에 문제가 발생하였습니다. :: soodang_mining</span>"; }
        }
    }
}

include_once("{$absolute_path}/adm/bonus/bonus_footer.php");

if($result_cnt > 0){
    if($debug){}else{
        $html = ob_get_contents();
        //ob_end_flush();
        $dir = "{$absolute_path}/data/log/{$code}/";

        if(!is_dir($dir)){
            mkdir($dir, '777');
        }
        $logfile = "{$absolute_path}/data/log/{$code}/{$code}_{$bonus_day}.html";
        fopen($logfile, "w");
        file_put_contents($logfile, ob_get_contents());
    }
}
?>