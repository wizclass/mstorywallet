<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./bonus_inc.php');

$g5['title'] = '보너스지급 및 지급내역';
include_once('../admin.head.php');

$token = get_token();
auth_check($auth[$sub_menu], 'r');


// 기간설정
if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-7 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

if($_GET['start_dt']){
	$fr_date = $_GET['start_dt'];
}
if($_GET['end_dt']){
	$to_date = $_GET['end_dt'];
}


$sql = "select * from {$g5['bonus_config']} where used = 1 order by no asc";
$list = sql_query($sql);




// 보너스검색 필터 
$allowcnt=0;
for ($i=0; $row=sql_fetch_array($list); $i++) {

	if($i != 0){
		$nnn="allowance_chk".$i;
		$html.= "<input type='checkbox' class='search_item' name='".$nnn."' id='".$nnn."'";

		if($$nnn !=''){
			$html.=" checked='true' ";
		}

		$html.=" value='".$row['code']."'><label for='".$nnn."' class='allow_btn'>". $row['name']."보너스</label>";
	}

	if(${"allowance_chk".$i}!=''){
		if($allowcnt==0){
			$sql_search .= " and ( (allowance_name='".${"allowance_chk".$i}."')";
		}else{
			$sql_search .= "  or ( allowance_name='".${"allowance_chk".$i}."' )";
		}
			$qstr.='&'.$nnn.'='.$row['allowance_name'].${"allowance_chk".$i};

		$allowcnt++;
	}
}
if ($allowcnt>0) $sql_search .= ")";



// 수당으로 검색
if(($allowance_name) ){
	$sql_search .= " and (";
		if($chkc){
		$sql_search .= " allowance_name='".$allowance_name."'";
		}
 $sql_search .= " )";
}

// 검색기간검색
if($fr_date){
	$sql_search .= " and day >= '{$fr_date}' ";
	$qstr .= "&start_dt=".$fr_date;
}
if($to_date){
	$sql_search .= " and day <= '{$to_date}'";
	$qstr .= "&end_dt=".$to_date;
}

// 이름검색
if ($stx) {
    $sql_search .= " and ( ";
	if(($sfl=='mb_id') || ($sfl=='mb_id')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
          
	}else{
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
          
    }
    $sql_search .= " ) ";
}

$sql_common = " from {$g5['bonus']} where (1) ";
$sql_order='order by day desc';


$sql = " select count(*) as cnt
		{$sql_common}
		{$sql_search}
		{$sql_order} ";
		
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$colspan = 7;
$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함



$sql = "select * 
	{$sql_common}
	{$sql_search}
	{$sql_order}
	limit {$from_record}, {$rows} ";


$excel_sql = urlencode($sql);
$result = sql_query($sql);
$send_sql = $sql;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

$qstr.='&fr_date='.$fr_date.'&to_date='.$to_date.'&chkc='.$chkc.'&chkm='.$chkm.'&chkr='.$chkr.'&chkd='.$chkd.'&chke='.$chke.'&chki='.$chki;
$qstr.='&diviradio='.$diviradio.'&r='.$r;
$qstr.='&stx='.$stx.'&sfl='.$sfl;
$qstr.='&aaa='.$aaa;


include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');



?>   


<link href="<?=G5_ADMIN_URL?>/css/scss/bonus/bonus_list.css" rel="stylesheet">
<script src="../../excel/tabletoexcel/xlsx.core.min.js"></script>
<script src="../../excel/tabletoexcel/FileSaver.min.js"></script>
<script src="../../excel/tabletoexcel/tableExport.js"></script>

<div class="local_desc01 local_desc">
    <p>
		공통 : 보너스기준일자로 각 보너스지급버튼 클릭<br>
		<strong>직급승급 : </strong>① 마이닝 수당 전체 지급후 마지막에 승급 실행(데이터 기록)<br>
		<!-- <span style='margin-left:155px;'></span>② 21일~ 말일 실행시 - 이번달 2분기(15~말일) 매출로 정산<br>
		<span style='margin-left:155px;'></span>③  1일 ~ 13일 실행시 - 지난달 2분기(15~말일) 매출로 정산
 -->
	</p>
</div>



<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<div class="local_ov01 local_ov white" style="border-bottom:1px dashed black;">

	<li class="right-border outbox{">
	<label for="to_date" class="sound_only">기간 종료일</label>
	<input type="text" name="to_date" value="<?php if($to_date){echo $to_date; }else{echo date("Ymd");} ?>" id="to_date" required class="required frm_input date_input" size="13" maxlength="10"> 
	
	
	<input type="radio" name="price" id="pv" value='pv' checked='true' style="display:none;">
		<br><span >보너스계산 기준일자</span>
	</li>

	<?
	$sql = "select * from {$g5['bonus_config']} where used = 1 order by no";
	$list = sql_query($sql);

	for($i=0; $row = sql_fetch_array($list); $i++ ){?>
		<?
			$code = $row['code'];	
			if($i != 0){
		?>
		
			<?if($code != 'rank'){?>
				<li class='outbox'>
				<input type='submit' name="act_button" value="<?=$row['name']?> 보너스 지급"  class="frm_input benefit" onclick="bonus_excute('<?=$code?>','<?=$row['name']?>');">
				<br><input type="submit" name="act_button" value="<?=$row['name']?> 보너스 내역"  class="view_btn" onclick="bonus_view('<?=$code?>');">
				</li>
			<?}else{?>
				<li class='outbox left-border'>
				<button type='button' name="act_button"  class="frm_input benefit" onclick="bonus_excute('<?=$code?>','<?=$row['name']?>');"> <i class="ri-medal-fill" style='font-size:16px; vertical-align:sub'></i> 직급 승급 </button>
				<br><input type="submit" name="act_button" value="회원 <?=$row['name']?> 내역"  class="view_btn" onclick="bonus_view('<?=$code?>');">
				</li>
			<?}?>
		
		<?}?>
	<?}?>

	<!-- <li class="outbox left-border">
		<input type="submit" name="act_button" value=" 보너스지급 취소(되돌리기)"  class="frm_input benefit red" onclick="bonus_cancle();">
	</li> -->
	<!-- <?if($member['mb_id'] == 'admin'){?>
		<li class="outbox left-border">
			<input type="submit" name="act_button" value="보너스초기화"  class="frm_input benefit red" onclick="bonus_reset();">
		</li>
		<li class="outbox left-border">
			<input type="submit" name="act_button" value="승급테스트DB생성"  class="frm_input benefit black" onclick="bonus_dumy();">
		</li>
	<?}?> -->
</div>


<!--
<?if($member['mb_id'] = 'admin'){?>
<div class="sysbtn">
	수동관리 :: 
	<a href="./member_grade.php" class="btn btn2" >멤버 등급(grade) 수동 갱신</a>
	
	<a href="#" class="btn btn2" onclick="clear_db('balance');">멤버 보너스,V7,매출전환,level 초기화(출금,전환 제외)</a>
	<a href="#" class="btn btn2" onclick="clear_db('amt');">멤버 출금, 전환 내역 초기화</a>-->
	<!--<a href="#" class="btn btn3" onclick="clear_db('pack_order');">B팩,Q팩 구매 DB 초기화</a>
	<a href="#" class="btn btn2" onclick="clear_db('soodang');">보너스지급 내역 전체 초기화</a>
</div>
<?}?>
-->


<form name="fsearch" id="fsearch" class="local_sch01 local_sch" style="clear:both;padding:10px 20px 20px;" method="get" >
	
			<label for="sfl" class="sound_only">검색대상</label>
			<select name="sfl" id="sfl">
				<option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>>
				<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>회원이름</option>
			</select>

			<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
			<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
			| 검색 기간 : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input" value="<?=$fr_date?>" /> 
			~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input" value="<?=$to_date?>"/>
			
			<?=$html?>
		
			<input type="submit" class="btn_submit search" value="검색"/>
			<input type="button" class="btn_submit excel" id="btnExport"  data-name='zeta_bonus_list' value="엑셀 다운로드" />
		
	</div>
</form>


<form name="benefitlist" id="benefitlist">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">
<div class="local_ov01 ">
    <?php echo $listall ?>
    전체 <?php echo number_format($total_count) ?> 건 
</div>
<div class="tbl_head01 tbl_wrap">
    <table id='table'>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
		<th scope="col">보너스날짜</th>
		<th scope="col">회원아이디</th>
        <th scope="col">보너스이름</th>
        <th scope="col">발생보너스</th>
		<th scope="col">보너스단위</th>
		<th scope="col">보너스근거</th>
		<th scope="col">지급시간</th>				
    </tr>
    </thead>
    <tbody>

	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {
		$bg = 'bg'.($i%2);
		$soodang = $row['benefit'];
		$soodang_sum += $soodang;
	?>

    <tr class="<?php echo $bg; ?>">
		<td width='100'><? echo $row['day'];?></td>
		<td width="100" style='text-align:center'>
			<a href='/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>'><?php echo get_text($row['mb_id']); ?></a>
		</td>

		<td width='80' style='text-align:center'><?=get_text($row['allowance_name']); ?></td>
		<td width="100" class='bonus'><?=Number_format($soodang,BONUS_NUMBER_POINT) ?></td>
		<td width="30" class='bonus'><?=$row['currency']?></td>
		

		<td width="300"><?= $row['rec']."<br> <span class='adm'> [".$row['rec_adm']."]</span>" ?></td>
		<td width="100" class='date'><?=$row['datetime']?></td>
    </tr>

    <?}
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>

	<tfoot>
	<tr class="<?php echo $bg; ?>">
		<td colspan=3>TOTAL :</td>
		<td width="150" class='bonus' style='color:red'><?=number_format($soodang_sum,BONUS_NUMBER_POINT)?></td>
        <td ></td>
		<td ></td>
		<td ></td>
    </tr>
	</tfoot>
    </table>
</div>
</form>



<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>



<!--
<div class="btn_confirm01 btn_confirm">
	<? if($what=='u') { ?>  <input type="submit" id="submit" value="수정" class="btn_submit"> <? } else{  ?> <input type="submit" id="submit" value="등록" class="btn_submit">   <? } ?>
</div> 
</form>
-->

</section>


<!--<script type="text/javascript" src="/adm/js/prototype.js"></script>-->
<script type="text/javascript" src="/js/common.js"></script>


<script>
var str ='';
$(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	$("#start_dt, #end_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	$('.search_item:checked').each(function() {
		$(this).addClass('active');
	});
	
	$('.search_item').on('click',function(){
		var chk = $(this).is(":checked");
		if(chk){
			$(this).addClass('active');
		}else{
			$(this).removeClass('active');
		}
	});
});



function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

function bonus_excute(n,name){
	// console.log("bonus_excute");
	if(name == '승급'){
		var tx = '을 실행';
	}else{
		var tx = '보너스를 지급';
	}
	if(!confirm(document.getElementById("to_date").value + '일 ' + name + tx +' 하시겠습니까?')){return false;}

	str=str+'to_date='+document.getElementById("to_date").value;
	location.href='/adm/bonus/bonus.'+ n +'.php?'+str; 
}


function bonus_view(n){
	console.log("bonus_view");
	var strdate = document.getElementById("to_date").value;
	file_src = n+"_"+strdate+".html";
	file_path = g5_url+"/data/log/"+n+"/"+file_src ; //롤다운
	console.log(file_path);
	
	if(UrlExists(file_path)){
		window.open(file_path); 
	}else{
		alert('해당내역이 없습니다.');
	}
	
}

function bonus_cancle(){
	
	date = document.getElementById("to_date").value;
	
	var pre = confirm(date+' 보너스지급 전으로 되돌립니다.');

    if(pre == true){
	 $.ajax({ 
          type : "POST", 
          url : "./bonus_cancle.php", 
          data:{to_date : date},
          error : function() { 
              alert('실패!!'); 
         }, 
         success : function(data) { 
            alert(data);
			location.reload();
        } 
    }); 
    }else{
        return;
	}
}


function bonus_reset(){
	
	date = document.getElementById("to_date").value;
	var pre = confirm('보너스내역,보너스지급,주문내역,지급로그,G테이블 내역을 초기화합니다');
    if(pre == true){
	 	$.ajax({ 
			type : "POST", 
			url : "./bonus_reset.php",
			error : function() { 
				alert('실패!!'); 
			}, 
			success : function(data) { 
				if(data.code == '0000'){
					alert('삭제처리되었습니다.');
					location.reload();
				}
			} 
    	}); 
    }else{
        return;
	}

}

function bonus_dumy(){
	
	date = document.getElementById("to_date").value;
	var pre = confirm('0. 관리자 제외 멤버 test1부터 test10까지 10ETH 잔고 부여  \n1. 관리자 제외 멤버 test1부터 test10까지 ETH > Package3 구매\n2. G0 아바타 더미데이터 5개생성\n ');
    if(pre == true){
	 	$.ajax({ 
			type : "POST", 
			url : "./bonus_dumy.php",
			error : function() { 
				alert('실패!!'); 
			}, 
			success : function(data) { 
				alert(data);
				location.reload();
			} 
    	}); 
    }else{
        return;
	}

}



/* 하단 스크립트 사용안함 */

	// function go_calc(n)
	// {
	// 	if(document.getElementById("pv").checked==true){

	// 		str=str+'&price=pv';
				
	// 	}else if(document.getElementById("bv").checked==true){
	// 		str=str+'&price=bv';
	// 	}else{
	// 		str=str+'&price=receipt';
	// 	}

	// 	var day_point = document.getElementById("to_date").value;

	// 	str=str+'&to_date='+document.getElementById("to_date").value;
	// 	str=str+'&fr_date='+document.getElementById("to_date").value;

		
		
	// 	switch(n){
	// 		case 0: 
	// 			location.href='bonus.daily.pay.php?'+str;         //일일보너스
	// 			break;
	// 		case 1: 
	// 			location.href='bonus.benefit.immediate.php?'+str;// 추천보너스
	// 			break;
	// 		case 2: 
	// 			location.href='bonus.member.level.php?'+str;// 멤버승급
	// 			break;

	// 		case 3: 
	// 			location.href='bonus.qpack.php?'+str;// Q팩
	// 			break;
	// 		case 4:
	// 			location.href='bonus.bpack.php?'+str;// B팩
	// 			break;
	// 		case 5: 
	// 			location.href='bonus.upstair.php?'+str;         //임시 해당일 업스테어
	// 			break;
	// 		case 6: 
	// 			location.href='bonus.all.php?'+str;         //전체보너스지급
	// 			break;
	// 		case 7: 
	// 			location.href='bonus.auto.php?'+str;         //전체보너스지급
	// 			break;
	// 		case 8: 
	// 			location.href='bonus.Bpack_auto.php?'+str;         //B팩
	// 			break;
	// 		case 9: 
	// 			location.href='bonus.avatar_exc.php?'+str;         //아바타
	// 			break;
	// 	}
		
	// }


	// function view_calc(n)
	// {
	// 	var day = document.getElementById("to_date").value;

	// 	switch(n){
	// 		case 0:
	// 			       //일배당
	// 			break;
	// 		case 1:
	// 			file_src = g5_url+"/log/roledown/roledown_"+day+".html"; //롤다운

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 		case 2:
	// 			file_src = g5_url+"/log/binary/binary_"+day+".html"; //바이너리

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
				
	// 		case 3:
				
	// 			break;
	// 		case 4:
	// 			file_src = g5_url+"/log/team/team_"+day+".html"; //롤다운

	// 			if(UrlExists(file_src)){
	// 				window.open(file_src); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 		case 5:
	// 			file_src0 = g5_url+"/log/recom/binary_recom_"+day+"_0.html"; //바이너리 매칭
	// 			file_src1 = g5_url+"/log/recom/binary_recom_"+day+"_1.html"; //바이너리 매칭		
				
	// 			console.log(file_src0);

	// 			if(UrlExists(file_src0)){
	// 				window.open(file_src0); 
	// 			}else{
	// 				if(UrlExists(file_src1)){
	// 					window.open(file_src1); 
	// 				}else{
	// 					alert('해당내역이 없습니다.');
	// 				}
	// 			}
	// 			break;

	// 		case 6:
	// 			file_src2 = g5_url+"/log/recom/binary_recom_"+day+"_2.html"; //바이너리 매칭

	// 			console.log(file_src2);

	// 			if(UrlExists(file_src2)){
	// 				window.open(file_src2); 
	// 			}else{
	// 				alert('해당내역이 없습니다.');
	// 			}
	// 			break;
	// 	}

// }
</script>

<?php
include_once ('../admin.tail.php');
?>
