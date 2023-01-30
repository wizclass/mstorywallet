<?php
$sub_menu = '650150';
include_once('./_common.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

auth_check($auth[$sub_menu], "r");

$g5['title'] = '상품권 구매 내역';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-3 months"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

$where = array();

$expiry = isset($_GET['expiry']) && $_GET['expiry'] != "" ? $_GET['expiry'] : "2";
$used = isset($_GET['used']) && $_GET['used'] != "" ? $_GET['used'] : "2";;

$expiry_array = ['기간만료','사용가능','전체'];
$used_array = ['사용완료','사용가능','전체'];
function set_selected($index, $param){
    return  $index == $param ? "selected" : "";
}

$sql_search = "";
if($sort1 == '') $sort1 = "insert_date";
if($sort2 == '') $sort2 = "desc";

if ($search != "") {
    if ($sel_field != "") {
        $where[] = " $sel_field like '%$search%'";
    }

    if ($save_search != $search) {
        $page = 1;
    }
}

if ($fr_date && $to_date) {
    $where[] = " insert_date between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}
$where = "and";
$sql_search_expiry = "";
// 기간만료
if ($expiry == 0) {
    $sql_search_expiry .= "{$where} expiry_date < date(now())";
} 
// 기간만료 X
else if ($expiry == 1) {
    $sql_search_expiry .= "{$where} expiry_date > date(now())";
}

$sql_search_used = "";
// 사용완료
if ($used == 0) {
    $sql_search_used .= "{$where} check_expiry_states = 1 and update_date != '0000-00-00 00:00:00'";
} 
// 사용가능
else if ($used == 1) {
    $sql_search_used .= "{$where} check_expiry_states = 0 and update_date = '0000-00-00 00:00:00'";
}

$sql_search = " {$sql_search} {$sql_search_expiry} {$sql_search_used}";

$sql_order = "order by ".$sort1." ".$sort2."";

$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";

$sql_common = " from {$g5['giftcard_history_table']} $sql_search";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql_order = "order by ".$sort1." ".$sort2."";

$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";
$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 통계 데이터 산출
$stats_sql = "select price_won, sum(price_won) as amt, COUNT(*) AS cnt ".$sql_common."group by price_won ";
$stats_result = sql_query($stats_sql);

// 구매상품명 리턴

function  od_name_return_rank($val){
    if(strlen($val) < 5){
        return substr($val,1,1);
    }else{
        return 0;
    }
}
?>

<style>
	.local_ov strong{color:red; font-weight:600;}
	.local_ov .tit{color:black; font-weight:600;}
	.local_ov a{margin-left:20px;padding-right:10px;}

    .od_cancle{border:1px solid #ccc;background:white;border-radius: 0;padding:5px 10px;}
    .od_cancle:hover{background: black;;border:1px solid black;color:white}
    .cancle_log_btn{border-radius: 0;}
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
    function change_select_expiry(){
        $('#input_expiry').val($('#select_expiry').val());
    }

    function change_select_used(){
        $('#input_used').val($('#select_used').val());
    }
</script>
<link rel="stylesheet" href="/adm/css/scss/admin_custom.css">
<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>

<div class="local_desc01 local_desc">
    <p>
        <strong> - 사용처리 :</strong> 상품권 사용처리 후 사용완료상태로 변경 <br>
	</p>
</div>


<form name="frmorderlist" class="local_sch01 local_sch">
<input type="hidden" name="doc" value="<?php echo $doc; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_search" value="<?php echo $search; ?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">

<label for="sel_field" class="sound_only">검색대상</label>
<select name="sel_field" id="sel_field">
    <option value="mb_id" <?php echo get_selected($sel_field, 'mb_id'); ?>>회원 ID</option>
    <option value="gt_name" <?php echo get_selected($sel_field, 'gt_name'); ?>>상품권명</option>
    <option value="origin_coupon" <?php echo get_selected($sel_field, 'origin_coupon'); ?>>쿠폰번호</option>
    <option value="insert_date" <?php echo get_selected($sel_field, 'insert_date'); ?>>구매일자</option>
    <option value="expiry_date" <?php echo get_selected($sel_field, 'expiry_date'); ?>>만료일자</option>
</select>

<label for="search" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="search" value="<?php echo $search; ?>" id="search" class="frm_input" autocomplete="off" style="width: 180px">
<input type="submit" value="검색" class="btn_submit">

</form>

<form class="local_sch02 local_sch">
    <input type="hidden" name="expiry" id="input_expiry" value="<?=$_GET['expiry']?>" />
    <input type="hidden" name="used" id="input_used" value="<?=$_GET['used']?>" />
    
    <div class="selectbox inline">
        <label for="select_expiry">만료 여부 : </label>
        <select id='select_expiry' onchange="change_select_expiry(this)" style='width:80px;'>
        <?php for($i = count($expiry_array)-1; $i >= 0; $i--){?>
            <option value="<?=$i?>" <?=set_selected($i,$expiry)?>><?=$expiry_array[$i]?></option>
        <?php } ?>
        </select>
    </div>

    <div class="selectbox inline">
        <label for="select_used">사용 여부 : </label>
        <select id='select_used' onchange="change_select_used(this)" style='width:80px;'>
        <?php for($i = count($used_array)-1; $i >= 0; $i--){?>
            <option value="<?=$i?>" <?=set_selected($i,$used)?>><?=$used_array[$i]?></option>
        <?php } ?>
        </select>
    </div>

    <div class="sch_last" style="display: inline-block">
        <strong style="text-align: center">구매일자</strong>
        <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
        <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
        <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
        <button type="button" onclick="javascript:set_date('어제');">어제</button>
        <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
        <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
        <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
        <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
        <button type="button" onclick="javascript:set_date('전체');">전체</button>
        <input type="submit" value="검색" class="btn_submit" style='width:100px;'> | 
        <input type="button" class="btn_submit excel" id="btnExport"  data-name='giftcard_orderlist' value="엑셀 다운로드" />
    </div>

</form>


<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    전체 주문내역 <?php echo number_format($total_count); ?>건
    
    <?
    while($stats = sql_fetch_array($stats_result)){
        echo "<a href='./orderlist.php?".$qstr."&price_won=".$stats['price_won']." '><span class='tit'>";
        echo shift_auto($stats['price_won'], BALANCE_CURENCY) . "원 상품권";
        echo "</span> : ".Number_format($stats['cnt']) . "건";
        echo " = <strong>".shift_auto($stats['amt'], BALANCE_CURENCY). ' ' . BALANCE_CURENCY . "</strong></a>|";
    }
    ?>
</div>


<form name="forderlist" id="forderlist" action="./giftcardupdate.php" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">

    <input type="hidden" name="expiry" value="<?php echo $expiry ?>">
    <input type="hidden" name="used" value="<?php echo $used ?>">
    <input type="hidden" name="fr_date" value="<?php echo $fr_date ?>">
    <input type="hidden" name="to_date" value="<?php echo $to_date ?>">

<div class="tbl_head02 tbl_wrap">
    <div class="btn_list01 btn_list" style="margin: 10px 0px">
        <input type="submit" name="act_button" value="사용처리" onclick="document.pressed=this.value">
    </div>
    <table id="table">
    <caption>주문 내역 목록</caption>
    <thead>
    <tr>
        <th scope="col" >
            <label for="chkall" class="sound_only">주문 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" id="th_odrid" >회원ID</th>
        <th scope="col" id="th_odrid" style="width: 8%">회원 이름</th>
        <th scope="col" id="odrstat">구매(매출)일자</th>
        <th scope="col" id="th_odrnum"><a href="<?php echo title_sort("gt_id", 1)."&amp;$qstr1"; ?>">주문번호</a></th>
		
        <th scope="col" id="odrstat" >상품권명</th>
        <th scope="col" id="odrstat" >쿠폰번호</th>
        <th scope="col" id="odrstat" ><a href="<?php echo title_sort("price_won", 1)."&amp;$qstr1"; ?>">상품가격(원)</th>
        <th scope="col" id="th_odrall">수수료(%)</th>
        
        <th scope="col" id="odrpay">결제수단</th>
		<th scope="col" id="th_odrcnt" style="width: 10%">구매수량(<?= ASSETS_CURENCY . ', 수수료 포함'?>)</th>
		<th scope="col" id="th_odrcnt" style="width: 5%">만료일자</th>
        <th scope="col" id="th_odrid" style="width: 5%">구매종류</th>
        <th scope="col" style="width: 5%">만료여부</th>
        <th scope="col" style="width: 5%">사용여부</th>
        <th scope="col" >사용처리</th>

    </tr>
    <tr>
        
        
        
    </tr>
    <tr>
       
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {

        $bg = 'bg'.($i%2);
    ?>


	<style>
	/* td{width:140px !important;} */
	</style>
    <tr class="orderlist<?php echo ' '.$bg; ?>">
        <!--체크박스-->
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['gt_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
        </td>
        <!--회원ID-->
        <td>
            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=mb_id&amp;search=<?php echo $row['mb_id']; ?>"><?php echo $row['mb_id']; ?></a>
        </td>
        <!--회원 이름-->
        <td>
            <?php echo $row['mb_name']; ?>
        </td>
        <!--구매(매출)일자-->
        <td>
            <?= substr($row['insert_date'], 0, 10 ) ?>
        </td>
        <!--주문번호-->
        <td headers="th_ordnum" class="td_odrnum2">
            <?= $row['pg_id'] ?>
        </td>
        <!--상품권명-->
        <td headers="th_ordnum" class="td_odrnum2">
            <?= shift_auto($row['price_won'], BALANCE_CURENCY) . '원 상품권' ?>
        </td>
        <!--쿠폰번호-->
        <td headers="th_ordnum" class="td_odrnum2">
            <input type="hidden" name="origin_coupon[<?php echo $i; ?>]" value="<?php echo $row['origin_coupon']; ?>">
            <?= $row['origin_coupon'] ?>
        </td>
        <!--상품가격-->
        <td style="width:150px;text-align:right;font-weight:600">
            <?= shift_auto($row['price_won'], BALANCE_CURENCY) . ' ' . BALANCE_CURENCY ?>
        </td>
        <!--수수료-->
        <td class="td_numsum">
            <?= $row['fee'] . '%'?>
        </td>
        <!--결제수단-->
        <td>
            <?= ASSETS_CURENCY ?>
        </td>
        <!--구매가격-->
        <td style="text-align:right;font-weight:600">
            <?= shift_auto($row['price_coin'], ASSETS_CURENCY) . ' ' . ASSETS_CURENCY ?>
        </td>
        <!--만료일자-->
        <td class="td_chk">
            <?= $row['expiry_date'] ?>
        </td>
        <!--구매 종류-->
        <td>
            <?php echo $row['buy_name']; ?>
        </td>
        <!--만료여부-->
        <td class="td_chk">
            <?= strtotime($row['expiry_date']) < strtotime(date("Y-m-d")) ? '만료' : '-' ?>
        </td>
        <!--사용여부-->
        <td class="td_chk">
            <?= $row['check_expiry_states'] ? '사용완료' : '-' ?>
        </td>
        <!--사용처리-->
        <td class="td_chk">
            <?php if ($row['check_expiry_states'] == 0 && $row['update_date'] == '0000-00-00 00:00:00') { ?>
                <input type='button' class='btn od_cancle' value='사용처리' onclick="giftcard_use(<?= $row['idx'] ?>)" />
            <?php } else { ?>
                <p style="margin: 10px auto; height: 16px"></p>
            <?php } ?>
        </td>

       
		<!-- ##end##  ## -->
    </tr>

    <tr class="<?php echo $bg; ?>">
        


    </tr>


    <?php
        $tot_pricewon   += ($row['price_won']);
        $tot_pricecoin    += ($row['price_coin']);
		$tot_odcount     = $i+1;
    }
    sql_free_result($result);
    if ($i == 0)
        echo '<tr><td colspan="13" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    <tfoot>
    <tr class="orderlist">
        <th scope="row" colspan="3">&nbsp;</th>
        <td></td>
        <td></td>
        <td><?php echo number_format($tot_odcount);?>건</td>
        <td>합 계</td>
        <td style='text-align:right; padding: 7px 5px'><?php echo shift_auto($tot_pricewon, BALANCE_CURENCY) . ' ' . BALANCE_CURENCY;?></td>
        <td></td>
        <td></td>
        <td style='text-align:right; padding: 7px 5px'><?php echo shift_auto($tot_pricecoin, ASSETS_CURENCY) . ' ' . ASSETS_CURENCY;?></td>
        <td></td>
        <td></td>
        <td></td>
		<td></td>
        <td></td>
        
    </tr>
    </tfoot>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="사용처리" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function forderlist_submit(f)
{
    if (document.pressed == "사용처리" && !is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

function giftcard_use(idx) {
    var act_button = '개별사용';
    var token = get_ajax_token();

    $.ajax({
        url: g5_url+'/adm/giftcardupdate.php',
        type: 'POST',
        async: false,
        cache: false,
        data: {
            token : token,
            act_button: act_button,
            gt_idx: idx
        },
        dataType: 'json',
        success: function(result) {
            if(result.code == 200) {
                alert("정상적으로 사용처리 되었습니다.");
                location.reload();
            } else if(result.code != 200) {
                alert("사용처리에 실패했습니다.");
                location.reload();
            }
        },
        error: function(e) {
            alert("오류가 있습니다.");
        }
    });
}

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
