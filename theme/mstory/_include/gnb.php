<?php
$myLang = 'kor';

if($_COOKIE['myLang'])
{
	$myLang = $_COOKIE['myLang'];
}

function national_flag($ncode, $return = 'icon')
{
	if ($return == 'icon') {
		switch ($ncode) {
			case 1:
				$tcode = 'us';
				break;
			case 81:
				$tcode = 'jp';
				break;
			case 82:
				$tcode = 'kr';
				break;
			case 84:
				$tcode = 'vn';
				break;
			case 86:
				$tcode = 'cn';
				break;
			case 66:
				$tcode = 'th';
				break;
			default:
				$tcode = 'etc';
		}
		$code_return = G5_IMG_URL . '/flag/' . $tcode . '.png';
		return $code_return;
	} else if ($return == 'ncode') {
	}
}
?>

<style>
	#gnb_anguage{padding:10px}
	.gnb_bottom{background:#f5f5f5}
	.hidden{display:none;}
</style>

<script>
$(document).ready(function(){

	function setCookie(cookie_name, value, days) {
	  var exdate = new Date();
	  exdate.setDate(exdate.getDate() + days);
	  var cookie_value = escape(value) + ((days == null) ? '' : ';    expires=' + exdate.toUTCString());
	  document.cookie = cookie_name + '=' + cookie_value;
	}

});
</script>


<section id="wrapper" class="<?if($menubar){echo "menu_back_gnb";}?>" >
<header>
	<?if($menubar){?>
	<div class="menuback">
		<a href="javascript:history.back();" class='back_icon'><i class="ri-arrow-left-s-line"></i></a>
	</div>
	<?}else{?>
	<div class="menu">
		<a href="#" class='menu_icon' style="vertical-align:sub"><i class="ri-menu-2-line" style="font-size:25px;vertical-align:middle;"></i></a>
	</div>
	<?}?>
	
	<?if(!$menubar){?>
	<nav class="left_gnbWrap">		
		<div class="gnb_top">
			<button type="button" class="close"><img src="<?=G5_THEME_URL?>/img/gnb/close.png" alt="close"></button>
		</div>
		<div class='user-content'>
			<ul class="user_wrap">
				<li class="">
					<p class='userid user_level'><img src="<?=G5_THEME_URL?>/img/profile.png" alt=""></p>
				</li>
				<li class="">
					<h4 class="font_weight user_name"><?=$member['mb_name']?>님<span class='mygrade badge'><?=$mb_level_array[$member['mb_level']]?></span></h4>
					<h4 class='user_id'><?=$member['mb_id']?></h4>
					
				</li>
			</ul>
		</div>
		<div class="b_line3"></div>
		<ul class="left_gnb">
			<!-- <li class="dashboard_icon <? if($_SERVER['REQUEST_URI'] === '/') {echo 'active';}?>">
				<a href="/">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">대시보드</div>
				</a>
			</li> -->
			<li class="profile_icon <? if($_GET['id'] === 'profile') {echo 'active';}?>">
				<a href="/page.php?id=profile">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">개인정보&보안설정</div>
				</a>
			</li>
			<?if($nw['nw_with'] == 'Y'){?>
			<li class="mywallet_icon <? if($_GET['id'] === 'mywallet') {echo 'active';}?>">
				<a href="/page.php?id=mywallet">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">입출금</div>
				</a>
			</li> 
			<?}?>
			
			<?php if($nw['nw_upstair'] == 'Y'){ ?>
				<?php if($member['mb_level'] != 0  ){ ?>
				<li class="staking_icon <?if($_GET['id'] === 'staking'){echo 'active';}?>">
					<a href="/page.php?id=staking">
						<div class="gnb_img_wrap"></div>
						<div class="gnb_title_Wrap">스테이킹</div>
					</a>
				</li>
				<? } ?>
			<?php } ?>
			<!-- <li class="giftcard_icon <?if($_GET['id'] === 'giftcard_purchase'){echo 'active';}?>">
				<a href="/page.php?id=giftcard_purchase">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">상품권</div>
				</a>
			</li> -->

			<li class="recommend_icon <?if($_GET['id'] === 'structure'){echo 'active';}?>">
				<a href="/page.php?id=structure">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">추천조직도</div>
				</a>
			</li>

			<li class="notice_icon <?if($_GET['id'] === 'news'){echo 'active';}?>">
				<a href="/page.php?id=news">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">공지사항</div>
				</a>
			</li>

			<li class="question_icon <?if($_GET['id'] === 'support_center' || $_GET['id'] === 'support_center.admin'){echo 'active';}?>">
				<a href="/page.php?id=support_center">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">1:1문의사항</div>
				</a>
			</li>

			<li class="reffer_icon <?if($_GET['id'] === 'referral_link'){echo 'active';}?>">
				<a href="/page.php?id=referral_link">
					<div class="gnb_img_wrap"></div>
					<div class="gnb_title_Wrap">추천인링크</div>
				</a>
			</li>

		</ul>	
		<div class="position_wrap">	
			<div class="gnb_language_wrap">
				<p class="language_text">언어선택</p>
				<? include_once(G5_THEME_PATH.'/_include/lang.php'); ?>
			</div>
			<div class="logout_wrap">
				<a href="javascript:void(0);" class="logout_pop_open"><i class="ri-logout-box-r-line"></i><span>로그아웃</span></a>
				<a href="/page.php?id=member_term"><span>회원약관</span></a>
			</div>	
		</div>
	</nav>
	<?}?>

	<div class="top_title">
		<h3>
			<!-- <a href="/"><img src= "<?=G5_THEME_URL?>/img/title.svg" alt="logo"></a> -->
			MSTORY WALLET

			<?if($member['mb_level'] >= 9){?>
				<a href= '<?=G5_ADMIN_URL?>' class='btn adm_btn ' style="line-height:16px;margin:0 0 0 10px;vertical-align:text-bottom">
					<i class="ri-user-settings-line"></i><span>Admin</span>
				</a>
			<?}?>
		</h3>
	</div>

	

</header>

<div id="loading" class="wrap-loading display-none"><span class="loading_img"></span></div>
<script>

	$( document ).ajaxStart(function() { 
		$('.wrap-loading').removeClass('display-none');
	});


	$( document ).ajaxStop(function() { 
		$('.wrap-loading').addClass('display-none');
	});

	$(function(){

		$(".left_gnb").on('scroll', function() {
			$(".left_gnbWrap").addClass('scroll');
		})
		$(".left_gnbWrap").removeClass('scroll');
		
		var left_gnb = $('.left_gnb');
		// console.log(left_gnb.height());
		if(left_gnb.height() < 433){
			$(".gnb_bottom").css('display','block');

			$(left_gnb).scroll(function () {
				var gnb_height = $(left_gnb).scrollTop();
				
				if(gnb_height > 30){
					$(".gnb_bottom i").attr('class','ri-arrow-up-s-line')
				}else if(gnb_height < 30){
					$(".gnb_bottom i").attr('class','ri-arrow-down-s-line')
				}
			}); 
			

			$(left_gnb).scroll(function () {
				var gnb_height = $(left_gnb).scrollTop();
				
				if(gnb_height > 30){
					$(".gnb_bottom i").attr('class','ri-arrow-up-s-line')
				}else if(gnb_height < 30){
					$(".gnb_bottom i").attr('class','ri-arrow-down-s-line')
				}
			}); 
		}
	});

	function move_to_shop(){
		<?php if(strpos($_SERVER['HTTP_USER_AGENT'],'webview//1.0') == true){ ?>
			App.moveToShop()
		<?php }else{?>

			var shop_url = "<?=SHOP_URL?>";
			var form = document.createElement("form");

				form.setAttribute("charset", "UTF-8");
				form.setAttribute("method", "Post");  //Post 방식
				form.setAttribute("action", "<?=SHOP_URL?>"); //요청 보낼 주소

				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_id");
				hiddenField.setAttribute("value", "<?=$member['mb_id']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_password");
				hiddenField.setAttribute("value", "<?=$member['mb_password']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "gnu_to_mall");
				hiddenField.setAttribute("value", "gnu_to_mall");
				form.appendChild(hiddenField);

				document.body.appendChild(form);
				form.submit();
		<?php } ?>
	}
</script>
