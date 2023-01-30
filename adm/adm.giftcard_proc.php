<?php
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

// $debug = 1;
$mb_id = isset($_POST['mb_id']) ? $_POST['mb_id'] : "";
$mb_no = isset($_POST['mb_no']) ? $_POST['mb_no'] : "";
$mb_name = $_POST['mb_name'];
$admin = "관리자";


$sql = "select mb_balance, mb_deposit_point, mb_deposit_calc, mb_shift_amt, mb_balance_eth, mb_calc_eth, mb_amt_eth from {$g5['member_table']} where mb_id = '{$mb_id}' and mb_no = {$mb_no}";
$row = sql_fetch($sql);

$total_token_balance = $row['mb_balance'] + $row['mb_deposit_point'] + $row['mb_deposit_calc'] - $row['mb_shift_amt'];

include_once(G5_PATH.'/util/giftcard_template.php');
?>

