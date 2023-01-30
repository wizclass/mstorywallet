<?php
include_once('./_common.php');
include_once('../adm.wallet.php');

check_admin_token();

if ($_POST['act_button'] == "선택수정") {

    if (!count($_POST['chk'])) {
        alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
    }

    auth_check($auth[$sub_menu], 'w');

    
    
    for ($i=0; $i<count($_POST['chk']); $i++) {
        
        $k = $_POST['chk'][$i];
        
        $sql = "SELECT pay_end,od_time FROM {$g5['g5_shop_order_table']} WHERE od_id = '{$_POST['od_id'][$k]}'";
        $result = sql_fetch($sql);
        
        $timedata = explode(' ', $result['od_time']);
        $od_time = $_POST['od_time'][$k];
        $od_hope_date = date('d', strtotime($od_time)) < 29 ? date("y-m-d", strtotime("+1 month", strtotime($od_time))) :  date("y-m-01", strtotime("first day of +2 month", strtotime($od_time)));
        
        $expiry_date = $result['pay_end'] - 1;
        $od_invoice_time = date("y-m-d",strtotime("+{$expiry_date} month", strtotime($od_hope_date)));

        $sql = "UPDATE {$g5['g5_shop_order_table']} s
        JOIN package_{$_POST['od_index'][$k]} p ON s.od_id = p.od_id
        SET s.pay_count      = '{$_POST['pay_count'][$k]}',
            s.od_time        = '{$od_time} {$timedata[1]}',
            s.od_receipt_time   = '{$od_time} {$timedata[1]}',
            s.od_date        = '{$od_time}',
            s.od_hope_date   = '{$od_hope_date}',
            s.od_invoice_time = '{$od_invoice_time}',
            p.cdate          = '{$od_time}',
            p.cdatetime      = '{$od_time} {$timedata[1]}',
            p.pdate          = '{$od_invoice_time}'
            WHERE s.od_id    = '{$_POST['od_id'][$k]}'";
        sql_query($sql);

    }
} 

goto_url("./rank_table.php?rlevel={$rlevel}&states={$states}&type={$type}&fr_id={$fr_id}&page={$page}");
?>
