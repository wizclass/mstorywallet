<?php
$sub_menu = '600410';
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

auth_check($auth[$sub_menu], "r");

$g5['title'] = $fr_year.' ~ '.$to_year.' 연간 스테이킹 매출현황';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql = "select *,
        SUBSTRING(od_time,1,10) as od_date,
        (od_cart_price + od_tax_flag ) as orderprice
        from {$g5['g5_shop_order_table']}
        where SUBSTRING(od_time,1,4) between '$fr_year' and '$to_year'";

if($_GET['ord']!=null && $_GET['ord_word']!=null){
    $sql_ord = "order by ".$_GET['ord_word']." ".$_GET['ord'];
}

if($sql_ord){
    $sql .=$sql_ord;
}
else{
    $sql .= " order by od_time desc ";
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

<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>


<form class="local_sch02 local_sch">
    <div class="sch_last">
        <input type="button" class="btn_submit excel" id="btnExport"  data-name='staking_saleyear' value="엑셀 다운로드" />
    </div>
</form>

<div class="tbl_head01 tbl_wrap">
    <table id="table">
    <caption><?php echo $g5['title']; ?></caption>
    <thead>
    <tr>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=od_id&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">주문번호No<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col">아이디 / 이름</th>
        <th scope="col">상품명</th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=orderprice&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">합계 (<?=ASSETS_CURENCY?>) <?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=od_tax_flag&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">수수료 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=od_cart_price&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">실 스테이킹 수량 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <!-- <th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=od_cash&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">현금가 (<?=BALANCE_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th> -->

		<th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=upstair&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">총지급예정 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>
		<th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=pv&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">1회지급량 (<?=ASSETS_CURENCY?>)<?php echo $ord_arrow[$ord_key]; ?></a></th>
        <th scope="col">지급상태</th>
		<th scope="col"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=od_invoice_time&fr_year=<?echo $fr_year;?>&to_year=<?=$to_year?>">만료일<?php echo $ord_arrow[$ord_key]; ?></a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    unset($tot);
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        if ($row['mb_id'] == '') { // 비회원일 경우는 주문자로 링크
            $href = '<a href="./orderlist.php?sel_field=od_name&amp;search='.$row['od_name'].'">';
        } else { // 회원일 경우는 회원아이디로 링크
            $href = '<a href="./orderlist.php?sel_field=mb_id&amp;search='.$row['mb_id'].'">';
        }


    ?>
        <tr>
            <td class="td_alignc"><?php echo $row['od_id']; ?></td>
            <td class="td_alignc"><?php echo $row['mb_id']; ?> ( <?=$row['od_memo']?> )</td>
            <td class="td_name"><?php echo $row['od_name']; ?>(<?=$row['od_settle_case']?>)</td>
            <td class="td_numsum"><?php echo shift_auto($row['orderprice'],ASSETS_CURENCY); ?></td>
            <td class="td_numsum"><?php echo shift_auto($row['od_tax_flag'],ASSETS_CURENCY); ?></td>

            <td class="td_numcoupon"><?php echo shift_auto($row['od_cart_price'],ASSETS_CURENCY); ?></td>

            <!-- <td class="td_numincome"><?php echo shift_auto($row['od_cash'],BALANCE_CURENCY); ?></td> -->
		
			<td class="td_numincome"><?php echo shift_auto($row['upstair'],ASSETS_CURENCY); ?></td>
			<td class="td_numincome"><?php echo shift_auto($row['pv'],ASSETS_CURENCY); ?></td>
			<td class="td_numincome"><?php echo $row['pay_count']?> / <?=$row['pay_end']?> 회</td>
            <td class="td_numrdy"><?php echo $row['od_invoice_time'] ?></td>
        </tr>
    <?php
        $tot['orderprice']    += $row['orderprice'];
        $tot['od_tax_flag'] += $row['od_tax_flag'];

        $tot['od_cart_price']   += $row['od_cart_price'];
        $tot['od_cash']        += $row['od_cash'] ;

        $tot['pv'] += $row['pv'];
        $tot['upstair'] += $row['upstair'];
    }

    if ($i == 0) {
        echo '<tr><td colspan="10" class="empty_table">자료가 없습니다</td></tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td>합 계</td>
        <td></td>
        <td></td>
        <td><?php echo shift_auto($tot['orderprice'],ASSETS_CURENCY); ?></td>
        <td><?php echo shift_auto($tot['od_tax_flag'],ASSETS_CURENCY); ?></td>
        <td><?php echo shift_auto($tot['od_cart_price'],ASSETS_CURENCY); ?></td>
        <!-- <td><?php echo shift_auto($tot['od_cash'],BALANCE_CURENCY); ?></td> -->
        <td><?php echo shift_auto($tot['upstair'],ASSETS_CURENCY); ?></td>
        <td><?php echo shift_auto($tot['pv'],ASSETS_CURENCY); ?></td>
        <td></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
