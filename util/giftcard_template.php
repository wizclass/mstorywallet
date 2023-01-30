<?php

function generate_coupon($length = 16) {
	$random_string = strtoupper(substr(md5(rand()),1,$length));
	return trim(substr(preg_replace('/(.{4})/',"-$1",$random_string),1));
}

$code = "300";
$msg = "잘못된 접근입니다.";

if($mb_id == "" || $mb_no == ""){
	echo json_encode(array("code"=>$code,"msg"=>$msg)); 
	exit;
}

$gt_id = isset($_POST['gt_id']) ? $_POST['gt_id'] : false;

if($gt_id){
    
    $msg = "해당 상품은 존재하지 않는 상품입니다.";

    $sql = "select *, count(*) as cnt from {$g5['g5_shop_giftcard_table']} where gt_id = {$gt_id}";
    $row = sql_fetch($sql);

    if($row['cnt'] > 0){
        $esgc_krw = isset($_POST['esgc_krw']) ? $_POST['esgc_krw'] : $coin['esgc_krw'];

        $origin_price_coin = shift_coin($row['gt_price']/$esgc_krw,BONUS_NUMBER_POINT);
        $price_coin_fee = shift_coin($origin_price_coin * ($row['gt_fee'] * 0.01),BONUS_NUMBER_POINT);
        $price_coin = shift_coin($origin_price_coin + $price_coin_fee,BONUS_NUMBER_POINT);

        $msg = "구매 가능 자산이 부족합니다.";

        if($total_token_balance >= $price_coin){

            $code = "500";
            $msg = "상품권 구매중에 문제가 발생하였습니다. 문제가 지속되면 관리자에 문의해주세요.";

            $ASSETS_CURENCY = ASSETS_CURENCY;
            $coupon = generate_coupon();
            $valid_day = $row['gt_valid'];
            $expiry_date = date("Y-m-d",strtotime("+{$valid_day} days"));
            $pg_id = date("YmdHis",time()) . sprintf('%02d', rand(0, 99));

            $insert_sql = "insert into {$g5['giftcard_history_table']}
            (`pg_id`,`mb_id`,`mb_name`,`buy_name`,`gt_id`,`gt_name`,`origin_coupon`,`coupon`,`fee`,`type`,`price_won`,`origin_price_coin`,`price_coin_fee`,`price_coin`,`valid_day`,`expiry_date`,`check_expiry_states`,`insert_date`)
            values
            ({$pg_id},'{$mb_id}','{$mb_name}','{$admin} 구매','{$row['gt_id']}','{$row['gt_name']}', '{$coupon}', '{$coupon}',{$row['gt_fee']},'{$ASSETS_CURENCY}',{$row['gt_price']},{$origin_price_coin},{$price_coin_fee}, {$price_coin}, {$valid_day}, '{$expiry_date}', 0, now())";
    
            $result = sql_query($insert_sql);

            if($result){

                $update_sql = "update {$g5['member_table']} set mb_deposit_calc = mb_deposit_calc - {$price_coin} where mb_id = '{$mb_id}' and mb_no = {$mb_no}";
                $update_result = sql_query($update_sql);

                if($update_result){
                    $code = "200";
                    if($admin == '관리자') {
                        $msg = "상품권 구매가 완료 되었습니다.";
                    } else {
                        $msg = "상품권 구매가 완료 되었습니다.<br>해당 상품권은 에코페이몰에서 등록 후<br>즉시 사용 가능하며, 상품권 유효기간은 {$valid_day}일 입니다.<br>등록된 상품권의 전환 포인트는 등록일로부터<br>1년간 사용 가능합니다.";
                    }
                }
            }
        }
    }
}
echo json_encode(array("code"=>$code, "msg"=>$msg));
?>