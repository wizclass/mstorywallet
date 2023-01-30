<?php
include_once('./_common.php');

$mb_id = $member['mb_id'];
$mb_no = $member['mb_no'];

$code = "300";
$msg = "잘못된 접근입니다.";
$data = [];

if($mb_id == "" || $mb_no == ""){
	echo json_encode(array("code"=>$code,"msg"=>$msg)); 
	exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : false;

if($type){
    
    $msg = "문제가 발생하였습니다.";
    
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 15;

    if($type == "all"){
        $sql = "(select txhash as credit, coin,amt,create_dt,'deposit' as states, status from {$g5['deposit']} where mb_id = '{$mb_id}') 
            union all 
            (select addr as credit, coin, amt_total AS amt ,create_dt, 'withdraw' as states, status from {$g5['withdrawal']} where mb_id = '{$mb_id}')";
    }

    if($type == "deposit"){

        $fr_date = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
        $to_date = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

        $sql = "select * FROM {$g5['deposit']} WHERE mb_id = '{$mb_id}'";
    }

    if($type == "withdraw"){
        $sql = "select * FROM {$g5['withdrawal']} WHERE mb_id = '{$mb_id}'";
    }

    
    $paging_sql = "{$sql} order by create_dt desc limit {$limit} offset {$offset}";
    
    $result = sql_query($paging_sql);

    if($result){
        $code = "200";
        $msg = "정상처리되었습니다.";

        
        for($i = 0; $i < $row = sql_fetch_array($result); $i++){
            array_push($data,$row);    
        }
    }
    
}

echo json_encode(array("code"=>$code,"msg"=>$msg,"data"=>$data));
?>