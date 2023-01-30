<?php
$sub_menu = "600200";
include_once('./_common.php');

// $debug=1;
include_once('./bonus_inc.php');
auth_check($auth[$sub_menu], 'r');
$category = 'mining';

if(!$debug){
    $dupl_check_sql = "select mb_id from {$g5['mining']} where day='".$bonus_day."' and allowance_name = '{$code}' ";
    $get_today = sql_fetch( $dupl_check_sql);

    if($get_today['mb_id']){
        alert($bonus_day.' '.$code." 수당은 이미 지급되었습니다.");
        die;
    }
}

if( !function_exists( 'array_column' ) ):
    
    function array_column( array $input, $column_key, $index_key = null ) {
    
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
endif;


//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_member";
$sql_search=" WHERE rank > 0 AND mb_level < 7";
$sql_mgroup=" ORDER BY mb_no asc";

$pre_sql = "select * 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";

if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$pre_result = sql_query($pre_sql);
$result = $pre_result;
$result_cnt = sql_num_rows($pre_result);


// 마이닝지급량
$mining = bonus_pick('mining');
$mining_rate = $mining['rate'];

ob_start();


// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_row['name']." 수당 정산</span><br>";
echo "<span style='border:1px solid black;padding:10px 20px;display:block;font-weight:600;'> MINING 지급비율 : ". $mining_rate." mh/s  </span><br>";
echo "<strong>".strtoupper($code)." 수당 지급비율 : <span class='red'>". $bonus_row['rate']."% </span> </strong> |    지급조건 -".$pre_condition.' | '.$bonus_condition_tx." | ".$bonus_layer_tx." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자(매출발생자) : ".$result_cnt."</span><br><br>";
echo "<div class='btn' onclick=bonus_url('".$category."')>돌아가기</div>";
?>

<html><body>
<header>정산시작</header>    
<div>

<?
$mem_list = array();

excute();

// 추천트리 하부 

function return_down_manager($mb_id,$cnt=0){
	global $config,$g5,$mem_list;

	$mb_result = sql_fetch("SELECT mb_id,mb_rate from g5_member WHERE mb_id = '{$mb_id}' ");
    
    // 본인제외
	// $list = [];
	// $list['mb_id'] = $mb_result['mb_id'];
	// $list['mb_rate'] = $mb_result['mb_rate'];
	// $mem_list = [$list];

	$result = recommend_downtree($mb_result['mb_id'],0,$cnt);
	return $result;
}


function recommend_downtree($mb_id,$count=0,$cnt = 0){
	global $mem_list;

	if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
		
		$recommend_tree_result = sql_query("SELECT mb_id,mb_rate from g5_member WHERE mb_recommend = '{$mb_id}' ");
		$recommend_tree_cnt = sql_num_rows($recommend_tree_result);

		if($recommend_tree_cnt > 0 ){
			++$count;
			while($row = sql_fetch_array($recommend_tree_result)){
				$list['mb_id'] = $row['mb_id'];
                $list['mb_rate'] = $row['mb_rate'];
				$list['depth'] = $count;
                
				array_push($mem_list,$list);
				recommend_downtree($row['mb_id'],$count,$cnt);
			}
		}
	}
	return $mem_list;
}

function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition, $code, $bonus_rates, $bonus_rate,$pre_condition_in,$bonus_limit,$bonus_layer;
    global $minings,$mining_target,$mining_amt_target,$mem_list,$mining_rate,$mining,$now_mining_coin ;
    global $debug;
    
    for ($i=0; $row=sql_fetch_array($result); $i++) {   

        $mb_id=$row['mb_id'];
        $mb_rate = $row['mb_rate'];
        $mb_balance = $row['mb_balance'];
        $item_rank = $row['rank'];
        $bonus_rates = $bonus_rate;

        $matching_lvl = $bonus_layer[$item_rank-1];

        echo "<br><br><span class='title block gold' style='font-size:30px;'>".$mb_id."</span><br>";
        
        $mining_down_tree_result = return_down_manager($row['mb_id'],$matching_lvl);
        $mining_matching_sum = array_sum(array_column($mining_down_tree_result, 'mb_rate'));
        $mining_matching_hash = $mining_matching_sum;
        

        echo "<br>";
        echo "▶ 보유상품등급: <strong>".$item_rank."</strong> | 매칭레벨 : <span class='blue'>".$matching_lvl."</span><br> ";
        echo "▶▶추천라인 하부 <span class='blue'>".$matching_lvl."대</span> 해쉬파워/채굴량 :: ";
        echo "<span class='blue'>".$mining_matching_hash." MH/s</span>";
        echo "<br>";

        // $hash_power = shift_auto(($mb_rate/$tp[0]),2);
        
        // 계산식
        echo "▶▶▶데일리 마이닝지급량  : mh :: <span class='blue'>".$mining_rate."</span><br>";
        echo "▶▶▶▶매칭수당지급량 : ".$bonus_rates.' '.$minings[$now_mining_coin];
        echo "<br><br>";

        $benefit = ($mining_matching_hash*$mining_rate)*$bonus_rates;
        
        echo "<code>";
        echo "수당계산 : ".$mining_matching_hash.' * '.$mining_rate.' * '.$bonus_rates.' = '.$benefit;
        echo "</code>";

        list($mb_balance,$balance_limit,$benefit_limit,$admin_cash) = mining_limit_check($mb_id,$benefit,$bonus_limit,$code);

        $benefit_limit_point = shift_auto($benefit_limit,COIN_NUMBER_POINT);
        $benefit_point = shift_auto($benefit,COIN_NUMBER_POINT);

        echo "<code>";
        echo "현재수당 : ".shift_auto($mb_balance,COIN_NUMBER_POINT)."  | 수당한계 :". shift_auto($balance_limit,COIN_NUMBER_POINT).' | ';
        echo "발생할수당: ".$benefit_point." | 지급할수당 :".$benefit_limit_point;
        echo "</code><br>";
        if($admin_cash == 1){
            $rec_adm= 'fall check';
        }

                
        if($benefit > $benefit_limit && $balance_limit != 0 ){

            $rec_adm .= "<span class=red> |  Bonus overflow :: ".shift_auto($benefit_limit - $benefit)."</span>";
            echo "<span class=blue> ▶▶ 수당 지급 : ".$benefit_point."</span>";
            echo "<span class=red> ▶▶▶ 수당 초과 (한계까지만 지급) : ".$benefit_limit_point." </span><br>";
        }else if($benefit != 0 && $balance_limit == 0 && $benefit_limit == 0){

            $rec_adm .= "<span class=red> | Sales zero :: ".shift_auto(($benefit_limit - $benefit),COIN_NUMBER_POINT)."</span>";
            echo "<span class=blue> ▶▶ 수당 지급 : ".shift_auto($benefit)."</span>";
            echo "<span class=red> ▶▶▶ 수당 초과 (기준매출없음) : ".$benefit_limit_point." </span><br>";
        }else if($benefit == 0){

            echo "<span class=blue> ▶▶ 수당 미발생 </span>";
        }else{
            echo "<span class=blue> ▶▶ 수당 지급 : ".$benefit_point."</span><br>";
        }


        if($benefit > 0 ){
            $rec=$code.' Bonus By '.$matching_lvl.' step | '.$mining_matching_hash.' MH :: '.$benefit_limit_point.' '.$minings[$now_mining_coin];
            

            if($benefit_limit < $benefit){
                $rec_adm =  $mining_matching_hash.' * '.$mining_rate.' * '.$bonus_rates.' = '.$benefit_limit_point." (".$benefit_point.")";
            }else{
                $rec_adm =  $mining_matching_hash.' * '.$mining_rate.' * '.$bonus_rates.' = '.$benefit_limit_point;
            }

            $record_result = mining_record($mb_id, $code, $benefit_limit_point,$bonus_rates,$minings[$now_mining_coin], $rec, $rec_adm, $bonus_day,$mining_matching_hash,$benefit_point);

            
            if($record_result){
                $balance_up = "update g5_member set {$mining_target} = {$mining_target} + {$benefit_limit}, recom_mining = {$mining_matching_hash}  where mb_id = '{$mb_id}' ";

                // 디버그 로그
                if($debug){
                    echo "<code>";
                    print_R($balance_up);
                    echo "</code>";
                }else{
                    sql_query($balance_up);
                }
            }
        }else{
            echo "<span class=red> ▶▶ 수당 미발생 </span>";
        }

        $mem_list = array();

    } // for
}
?>

<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>