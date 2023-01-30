<?
include_once(G5_THEME_PATH.'/_include/wallet.php');

$shop_item = get_shop_item();
$item_default = substr($shop_item[0]['it_maker'],0,1);
$shop_item_cnt = count($shop_item);

    function package_have_return($mb_id,$have=0,$type="ESGC"){
        
        global $shop_item_cnt,$item_default,$shop_item;
        
        $my_package = [];

        if($have==1){
            $where  = "AND p.promote = 1 ";
        }else if($have==0){
            $where  = "AND p.promote != 1 ";
        }

        for($i=0;$i <= $shop_item_cnt; $i++ ){
            $target = "package_".strtolower($item_default).$i;
            $sql_r = "SELECT i.it_id, COUNT(*) AS cnt, SUM(s.od_cart_price) AS staking FROM {$target} p JOIN g5_shop_order s JOIN g5_shop_item i ON p.od_id=s.od_id AND i.it_id = p.it_id WHERE p.mb_id = '{$mb_id}' AND s.od_settle_case = '{$type}' {$where} GROUP BY i.it_id";
        //    echo $sql_r;
            $result = sql_query($sql_r);
            
            for($j = 0; $j = $row = sql_fetch_array($result); $j++){
                $my_package[$row['it_id']] = array($row['cnt'],$row['staking']); 
            }
        }
       
        return $my_package;
    }

    function my_total_package($mb_id){
        global $item_default,$shop_item_cnt;
        
        $package_head_sql = "SELECT sum(total) as total FROM (";
        $package_sql='';

        for($i=1;$i <= $shop_item_cnt; $i++ ){
            $target = "package_".$item_default.$i;
            $package_sql .= " SELECT COUNT(*) AS total FROM {$target} WHERE mb_id = '{$mb_id}' AND promote != 1 "; 
            if($i < $shop_item_cnt){
                $package_sql .="union all ";
            }
        }
        
        $total_package_sql = $package_head_sql.$package_sql.") tb";
        $total = sql_fetch($total_package_sql)['total'];

        if($total < 1){$total = '-';}
        return $total;
    }
