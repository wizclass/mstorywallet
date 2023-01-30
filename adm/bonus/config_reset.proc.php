<?php
include_once('./_common.php');
// include_once('./bonus_inc.php');
include_once('../../util/purchase_proc.php');

$today = date("Y-m-d H:i:s",time());
$todate = date("Y-m-d",time());
$datemd = date("mdis",time());

if($_GET['debug']) $debug = 1;

if($_POST['nw_member_reset'] == 'on'){

    $member_update_sql = " UPDATE g5_member set  mb_deposit_point = 100000, mb_deposit_calc=0, mb_balance = 0, mb_shift_amt=0, mb_balance_eth=0,mb_amt_eth = 0 WHERE mb_level < 9 ";
    sql_query($member_update_sql);

    if($member_update_sql){
        $result = 1;
    }
}

if($_POST['nw_asset_reset'] == 'on'){

    $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['withdrawal']}` ");
    $trunc3 = sql_query(" TRUNCATE TABLE `{$g5['deposit']}` ");

    if($trunc3){
        $result = 1;
    }
}

if($_POST['nw_mining_reset'] == 'on'){

    $trunc5 = sql_query(" TRUNCATE TABLE `g5_shop_order` ");
    $trunc6 = sql_query(" TRUNCATE TABLE `soodang_mining` ");

    $pack_cnt = sql_fetch("SELECT count(it_id) as cnt from g5_shop_item WHERE it_use > 0")['cnt'];
    $pack_name_sql = sql_fetch("SELECT it_maker from g5_shop_item WHERE it_use > 0 limit 0,1 ")['it_maker'];
    $pack_name = substr($pack_name_sql,0,1);
   
    for($i=0;$i<=$pack_cnt;$i++){
        $pack_where = "package_".$pack_name.$i;
        sql_query(" TRUNCATE TABLE {$pack_where}; ");
        
    }

    if($trunc6){
        $result = 1;
    }
}


if($_POST['nw_data_test'] == 'on'){
    
    $insert_order_sql = " INSERT INTO `g5_shop_order` (od_id,mb_id,mb_no,od_tax_flag,od_cart_price,od_cash,upstair,pv,pay_count,pay_acc,pay_acc_eth,pay_end,od_tax_mny,od_hope_date,od_name,od_tno,od_time,od_date,od_memo,od_app_no,od_settle_case,od_receipt_time,od_status,od_invoice_time) VALUE " ;
   
    for($i=0; $i <= 10 ; $i++){
        $od_id = date("YmdHis",time()).mt_rand(0000,9999);
        $member_id = 'test'.($i+20);
        $logic = purchase_package($member_id,2022111101,1);
        $od_invoce_time = date("Y-m-d",strtotime("+1 years"));
        $insert_order_sql_arry .= " ({$od_id}, '{$member_id}', {$i}+21 ,1000 ,9000.0, 514164, 900.0, 75.0, 0, 0, 0, 12, 10, curdate(),'10 % / 1년',2022111101,now(),curdate(),'{$member_id}',20,'ESGC',now(),'스테이킹 테스트',curdate()),";
    }

    $result_insert_sql = substr($insert_order_sql.$insert_order_sql_arry, 0, -1);

    if($debug){
        print_R($result_insert_sql);
        $result = 1;
    }else{
        $result = sql_query($result_insert_sql);
    }
   
}



if($_POST['nw_data_del'] == 'on'){
    
    $del_member = " DELETE from `g5_member` WHERE mb_no > 1; ";
    
    if($debug){
        print_R($del_member);
        $del_result = 1;
    }else{ 
        $del_result = sql_query($del_member);
    }


    if($del_result){
        $alter_table_query = " ALTER TABLE `g5_member` set AUTO_INCREMENT = 2; ";

        if($debug){
            print_R($alter_table_query);
        }else{ 
            sql_query($alter_table_query);
        }
        
    }
    
}

if($debug){}else{
    if($result){
        alert('정상 처리되었습니다.');
        goto_url('./config_reset.php');
    }
}
?>