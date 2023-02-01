
<?php
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

	login_check($member['mb_id']);
	
	$title = 'profile';

	// 임의의수까지 숫자 랜덤
	function generate_code($length = 6) {
		$numbers  = "0123456789";
		$svcTxSeqno = date("YmdHis");
		$nmr_loops = 6;
		while ($nmr_loops--) {
			$svcTxSeqno .= $numbers[mt_rand(0, strlen($numbers))];
		}
		return $svcTxSeqno;
	}

	function format_phone($phone){ $phone = preg_replace("/[^0-9]/", "", $phone); $length = strlen($phone); switch($length){ case 11 : return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone); break; case 10: return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone); break; default : return $phone; break; } }

	//kyc 인증
	$kyc_sql = "select * from g5_write_kyc where mb_id = '{$member['mb_id']}' order by wr_last desc limit 0,1";
	$kyc_res = sql_fetch($kyc_sql);
	if($kyc_res ){
		$kyc_cert = $kyc_res['wr_2'];
	}else{
		if($member['kyc_cert'] > 0)
		$kyc_cert = $member['kyc_cert'];
	}

?>
	<link href="<?=G5_THEME_URL?>/css/scss/radio_set_<?=$_COOKIE['mode']?>.css" rel="stylesheet">
	<main>
		<div class='container profile nomargin nopadding'>
			<section class="profile_wrap content-box6">
				<div class="col-sm-12 col-12 profile-box">
					<h3 class='title b_line'>
						<i class='p1'><img src="<?=G5_THEME_URL?>/img/personl_information.png" alt=""></i>
						<span >개인정보</span>
					</h3>
					<ul class="user_wrap person_info">
						<li>
							<div class="user_img_wrap">
								<img src="<?=G5_THEME_URL?>/img/profile.png" alt="">
							</div>
						</li>
						<li>
							<h4 class="font_weight mb_name">
								<span class="mb_name"><?=$member['mb_name']?>님</span>
								<? if($member['mb_level'] != 10) { ?>
									<span class='mb_level'><?=$mb_level_array[$member['mb_level']]?></span>
								<?} ?>
							</h4>
							<h4 class='mb_id'><?=$member['mb_id']?></h4>
						</li>
					</ul>
					<ul class='row person_info'>
						<li class='col-12'>
							<label>휴대폰 번호</label>  
							<div class='row'>
								<div class='col-8'><p><?=format_phone($member['mb_hp'])?></p></div>

								<?//if($member['mb_hp'] == '' || $member['mb_certify'] != 1){?>
									<div class='col-4 text-right'><input type="button" value="수정" class="num_pop_open pop_open" style="margin-top: -7px"></div>
								<?//}?>
							</div>
						</li>

						<li class='col-12 mt20' style="margin-top: 0;">
							<label>이메일</label>  
							<p><?=$member['mb_email']?></p>
						</li>
						
						<?php if($member['mb_wallet'] != ""){?>
						<li class='col-12 mt20'>
							<label><?=ASSETS_CURENCY?> 지갑주소</label>  
							<p><?=Decrypt($member['mb_wallet'],$member['mb_id'],'x')?></p>
						</li>
						<?php } ?>

						<?php if($member['eth_my_wallet'] != ""){?>
						<li class='col-12 mt20'>
							<label><?=WITHDRAW_CURENCY?> 지갑주소</label>  
							<p><?=Decrypt($member['eth_my_wallet'],$member['mb_id'],'x')?></p>
						</li>
						<?php } ?>
						<!-- <li class='col-12 mt20'>
							<label>아이디</label>  
							<p><?=$member['mb_id']?></p>
						</li>
						
						<li class='col-12 mt20'>
							<label>이름</label>  
							<p><?=$member['mb_name']?></p>
						</li> -->	
						<!-- 
							<li class='col-sm-3 col-4 text-right grid'>
								<input type="button" value="Change" class="btn inline white email_pop_open pop_open" value="변경">
							</li>
						-->
					</ul>
				</div>
				<div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i class="p2"><img src="<?=G5_THEME_URL?>/img/security_setting.png" alt=""></i>
						<span >보안설정</span>
					</h3>
					<ul class='row' id='logpw'>
						<li class='col-sm-9 col-8'><span >로그인 비밀번호</span></li>
						<li class='col-sm-3 col-4 text-right'>
							<!-- <i class="ri-arrow-drop-right-line ch_pw_open pop_open"></i> -->
							<span class="ch_pw_open pop_open">수정</span>
						</li>
					</ul>

					<ul class='row' style="margin-top: -15px;">
						<li class='col-sm-9 col-8'><span >핀번호</span></li>
						<li class='col-sm-3 col-4 text-right'>
							<!-- <i class="ri-arrow-drop-right-line ch_tpw_open pop_open"></i> -->
							<span class="ch_tpw_open pop_open">수정</span>
						</li>
					</ul>
				</div>
				<div class='col-sm-12 col-12 profile-box certificate'>
					<h3 class='title b_line'>
						<i class="p2"><img src="<?=G5_THEME_URL?>/img/kyc.png" alt=""></i>
						<span >인증 정보</span>
					</h3>
					<ul class='row mt10'>
						<li class='col-sm-9 col-8' style="padding-right: 0">
							<label >KYC 인증정보</label>
							<p class='status_<?=$kyc_cert?>' style="width:100%">
								<?if($kyc_res){
									echo person_key($kyc_res['wr_subject'],$kyc_cert,$kyc_res['wr_content']);
								}else{
									if($member['kyc_cert']>0){
										echo person_key($member['mb_name'],$member['kyc_cert']);
									}else{
										echo "미등록";
									}
								}?>
							</p>
						</li>
						<li class='col-sm-3 col-4 text-right'>
							<span class="reg_btn" data-name="ch_tax"> 등록 / 수정</span>
						</li>
					</ul>
				</div>

				<div class='col-sm-12 col-12 profile-box' style="padding-bottom:80px;">
					<h3 class='title b_line'>
						<i class="p3"><img src="<?=G5_THEME_URL?>/img/recommendation_information.png" alt=""></i>
						<span >추천인 정보</span>
					</h3>
					<ul class='row mt10'>
						<li class='col-sm-12 col-12'>
							<label >나의 추천</label>
							<p ><?=$member['mb_recommend']?></p>
						</li>
					</ul>
				</div>
			</section>
		</div>
	</main>
	<?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
	<div class="gnb_dim"></div>
</section>

<script>
	$(function(){
		var agree_content = $("#argee_content");
			agree_content.load("user_agree.html");

		$(".top_title h3").html("<span>개인정보&보안설정</span>")

		$('.reg_btn').click(function(){
			var target = $(this).data("name");
			dimShow();
			$('#'+target).css({top: "50%", transform: "translateY(-50%)", display: "block"});			
		});

		$('.person_agree_view').on('click',function(){
			// $(this).next('div').slideToggle()
			commonModal("고유식별정보 처리방침",agree_content.html(),"confirm");
		})
	});
</script>
	<!-- 트랜잭션 비밀번호 변경 -->
	<div class="pop_wrap chage_tpw_pop">
		<p class="pop_title font_red">경고</p>
		<div>
			본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="go_tpw1">계속</a>
		</div>
	</div>

	<div class="pop_wrap chage_tpw_pop1 input_pop_css">
		<form action="">

			<label for="" >사용중인 거래 비밀번호</label>
			<input type="password" id="current_tpw" maxlength="6">
			<label for="" >새로운 거래 비밀번호</label>
			<input type="password" id="new_tpw" maxlength="6">
			<label for="" >새로운 거래 비밀번호 확인</label>
			<input type="password" id="new_tpw_re" maxlength="6">
			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">
			<div class="btn2_btm_wrap">
				<input type="button" value="취소" class="btn btn_double default_btn cancel btn_cancel pop_close" >
				<input type="button" value="수정" class="btn btn_double main_btn2 save go_tpw3">
			</div>
		</form>
	</div>

	<div class="pop_wrap chage_tpw_pop2 notice_img_pop">
		<p class="pop_title" >인증번호 전송</p>
		<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="체크">
		<div >인증번호가 이메일로 전송되었습니다</div>
		<a href="javascript:void(0);" class="back_tpw1 gray_close f_right" >창닫기</a>
	</div>

	<div class="pop_wrap chage_tpw_pop3 notice_img_pop">
		<p class="pop_title" >거래 비밀번호 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/success_check.png" alt="이미지">
			<p >변경이 성공적으로 완료되었습니다</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" id="pin_close" class="btn wd main_btn2" >확인</a>
		</div>
	</div>
<script>

	//  트랜잭션 비밀번호변경
	$('.ch_tpw_open').click(function(){
		$('.auth_email').removeClass('_send_email_chage_pw_pop1');
		$('.auth_email').addClass('_send_email_chage_tpw_pop1');
		$('.auth_email').css("display","block");
	});

	if(<?= $member['reg_tr_password_change'] ?> == 0) {
		$('#current_tpw').hide();
		$('#current_tpw').prev().hide();
	}

	$(document).on('click','._send_email_chage_tpw_pop1 #send_email',function(){

		check_email_condition((state,email)=>{
			if(state){
				ajax_send_mail(email);
			}
		})

	})


	$(document).on('click','._send_email_chage_tpw_pop1 .auth_save',function(){
		check_email_condition((state,email)=>{
			if(state){
				ajax_check_mail(email,"chage_tpw_pop1");
			}
		})

	})


	onlyNumber('current_tpw');
	onlyNumber('new_tpw');
	onlyNumber('new_tpw_re');

	$('.chage_tpw_pop1 .save').click(function(){

		var current_tpw = $('.chage_tpw_pop1 #current_tpw').val();
		var new_tpw = $('.chage_tpw_pop1 #new_tpw').val();
		var new_tpw_re = $('.chage_tpw_pop1 #new_tpw_re').val();


		if(new_tpw.length < 6){
			dialogModal('입력확인','<strong> 출금비밀번호(핀코드)는 6자리 숫자입니다.</strong>','failed');
			return false;
		}

		if(new_tpw != new_tpw_re){
			dialogModal('입력확인','<strong> 입력한 출금비밀번호(핀코드)가 일치하지 않습니다.</strong>','failed');
			return false;
		}

		$.ajax({
				type: "POST",
				url: "/util/profile_proc.php",
				dataType: "json",
				data:  {
					"current_tpw" : current_tpw,
					"new_tpw" : new_tpw,
					"new_tpw_re" : new_tpw_re,
					"auth_pwd" : $('.chage_tpw_pop1 #auth_pwd').val(),
					"category" : "tpw"
				},
				success: function(data) {
					if(data.result =='success'){
						$('.chage_tpw_pop1').css("display","none");
						$('.chage_tpw_pop3').css("display","block");
						$('#pin_close').click(function(){
							window.location.reload();
						})
					}else{
						dialogModal('처리에러!','<strong> '+ data.sql+'</strong>','failed');
					}
				},
				error:function(e){
					dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
				}
			});

	});

</script>

<!--  비밀번호 변경 -->
	<div class="pop_wrap chage_pw_pop">
		<p class="pop_title font_red">경고</p>
		<div>
			본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="go_ch_pw1">계속</a>
		</div>
	</div>

	<div class="pop_wrap chage_pw_pop1 input_pop_css">
		<form action="">
			<label for="" >사용중인 비밀번호</label>
			<input type="password" id="current_pw" minlength='8' maxlength="16">
			<label for="" >새로운 비밀번호</label>
			<input type="password" id="new_pw" minlength='8' maxlength="16">
			<label for="" >새로운 비밀번호 확인</label>
			<input type="password" id="new_pw_re" minlength='8' maxlength="16">
			
			<div class="btn2_btm_wrap">
				<input type="button" value="취소" class="btn btn_double default_btn cancel btn_cancel pop_close" >
				<input type="button" value="수정" class="btn btn_double default_btn main_btn2 save go_ch_pw3">
			</div>
		</form>
	</div>

	<div class="pop_wrap chage_pw_pop3 notice_img_pop">
		<p class="pop_title" >비밀번호 변경</p>
		<div>
			<img src="<?=G5_URL?>/img/success_check.png" alt="이미지">
			<span >비밀번호가 성공적으로 변경되었습니다
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" id="pass_close" class="btn wd pop_close main_btn2">확인</a>
		</div>
	</div>


	<div class="pop_wrap auth_email input_pop_css">
		<p class="pop_title" >이메일 인증</p>
		<div class="email_row">
			<input type="text" id="current_email" value="<?=$member['mb_email']?>" readonly>
			<button class="btn btn_email nomargin ml-10" id="send_email">이메일 전송</button>
		</div>
		<div class="btn2_btm_wrap">
			<input type="button" value="취소" class="btn btn_double default_btn cancel btn_cancel pop_close" >
			<input type="button" value="확인" class="btn btn_double default_btn main_btn2 auth_save">
		</div>
	</div>

<script>


	let check_email_condition = (invoke) =>{
		let current_email = document.getElementById('current_email').value.trim();
		
		if(current_email == '' || current_email == null || current_email == undefined){
			dialogModal('이메일 인증','<strong>이메일을 입력해주세요.</strong>','failed',false);
			invoke(false,current_email);
			return false;
		}

		if(current_email != "<?=$member['mb_email']?>"){
			dialogModal('이메일 인증','<strong>회원님의 이메일이 일치하지 않습니다.</strong>','failed',false);
			invoke(false,current_email);
			return false;
		}

		invoke(true,current_email);

	}

	let ajax_send_mail = (email) => {
		$.ajax({
			url: "/mail/send_mail.php",
			type: "POST",
			dataType: "json",
			async: false,
			cache: false,
			data:{
				user_email : email
			},
			complete: (_) => {
				dialogModal('이메일 인증','<strong>해당 메일로 이메일을 발송하였습니다.<br>이메일을 확인해주세요.</strong>','failed',false);
			}
		})
	}

	let ajax_check_mail = (email, clazz) => {
		$.ajax({
				url: "/mail/check_mail_for_register.php",
				type: "POST",
				dataType: "json",
				async: false,
				cache: false,
				data:{
					user_email : email
				},
				success: (res) => {
					if(res.result == "OK"){
						if($('.auth_email').css("display") == "block"){
							$('.auth_email').css("display","none");
							$(`.${clazz}`).css("display","block");
						}
					}else{
						dialogModal('이메일 인증',`<strong>${res.res}</strong>`,'failed',false);
					}
				}
			})
	}



	$('.ch_pw_open').click(function(){
		$('.auth_email').removeClass('_send_email_chage_tpw_pop1');
		$('.auth_email').addClass('_send_email_chage_pw_pop1');
		$('.auth_email').css("display","block");
	});

	$(document).on('click','._send_email_chage_pw_pop1 #send_email',function(){

		check_email_condition((state,email)=>{
			if(state){
				ajax_send_mail(email);
			}
		})

	})

	$(document).on('click','._send_email_chage_pw_pop1 .auth_save',function(){

		check_email_condition((state,email)=>{
			if(state){
				ajax_check_mail(email,"chage_pw_pop1");
			}
		})

	})


	$('.chage_pw_pop1 .save').click(function(){

		var current_pw = $('.chage_pw_pop1 #current_pw').val();
		var new_pw = $('.chage_pw_pop1 #new_pw').val();
		console.log(new_pw)
		var check_new_pw = CheckPass(document.getElementById('new_pw').value);
		var new_pw_re = $('.chage_pw_pop1 #new_pw_re').val();

		if(check_new_pw == false){
			dialogModal('입력확인','<strong>비밀번호는 최소 8자 최대 16자, 영문과 특수문자 숫자가 하나 이상 포함되어야 합니다.</strong>','failed',false);
			return false;
		}

		if(new_pw != new_pw_re){
			dialogModal('입력확인','<strong>새 비밀번호가 일치하지 않습니다.</strong>','failed',false);
			return false;
		}


		$.ajax({
			type: "POST",
			url: "/util/profile_proc.php",
			dataType: "json",
			data:  {
				"current_pw" : current_pw,
				"new_pw" : new_pw,
				"new_pw_re" : new_pw_re,
				"category" : "pw"
			},
			success: function(data) {
				if(data.result =='success'){
					$('.chage_pw_pop1').css("display","none");
					$('.chage_pw_pop3').css("display","block");
					$('#pass_close').click(function(){
						window.location.reload();
					})
				}else{
					dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed');
				}
			},
			error:function(e){
				dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
			}
		});

	});

</script>
<!--  비밀번호 변경 -->

<!-- 이메일 주소 변경 -->
	<div class="pop_wrap chage_email_pop input_pop_css">
		<form>
			<label for="" >사용중인 이메일 주소</label>
			<div class='current'><?=$member['mb_email']?></div>

			<label for="" >새로운 이메일 주소</label>
			<input type="text"  name="email_new" id="email_new" value="" onchange="validateEmail(this.value);">

			<label for="" >새로운 이메일 주소 확인</label>
			<input type="text"  name="email_new_re" id="email_new_re" value="">

			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">

			<div class="btn2_btm_wrap">
				<input type="button" value="Close" class="btn btn_double deault_btn cancel pop_close" >
				<input type="button" value="Save" class="btn btn_double blue save">
			</div>
		</form>
	</div>

	<div class="pop_wrap chage_email_pop1 notice_img_pop">
		<p class="pop_title" >이메일 변경</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
		<p> 변경이 성공적으로 완료되었습니다</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:parent.location.reload();" class="btn inline wd pop_close" >창닫기</a>
		</div>
	</div>

	<script>
	function validateEmail(email) {
		var email = "da@naver.com";
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if (email == '' || !re.test(email)) {
			alert("wrong E-mail type! please check e-mail");
			return false;
		}
	}

	$(function() {
		$('.email_pop_open').click(function(){
			$('.chage_email_pop').css("display","block");
		});
		
		$('.go_ch_em1').click(function(){
			$('.chage_email_pop').css("display","none");
			$('.chage_email_pop1').css("display","block");
		});

		$('.chage_email_pop .save').click(function(){
			var email2 = $('.chage_email_pop #email_new').val();
			var email3 = $('.chage_email_pop #email_new_re').val();

			if( email2 != email3){
				dialogModal('입력확인','<strong> 입력한 메일주소가 일치하지 않습니다.</strong>','failed');
				return false;
			}

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"email2" : email2,
						"email3" : email3,
						// "confirm" : email_vaild_code,
						"auth_pwd" : $('.chage_email_pop #auth_pwd').val(),
						"category" : "email"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_email_pop2').css("display","none");
							$('.chage_email_pop1').css("display","block");
						}else{
							dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
					}
				});

		});
	});
	</script>
<!-- 이메일 주소 변경 -->

<!-- 전화번호 변경 -->
	<div class="pop_wrap num_pop_wrap input_pop_css">
		<form action="">
			<label for="" >사용중인 전화번호</label>
			<div class="num_pop_div clear_fix" >
				<div class='current'><?=" + ".$member['nation_number']." ".$member['mb_hp']?></div>
			</div>
			<label for="" >새로운 전화번호 (ex: 01011112222)</label>
			<div class="num_pop_div clear_fix">
				<input type="text" id="new_hp_num" value="" style="width:100%;margin-left:0;" placeholder="전화번호(숫자만)" >
			</div>
			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">
			<div class="btn2_btm_wrap" >
				<input type="button" value="취소" class="btn btn_double default_btn cancel btn_cancel pop_close" >
				<input type="button" value="수정" class="btn btn_double main_btn2 save proceed">
			</div>
		</form>
	</div>

	<!-- 변경완료 -->
	<div class="pop_wrap num2_pop_wrap notice_img_pop">
		<p class="pop_title" >전화번호 변경</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
		<p >변경이 성공적으로 완료되었습니다</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd main_btn3 pop_close" >창닫기</a>
		</div>
	</div>

	<script>
		$(function() {
			onlyNumber('new_hp_num');

			$('.num_pop_open').click(function(){
				$('.num_pop_wrap').css("display","block");
			});

			$('.num1_pop_close').click(function(){
				$('.num_pop_wrap').css("display","block");
				$('.num1_pop_wrap').css("display","none");
			});

			$('.proceed').click(function(){
				var new_hp_num = $('.num_pop_wrap #new_hp_num').val();

				$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						// "hp_num" : hp_num,
						// "new_nation_num" : new_nation_num,
						"new_hp_num" : new_hp_num,
						"auth_pwd" : $('.num_pop_wrap #auth_pwd').val(),
						"category" : "phone"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.num_pop_wrap').css("display","none");
							$('.num2_pop_wrap').css("display","block");
							$('.num2_pop_wrap .pop_close').click(function(){
								parent.location.reload();
							});
						}else{
							dialogModal('입력확인','<strong>'+data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
					}
				});
			});
		});
	</script>
<!-- 전화번호 변경 -->

<!-- 이름변경 -->
	<div class="pop_wrap chage_name_pop1 input_pop_css">
		<form action="">
			<label for="" >현재 성함</label>
			<div class='current'><?=$member['first_name']." ".$member['last_name']?></div>
			<label for="" >변경하실 성</label>
			<input type="text" id="new_last_name">
			<label for="" >변경하실 이름</label>
			<input type="text" id="new_first_name">
			<label for="" >로그인 비밀번호</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">
			<div class="btn2_btm_wrap">
				<input type="button" value="Close" class="btn btn_double default_btn cancel btn_cancel pop_close" >
				<input type="button" value="Save" class="btn btn_double blue save go_ch_name">
			</div>
		</form>
	</div>
	<div class="pop_wrap chage_name_pop3 notice_img_pop">
		<p class="pop_title" >이름 변경</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
			<span>변경 처리되었습니다</span>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd pop_close">Close</a>
		</div>
	</div>

	<script>
	$(function() {

		$('.ch_name_open').click(function(){
			$('.chage_name_pop1').css("display","block");
		});

		$('.chage_name_pop1 .save').click(function(){
			var new_last_name = $('.chage_name_pop1 #new_last_name').val();
			var new_first_name = $('.chage_name_pop1 #new_first_name').val();
			var auth_pwd = $('.chage_name_pop1 #auth_pwd').val();

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"new_last_name" : new_last_name,
						"new_first_name" : new_first_name,
						"auth_pwd" : auth_pwd,
						"category" : "name"
					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_name_pop3').css("display","block");
						}else{
							dialogModal('처리 실패!','<strong> '+ data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
					}
				});
		});
	});
	</script>
<!-- 이름변경 -->


<!--세금신고-->
<div class="pop_wrap input_pop_css" id="ch_tax" style="padding: 0">
	<form method="post" action="">
		<div class="ch_tax_wrap">
			<label for="" >성명</label>
			<input type="text" id="tax_name" class="basic_input" maxlength="6" value="">
			<label style="display:block" for="" >주민등록번호</label>
			<input type="text" pattern="\d*" id="tax_person_number_1" maxlength="6" class="half" inputmode="number"> 
			<label style="display:inline-block; height: 46px; line-height: 44px;font-size:22px;color:#dfe4ec;margin: 0 2px;">-</label>
			<input type="password" pattern="\d*" id="tax_person_number_2" maxlength="7" class="half" inputmode="number">
			<input type="hidden" id="tax_person_number_3" maxlength="7" class="half" >

			<div class='box'>
				<label style="display:inline;">KYC신분증 첨부 </label>
				<!-- <input type="file" accept="image/*" class='filebox' name="bf_file[1]"  > -->
				<!-- <input class="upload-name" value="+ 파일 첨부" placeholder="+파일 첨부">
				<label class="file_label" for="bf_file_1"></label> 
				<input type="file" accept="image/*" class='filebox' name="bf_file[1]" onchange="read_url(this,'all');"> -->
				<input class="upload-name1" placeholder="선택된 파일 없음">
				<input type="file" id="bf_file1" accept="image/*" name="bf_file[1]" class="filebox">
				<label class="file_label" for="bf_file1">파일선택</label> 
				<label class='kyc_label'>신분확인 가능한 주민등록증, 운전면허증 사진을 첨부해주세요.</label>
				<button type="button" class="btn_del">&times;</button>
			</div>

			<hr class="hr_w">

			<!-- <div class='box'>
				<label style="display:inline;" class="mt20">출금지갑주소 첨부 </label>
				<input type="file"  accept="image/*" class='filebox' name="bf_file[2]"> 
				<input class="upload-name2" placeholder="선택된 파일 없음">
				<input type="file" id="bf_file2" accept="image/*" name="bf_file[2]" class="filebox">
				<label class="file_label" for="bf_file2">파일선택</label> 
				<label class='kyc_label' style="font-size:11px;margin:5px;font-weight:100;">출금 지갑주소가 확인되는 캡쳐이미지,사진을 첨부해주세요.</label>
				<button type="button" class="btn_del">&times;</button>
			</div> -->

			<!-- <div class="radio_set">
				<input type="radio" id="wallet_type1" name="wallet_type" value="0"/><label for="wallet_type1">국내거래소 지갑</lable>
				<input type="radio" id="wallet_type2" name="wallet_type" value="1"/><label for="wallet_type2">해외거래소 지갑</lable>
				<input type="radio" id="wallet_type3" name="wallet_type" value="2"/><label for="wallet_type3">기타/개인 지갑</lable>
			</div> -->

			<!-- <div class='box'>
			<label style="display:inline;" class="mt30">출금지갑 종류선택 </label>
				<div class="radio_set">
					<label>
						<input type="radio" name="wallet_type" id="wallet_type1" class="selector-item_radio" value="1">
						<label for="wallet_type1" class="wallet_type1">국내거래소 지갑</label>
					</label>
					<label>
						<input type="radio" name="wallet_type" id="wallet_type2" class="selector-item_radio" value="2">
						<label for="wallet_type2" class="wallet_type2">해외거래소 지갑</label>
					</label>
					<label>
						<input type="radio" name="wallet_type" id="wallet_type3" class="selector-item_radio" value="2">
						<label for="wallet_type3" class="wallet_type3">개인/기타 지갑</label>
					</label>
				</div>
			</div> -->
			<div class='mb15' style="display:flex;align-items: center;">
				<input type="checkbox" name="tax_person_number_agree" id="tax_person_number_agree" value="">
				<label for="tax_person_number_agree" class="tax_person_number_agree">고유식별정보 처리동의</label>

				<a href="javascript:void(0);" class="inline_btn person_agree_view" >전문보기</a>
				<div class="preclose">
					<textarea id="tax_person_agree_content" class="textbox">
					</textarea>
				</div>
				<div id="argee_content" style="display: none">

				</div>
			</div>
		</div>
		<div class="submit_wrap container">
			<div class="btn2_btm_wrap">
				<input type="button" value="닫기" class="btn btn_double default_btn cancel btn_cancel pop_close" >
				<input type="button" value="등록하기" class="btn btn_double main_btn2 save" id="kyc_rec_btn">
			</div>
		</div>		
	</form>
</div>
<script type="text/javascript">
	$(function(){  
		$("#kyc_rec_btn").on('click',function(){
			var kyc_name = $("#tax_name").val();
			var kyc_person_number = $("#tax_person_number_1").val()+'-'+$('#tax_person_number_3').val()
			
			var fileInput = $(".filebox");
			
			// var upload_name = $(".upload-name").val();
			var kyc_agree = $("#tax_person_number_agree").is(':checked');
			if(!kyc_agree){
				dialogModal('KYC 인증','<strong> 고유식별정보 처리방침에 동의해주세요. </strong>','warning');
				return false;
			}

			var rule = 0;
			var person_number_rule1 = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))/;
			if(!$("#tax_person_number_1").val().match(person_number_rule1)){
				dialogModal('KYC 인증처리','<strong> 올바른 주민등록번호를 입력해주세요.</strong>','failed');
				return false;
			}else{
				rule += 1;
			}
			var person_number_rule2 = /^[1-4][0-9]{6}$/;
			if(!$("#tax_person_number_2").val().match(person_number_rule2)){
				dialogModal('KYC 인증처리','<strong> 주민등록번호 형식이 맞지 않습니다.\n올바른 주민등록번호를 입력해주세요.</strong>','failed');
				return false;
			}else{
				rule += 1;
			}

			// console.log("파일업로드1 :: " + fileInput[0].files.length);
			// console.log("파일업로드2 :: " + fileInput[1].files.length);
			
			// var wallet_type = $('input[name=wallet_type]:checked').val();

			// if(!wallet_type){
			// 	alert(" 출금지갑 종류를 선택해주세요 ");
			// 	return false;
			// }else{
			// 	rule += 1;
			// }


			if(fileInput.val() != "") {		
				var ext = fileInput.val().split(".").pop().toLowerCase();		    
				if($.inArray(ext, ["jpg", "jpeg", "png", "gif", "bmp"]) == -1) {
					dialogModal('KYC 인증처리','<strong> 첨부파일은 이미지 파일만 등록 가능합니다.</strong>','failed');
					fileInput.val("");
					return false;
				}
			
				var maxSize = 10 * 1024 * 1024; // 10MB

				var fileSize = fileInput[0].files[0].size;
				if(fileSize > maxSize){
					dialogModal('KYC 인증처리','<strong> 첨부파일 사이즈는 10MB 이내로 등록 가능합니다.</strong>','failed');
					fileInput.val("");
					return false;
				}
			}

			
			if(fileInput[0].files.length < 1){

				dialogModal('KYC 인증','<strong> 신분확인이 가능한 사진과 출금지갑 인증파일을 첨부해주세요. </strong>','warning');
				return false;
			}else{
			
				const formData = new FormData();
				formData.append("w",'');
				formData.append("wr_subject",kyc_name);
				formData.append("wr_content", kyc_person_number);
				// formData.append("wr_wallet_type", wallet_type);


				for (var i = 0; i < fileInput.length; i++) {
					if (fileInput[i].files.length > 0) {
						
						var ext = fileInput.val().split(".").pop().toLowerCase();		    
						if($.inArray(ext, ["jpg", "jpeg", "png", "gif", "bmp"]) == -1) {
							dialogModal('KYC 인증처리','<strong> 첨부파일은 이미지 파일만 등록 가능합니다.</strong>','failed');
							fileInput.val("");
							return false;
						}
					
						var maxSize = 10 * 1024 * 1024; // 10MB

						var fileSize = fileInput[0].files[0].size;
						if(fileSize > maxSize){
							dialogModal('KYC 인증처리','<strong> 첨부파일 사이즈는 10MB 이내로 등록 가능합니다.</strong>','failed');
							fileInput.val("");
							return false;
						}


						for (var j = 0; j < fileInput[i].files.length; j++) {
							
							// formData에 'file'이라는 키값으로 fileInput 값을 append 시킨다.  
							formData.append("bf_file[]", fileInput[i].files[j]);
						}
					}
				}

				$.ajax({
					type: "POST",
					url: "/util/file_upload.php",
					data: formData,
					cache: false,
					processData: false,
					contentType: false,
					enctype: "multipart/form-data",
					dataType: "json",
					success: function(data) {
						console.log(data)
						if(data.result =='success'){
							dialogModal('KYC 인증처리',"<strong> 등록되었습니다.<br>관리자 승인까지 최대 24시간 소요될수 있습니다.</strong>",'success');

							$('.closed').click(function(){
								window.location.reload();
							})
						}else{
							dialogModal('처리에러!','','failed');
						}
					},
					error:function(e){
						dialogModal('처리 실패!','<strong> 다시시도해주세요 문제가 계속되면 관리자에게 연락주세요.</strong>','failed');
					}
				});
			} 
		});

		$("#tax_person_number_2").on('change', function(e){
			$("#tax_person_number_3").val($("#tax_person_number_2").val()); 
		});
	});	

	const url = location.href;
	if(url.indexOf('profile') != -1) {
		$("main").css('background-color','#fff');
	}

	$('.btn_del').css('top','47%');

	$('#bf_file1').on('change', function(e) {
		if(e.target.files.length > 0) {
			$('.upload-name1').val(e.target.files[0].name);
			$('.upload-name1').siblings('.btn_del').show();
		}
	});

	$('#bf_file2').on('change', function(e) {
		if(e.target.files.length > 0) {
			$('.upload-name2').val(e.target.files[0].name);
			$('.upload-name2').siblings('.btn_del').show();
		}
	});

	$('.upload-name1').siblings('.btn_del').on('click', function(){
		$('.upload-name1').val('')
		$(this).hide();
	})
	$('.upload-name2').siblings('.btn_del').on('click', function(){
		$('.upload-name2').val('')
		$(this).hide();
	})
</script>