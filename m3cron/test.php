<?php
// define('ASSETS_NUMBER_POINT',0); // 원 단위
// define('BONUS_NUMBER_POINT',0); // ESGC 단위
// define('COIN_NUMBER_POINT',8); // 이더 단위

// $absolute_path = "/var/www/html/esgcwallet";
// // include_once("{$absolute_path}/logcompany.php");
// include_once("{$absolute_path}/theme/mstory/_include/coin_price.php");

// $host_name = 'localhost';
// $user_name = 'root';
// $user_pwd = 'wizclass235689!@';
// $database = 'esgcwallet';
// $conn = mysqli_connect($host_name,$user_name,$user_pwd,$database);


// $debug = false;

$bonus_day = date('Y-m-d');
// $code = "staking";

// $pre_sql = "select m.mb_id, m.mb_level, s.* from g5_member m join g5_shop_order s on m.mb_no = s.mb_no where 
// s.od_hope_date = curdate() and 
// s.pay_count < s.pay_end and 
// s.od_refund_price <= 0";

// if($debug){
//     echo "<code>";
//     print_r($pre_sql);
//     echo "</code><br>";
// }

// $pre_result = mysqli_query($conn,$pre_sql);
// $result_cnt = mysqli_num_rows($pre_result);
echo $bonus_day;
?>