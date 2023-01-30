<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_PATH . '/util/package.php');

login_check($member['mb_id']);

if($nw['nw_purchase'] == 'Y'){
	$nw_purchase = 'Y';
	// include_once(G5_PATH.'/service_pop.php');
}else{
	$nw_purchase = 'N';
	alert("현재 서비스를 이용할수없습니다.");
}

$title = 'upstairs';

// $pack_sql = "SELECT it_id, it_name,it_price,it_point,it_supply_point,it_use,it_option_subject, ca_id,ca_id3, it_maker FROM g5_shop_item WHERE it_use > 0 order by it_order asc ";
// $pack_result = sql_query($pack_sql);

$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$sql_common = "FROM g5_shop_order";
$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
$sql_search .= " AND od_date between '{$fr_date}' and '{$to_date}' ";

$sql = " select count(*) as cnt
{$sql_common}
{$sql_search} ";

$row = sql_fetch($sql);

$total_count = $row['cnt'] + $reset_count;

$rows = 15; //한페이지 목록수
$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = "SELECT mb_id, od_cart_price, od_receipt_time, od_name, od_cash, od_settle_case, upstair, od_status,od_date,pv
{$sql_common}
{$sql_search} ";

$sql .= "order by od_receipt_time desc limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/default.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<style>
.product_buy_wrap .title{padding-right:0;}
.mining_ico{vertical-align: middle;}
.mining_ico, .mining_ico img{margin-left:5px;height:22px;}
.hash{color:white;font-weight:300;font-size:18px;letter-spacing:-0.25px;margin:5px 0 -10px;font-family:"Helvetica Neue","Apple SD Gothic Neo",sans-serif;    font-family: "Helvetica Neue","Apple SD Gothic Neo",sans-serif;
    background: rgba(0,0,0,0.2);
    padding: 0px 15px 2px 20px;
    border-radius: 10px;
    box-shadow: inset 1px 1px 2px rgb(0 0 0 / 80%), 1px 1px 1px rgb(255 255 255 / 30%);
    text-align: center;}

</style>

<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>

<main>
	<div class="container upstairs">
		<div class="upstairs_buy_wrap">
			<div class="package_wrap mt20">
				<div class="box-header">
					<div class="col-9">
						<h3 class="title upper" >패키지 상품</h3>
					</div>
				</div>
				<div class="box-body round">
					<div class="r_card_wrap ">
						<div class="row nopadding nomargin">
						<? 
						$row = get_shop_item();
						$count_items = count($row);
						if($count_items == 0) {
							echo "<div class='no_data'>패키지 상품이 존재하지 않습니다</div>";
						}else{
		
							for($i=1; $i <= $count_items; $i++){

							$origin_price = $usd_price * $row[$i-1]['it_price'];
							$sign = "원";

							$data_arr = array();
							array_push($data_arr, array(
								"it_id"=>$row[$i-1]['it_id'],
								"it_name"=>$row[$i-1]['it_name'],
								"it_price"=>$row[$i-1]['it_price'],
								"it_point"=>$row[$i-1]['it_point'],
								"it_cust_price"=>$row[$i-1]['it_cust_price'],
								"it_maker"=>$row[$i-1]['it_maker'],
								"it_supply_point"=>$row[$i-1]['it_supply_point'],
								"it_option_subject"=>$row[$i-1]['it_option_subject'],
								"it_model"=>$row[$i-1]['it_model'],
								"sign" => $sign
							));

							if($row[$i-1]['it_id'] == '2021091720'){
								$row_col = 'col-12 col-lg-12';
							}else{
								$row_col = 'col-6 col-lg-4';
							}
						?>
							<div class="<?=$row_col?> r_card_box">
								<div class="r_card r_card_<?=$i-1?>" data-row=<?=json_encode($data_arr,JSON_UNESCAPED_UNICODE)?>>
									<p class="title">
										<span style='vertical-align:middle'><?=$row[$i-1]['it_name']?></span>
								
										<span style='font-size:13px;float:right;line-height:36px;'><?=$row[$i-1]['it_option_subject']?></span>
										
									</p>
									<div class="b_blue_bottom"></div>
									<div >
										<div class='hash'>
											<?=$row[$i-1]['it_supply_point']?> <span class='f_small'>%</span>
										</div>
										<div class=" text_wrap">
											<div class="it_price">￦<?=Number_format($row[$i-1]['it_price'])?></div>
											<div class='origin_price'>VAT ￦<?=Number_format($row[$i-1]['it_price']*0.1)?></div>
										</div>
									</div>
								</div>
							</div>
						<?}
						} ?>
						</div>
					</div>
				</div>

				
			<div class="pakage_sale content-box round mt20" id="pakage_sale">
				<ul class="row">
					<li class="col-12">
						<h3 class="tit upper" >Package 상품구매</h3>
					</li>
					<!-- <li class="col-4">
						<select class="form-control" name="" id="coin_select">
							<option value="eth" selected>ETH</option>
							<option value="mbm">MBM</option>
						</select>
					</li> -->
				</ul>
				<div class='row' style="align-items: center;">
					<div class='col-5 current_currency coin'>선택 상품 금액 </div>
					
					<div class='col-1 shift_usd'><i class="ri-exchange-fill exchange"></i></div>
					
					<div class='col-6'>
						<input type="text" id="trade_total" class="trade_money input_price" placeholder="0" min=5 readonly>
						<!-- <span class='currency-right coin'><?=ASSETS_CURENCY?></span> -->
						<div id='shift_won'></div>
					</div>
				</div>

				<div class='row select_box' id='usd' style='margin-top:10px'>
					<div class='col-12'><h3 class='tit'> 구매가능잔고</h3></div>

					<div class='col-5 my_cash_wrap'>
						<!-- <input type='radio' value='eth' class='radio_btn' name='currency'><input type="text" id="trade_money_eth" class="trade_money" placeholder="0" min=5 data-currency='eth' readonly> -->
						<div>
							<input type="text" id="total_coin_val" class='input_price' value="<?=number_format($available_fund)?>" readonly>
							<span class="currency-right coin"><?=ASSETS_CURENCY?></span>
						</div>
					</div>
						
					<div class='col-1 shift_usd'><div class='ex_dollor'><i class="ri-arrow-right-fill"></i></div></div>

					<div class='col-6'>
						<input type="text" id='shift_dollor' class='input_price red' readOnly>
						<span class="currency-right coin "><?=ASSETS_CURENCY?></span>
					</div>
				</div>

				<div class="submit mt20">
					<button id="purchase" class="btn wd main_btn b_blue b_darkblue round" >구매</button>
					<button id="go_wallet_btn" class="btn wd main_btn b_green b_skyblue round" >입금</button>
				</div>
				
			</div>
		</div>

		<!--
		<div class="box-header ">
			<div class='col-9'>
				<h3 class="title upper" style='line-height:40px' >내 보유 패키지</h3>
			</div>
		</div>
			
		 <?
		$ordered_items = ordered_items($member['mb_id']);
		if(count($ordered_items) == 0) { ?>
				<div class="no_data box_on">내 보유 상품이 존재하지 않습니다</div>
		<?}else{?>
				
		<div class="box-body row slide_product">
		<?php	  
		for($i = 0; $i < count($ordered_items); $i++){	
			$color_num = substr($ordered_items[$i]['it_maker'],1,1); 
			
			if(count($ordered_items) > 3){$spread_average = 3;}else{$spread_average = 1;}
		?>		

			<div class="content-box3 product_buy_wrap pack_<?=$color_num?> col-11">
				<li class="row">
					<p class="title col-12"><?=strtoupper($ordered_items[$i]['it_name'])?></p>
				</li>
				<li class="row">
					<p class="value col-8">구매일 : <?=$ordered_items[$i]['od_time']?></p>
				</li>
			</div>

		<?php 
			echo "<script>slide_color('$color_num')</script>";
		} }?>
		</div> 
		
			
			<!-- 내 보유 상품 슬라이드 
			<script>
				$(document).ready(function(){
					var spread_average = '<?=$spread_average?>';
					$('.slide_product').slick({
						slide: 'div',
						speed: 300,
						slidesToShow : 1,
						autoplay: true,
						infinite: false,
						arrows : false,
						dots: true,
						customPaging: function(slick, index) {
							return "<div class='product_pagination'></div>"
						},
						responsive: [ // 반응형 웹 구현 옵션
						{  
							breakpoint: 2600, //화면 사이즈 960px
							settings: {
								//위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
								
								slidesToShow:spread_average 
							} 
						},
						{ 
							breakpoint: 768, //화면 사이즈 768px
							settings: {	
								//위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
								slidesToShow:1 
							} 
						}
						]
					});
					
				});
			</script>
			
		</div>
		-->

		<!-- <div class="col-sm-12 col-12 content-box round secondary mt20" > -->

		<div class="history_box content-box mt40">
			<h3 class="hist_tit" >Package 구매 내역</h3>

			<?if(sql_num_rows($result) == 0) {?>	
				<div class="no_data"> Package 구매 내역이 존재하지 않습니다</div>
			<?}?>

			<?while( $row = sql_fetch_array($result) ){
				if(strlen($row['od_name']) > 2){
					$od_name = "P0";
				}else{
					$od_name = $row['od_name'];
				}
			?>
				
			<div class="hist_con">
				<div class="hist_con_row1">
					<div class="row">
						<span class="hist_date"><?= $row['od_receipt_time'] ?></span>
						<span class="hist_value">$ <?=Number_format($row['od_cart_price'])?></span>
					</div>

					<div class="row">
						<h2 class="pack_name pack_f_<?=substr($od_name,1,1)?>"><?= strtoupper($row['od_name']) ?> </h2>
						<span class='hist_sub_price'><?=Number_format($row['od_cash'])?><?=$row['od_settle_case']?></span>
					</div>
				</div>
			</div>
			<?}?>

			<?php
			$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=upstairs&$qstr");
			echo $pagelist;
			?>
		</div>
	</div>
</main>

<?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

<div class="gnb_dim"></div>

</section>


<!-- <script src="<?= G5_THEME_URL ?>/_common/js/timer.js"></script> -->
<script>

$(function(){
	$(".top_title h3").html("<span >패키지구매</span>")
});

$(function(){

	var mb_id = "<?=$member['mb_id']?>";
	var mb_no = "<?=$member['mb_no']?>";

	// 시세
	var vat_price = 1.1;
	var usd_price = vat_price;
	
	// 패키지
	var data, it_id, it_name, it_price, func, od_id, it_supply_point, input_val, won_price,origin_bal,price_calc;
	var processing = true;

	/* window.onload = function(){
		getTime("<?=$next_rate_time?>");
		$('.select_box').removeClass('active');
		$('.select_box').first().addClass('active');
		$('.select_box').first().find('.radio_btn').prop('checked', true); 
		var radioVal = $('input[name="currency"]:checked').val();
		$('.current_currency > .txt').text(radioVal);
	} */
	
	// 패키지 리스트 선택
	$('.r_card').on('click',function(){
		data = $(this).data('row');
		console.log(data);

		it_id = data[0].it_id;
		it_name = data[0].it_name;
		it_maker = data[0].it_maker;
		it_price = data[0].it_price; //상품가격
		it_point = data[0].it_point; //PV
		it_supply_point = data[0].it_supply_point; //MP 
		won_price = data[0].it_cust_price;
		func = "new";
		od_id = "";
		origin_bal = '<?=$available_fund?>';
		price_calc = origin_bal - won_price ;
		change_coin = "원";

		change_coin_status();
	});

	/* $('#coin_select').on('change',function(){
		change_coin_status()
	}) */

	/* $('.upgade').click(function(){
		data = $(this).data('row_ordered');
		if(data.it_name != "M6"){
			it_id = data.upgrade_id
			it_name = data.it_name+"->"+data.upgrade_name
			it_price = data.upgrade_price - data.it_price

			it_supply_point = data.it_supply_point
			func = "upgrade"
			od_id = data.row.od_id
			change_coin_status()
		}else{
			alert("Worng Way")
		}
	}); */

	function change_coin_status(){
		$('#trade_total').val( '￦ ' + Price(it_price) );
		$('#shift_won').text( 'VAT 포함 : ￦' + Price(won_price) + '원' );
		$('#shift_dollor').val( Price(price_calc) );
		
		// 상품구매로 이동
		var scrollPosition = $('#pakage_sale').offset().top;
		window.scrollTo({top: scrollPosition, behavior: 'smooth'});
	}


	// 패키지구매
	$('#purchase').on('click', function(){
		var nw_purchase = '<?=$nw_purchase?>'; // 점검코드
		
		// 부분시스템 점검
		if(nw_purchase == 'N'){
			dialogModal('구매 처리 실패','<strong>현재 이용 가능 시간이 아닙니다.</strong>','warning');
			if(debug) console.log('error : 1');
			return false;
		}

		// 금액이 0 일때
		if( it_price == undefined || it_price == 0){
			dialogModal('구매 상품 확인','<strong>구매 상품을 선택해주세요.</strong>','warning');
			if(debug) console.log('error : 2' );
			return false;
		}

		// 잔고 확인 
		if(price_calc < 0){
			dialogModal('구매 가능 잔고 확인','<strong>구매 가능 잔고가 부족합니다.</strong>','warning');
			if(debug) console.log('error : 4' );
			return false;
		}

		/* if (confirm(it_name + '팩을 구매 하시겠습니까?')) {
			} else {
				return false;
			} 
		*/

		
		dialogModal('Package 상품구매 확인','<strong>'+ it_name + '팩을 구매 하시겠습니까?</strong>','confirm');

		$('#modal_confirm').on('click',function(){
			dimHide();

			if(processing){
			$.ajax({
				type: "POST",
				url: "/util/upstairs_proc.php",
				dataType: "json",
				async : false,
				data:  {
					"func" : func,
					"input_val" : won_price,
					"output_val" : it_price,
					"select_pack_name" : it_name,
					"select_pack_id" : it_id,
					"select_maker" : it_maker,
					"it_point" : it_point,
					"it_supply_point" : it_supply_point
				},
				success: function(data) {

					// 중복클릭방지
					processing = false;
					$('#purchase').attr("disabled", true);

					dialogModal('패키지 구매 처리','<strong>패키지 상품 구매처리가 정상 처리되었습니다.</strong>','success');

					$('.closed').on('click', function(){
						location.href="<?=G5_URL?>/page.php?id=upstairs";
					});
				},
				error:function(e){
					commonModal('패키지 구매 처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>',100);
				}
			});
		}else{
			commonModal('패키지 구매','<strong> 구매 처리 진행중입니다. 잠시 기다려주세요.</strong>',80);
		}
		
		});
		
		

	});

	// 입금하기
	$('#go_wallet_btn').click(function(e){
		if(won_price > 0){
			if(price_calc < 0){
				price_calc = price_calc * -1;
			}
			go_to_url('mywallet'+'&sel_price='+price_calc);
		}else{
			go_to_url('mywallet');
		}
	});

});

</script>


