<?php
$sub_menu = "600400";
include_once('./_common.php');
include_once(G5_PATH.'/util/package.php');

$g5['title'] = "스테이킹 지급관리";
// check_admin_token();
auth_check($auth[$sub_menu], 'r');
include_once(G5_ADMIN_PATH.'/admin.head.php');

$rlevel = 'all';
$states = isset($_GET['states']) && $_GET['states'] != "" ? $_GET['states'] : "2";
if(ETH_AVAILABLE){
    $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "2";
} else {
    $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "1";
}

$mb_info = isset($_GET['mb_info']) && $_GET['mb_info'] != "" ? $_GET['mb_info'] : "1";

if($_GET['rlevel']){
    $rlevel = $_GET['rlevel'];
}

//주문번호
function order_number($val){
    $order_num = $val;
    $order_no = substr_replace($val,'-',8,0);
    return "<a href='/adm/shop_admin/orderlist.php?sel_field=od_id&search=".$order_num."' target='_blank'>".$order_no."</a>";
}

// 레벨선택
function onselect($val){
    global $rlevel;
    if($rlevel == $val){echo ' selected';}else{ echo '';}
}

$state_array = ['만료','진행중','전체'];
$type_array = ['ETH','MST','전체'];
$times_array = ['ETH','MST','MST | ETH'];
$mb_info_array = ['회원이름', '회원ID'];

function set_selected($index, $param){
    return  $index == $param ? "selected" : "";
}

function get_sql($index){
    global $g5,$states,$type,$type_array,$mb_info,$fr_id;

    $sql_common = "from {$g5['g5_shop_order_table']} s join package_{$index} p on s.od_id = p.od_id";

    $sql_search_states = "";
    $where = "where";
    if($states == 0){
        $sql_search_states .= "{$where} s.pay_count >= s.pay_end";
        $where = "and";
    }else if($states == 1){
        $sql_search_states .= "{$where} s.pay_count < s.pay_end";
        $where = "and";
    }
    
    $sql_search_coin = "";
    if($type == 0){
        $sql_search_coin = "{$where} s.od_settle_case = '{$type_array[0]}'";
        $where = "and";
    }else if($type == 1){
        $sql_search_coin = "{$where} s.od_settle_case = '{$type_array[1]}'";
        $where = "and";
    }

    $sql_search_mb_id = "";
    if($mb_info == 1){
        $sql_search_mb_id = "{$where} p.mb_id LIKE '%{$fr_id}%'";
    } else if($mb_info == 0) {
        $sql_search_mb_id = "{$where} s.od_memo LIKE '%{$fr_id}%'";
    }

    $sql_search = "{$sql_search_states} {$sql_search_coin} {$sql_search_mb_id}";

    return "(select p.*,s.od_name,s.od_tno,s.od_tax_flag,s.od_cart_price,s.pay_acc,s.pay_acc_eth,s.od_cash,s.upstair,s.pv,s.od_settle_case, s.pay_count, s.pay_end, s.od_hope_date, s.od_refund_price,s.od_memo,s.od_invoice_time,count(p.no) as cnt
            {$sql_common}
            {$sql_search})";
}
?>

<?
    $colspan = 18;

    $sql_common = "";
    if($rlevel == "all"){
        for($i = 0; $i <= 6; $i++){
            $sql_common .= get_sql("p{$i}");

            if($i < 6) $sql_common .= " union all ";
        }
    }else{
        $sql_common = get_sql($rlevel);
    }

    $rows = sql_fetch("select sum(cnt) as cnt from({$sql_common}) t");
    $total_count = $rows['cnt'];

    if($_GET['view_mode'] == 'all'){
        $rows = 10000;
    }else{
        $rows = 100;
    }

    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    
    $sql_common = str_replace(',count(p.no) as cnt','',$sql_common);
    $sql = "{$sql_common}
            {$sql_search}
            order by cdatetime desc
            limit {$from_record}, {$rows} ";
    // print_R($sql);
    $result = sql_query($sql);

    $qstr = "to_id=".$fr_id."&rlevel=".$rlevel."&states=".$states."&type=".$type."&mb_info=".$mb_info;

    $query_string = $qstr ? '?'.$qstr : '';

    include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

?>

<style>
    .red{color:red}
    .text-center{text-align:center}
    .text-right{text-align:right;margin-right:5px;}
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
    .btn{display: inline-block;text-align: center;padding:5px 15px;border:1px solid #ccc;border-radius:0;}
    .local_ov strong{color:red}
    .gray a,.gray input{color:#777;}
    input.widewhite{background:white !important;padding:5px 10px;border:1px solid #f1f1f1}

    tbody {color:#555;}
    
    td.strong{font-weight:900; color:black;}

    .td_id {
		color: black;
		font-size: 15px;
		/* padding-left: 6px !important; */
		font-weight: 800;
		min-width: 80px;
		font-family: Montserrat, Arial, sans-serif
	}
    .td_name{
        color:black;
        font-size:15px;
        font-weight:800;
        font-family: Montserrat, Arial, sans-serif;
        width:70px;
    }
    .td_email{
        color: #555;
		font-size: 11px;
		font-weight: 500;
        font-family: Montserrat, Arial, sans-serif
    }
</style>


<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

<script>
$(function(){

    $('#membership_reset').on('click',function(){

        if (confirm('멤버쉽 구매 제한을 초기화하시겠습니까?')) {
			} else {
				return false;
            }
            
        $.ajax({
            url: "/util/limit_reset.php",
            type:"post",
            dataType: "json",
            data: {
                func : 'reset'
            },
            success: function(data){
                if(data.code == '0000'){
                    alert('정상적으로 초기화되었습니다.');
                    location.reload();
                }
            }
        })
    });

});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}

function select_change(f){
   
    $('#input_rlevel').val($('#select_rlevel').val());
}

function change_select_states(){
    $('#input_states').val($('#select_states').val());
}

function change_select_type(){
    $('#input_type').val($('#select_type').val());
}

function change_select_mbinfo(){
    $('#input_mb_info').val($('#select_mb_info').val());
}

</script>

<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>


<div class="local_ov01 local_ov">
	<a href="./rank_table.php?view_mode=all" class="ov_listall">전체상품목록</a><a href="<?=$query_string?>">	총 <strong><?=$total_count?></strong>개</a>
</div>

<div class="local_desc01 local_desc">
    <p>
        <!-- <strong>- 일괄지급 :</strong> (임시)지급테스트용 - 만료회차까지 지급  <br> -->
        <!-- <strong>- 상품종류 :</strong> ESGC->ETH 변경시 해당 구매 회원 등급 자동조정<br> -->
        <strong>- 구매일/지급예정일/지급회차 :</strong> 상품구매일 기준 29일~31일인경우 익익월 1일로 자동계산 ~ 만료일까지 자동계산
	</p>
</div>

<section class='rank_table'>

    <form name="frank" id="frank" class="local_sch02 local_sch" method="get">

    <input type='hidden' name='rlevel' id='input_rlevel' value='<?=$_GET['rlevel']?>'>
    <input type='hidden' name='states' id='input_states' value='<?=$_GET['states']?>'>
    <input type='hidden' name='type' id='input_type' value='<?=$_GET['type']?>'>
    <input type='hidden' name='mb_info' id='input_mb_info' value='<?=$_GET['mb_info']?>'>

        <div class="selectbox inline">
            <label for='select_rlevel'>기간 선택 : </label>
            <select id='select_rlevel' onchange="select_change(this);">
                <option value='all' <?=onselect('all')?> >전체</option>
                <option value='p0' <?=onselect('p0')?> >락업</option>
                <option value='p1' <?=onselect('p1')?> >1년</option>
                <option value='p2' <?=onselect('p2')?> >2년</option>
                <option value='p3' <?=onselect('p3')?> >3년</option>
                <option value='p4' <?=onselect('p4')?> >4년</option>
                <option value='p5' <?=onselect('p5')?> >5년</option>
                <option value='p6' <?=onselect('p6')?> >6년</option>
            </select>
        </div>

         <div class="selectbox inline">
            <label for='select_states'>스테이킹 상태 : </label>
            <select id='select_states' onchange="change_select_states(this)" style='width:80px;'>
            <?php for($i = count($state_array)-1; $i >= 0; $i--){?>
                <option value="<?=$i?>" <?=set_selected($i,$states)?>><?=$state_array[$i]?></option>
            <?php } ?>
            </select>
        </div>

        <?if(ETH_AVAILABLE){?>
            <div class="selectbox inline">
                <label for='select_type'>상품종류 : </label>
                    <select id='select_type' onchange="change_select_type(this)" style='width:80px;'>
                    <?php for($i = count($type_array)-1; $i >= 0; $i--){?>
                        <option value="<?=$i?>" <?=set_selected($i,$type)?>><?=$type_array[$i]?></option>
                    <?php } ?>
                </select>
            </div>
        <?}?>

        
        <div class='selectbox inline'> 
        <label for="fr_id">검색 : </label>
            <select id="select_mb_info" onchange="change_select_mbinfo(this)" style='width:80px;'>
                <?php for($i = count($mb_info_array)-1; $i >= 0; $i--){?>
                    <option value="<?=$i?>" <?=set_selected($i,$mb_info)?>><?=$mb_info_array[$i]?></option>
                <?php } ?>
            </select>
            <input type="text" name="fr_id" value="<?php echo $fr_id ?>" id="fr_id" class="frm_input" size="30">
        </div>

        <div class='inline'> 
            <input type="submit" value="검색" class="btn_submit">
            <input type="button" class="btn_submit excel" id="btnExport" data-name='staking_status' value="엑셀 다운로드" />
        </div>

    </form>

    <form name="franktableupdate" method="post" action="./rank_table_update.php" onsubmit="return franktable_submit(this);" autocomplete="off">

        <input type="hidden" name="rlevel" value="<?php echo $rlevel ?>">
        <input type="hidden" name="states" value="<?php echo $states ?>">
        <input type="hidden" name="type" value="<?php echo $type ?>">
        <input type="hidden" name="fr_id" value="<?php echo $fr_id ?>">

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
    <div class="tbl_head01 tbl_wrap">
        <table id="table">
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th>
                <label for="chkall" class="sound_only">상품 전체</label>
                <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
            </th>
            <th>no</th>
            <!-- <th>구매순서</th> -->
            <th>이름</th>
            <th>아이디</th>
            <th>상품종류</th>
            <th>상품명</th>
            <!-- <th>해당상품코드</th> -->
            <th>상품구매일</th>
            <th>지급예정일</th>
            <th>구매주문번호</th>
            <th>수수료 <br>(<?=ASSETS_CURENCY?>)</th>
            <th>예치금 <br>(<?=ASSETS_CURENCY?>)</th>
            <!-- <th>현금가 (<?=BALANCE_CURENCY?>)</th> -->
            <!-- <th>합계 <br>(<?=ASSETS_CURENCY?>)</th> -->
            <th>총지급예정 <br>(<?=ASSETS_CURENCY?>)</th>

            <th>누적지급량 <br>(<?=$times_array[$type]?>)</th>
            
            <th>1회지급량 <br>(<?=ASSETS_CURENCY?>)</th>
            <th>지급회차</th>
            <th>전체회차</th>
            <th>지급만료일</th>
            <th>예치금 반환</th>
            <!-- <th>일괄지급(테스트)</th> -->
        </tr>
        </thead>
        <tbody>
        <div class="btn_list01 btn_list" style="margin: 0px 0px 10px 0px">
            <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
        </div>
        <?php
        $num = (($page-1)*$rows)+1;
        for ($i=0; $row=sql_fetch_array($result); $i++) {
            $bg = 'bg'.($i%2);
            $paied_bonus = $row['od_settle_case'] == WITHDRAW_CURENCY ? $row['pay_acc_eth'] : $row['pay_acc'];
        ?>

        
        
        <tr class="<?php echo $bg; ?>">
            <td class="td_chk">
                <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
            </td>

            <td class='no text-center'><?=$num + $i?></td>
            <!-- <td class='no text-center'><?=$row['idx']?></td> -->
            <td class='td_name'><?=$row['od_memo']?></td>
            <td class='td_id'>
                <span class='td_email'><a href="/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>" target="_blank"><?=$row['mb_id']?></a></span>
            </td>
            <td class='no text-center'>
                <? if(ETH_AVAILABLE){ ?>
                <select onchange="set_staking_type(this);" <?=$row['od_settle_case'] == WITHDRAW_CURENCY ? "disabled" : "" ?> data-od_id="<?=$row['od_id']?>">
                    <option value="<?=ASSETS_CURENCY?>" <?=$row['od_settle_case'] == ASSETS_CURENCY ? "selected" : "" ?>><?=ASSETS_CURENCY?></option>
                    <option value="<?=WITHDRAW_CURENCY?>" <?=$row['od_settle_case'] == WITHDRAW_CURENCY ? "selected" : "" ?>><?=WITHDRAW_CURENCY?></option>
                </select>
                <? } else { ?>
                    <?=ASSETS_CURENCY?>
                <? } ?>
            </td>

            <?php  $sql = "SELECT it_maker FROM {$g5['g5_shop_item_table']} s JOIN {$g5['g5_shop_order_table']} p ON s.it_id = p.od_tno WHERE p.od_tno = '".$row['od_tno']."'";
            $od_index = sql_fetch($sql);?>
            <td class='no text-center'><input type="hidden" name="od_index[<?php echo $i; ?>]" value="<?=strtolower($od_index['it_maker'])?>" /> <?=$row['od_name']?>
            </td>

            <td class='text-center gray'><input type="date" name="od_time[<?php echo $i; ?>]" required class="frm_input required widewhite" value="<?=$row['cdate'];?>" style="text-align: center"/></td>
            <!-- <td class='text-center gray'><input type="date" name="hope_dt[<?php echo $i; ?>]" required class="frm_input required widewhite" value="<?=$row['od_hope_date'];?>" style="text-align: center"/></td> -->
            <td class='text-center gray'><?=$row['od_hope_date']?></td>
            <td class='text-center gray' ><input type="hidden" name="od_id[<?php echo $i; ?>]" value="<?=$row['od_id']?>" /> <?=order_number($row['od_id']);?></td>
            
            <td class='text-center'><?=shift_auto($row['od_tax_flag'],ASSETS_CURENCY)?></td>
            
            <td class='text-right strong'><?=shift_auto($row['od_cart_price'],ASSETS_CURENCY)?></td>
            <!-- <td class='text-right'><?=shift_auto($row['od_cart_price']+$row['od_tax_flag'],ASSETS_CURENCY)?></td> -->

            <td class='text-right'><?=shift_auto($row['upstair'],ASSETS_CURENCY)?></td>
            <td class='text-right'><?=shift_auto($paied_bonus,$row['od_settle_case'])?></td>
            <td class='text-right strong'><?=shift_auto($row['pv'],ASSETS_CURENCY)?></td>


            <td class='text-center strong'><input type="text" name="pay_count[<?php echo $i; ?>]" value="<?=$row['pay_count']?>" class="frm_input required widewhite" style="width: 20px; text-align: center"/></td>	
            <td class='text-center'><?=$row['pay_end']?></td>	
            <td class='text-center'><?=$row['od_invoice_time']?></td>

            <td class='text-center'>
                <?
                if($row['promote'] <= 0 && $row['od_refund_price'] <= 0){ 
                    if($row['pay_count'] == $row['pay_end']){
                        echo "<a href='#' onclick='refund_staking(this);return false;' style='color:red;cursor:pointer;' data-od_id='{$row['od_id']}'>만료반환하기</a>";
                    }else{
                        echo "<a href='#' onclick='refund_staking(this);return false;' style='color:blue;cursor:pointer;' data-od_id='{$row['od_id']}'>반환하기</a>";
                    }
                }else{
                    echo "<span style='color:black;'>반환완료</span>";
                }
                ?>
                </td>
            <!-- <td class='text-center'><?= $row['od_refund_price'] <= 0 ? "<button class='btn inline all_proc' data-id='{$row['od_id']}' >일괄지급</button>" : '-' ?></td> -->
        </tr>

        <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>

    <div class="btn_list01 btn_list">
        <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
    </div>

</form>

    <?php
    if (isset($domain))
        $qstr .= "&amp;domain=$domain";
        $qstr .= "&amp;page=";
        
    $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
    echo $pagelist;
    ?>

</section>

<script>

    function refund_staking(_this){

        let confirm_result = confirm("스테이킹 예치금을 반환하시겠습니까?");

        if(!confirm_result) return false;

        let token = get_ajax_token();

        $.ajax({
            url:"./staking_refund.php",
            type:"POST",
            dataType:"json",
            cache:false,
            async:false,
            data:{
                token : token,
                od_id: _this.dataset.od_id
            },
            success : (res)=> {
                alert(res.msg);
                if(res.code == "200") window.location.reload();
            }
        })
    }

    $('.view_all').on('click',function(){
        loction.href='bonus/rank_table.php?view_mode=all';

    });

    $('.all_proc').on('click',function(){
        /* alert('준비중입니다.');
        return false; */

        if(!confirm("스테이킹 수익금을 일괄지급하시겠습니까?\n이작업은 되돌릴수 없으며, 지급만료일까지 일괄 지급됩니다.")){return false;}
        let od_id = $(this).data('id');
        let token = get_ajax_token();

        $.ajax({
            url:"./staking_all_proc.php",
            type:"POST",
            dataType:"json",
            data:{
                token : token,
                od_id: od_id
            },
            async:true,
            cache:false,
            success: function(res) {
                alert(res.msg);
				if(res.code == '200'){
                    location.reload();
                }
			}
        })

        return false;
    });

    function set_staking_type(_this){
    
        let eth = "<?=WITHDRAW_CURENCY?>";
        let esgc = "<?=ASSETS_CURENCY?>";

        if(_this.value == esgc) return false;

        let confirm_result = confirm(`${esgc} (을)를 ${eth} 로 변경하시겠습니까?`);
        if(!confirm_result) return false;
        
        let token = get_ajax_token();

        $.ajax({
            url:"./change_staking_type.php",
            type:"POST",
            dataType:"json",
            cache:false,
            async:false,
            data:{
                token : token,
                od_id: _this.dataset.od_id,
                od_settle_case: eth
            },
            success : (res)=> {
                alert(res.msg);
                if(res.code == "200") window.location.reload();
            }
        })
    }

    function franktable_submit(f) {
        if (document.pressed == "선택수정" && !is_checked("chk[]")) {
            alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
            return false;
        }

        return true;
    }

    

</script>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


