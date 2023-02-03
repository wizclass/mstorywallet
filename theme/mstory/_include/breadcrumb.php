<?
//입금액
$mysales = $member['mb_deposit_point'];


// 공지사항
/* 
$notice_sql = "select * from g5_write_notice where wr_1 = '1' order by wr_datetime desc limit 0,1";
$notice_sql_query = sql_query($notice_sql);
$notice_result_num = sql_num_rows($notice_sql_query); */

// 스테이킹 총액
$total_staking_sql = "select sum(od_cart_price) as total_staking FROM {$g5['g5_shop_order_table']} WHERE mb_id = '{$member['mb_id']}' and od_refund_price < 1";
$total_staking_row = sql_fetch($total_staking_sql);

$sql = " select * from maintenance";
$nw = sql_fetch($sql);

function check_value($val){
	if($val == 1){
		$icon = "<i class='ri-checkbox-circle-line icon value_yes'></i>";
	}else{
		$icon = "<i class='ri-close-circle-line icon value_no'></i>";
	}
	return $icon;
}

function side_exp($val){
	return "<span class='sideexp'>".$val."</span>";
}

$title = 'Dashboard';
?>

<section class='breadcrumb'>
		<!-- 공지사항 사용안함-->
		<?if($notice_result_num > 0){ ?>
			
			<div class="col-sm-12 col-12 content-box round dash_news" style='margin-bottom:-10px;'>
				<h5>
					<span class="title">공지사항</span>
					
					<i class="ri-close-circle-line close_news" style="font-size: 30px;float: right;cursor: pointer;"></i>
					<button class="f_right btn line_btn close_today" >
						<span > 오늘하루 열지않기</span>
					</button>
					<!-- <img class="close_news f_right small" src="<?=G5_THEME_URL?>/_images/close_round.gif" alt="공지사항 닫기"> -->	
				</h5>

				<?while( $row = sql_fetch_array($notice_sql_query) ){ ?>
				<div>
					<span><?=$row['wr_content']?></span>
				</div>
				<?}?>
			</div>
		<?}?>

		
		<div class="user-info">
			<!-- 회원기본정보 -->			
			<div class='user-content dp-flex'>
				<div class="user_img_wrap">
					<span class='userid user_level'><img src="<?=G5_THEME_URL?>/img/profile.png" alt=""></span>
				</div>
				<div class="user_info_wrap">
					<h4 class='bold'><?=$member['mb_name']?>님</h4>
					<? if($member['mb_level'] != 10){ ?>
						<h4 class='mygrade badge'><?=$mb_level_array[$member['mb_level']]?></h4>
					<?}?>
					<h4 class='user_id'><?=$member['mb_id']?></h4>	
				</div>
			</div>			

			<!-- 회원상세정보 -->
			<div class="total_view_wrap">
				<div class="total_view_top">
					<?
						$memlev = $member['mb_level']; 
						if($memlev > 1){$numrow = 'col-4';}else{$numrow = 'col-6';}
						if(!ETH_AVAILABLE){$numrow = 'col-6';}
					?>
					<ul class="row top">
						<?if($memlev == 0){?>
							<li class="<?=$numrow?>"><dt class=" title"><?=ASSETS_CURENCY?></dt></li>
							<li class="<?=$numrow?>"><dd class=" value"><?=$shift_total_token_balance?></dd></li>
							<style>
								.breadcrumb .total_view_wrap .total_view_top li + li::before {margin: 0}
							</style>
						<?}else if($memlev > 0){?>
							<li class="<?=$numrow?>">
								<dt class=" title"><?=ASSETS_CURENCY?></dt>
								<dd class=" value"><?=$shift_total_token_balance?></dd>
							</li>

							<?if(ETH_AVAILABLE){?>
								<?if($memlev > 1){?>
									<li class="<?=$numrow?>">
										<dt class="title">ETH</dt>
										<dd class="value"><?=$shift_total_eth_balance?></dd>
									</li>
								<?}?>
							<?}?>
							
							<li class="<?=$numrow?>">
								<dt class="title">스테이킹</dt>
								<dd class="value"><?=shift_auto($total_staking_row['total_staking'],ASSETS_CURENCY)?> </dd>
							</li>
						<?}?>
					</ul> 
				</div>
				<?php
					if($nw['nw_change'] == 'Y'){ ?>
					<div class="quote_wrap" id="price_refresh">
						<img class="refresh" src="<?=G5_THEME_URL?>/img/refresh.svg" alt=""> 현재 <?=ASSETS_CURENCY?> 시세: <span id="coin_price"><?=shift_auto($coin['esgc_krw'],BALANCE_CURENCY)?></span> <?=BALANCE_CURENCY?>
					</div>
				<?php } ?>
			
		</div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/egjs-jquery-transform/2.0.0/transform.min.js" integrity="sha512-vOc3jz0QulHRiyMXfp676lHxeSuzUhfuw//VUX12odAmlUbnKiXH4GQxBRqwKhF3Mkswqr5ILY9MtEM4ZwcS2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- 펼쳐보기 -->
<script>

		document.querySelector('#price_refresh').addEventListener('click',()=>{
			document.querySelector('#price_refresh .refresh').animate([{transform : 'rotate(360deg)'}],{duration:1000});
			document.querySelector('#coin_price').innerHTML = "<?=shift_auto($coin['esgc_krw'],BALANCE_CURENCY)?>";
		})
		
		function collapse(id, mode) {
			
			$('.fold_img_wrap img').attr('src','<?=G5_THEME_URL?>/img/arrow_up.png');
			
			var user_height = $('.user-info').height();
			console.log(user_height)
			if ($(id).css("display") == "none") {
				$(id).css("display", "block");
				$(id).animate({
					height: user_height - 80
				}, 500, function() {
		
					$('.fold_wrap p').text('접기');
				});
				animateRotate2(0)
			} else {
				$(id).animate({
					height: "0px",
				}, 500, function() {
					$(id).css("display", "none");
					$('.fold_wrap p').text('펼쳐보기');
				});
				animateRotate(180);
			}
		}

		function animateRotate(d) {
			$('.fold_img_wrap').animate({
				'-moz-transform':'rotateX('+d+'deg)',
				'-webkit-transform':'rotateX('+d+'deg)',
				'-o-transform':'rotateX('+d+'deg)',
				'-ms-transform':'rotateX('+d+'deg)',
				'transform':'rotateX('+d+'deg)'
			});
		}

		function animateRotate2(d) {
			$('.fold_img_wrap').animate({
				'-moz-transform':'rotateX('+d+'deg)',
				'-webkit-transform':'rotateX('+d+'deg)',
				'-o-transform':'rotateX('+d+'deg)',
				'-ms-transform':'rotateX('+d+'deg)',
				'transform':'rotateX('+d+'deg)'
			});
		}

	$(document).ready(function(){
		// move(<?=bonus_per()?>,1);

		// 공지사항 - 하단공지로 사용안함
		var notice_open = getCookie('notice');

		if(notice_open == '1'){
			$('.dash_news').css("display","none");
		}else{
			$('.dash_news').css("display","block");
		}

		
		$('.close_news').click(function(){
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});

		$('.close_today').click(function(){
			setCookie('notice', '1', 1);
			$('.dash_news').css("display","none");
			$('.notice_open').css("display","block");
		});


		$('.notice_open').click(function(){
			$('.dash_news').css("display","block");
			$(this).css("display","none");
		});

		$(".extra").on('click',function(){
			location.href= g5_url + '/page.php?id=mypool&stage=super#minings'
		})
	});
</script>

