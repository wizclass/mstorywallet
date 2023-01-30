<?php
$sub_menu = '650160';
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

auth_check($auth[$sub_menu], "r");

$fr_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $fr_date);
$to_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $to_date);

$g5['title'] = $fr_date.' ~ '.$to_date.' 일간 상품권 매출현황';
include_once (G5_ADMIN_PATH.'/admin.head.php');

if($_GET['ord']!=null && $_GET['ord_word']!=null){
	$sql_ord = "order by ".$_GET['ord_word']." ".$_GET['ord'];
}

$sql = "select * from {$g5['giftcard_history_table']}
      where SUBSTRING(insert_date,1,10) between '$fr_date' and '$to_date'
       ";

if($sql_ord){
    $sql .=$sql_ord;
}
else{
    $sql .= " order by insert_date desc";
}

$result = sql_query($sql);

?>

<?php
$ord_array = array('desc','asc'); // 정렬 방법 (내림차순, 오름차순)
$ord_arrow = array('▼','▲'); // 정렬 구분용
$ord = isset($_REQUEST['ord']) && in_array($_REQUEST['ord'],$ord_array) ? $_REQUEST['ord'] : $ord_array[0]; // 지정된 정렬이면 그 값, 아니면 기본 정렬(내림차순)
$ord_key = array_search($ord,$ord_array); // 해당 키 찾기 (0, 1)
$ord_rev = $ord_array[($ord_key+1)%2]; // 내림차순→오름차순, 오름차순→내림차순
?>

<!-- "excel download" -->

<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>


<form class="local_sch02 local_sch">
    <div class="sch_last">
        <input type="button" class="btn_submit excel" id="btnExport"  data-name='giftcard_saledate' value="엑셀 다운로드" />
    </div>
</form>

<div class="tbl_head01 tbl_wrap">
    <table id="table">
    <caption><?php echo $g5['title']; ?></caption>
    <thead>
    <tr>
        <th scope="col">아이디 / 이름</th>
        <th scope="col">상품명</th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=price_coin&fr_date=<?echo $fr_date;?>&to_date=<?echo $to_date;?>">합계 (<?=ASSETS_CURENCY?>) <?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=price_coin_fee&fr_date=<?echo $fr_date;?>&to_date=<?echo $to_date;?>">수수료 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=origin_price_coin&fr_date=<?echo $fr_date;?>&to_date=<?echo $to_date;?>">실 사용 수량 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>

		<th scope="col">쿠폰번호</th>
		<th scope="col">유효기간 (일)</th>
		<th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=expiry_date&fr_date=<?echo $fr_date;?>&to_date=<?echo $to_date;?>">만료일<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col">사용여부</th>
    </tr>
    </thead>
    <tbody>
    <?php
    unset($tot);
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
    ?>
        <tr>
            <td class="td_alignc"><?php echo $row['mb_id']; ?> ( <?=$row['mb_name']?> )</td>
            <td class="td_alignc"><?php echo $row['gt_name']; ?></td>
            <td class="td_numsum"><?php echo shift_auto($row['price_coin'],ASSETS_CURENCY); ?></td>
            <td class="td_numsum"><?php echo shift_auto($row['price_coin_fee'],ASSETS_CURENCY); ?></td>
            <td class="td_numcoupon"><?php echo shift_auto($row['origin_price_coin'],ASSETS_CURENCY); ?></td>
			<td class="td_alignc"><?php echo $row['origin_coupon']; ?></td>
			<td class="td_numincome"><?php echo $row['valid_day'] ?></td>
			<td class="td_numincome"><?php echo $row['expiry_date']?></td>
            <td class="td_numrdy"><?php echo $row['check_expiry_states'] <= 0 ? "미사용" : "사용" ?></td>
        </tr>
    <?php
        $tot['price_coin']    += $row['price_coin'];
        $tot['price_coin_fee'] += $row['price_coin_fee'];
        $tot['origin_price_coin']   += $row['origin_price_coin'];
    }

    if ($i == 0) {
        echo '<tr><td colspan="15" class="empty_table">자료가 없습니다</td></tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td>합 계</td>
        <td></td>
        <td><?php echo shift_auto($tot['price_coin'],ASSETS_CURENCY); ?></td>
        <td><?php echo shift_auto($tot['price_coin_fee'],ASSETS_CURENCY); ?></td>
        <td><?php echo shift_auto($tot['origin_price_coin'],ASSETS_CURENCY); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
