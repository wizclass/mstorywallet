<?php
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

check_admin_token();

$od_id = isset($_POST['od_id']) ? $_POST['od_id'] : false;

$code = "300";
$msg = "잘못된 접근입니다.";

if($od_id){

    $sql = "select *, count(od_id) as cnt from {$g5['g5_shop_order_table']} where od_id = {$od_id}";
    $row = sql_fetch($sql);
    
    $msg = "해당 주문상품은 존재하지 않습니다.";
    
    if($row['cnt'] > 0){
        $item_sql = "select it_maker, count(it_maker) as cnt from {$g5['g5_shop_item_table']} where it_id = {$row['od_tno']}";
        $item_row = sql_fetch($item_sql);
    
        $msg = "해당 상품은 존재하지 않습니다.";
    
        if($item_row['cnt'] > 0){
            $package = strtolower($item_row['it_maker']);
            $refund_update = "update {$g5['g5_shop_order_table']} s join package_{$package} p on s.od_id = p.od_id set s.od_refund_price = 1, p.promote = 1 where p.od_id = '{$od_id}'";
            $refund_update_result = sql_query($refund_update);
           
            $code = "500";
            $msg = "스테이킹 예치금 반환중 문제가 발생하였습니다.";
            
            if($refund_update_result){

                $mb_id = $row['mb_id'];
                $refund_token = $row['od_cart_price'];
                $_rate = $row['od_tax_mny'];
                $rate = $_rate * 0.01;
                $ASSETS_CURENCY = ASSETS_CURENCY;
                $od_settle_case = $row['od_settle_case'];
                $acc = $od_settle_case == WITHDRAW_CURENCY ? "pay_acc_eth" : "pay_acc";
                $shift_acc = shift_auto($row[$acc],$od_settle_case);

                $shift_refund_token = shift_auto($refund_token, $ASSETS_CURENCY);
          
                $rec = "관리자 반환처리";
                $rec_adm = "{$rec} - {$shift_refund_token} {$ASSETS_CURENCY} :: Total Bonus {$shift_acc} {$od_settle_case} ( {$row['pay_count']} / {$row['pay_end']} )";

                $refund_log_sql = "insert into {$g5['mining']}(`day`,`allowance_name`,`mb_id`,`mb_name`,`mining`,`currency`,`rate`,`rec`,`rec_adm`,`datetime`,`shop_order_id`,`overcharge`)
                                    values(curdate(),'{$row['od_name']}','{$mb_id}','{$row['od_memo']}',0,'{$od_settle_case}',{$rate},'{$rec}','{$rec_adm}',now(), {$od_id}, 0)";
    
                $refund_log_result = sql_query($refund_log_sql);

                if($refund_log_result){
                
                    $refund_to_member_sql = "update {$g5['member_table']} set mb_deposit_calc = mb_deposit_calc + {$refund_token} where mb_id = '{$mb_id}'";
                    $refund_to_member_result = sql_query($refund_to_member_sql);

                    if($refund_to_member_result){
                        $code = "200";
                        $msg = "스테이킹 예치금 반환이 정상 처리되었습니다.";
                    }

                }
                                    
            }
        }
    }
}

echo json_encode(array("code"=>$code, "msg"=>$msg));
?>