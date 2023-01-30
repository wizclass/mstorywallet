<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

// $debug = 1;

$mb_id = $member['mb_id'];
$mb_no = $member['mb_no'];
$mb_name = $member['mb_name'];
$admin = "";

if($mb_id == "" || $mb_no == ""){
	echo json_encode(array("code"=>"300","msg"=>"잘못된 접근입니다.")); 
	exit;
}


include_once(G5_PATH.'/util/upstairs_template.php');

?>