<?php
include_once('./_common.php');

$mb_email = isset($_POST['mb_email']) ? sql_escape_string($_POST['mb_email']) : false;
$mb_name = isset($_POST['mb_name']) ? sql_escape_string($_POST['mb_name']) : false;
$auth_number = isset($_POST['auth_number']) ? sql_escape_string($_POST['auth_number']) : false;

$code = "300";
$msg = "잘못된 접근입니다.";
$wanted_id = "";

if($mb_email && $mb_name && $auth_number){

    $msg = "인증번호가 일치하지 않습니다.";

    $sql = "select mb_id, count(mb_id) as cnt from {$g5['member_table']} where mb_name = '{$mb_name}' and mb_email = '{$mb_email}' and mail_invalid = '{$auth_number}'";
    $row = sql_fetch($sql);

    if($row['cnt'] > 0){
        $code = "200";
        $msg = "인증이 완료되었습니다.";

        $mb_id = $row['mb_id'];
        $half = floor(strlen($mb_id)/2);
        $wanted_id_len = strlen($mb_id)- $half;
        $masking = str_repeat("*",$half);
        $wanted_id = substr_replace($mb_id,$masking,$wanted_id_len);
    }
}

echo json_encode(array("code" => $code, "msg" => $msg, "wanted_id"=>$wanted_id));


?>