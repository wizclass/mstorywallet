<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_PATH . '/util/package.php');

auth_check($auth[$sub_menu], 'r');

$get_shop_item = get_shop_item();

if($_GET['mode'] == 'del'){
	$sql_target = 'g5_member_del';
	$mode = 'del';
}else{
	$sql_target = 'g5_member';
}

$sub_sql = "";

if ($_GET['sst'] == "total_fund") {
	$sub_sql = " , (mb_deposit_point + mb_deposit_calc + mb_balance - mb_shift_amt) as total_fund";
}

if ($_GET['sst'] == "total_eth_fund") {
	$sub_sql = " , (mb_balance_eth - mb_amt_eth) as total_eth_fund";
}

if ($_GET['sst'] == "deposit_point") {
	$sub_sql = " , (mb_deposit_point) as deposit_point";
}


if ($_GET['sst'] == "mining") {
	$sub_sql = " , ($mining_target - $mining_amt_target) as mining";
}


$sql_common = " {$sub_sql} from {$sql_target} ";

$sql_search = " where mb_id != 'admins' ";
if ($stx) {
	$sql_search .= " and ( ";
	switch ($sfl) {
		case 'mb_point':
			$sql_search .= " ({$sfl} >= '{$stx}') ";
			break;
		case 'mb_level':
			if($stx == '일반회원') {
				$memer_level = 0;
			} else if($stx == '정회원') {
				$memer_level = 1;
			} else if($stx == '정회원S' || $stx == '정회원s') {
				$memer_level = 2;
			}
			$sql_search .= " ({$sfl} = '{$memer_level}') ";
			break;
		case 'mb_tel':
		case 'mb_hp':
			$sql_search .= " ({$sfl} like '%{$stx}%') ";
			break;
		case 'all':
			$sql_search .= " (mb_id like '%{$stx}%')";
			break;

		default:
			$sql_search .= " ({$sfl} like '%{$stx}%') ";
			break;
	}
	$sql_search .= " ) ";
}

if ($_GET['level']) {
	$sql_search .= " and mb_level = " . $_GET['level'];
}

if ($_GET['block']) {
	$sql_search .= " and mb_block = 1 ";
}

if (!$sst) {
	$sst = "mb_datetime";
	$sod = "desc";
}

if ($_GET['grade'] > -1) {
	$sql_search .= " and grade = " . $_GET['grade'];
}

$sql_order = " order by {$sst} {$sod}";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

// $rows = $config['cf_page_rows'];
if($_GET['range'] == 'all'){
	$range = $total_count;
}else{
	$range = 50;
}
$rows = $range;

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
// print_R($sql);
$row = sql_fetch($sql);
$leave_count = $row['cnt'];


// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '" class="ov_listall">전체목록</a>';



$g5['title'] = '회원관리';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";

$result = sql_query($sql);
$colspan = 13;

$sub_sql = "SELECT mb_id from (SELECT mb_id {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows}) as m";

$total_staking_member_sql = "SELECT mb_id, SUM(od_cart_price + od_tax_flag) as staking, SUM(od_tax_flag) as fee FROM {$g5['g5_shop_order_table']}
WHERE od_refund_price < 1 AND od_refund_price <= 0 AND mb_id in ({$sub_sql})
GROUP BY mb_id";

$total_staking_member_result = sql_query($total_staking_member_sql);

$total_staking_member = array();
for($i = 0; $i < $row = sql_fetch_array($total_staking_member_result); $i++){
	$total_staking_member[$row['mb_id']] = $row['staking'];
}

$total_purchase_giftcard_sql = "SELECT mb_id, SUM(price_coin) AS purchase FROM wpurchase_giftcard_history WHERE mb_id IN ({$sub_sql}) GROUP BY mb_id";
$total_purchase_giftcard_result = sql_query($total_purchase_giftcard_sql);

$total_purchase_member = array();
for($i = 0; $i < $row = sql_fetch_array($total_purchase_giftcard_result); $i++){
	$total_purchase_member[$row['mb_id']] = $row['purchase'];
}

/* 레벨 */
$grade = "SELECT mb_level, count( mb_level ) as cnt FROM {$sql_target} GROUP BY mb_level order by mb_level";
$get_lc = sql_query($grade);

function active_check($val, $target)
{
	$bool_check = $_GET[$target];
	if ($bool_check == $val) {
		return " active ";
	}
}

function out_check($val)
{
	$bonus_OUT_CALC = $val;

	if ($bonus_OUT_CALC > 100) {
		$class = 'over';
	} else {
		$class = '';
	}
	return "<span class=" . $class . ">" . number_format($bonus_OUT_CALC) . " % </span>";
}

// 통계수치
$stats_sql = "SELECT COUNT(*) as cnt, 
SUM(mb_deposit_point) AS deposit, 
SUM(mb_balance) AS balance,
SUM($mining_target) AS mining_total, 
SUM(mb_deposit_point + mb_deposit_calc + mb_balance - mb_shift_amt) AS able_with, 
SUM($mining_target - $mining_amt_target) AS able_mining
{$sql_common} {$sql_search}";

$stats_result = sql_fetch($stats_sql);

?>


<style>
	.local_ov strong {
		color: red;
		font-weight: 600;
	}

	.local_ov {
		color: #777;
		font-weight: 500;
		line-height: 20px;
	}

	.local_ov a {
		margin-left: 20px;
	}

	.local_ov span {
		margin-left: 10px;
		padding-right: 5px;
	}

	.local_ov .bonus {
		margin-top: 5px;
		border-left: 3px solid green;
		background: white;
		display: inline-block;
		padding: 3px 5px;
		border-radius: 5px;
		color: black;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
	}

	.local_ov .bonus.mining {
		border-left: 3px solid purple;
		margin-left: 10px;
	}

	select#sfl {
		padding: 9px 10px;
	}

	#stx {
		padding: 5px;
	}
	.local_sch{
		display:contents;
	}

	.local_sch .btn_submit {
		width: 80px;
		height: 33px;
	}

	.f_blue {
		color: blue !important;
		font-weight: 600
	}
	.icon,.icon img{width:26px;height:26px;font-size:18px;}

	.badge.over{    position: absolute;
    padding: 2px 5px;
    background: #eee;
    font-size: 12px;
	margin-left:-10px;
	margin-top:12px;
    display: inline-block;
    font-weight: 600;
    color: black;}
	.icon{display:inline-block;vertical-align: bottom;}
	.icon i{vertical-align: -webkit-baseline-middle;}
	#member_depth {
		background: lightskyblue
	}

	#member_depth:hover {
		background: black;
		color: white;
	}

	.mem_icon {
		width: 20px;
		height: 20px;
		margin-right: 5px;
	}

	.area {
		display: inline-block;
		margin-right: 20px;
		vertical-align: middle
	}

	.area span {
		cursor: pointer
	}

	.area span:hover {
		text-decoration: underline
	}

	.area.nation {
		border-right: 3px solid black;
		padding-right: 20px;
	}

	.nation_item {
		display: inline-block;
		padding: 5px 10px;
		border: 1px solid #c8ced1;
		background: #d6dde1;
		text-decoration: none;
	}

	.nation_item:hover {
		background: #3e4452;
		color: white;
	}

	.nation_item.active {
		background: #f9a62e;
		border: 1px solid #f9a62e;
		color: black;
	}

	.nation_icon {
		vertical-align: bottom;
		margin-right: 3px;
	}

	.total {
		background: #555 !important;
		color: white !important;
	}

	.bonus_total {
		background: teal !important;
		color: white !important;
	}

	.bonus_usdt {
		background: crimson !important;
		color: white !important;
	}

	.bonus_usdt a {
		color: white !important;
		font-weight: 300
	}

	.bonus_eth {
		background: #0062cc !important;
		color: white !important;
	}

	.bonus_aa {
		background: yellowgreen !important
	}

	.bonus_bb {
		background: skyblue !important
	}

	.bonus_bb.bonus_out {
		background: deepskyblue !important
	}

	.bonus_bb.green {
		background: green !important;
		color: white
	}

	.bonus_bb.bonus_benefit {
		background: gold !important
	}

	.bonus_calc {
		background: #3e1f9c !important;
	}

	.bonus_calc a {
		color: white !important;
		font-weight: 400
	}

	.td_mbgrade select {
		min-width: 50px;
		padding: 5px 5px;
		color:#777;
	}
	.tbl_head02 tbody{color:#777;}
	.tbl_head02 tbody td {
		padding: 5px;
	}

	.over {
		color: red;
	}

	.td_mngsmall a {
		border: 1px solid #ccc;
		padding: 3px 10px;
		display: inline-block;
		text-decoration: none;
	}

	.td_mngsmall a:hover {
		background: black;
		border: 1px solid black;
		color: white;
	}

	.labelM {
		text-align: left;
	}

	.red {
		color: red;
		font-weight: 600;
	}

	.btn_add01 {
		padding-bottom: 10px;
		border-bottom: 1px solid #bbb;
		text-align:left;
	}

	.center {
		text-align: center !important;
	}

	.td_mail {
		font-size: 11px;
		letter-spacing: -0.5px;
	}

	.td_name {
		font-size:12px;
		min-width: 70px;
		width: 70px;
	}

	.td_id{
		font-size:12px;
	}
	.bonus_eth a {
		color: white !important
	}

	.name {
		color: #777;
		font-size: 11px;
	}

	.icon,
	.icon img {
		width: 26px;
		height: 26px;
	}
	.grade_icon{width:22px;height:22px;opacity: 0.75;}

	.badge.over {
		position: absolute;
		padding: 2px 5px;
		background: #eee;
		font-size: 12px;
		margin-left: -10px;
		margin-top: 12px;
		display: inline-block;
		font-weight: 600;
		color: black;
	}

	.strong{font-weight:600;color:black;}

	.td_mbstat {
		text-align: right;
		padding-right: 10px !important;
		font-size: 12px;
		width: 75px;
		color:#333;
	}
	.td_index{
		min-width:40px;width:40px;text-align:center !important;
	}
	.td_grade{
		width:30px;min-width:30px;
	}
	.user_icon i{
		vertical-align:-webkit-baseline-middle !important;
	}
	.no-swap{display:block;color:#bbb;font-size:11px;font-weight:300;width:100%;margin:0;padding:0;line-height:10px;}

	.before_eth{border-right:1px solid #333 !important}
</style>
<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/custom.css">
<style>

</style>
<!-- 
<div class="local_desc01 local_desc">
	
		<p>
		- <strong style="color:#0062cc;">총ESGC수량</strong> : 회원이 <strong style="color:#0062cc;">보유한 ESGC</strong> 총 수량. 
		<p>
		<p>
		- <strong style="color:#3e1f9c;">총입금ESGC수량</strong> : 회원이 <strong style="color:#3e1f9c;">입금한 ESGC</strong> 총 수량
		</p>
		<p>
		- <strong style="color:skyblue;">상품권 구매ESGC수량</strong> : <strong style="color:skyblue;">상품권 구매시 사용한 ESGC</strong> 총 수량
		</p>
		<p>
		- <strong style="color:crimson;">출금ESGC수량</strong> : <strong style="color:crimson;">출금한 ESGC</strong> 총 수량
		</p>
		<p>
		- <strong style="color:gold;">스테이킹ESGC수량</strong> : <strong style="color:gold;">스테이킹 상품 구매한 ESGC</strong> 총 수량
		</p>
		<p>
		- <strong style="color:gold;">스테이킹 수수료ESGC수량</strong> : <strong style="color:gold;">스테이킹 상품 구매시 발생한 수수료 ESGC</strong> 총 수량
		</p>
		<p>
		- <strong style="color:yellowgreen;">누적보너스ESGC수량</strong> : <strong style="color:yellowgreen;">스테이킹으로 보너스 받은 ESGC</strong> 총 수량 	
		<p>
		
		<strong style="color:#3e1f9c;">총입금ESGC수량</strong> - 
		<strong style="color:skyblue;">상품권 구매ESGC수량(+수수료)</strong> - 
		<strong style="color:crimson;">출금ESGC수량(+수수료)</strong> - 
		<strong style="color:gold;">스테이킹ESGC수량</strong> - 
		<strong style="color:gold;">스테이킹 수수료ESGC수량</strong> + 
		<strong style="color:yellowgreen;">누적보너스ESGC수량</strong>
		</p>
</div> -->


<div class="local_ov01 local_ov">
	<?php echo $listall ?>
	총회원수 <strong><?php echo number_format($total_count) ?></strong> 명::
	
<?
function get_level_title($level){
	if($level == 0){
		echo "일반회원";
	}else if($level == 1){
		echo "정회원";
	}else if($level == 2){
		echo "정회원S";
	}else{
		echo "관리자";
	}
}

$i = 0;
while ($l_row = sql_fetch_array($get_lc)) {
	echo  get_level_title($l_row['mb_level'])." : <a href='' style='margin:0;font-weight:600'>".number_format($l_row['cnt'])."</a> 명 | ";
	++$i;
} ?>

	<?
	if ($member['mb_id'] == 'admin' || $member['mb_id'] == "admins") {
		echo "<br><span class='bonus' style='margin:0;'>총 입금 합계 <strong>" .shift_auto($stats_result['deposit'],ASSETS_CURENCY). " " .ASSETS_CURENCY. " </strong></span> | ";
		
		echo "<div class='bonus'>스테이킹 누적 보너스<strong>(".strtoupper($minings[0]).")</strong><span>:<strong>" . shift_auto($stats_result['balance'],ASSETS_CURENCY) .' '.strtoupper($minings[0])." </strong></span> | ";
		echo "<span>출금 가능 : <span class='f_blue'>" . shift_auto($stats_result['able_with'],ASSETS_CURENCY) .' '.strtoupper($minings[0])."  </span></span></div> ";

		echo "<div class='bonus mining'>스테이킹 누적 보너스<strong>(".strtoupper($minings[1]).")</strong><span>:<strong>" . shift_auto($stats_result['mining_total']) .' '.strtoupper($minings[1])." </strong></span> | ";
		echo "<span>출금 가능 : <span class='f_blue'>" . shift_auto($stats_result['able_mining']) .' '.strtoupper($minings[1])."  </span></span></div> ";
	}
	?>
	<br>
	
</div>



<!-- "excel download" -->

<script src="../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../excel/tabletoexcel/tableExport.js"></script>


<?php if ($is_admin == 'super') { ?>
	<div class="btn_add01 btn_add">

	<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
		<label for="sfl" class="sound_only">검색대상</label>
		<select name="sfl" id="sfl">
			<option value="all" <?php echo get_selected($_GET['sfl'], "all"); ?>>전체</option>
			<option value="mb_id" <?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
			<!-- <option value="mb_nick"<?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>닉네임</option> -->
			<option value="mb_name" <?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
			<!-- <option value="mb_level"<?php echo get_selected($_GET['sfl'], "mb_level"); ?>>권한</option>
			<option value="mb_email"<?php echo get_selected($_GET['sfl'], "mb_email"); ?>>E-MAIL</option> -->
			<!-- <option value="mb_tel"<?php echo get_selected($_GET['sfl'], "mb_tel"); ?>>전화번호</option> -->
			<option value="mb_hp" <?php echo get_selected($_GET['sfl'], "mb_hp"); ?>>휴대폰번호</option>
			<!-- <option value="mb_point"<?php echo get_selected($_GET['sfl'], "mb_point"); ?>>PV</option> -->
			<option value="mb_datetime" <?php echo get_selected($_GET['sfl'], "mb_datetime"); ?>>가입일시</option>
			<option value="mb_ip" <?php echo get_selected($_GET['sfl'], "mb_ip"); ?>>IP</option>
			<option value="mb_level" <?php echo get_selected($_GET['sfl'], "mb_level"); ?>>회원등급</option>
			<!-- <option value="mb_recommend" <?php echo get_selected($_GET['sfl'], "mb_recommend"); ?>>추천인</option> -->
			<!-- <option value="mb_wallet"<?php echo get_selected($_GET['sfl'], "mb_wallet"); ?>>지갑</option> -->
		</select>

		<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="required frm_input" required>
		<input type="submit" class="btn_submit" value="검색">
	</form>	

		<!-- <a href="./member_table_depth.php" id="member_depth">회원추천/직추천갱신</a>
		<a href="./member_table_fixtest.php">추천관계검사</a> -->
		<a href="./member_list.php?mode=del" >삭제/탈퇴 회원보기</a>
		<a href="./member_form.php" id="member_add">회원직접추가</a>
		<?if($range == 'all'){?>
			<a href="./member_list.php?range=" >회원전체보기</a>
		<?}else{?>
			<a href="./member_list.php?range=all" >회원전체보기</a>
		<?}?>
		<a id="btnExport" data-name='member_info' class="excel" style="padding:10px 10px;">엑셀 다운로드</a>
	</div>
<?php } ?>

</div>

<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">

<div class="btn_list01 btn_list">
		<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
		<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>

	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
	<input type="hidden" name="stx" value="<?php echo $stx ?>">
	<input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="token" value="">

	<div class="tbl_head02 tbl_wrap" style="clear:both">
		<table id='table'>
			<caption><?php echo $g5['title']; ?> 목록</caption>

			<thead>
				<tr>
					<th scope="col" rowspan="2" id="mb_list_chk">
						<label for="chkall" class="sound_only">회원 전체</label>
						<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
					</th>
					<th scope="col" id="mb_list_authcheck" style='min-width:130px;' rowspan="2"><?php echo subject_sort_link('mb_level', '', 'desc') ?>등급</a></th>
					<th scope="col" rowspan="2" id="mb_list_id" class="td_name center"><?php echo subject_sort_link('mb_id') ?>아이디/회원상세내역</a></th>
					<th scope="col" rowspan="2" id="mb_list_id" class="td_name center"><?php echo subject_sort_link('mb_name') ?>이름</a></th>
					<?if($mode=='del'){?><th scope="col" rowspan="2" id="mb_list_member" class="td_leave_date"><?php echo subject_sort_link('mb_name') ?>탈퇴일</a></th><?}?>
					<th scope="col" id="mb_list_auth" class="bonus_eth" rowspan="2"><?php echo subject_sort_link('total_fund') ?>총 <?=ASSETS_CURENCY?> 잔고<br></a></th>
					<th scope="col" id="mb_list_auth2" class="bonus_calc" rowspan="2"><?php echo subject_sort_link('deposit_point') ?>총입금  <br></th>
					<th scope="col" id="mb_list_auth2" class="bonus_bb" rowspan="2">총 상품권 구매<br>(+수수료)</th>
					

					<!-- <th scope="col" id="mb_list_auth2" class="bonus_bb" rowspan="2">상품권구매<br>(+수수료)</th> -->
					<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_benefit" rowspan="2">총 스테이킹 구매<br>(+수수료)</th>
					<!-- <th scope="col" id="mb_list_auth2" class="bonus_bb bonus_benefit" rowspan="2">스테이킹<br>수수료<br></th> -->
					<th scope="col" id="mb_list_auth2" class="" rowspan="2"><?php echo subject_sort_link('mb_balance') ?> 누적 보너스<br><?=ASSETS_CURENCY?></th>
					<th scope="col" id="mb_list_auth2" class="bonus_usdt before_eth" style='color:white !important' rowspan="2"><?php echo subject_sort_link('mb_shift_amt') ?>총 출금<br>(+수수료)<br></th>

					<th scope="col" id="mb_list_auth" class="bonus_eth" rowspan="2"><?php echo subject_sort_link('total_eth_fund') ?>총 <?=WITHDRAW_CURENCY?> 잔고<br></a></th>
					<th scope="col" id="mb_list_auth2" class="" rowspan="2"><?php echo subject_sort_link('mb_balance_eth') ?>누적 보너스<br><?=WITHDRAW_CURENCY?></th>
					<th scope="col" id="mb_list_auth2" class="bonus_usdt" style='color:white !important' rowspan="2"><?php echo subject_sort_link('mb_amt_eth') ?>출금<?=WITHDRAW_CURENCY?><br>(+수수료)<br></th>
					<th scope="col" id="mb_list_member"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>최종접속</a></th>
					<th scope="col" rowspan="2" id="mb_list_mng">관리</th>
				</tr>

				<tr>
					<th scope="col" id="mb_list_join"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>가입일</a></th>
				</tr>

			</thead>

			<tbody>
				<?php
				for ($i = 0; $row = sql_fetch_array($result); $i++) {
			
					// 접근가능한 그룹수
					$sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
					$row2 = sql_fetch($sql2);

					$group = '';
					if ($row2['cnt'])
						$group = '<a href="./boardgroupmember_form.php?mb_id=' . $row['mb_id'] . '">' . $row2['cnt'] . '</a>';

					if ($is_admin == 'group') {
						$s_mod = '';
					} else {
						$s_mod = '<a href="./member_form.php?' . $qstr . '&amp;w=u&amp;mb_id=' . $row['mb_id'] . '">회원수정</a>';
					}

					$leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
					$divide_date = $row['mb_divide_date'] ? $row['mb_divide_date'] : date('Ymd', G5_SERVER_TIME);
					$intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

					$mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);

					$mb_id = $row['mb_id'];

					$total_deposit = $row['mb_deposit_point'] + $row['mb_deposit_calc'];
					$total_bonus = $row['mb_balance'];
					$total_amt = $row['mb_shift_amt'];
					$total_fund = $total_deposit + $total_bonus - $total_amt;

					$leave_msg = '';
					$intercept_msg = '';
					$intercept_title = '';
					if ($row['mb_leave_date']) {
						$mb_id = $mb_id;
						$leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
					} else if ($row['mb_intercept_date']) {
						$mb_id = $mb_id;
						$intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
						$intercept_title = '차단해제';
					}
					if ($intercept_title == '')
						$intercept_title = '차단하기';

					$address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

					$bg = 'bg' . ($i % 2);

					switch ($row['mb_certify']) {
						case 'hp':
							$mb_certify_case = '휴대폰';
							$mb_certify_val = 'hp';
							break;
						case 'ipin':
							$mb_certify_case = '아이핀';
							$mb_certify_val = '';
							break;
						case 'admin':
							$mb_certify_case = '관리자';
							$mb_certify_val = 'admin';
							break;
						default:
							$mb_certify_case = '&nbsp;';
							$mb_certify_val = 'admin';
							break;
					}

				?>


					<tr class="<?php echo $bg; ?>">
						<td headers="mb_list_chk" class="td_chk" rowspan="2">
							<input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
							<label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo get_text($row['mb_nick']); ?>님</label>
							<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
						</td>

						<td headers="mb_list_member" class="td_mbgrade" rowspan="2" style="width:70px;max-width:70px;">
							<?php echo get_member_level_select("mb_level[$i]", 0,10, $row['mb_level']) ?>
						</td>

						<td headers="mb_list_id" rowspan="2" class="td_name td_id <?if($row['mb_divide_date'] != ''){echo 'red';}?>" style="min-width:140px; width:auto">
						<a href="./member_history.php?mb_id=<?=$mb_id?>" target="_blank"><?php echo $mb_id ?></a>
						</td>
						<td rowspan="2" class="td_name " style='width:70px;'><?php echo get_text($row['mb_name']); ?></td>
		
						
						<?if($mode=='del'){?><th scope="col" rowspan="2" class="td_mbstat" style='letter-spacing:0;'><?=$row['mb_leave_date']?></th><?}?>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($total_fund,ASSETS_CURENCY) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($row['mb_deposit_point'],ASSETS_CURENCY) ?></td>
						<!-- <td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($row['mb_deposit_calc'],ASSETS_CURENCY) ?></td> -->
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?=$total_purchase_member[$row['mb_id']] ? shift_auto($total_purchase_member[$row['mb_id']],ASSETS_CURENCY)  : 0?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= $total_staking_member[$row['mb_id']] ? shift_auto($total_staking_member[$row['mb_id']],ASSETS_CURENCY)  : 0?></td>
						
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto($total_bonus,ASSETS_CURENCY) ?></td>
						<td headers="mb_list_auth" class="td_mbstat before_eth" style='color:red' rowspan="2"><?= shift_auto($total_amt,ASSETS_CURENCY) ?></td>

						<!-- ETH -->
						<td headers="mb_list_auth" class="td_mbstat " rowspan="2" ><?= shift_auto($row['mb_balance_eth'] - $row['mb_amt_eth']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" rowspan="2" ><?= shift_auto($row['mb_balance_eth']) ?></td>
						<td headers="mb_list_auth" class="td_mbstat" style='color:red' rowspan="2"><?= shift_auto($row['mb_amt_eth']) ?></td>
						<td headers="mb_list_lastcall" class="td_date"><?php echo substr($row['mb_today_login'], 2, 8); ?></td>
						<td headers="mb_list_mng" rowspan="2" class="td_mngsmall" style="width:100px;"><?php echo $s_mod ?></td>
					
					</tr>
					<tr class="<?php echo $bg; ?>">
						<td headers="mb_list_join" class="td_date"><?php echo substr($row['mb_datetime'], 2, 8); ?></td>
					</tr>

				<?php
				}
				if ($i == 0)
					echo "<tr><td colspan=\"" . $colspan . "\" class=\"empty_table\">자료가 없습니다.</td></tr>";
				?>
			</tbody>
		</table>
	</div>

	<div class="btn_list01 btn_list">
		<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
		<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
	</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>

<script>
	function fmemberlist_submit(f) {
		if (!is_checked("chk[]")) {
			alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
			return false;
		}

		if (document.pressed == "선택삭제") {
			if (!confirm("선택한 회원을 정말 삭제하시겠습니까?")) {
				return false;
			}
		}
		return true;
	}

	function level_search(param) {
		$('#search_bar #level').val(param);
		//console.log($('#search_bar #level').val());
		$('#search_bar').submit();
	}

	function grade_search(param) {
		$('#search_bar #grade').val(param);
		//console.log($('#search_bar #level').val());
		$('#search_bar').submit();
	}

	function nation_search(param) {
		$('#search_bar #nation').val(param);
		//console.log($('#search_bar #nation').val());
		$('#search_bar').submit();
	}

	// 엑셀 다운로드
	$('#excel_btn').on("click", function() {

		var s_date = $('#s_date').val();
		var e_date = $('#e_date').val();
		//var idx_num = $('.select-btn').val();
		var idx_num = '';
		var ck_box = true;
		$('.ckbox').each(function() {
			if ($(this).prop('checked')) {
				if (ck_box == true) {
					ck_box = false;
					idx_num += $(this).val();
				} else {
					idx_num += '_' + $(this).val();
				}
			}
		})
		//console.log("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?= $idx ?>");

		window.open("/excel/metal.php?s_date=" + s_date + "&e_date=" + e_date + "&idx_num=" + idx_num + "&idx=<?= $idx ?>");
	});

	// 검색 키워드 '전체'일 경우 inputbox required 해제 처리
	$(function() {
		if($('#sfl').val() == 'all') {
			$('#stx').removeAttr('required').removeClass('required');
		} else {
		}
	});
	
	$('#sfl').change(function() {
		if($('#sfl').val() == 'all') {
			$('#stx').val("");
			$('#stx').removeAttr('required').removeClass('required');
		} else {
			$('#stx').attr('required', true).addClass('required');
		}
	});
</script>

<?php
include_once('./admin.tail.php');
?>