<?php
include_once('./_common.php');

$sql = "insert into g5_shop_order(`od_id`,`mb_id`,`mb_no`,`od_cart_price`,`od_cash`,`upstair`,`pv`,`pay_count`,`pay_acc`,`pay_acc_eth`,`pay_end`,`od_tax_mny`,`od_hope_date`) values";
// 2~997
$values = "";
for($i = 112; $i <= 116; $i++){
    $num = $i-4;
    $values .= "({$i},'test{$num}',{$i},5000,30000,1500,41.6666,0,0,0,36,30,curdate()),";

}

$sql .= rtrim($values,",");

echo $sql;
?>
