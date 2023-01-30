<?php
$sub_menu = '650100';
include_once('./_common.php');
// include_once('../adm.wallet.php');
include_once(G5_THEME_PATH."/_include/coin_price.php");

auth_check($auth[$sub_menu], "r");

$g5['title'] = '상품권 관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_giftcard_table']} ";
$sql .= " order by no";
$result = sql_query($sql);

$sql_common = " from {$g5['g5_shop_giftcard_table']}";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql_order = "order by no";


$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";
$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>

<script src="<?=G5_THEME_URL?>/_common/js/common.js" crossorigin="anonymous"></script>

<form name="fitemlistupdate" method="post" action="./giftcardupdate.php" onsubmit="return fitemlist_submit(this);" autocomplete="off">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<style>
    thead th{width:10%}
    thead th.num{width:20px !important;}
</style>

<div class="tbl_head02 tbl_wrap">
    <table class="gift_card">
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" class="num">
            <label for="chkall" class="sound_only">상품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>

        <th scope="col" >상품코드</th>
        <!-- <th scope="col" id="th_pc_title" style='width:100px;'><?php echo subject_sort_link('gt_name', 'sca='.$sca); ?>상품명</a></th> -->
        <th scope="col" id="th_pc_title" style='width:100px;'><?php echo subject_sort_link('gt_price', 'sca='.$sca); ?>상품가격</a></th>
        <!-- <th scope="col" id="th_pc_title" style='width:100px;'><?php echo subject_sort_link('gt_coin', 'sca='.$sca); ?>코인수량(<?= ASSETS_CURENCY ?>)</a></th> -->
        <th scope="col" id="th_pt" style='width:100px;'><?php echo subject_sort_link('gt_valid', 'sca='.$sca); ?>유효기간</a></th>
        <!-- <th scope="col" id="th_pt" style='width:100px;'><?php echo subject_sort_link('gt_cdate', 'sca='.$sca); ?>생성시간</a></th>
        <th scope="col" style='width:30px;'><?php echo subject_sort_link('gt_edate', 'sca='.$sca); ?>수정시간</a></th> -->
        <th scope="col" style='width:20px;'><?php echo subject_sort_link('gt_fee', 'sca='.$sca); ?>수수료</a></th>
        <th scope="col" style='width:20px;'><?php echo subject_sort_link('gt_order', 'sca='.$sca); ?>노출순서</a></th>
        <th scope="col" style='width:30px;'><?php echo subject_sort_link('gt_use', 'sca='.$sca, 1); ?>판매</a></th>
        
    </tr>
    </thead>
    <tbody class="gift_card_row">
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $href = G5_SHOP_URL.'/item.php?it_id='.$row['it_id'];
        $bg = 'bg'.($i%2);

        $it_point = $row['it_point'];
        if($row['it_point_type'])
            $it_point .= '%';
    ?>

    <tr class="<?php echo $bg; ?>">
        <!--체크박스-->
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['gt_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
        </td>
        
        <!--상품코드-->
        <td  class="td_num">
            <input type="hidden" name="gt_id[<?php echo $i; ?>]" value="<?php echo $row['gt_id']; ?>">
            <?php echo $row['gt_id']; ?>
        </td>

        <!--상품명-->
        <!-- <td headers="th_pc_title" class="td_input">
            <label for="name_<?php echo $i; ?>" class="sound_only">상품명</label>
            <input type="text" name="gt_name[<?php echo $i; ?>]" value="<?php echo htmlspecialchars2(cut_str($row['gt_name'],250, "")); ?>" id="name_<?php echo $i; ?>" class="frm_input" size="30">
        </td> -->

        <!--상품가격-->
        <td headers="th_pc_title" class="td_numbig td_input">
            <label for="price_<?php echo $i; ?>" class="sound_only">상품가격</label>
            <input type="text" name="gt_price[<?php echo $i; ?>]" value="<?php echo shift_auto($row['gt_price'], BALANCE_CURENCY); ?>" id="price_<?php echo $i; ?>" required class="frm_input required" size="30" style="width: 100px" inputmode = "numeric"> 원
        </td>

        <!--코인수량-->
        <!-- <td headers="th_pc_title" class="td_numbig td_input">
            <label for="coin_<?php echo $i; ?>" class="sound_only">코인수량</label>
            <input type="text" name="gt_coin[<?php echo $i; ?>]" value="<?php echo shift_auto($row['gt_coin'], ASSETS_CURENCY); ?>" id="coin_<?php echo $i; ?>" required class="frm_input required" size="30" style="width: 100px" inputmode = "numeric">
        </td> -->
        
        <!--유효기간-->
        <td headers="th_pt" class="td_numbig td_input" id="gt_valid">
            <label for="valid_<?php echo $i; ?>" class="sound_only">유효기간</label>
            <input type="text" name="gt_valid[<?php echo $i; ?>]" value="<?=$row['gt_valid'] ?>" id="valid_<?php echo $i; ?>" class="frm_input hasDatepicker" size="5" style="width: 100px; text-align :center" inputmode = "numeric">일
        </td>

        <!--수수료-->
        <td class="td_chk">
            <label for="fee_<?php echo $i; ?>" class="sound_only">수수료</label>
            <input type="text" name="gt_fee[<?php echo $i; ?>]" <?php echo $row['gt_fee']; ?> class="frm_input" size="3" value="<?php echo $row['gt_fee']; ?>" id="fee_<?php echo $i; ?>"> %
        </td>

        <!--노출순서-->
        <td class="td_chk">
            <label for="order_<?php echo $i; ?>" class="sound_only">노출순서</label>
            <input type="text" name="gt_order[<?php echo $i; ?>]" <?php echo $row['gt_order']; ?> class="frm_input" size="3" value="<?php echo $row['gt_order']; ?>" id="order_<?php echo $i; ?>">
        </td>

        <!--사용여부-->
        <td class="td_chk">
            <label for="use_<?php echo $i; ?>" class="sound_only">판매</label>
            <input type="checkbox" name="gt_use[<?php echo $i; ?>]" value="1" id="use_<?php echo $i; ?>" class="frm_input" size="3" <?php echo ($row['gt_use'] ? 'checked' : ''); ?> />
        </td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 한건도 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="신규등록" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
    <?php if ($is_admin == 'super') { ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
    <?php } ?>
</div>
<!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
function fitemlist_submit(f)
{
    if ((document.pressed == "선택삭제" || document.pressed == "선택수정") && !is_checked("chk[]")) {
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

$(function() {
    $(".itemcopy").click(function() {
        var href = $(this).attr("href");
        window.open(href, "copywin", "left=100, top=100, width=300, height=200, scrollbars=0");
        return false;
    });

    $(".vat_calc").on('click',function(){
        var select_num = $(this).data('num');
        var it_price = $("#price_"+select_num).val().replace(/,/g,'');
        var it_sell_price = 1.1 * it_price;

        var cust_price = $("#cust_price_"+select_num);
        console.log(cust_price.val());
        cust_price.val(Price(it_sell_price.toFixed()));
    });
    
});

// 숫자에 콤마 찍기
function Price(x){
	return String(x).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function excelform(url)
{
    var opt = "width=600,height=450,left=10,top=10";
    window.open(url, "win_excel", opt);
    return false;
}

// 신규등록 시 Row 추가
/* function createGiftCard() {
    var rowNum = <?php echo $total_count?>;
    $(".gift_card_row").append("<tr class='bg" + rowNum + "'><td class='td_chk'><label for='chk_" + rowNum + "' class='sound_only'></label><input type='checkbox' name='chk[]' id='chk_" + rowNum + "' value='" + rowNum + "'></td>" + 
    "<td class='td_num'><input type='hidden' name='gt_id[" + rowNum + "]'></td>" +
    "<td headers='th_pc_title' class='td_input'><label for='name_" + rowNum + "' class='sound_only'>상품명</label><input type='text' name='gt_name[" + rowNum + "]' id='name_" + rowNum + "' class='frm_input required' size='30'></td>" +
    "<td headers='th_pc_title' class='td_numbig td_input'><label for='price_" + rowNum + "' class='sound_only'>상품가격</label><input type='text' name='gt_price[" + rowNum + "]' id='price_" + rowNum + "' class='frm_input required' size='30' style='width: 100px' inputmode='numeric'>원</td>" +
    "<td headers='th_pc_title' class='td_numbig td_input'><label for='price_" + rowNum + "' class='sound_only'>코인수량</label><input type='text' name='gt_coin[" + rowNum + "]' id='coin_" + rowNum + "' class='frm_input required' size='30' style='width: 100px' inputmode='numeric'></td>" +
    "<td headers='th_pt' class='td_numbig td_input' id='gt_valid'><label for='valid_" + rowNum + "' class='sound_only'>유효기간</label><input type='text' name='gt_valid[" + rowNum + "]' id='valid_" + rowNum + "' class='frm_input hasDatepicker' size='5'></td>" +
    "<td class='td_chk'><label for='order_" + rowNum + "' class='sound_only'>노출순서</label><input type='text' name='gt_order[" + rowNum + "]' id='order_" + rowNum + "' class='frm_input' size='3'></td>" +
    "<td class='td_chk'><label for='use_" + rowNum + "' class='sound_only'>사용여부</label><input type='checkbox' name='gt_use[" + rowNum + "]' id='use_" + rowNum + "' class='frm_input'></td>" +
    "</tr>");
    rowNum++;
} */
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
