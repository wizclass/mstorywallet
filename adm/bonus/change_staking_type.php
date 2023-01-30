<?php
include_once('./_common.php');

check_admin_token();

$od_settle_case = isset($_POST['od_settle_case']) ? $_POST['od_settle_case'] : false;
$od_id = isset($_POST['od_id']) ? $_POST['od_id'] : false;

$code = "300";
$msg = "잘못된 접근입니다.";

if($od_settle_case == "" || $od_id == ""){
    echo "<script>history.back();</script>";
    exit;
}


if($od_settle_case && $od_id){
    $sql = "select pay_count, count(pay_count) as cnt, od_settle_case from {$g5['g5_shop_order_table']} where od_id = {$od_id}";
    $row = sql_fetch($sql);
    
    $msg = "존재하지 않는 스테이킹 입니다.";
    if($row['cnt'] > 0){

        $msg = "이미 {$row['od_settle_case']}로 지급된 내역이 있어 반영되지 않았습니다.";

        if($row['pay_count'] <= 0){
            $sql = "update {$g5['g5_shop_order_table']} set od_settle_case = '{$od_settle_case}' where od_id = {$od_id}";
            $result = sql_query($sql);

            $code = "500";
            $msg = "스테이킹 상품종류 변경중에 문제가 발생하였습니다. 나중에 다시 시도해주세요.";

            if($result){
                $code = "200";
                $msg = "스테이킹 상품종류가 변경되었습니다.";
            }
        }   
    }   
}

echo json_encode(array("code"=>$code,"msg"=>$msg));
?>