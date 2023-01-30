<?php
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

$g5['title'] = "회원 상세 내역 현황";
 
include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

// 기간설정
if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-365 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

if($_GET['fr_date']){$fr_date = $_GET['fr_date'];}
if($_GET['to_date']){$to_date = $_GET['to_date'];}

if($_GET['states'] == ""){$states = "all";}

$mb_id = isset($_GET['mb_id']) && $_GET['mb_id'] != "" ? $_GET['mb_id'] : false;

$sql = "select mb_id, mb_name, mb_no, count(mb_id) as cnt from {$g5['member_table']} where mb_id = '{$mb_id}'";
$row = sql_fetch($sql);

if($row['cnt'] <= 0){
    echo "<script>alert('존재하지 않는 회원입니다.');
        history.back();
    </script>";
    exit;
}

$mb_id = $row['mb_id'];
$mb_name = $row['mb_name'];
$mb_no = $row['mb_no'];

?>

<?
    $colspan = 7;

    $deposit_sql = "select txhash as credit, coin, amt, create_dt, '입금' as states, admin_states from {$g5['deposit']} where mb_id = '{$mb_id}' and date_format(create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}' AND status = 1 ";
    $withdraw_sql = "select addr as credit, coin, amt_total as amt ,create_dt, '출금' as states, '' as admin_states from {$g5['withdrawal']} where mb_id = '{$mb_id}' and date_format(create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}' AND status = 1";
    $staking_sql = "select od_name as credit, od_settle_case as coin ,od_cart_price as amt,  od_time as create_dt, '스테이킹' as states, '' as admin_states  from {$g5['g5_shop_order_table']} where mb_id = '{$mb_id}' and date_format(od_date, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}'";
    $buying_giftcard_sql = "select concat(price_won,'원 상품권',if(check_expiry_states > 0,' (사용',' (미사용'),if(expiry_date <= date_add(CURDATE(),interval -1 day),concat(' / 기간만료) - ',buy_name),concat(') - ',buy_name))) as credit, type as coin, price_coin as amt, insert_date as create_dt, '상품권구매' as states, '' as admin_states from {$g5['giftcard_history_table']} where mb_id = '{$mb_id}' and date_format(insert_date, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}'";

    if($states == "deposit"){ $sql = $deposit_sql; }

    if($states == "withdrawal"){ $sql =  $withdraw_sql;}

    if($states == "staking"){ $sql =  $staking_sql;} 

    if($states == "giftcard") {$sql =  $buying_giftcard_sql;}

    if($states == "all"){$sql =  "({$deposit_sql}) union all ({$withdraw_sql}) union all ({$staking_sql}) union all ({$buying_giftcard_sql})";} 
// print_R($sql);
    $result = sql_query($sql);

    $total_count = sql_num_rows($result);

    $rows = 10;
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = "{$sql} order by create_dt desc limit {$from_record}, {$rows} ";

    $result = sql_query($sql);

    $qstr = "fr_date={$fr_date}&to_date={$to_date}&states={$states}&mb_id={$mb_id}";

    $query_string = $qstr ? '?'.$qstr : '';

    function  onselect($val){
        global $states;
        if($states == $val){echo ' selected';}else{ echo '';}
    }
?>

<style>
    .red{color:red}
    .text-center{text-align:center}
    .sch_last{display:inline-block;}
    .rank_img{width:20px;height:20px;margin-right:10px;}
    .btn_submit{width:100px;height:30px; margin-left:20px;}
    .btn.reset_btn{width:100px;height:30px; margin-left:20px;background:black;border-radius:0;color:white}
    .black_btn{background:#333 !important; border:1px solid black !important; color:white;}

    .local_sch .btn_submit{height:30px;}
    .selectbox select{width:150px;height:30px;}
    .inline{display:inline-block;}
    .inline label {font-weight: 600;margin-left:10px;}
    .pro{color:red}

    .local_ov strong{color:red}
</style>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function select_change(f){
    $('#input_states').val($('#select_states').val());
}

</script>

<script src="../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../excel/tabletoexcel/tableExport.js"></script>

<div class="local_ov01 local_ov">
	<a href="./rank_table.php" class="ov_listall">전체상품목록</a><a href="<?=$query_string?>">	총 <strong><?=$total_count?></strong>개</a>
</div>

<section class='rank_table'>

    <form name="frank" id="frank" class="local_sch02 local_sch" method="get">

    <input type='hidden' name='states' id='input_states' value='<?=$states?>'>
    <input type='hidden' name='mb_id' id='mb_id' value='<?=$mb_id?>'>

        <div class="selectbox inline">
            <label for='select_states'>종류 : </label>
            <select id='select_states' onchange="select_change(this);">
                <option value='all' <?=onselect('all')?> >전체</option>
                <option value='deposit' <?=onselect('deposit')?> >입금</option>
                <option value='withdrawal' <?=onselect('withdrawal')?> >출금</option>
                <option value='staking' <?=onselect('staking')?> >스테이킹</option>
                <option value='giftcard' <?=onselect('giftcard')?> >상품권구매</option>
            </select>
        </div>

        | 검색 기간 : <input type="date" name="fr_date" id="fr_date" placeholder="시작일" class="frm_input" style='padding-left:5px;' value="<?=$fr_date?>" /> 
        ~ <input type="date" name="to_date" id="to_date" placeholder="종료일" class="frm_input" style='padding-left:5px;' value="<?=$to_date?>"/>

        <div class='inline'> 
            <input type="submit" value="검색" class="btn_submit">
            <input type="button" class="btn_submit excel" id="btnExport" data-name='member_history' value="엑셀 다운로드" />
        </div>

    </form>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
    <div class="tbl_head01 tbl_wrap">
        <table id="table">
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th>no</th>
            <th>아이디</th>
            <th>이름</th>
            <th>종류</th>
            <th>금액</th>
            <th>생성일자</th>
            <th>비고</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = (($page-1)*$rows)+1;
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            $bg = 'bg'.($i%2);
            $states = $row['states'];
            $sign = $states == "입금" ? ($row['amt'] < 0 ? "" : "+")  :  "-" ; 
            $type = $row['admin_states'] != "" ? $row['admin_states'] :  $states ; 
            $coin = $row['coin'];
        ?>
    
        <tr class="<?php echo $bg; ?>">
            <td class='no text-center'><?=$num + $i?></td>
            <td class='no text-center'><?=$mb_id?></td>
            <td class='no text-center'><?=$mb_name?></td>
            <td class='text-center'><strong><?=$type?></strong></td>
            <td class='no text-center'><?=$sign?><?=shift_auto($row['amt'], $coin)?> <?=$coin?></td>
            <td class='text-center'> <?=$row['create_dt'];?></td>
            <td class='text-center'><?=$row['credit'];?></td>	
        </tr>

        <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <?php
    if (isset($domain))
        $qstr .= "&amp;domain=$domain";
        $qstr .= "&amp;page=";
        
    $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
    echo $pagelist;
    ?>

</section>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


