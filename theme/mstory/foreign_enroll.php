<?php
$menubar = 1;
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/lang.php');

if($nw['nw_enroll'] == 'Y'){
}else{
	alert("현재 서비스를 이용할수없습니다.");
}

$service_term = get_write("g5_write_agreement", 1);
$private_term = get_write("g5_write_agreement", 2);
$marketing_term = get_write("g5_write_agreement", 3);

if($service_no) {
  $sql = "SELECT * from {$g5['foreign_member_info']} WHERE mb_no = '{$service_no}'";
  $result = sql_fetch($sql);
  $sql = "UPDATE {$g5['foreign_member_info']} SET mb_datetime = '".G5_TIME_YMDHIS."' WHERE mb_no = '{$service_no}'";
  sql_query($sql);
} else {
  alert("'service_no' 파라미터가 없습니다.");
}

?>

<link href="<?= G5_THEME_URL ?>/css/scss/enroll.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v5.2.0/js/all.js"></script>
<script src="<?=G5_URL?>/js/certify.js"></script>

<div class="v_center" style="padding:0;">

	<div class="enroll_wrap" style="padding:0;">
		<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" style="padding:1rem" method="post" enctype="multipart/form-data" autocomplete="off">
      <input type="hidden" name="mb_id" value="<?=$result['mb_id']?>" />
			<p class="check_appear_title"><span>개인 정보 & 인증</span></p>
			<div>
				
				
				<!-- <input type="text" name="mb_hp"  id="reg_mb_hp" class='cabinet'  pattern="[0-9]*" style='padding:15px' required  placeholder="휴대폰번호"/>
				<span class='cabinet_inner' style=''>※'-'를 제외한 숫자만 입력해주세요</span> -->

				
				<!-- <div class='in_btn_ly'><input type="button" id='win_hp_cert' class='btn_round check hp_cert' value="휴대폰 본인인증" style="width:80px;"></div> -->
				
				<input type="email" name="" class='cabinet' style='padding:15px' id="reg_mb_id" value="<?=$result['mb_id']?>" disabled required/>
        <div class='in_btn_ly'><input type="button" id='EmailChcek' class='btn_round check' value="이메일 인증"></div>
        <input type="text" name="mb_name" style='padding:15px;' id="reg_mb_name" required placeholder="이름"/>
				<span class='cabinet_inner' style=''>※이메일형식으로 입력해주세요</span>	
			</div>

			

			<ul class="clear_fix pw_ul mt20">
				<li>
					<input type="password" name="mb_password" id="reg_mb_password" minlength="8" maxlength="16" placeholder="로그인 비밀번호" />
					<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="8" maxlength="16" placeholder="로그인 비밀번호 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;'>비밀번호 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pm_1" >8자 이상 16자 이하</li>
						<li class="x_li" id="pm_3" >영문+숫자+특수문자</li>
						<li class="x_li" id="pm_5" >비밀번호 비교</li>
					</ul>
				</li>
				<li style='margin-left:5px'>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password" name="reg_tr_password" placeholder="출금비밀번호(핀코드)" />
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password_re" name="reg_tr_password_re" placeholder="출금비밀번호(핀코드) 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;' >핀코드 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pt_1" >6 자리</li>
						<li class="x_li" id="pt_3" >숫자</li>
						<li class="x_li" id="pt_2" >핀코드 비교</li>
					</ul>
				</li>
			</ul>

			<p class="check_appear_title mt40"><span >회원가입 약관동의 </span></p>
			<div class="mt20">
				<div class="term_space">
					<input type="checkbox" id="service_checkbox" class="checkbox-style-square" name="term_required" >
					<label for="service_checkbox">
						<span style='line-height:30px;'><?= $service_term['wr_subject'] ?> 동의 (필수)</span>
						<a id="service" href="javascript:collapse('#service');"  style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="service_term" class="term_textarea term_none"><?= $service_term['wr_content'] ?></textarea>
				</div>	
				<div class="term_space">
					<input type="checkbox" id="private_checkbox" class="checkbox-style-square" name="term_required" >
					<label for="private_checkbox">
						<span style='line-height:30px;'><?= $private_term['wr_subject'] ?> 동의 (필수)</span>
						<a id="private" href="javascript:collapse('#private');"  style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="private_term" class="term_textarea term_none"><?= $private_term['wr_content'] ?></textarea>
				</div>		
				<!-- <div class="term_space">
					<input type="checkbox" id="marketing_checkbox" class="checkbox-style-square" name="mb_sms" value="1">
					<label for="marketing_checkbox">
						<span style='line-height:30px;'><?= $marketing_term['wr_subject'] ?> 동의 (선택)</span>
						<a id="marketing" href="javascript:collapse('#marketing');"  style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="marketing_term" class="term_textarea term_none"><?= $marketing_term['wr_content'] ?></textarea>
				</div> -->				
			</div>			

			<div class="btn2_wrap enroll_btn_wrap" style='width:100%;height:60px; display: flex'>
				<input class="btn btn_double btn_cancle" type="button" value="취소">
				<input class="btn btn_double b_skyblue submit main_btn" type="button" onclick="fregisterform_submit();" value="신규 회원 등록하기">
			</div>
		</form>
	</div>

</div>

</section>

<div class="gnb_dim"></div>



<script>
	$(function() {
		$(".top_title h3").html("<span style='font-size:16px;line-height:16px;'>신규 회원등록</span>");

		$('.cabinet').on('click',function(){
			$(this).next().css('display','contents');
		});

		$('.cabinet').on('mouseout',function(){
			$(this).next().css('display','none');
		});

		$('.btn_cancle').on('click',function(){
			dialogModal("회원가입 취소", "입력했던 내용을 모두 삭제하고 초기화면으로 돌아갑니다.", 'warning');
			$('#modal_return_url').on('click',function(){
				location.href= g5_url;
			});
		});

    /*이메일 체크*/
		$('#EmailChcek').on('click',function(){
			var email = $('#reg_mb_id').val();
			var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

			if (email == '' || !re.test(email)) {
				dialogModal("이메일 인증", "사용가능한 이메일 주소를 입력해주세요.", 'warning')
				return false;
			}

			// loading.show();

			$.ajax({
				type: "POST",
				url: "/mail/send_mail_smtp.php",
				dataType: "json",
				data: {
					user_email: email
				},
				complete: function(res) {
					var email_check = res.hasOwnProperty("responseJSON")
					if(email_check){
						dialogModal("이메일 인증", "이미 사용중인 이메일 입니다.", 'failed');
						return false;
					}
					dialogModal("인증메일발송", "인증메일이 발송되었습니다.<br>메일인증확인후 돌아와 완료해주세요", 'success');
				}
			});
		});

		// 핀번호 (오직 숫자만)
		document.getElementById('reg_tr_password').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		document.getElementById('reg_tr_password_re').oninput = function() {
			// if empty
			if (!this.value) return;

			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		$('#reg_mb_password').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});
		$('#reg_mb_password_re').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});
	});

	
	function collapse(id) {
		if ($(id + "_term").css("display") == "none") {
			$(id + "_term").css("display", "block");
			$(id + "_term").animate({
				height: "150px"
			}, 100, function() {
				$(id + ' .svg-inline--fa').css('transform', "rotate(180deg)");
			});
		} else {
			$(id + "_term").animate({
				height: "0px"
			}, 100, function() {
				$(id + "_term").css("display", "none");
				$(id + ' .svg-inline--fa').css('transform', "rotate(360deg)");
			});
		}
	}

	/* 패스워드 확인*/
	function chkPwd_1(str, str2) {
		var pw = str;
		var pw_rule = 0;
		var num = pw.search(/[0-9]/g);
		var eng = pw.search(/[a-z][A-Z]/ig);
		//var eng_large = pw.search(/[A-Z]/ig);
		var spe = pw.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

		// var pattern = /^(?!((?:[0-9]+)|(?:[a-zA-Z]+)|(?:[\[\]\^\$\.\|\?\*\+\(\)\\~`\!@#%&\-_+={}'""<>:;,\n]+))$)(.){4,}$/;
		var pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,16}$/;

		if (pw.length < 8) {
			$("#pm_1").attr('class', 'x_li');
		} else {
			$("#pm_1").attr('class', 'o_li');
			pw_rule += 1;
		}


		if (!pattern.test(pw)) {
			$("#pm_3").attr('class', 'x_li');
		} else {
			$("#pm_3").attr('class', 'o_li');
			pw_rule += 1;
		}


		if (pw_rule == 2 && str == str2) {
			$("#pm_5").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pm_5").attr('class', 'x_li');
		}

		if (pw_rule == 3) {
			return true;
		} else {
			return false;
		}
	}
	/* 출금패스워드(핀번호 확인) */
	function chkPwd_2(str, str2) {
		var pw_rule = 0;

		if (str.length < 6) {
			$("#pt_1").attr('class', 'x_li');
		} else {
			$("#pt_1").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (str == str2) {
			$("#pt_2").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pt_2").attr('class', 'x_li');
		}

		if (isNaN(str) && isNaN(str2)) {
			$("#pt_3").attr('class', 'x_li');
		} else {
			$("#pt_3").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (pw_rule >= 3) {
			return true;
		} else {
			return false;
		}
	}

	// submit 최종 폼체크
	function fregisterform_submit() {
		var f = $('#fregisterform')[0];
		

		// 이름
		if (f.mb_name.value == '' || f.mb_name.value == 'undefined') {
			dialogModal('이름입력확인', '이름을 확인해주세요', 'warning');
			return false;
		}

		// 패스워드
		if (!chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val())) {
			dialogModal('비밀번호 규칙 확인', ' 로그인 패스워드가 일치하지 않습니다', 'warning');
			return false;
		}

		// 핀코드
		if (!chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val())) {
			dialogModal('출금비밀번호(핀코드) 규칙 확인', ' 출금비밀번호(핀코드)가 일치하지 않습니다', 'warning');
			return false;
		}

		/*이용약관 체크*/
		for (var i = 0; i < $("input[name=term_required]:checkbox").length; i++) {
			if ($("input[name=term_required]:checkbox")[i].checked == false) {
				dialogModal('이용약관 동의', '이용약관과 개인정보 수집처리방침에 동의해주세요', 'warning');
				return false;
			}
		}

    f.submit();
	}

	function chkChar(obj){
		var RegExp = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+┼<>@\#$%&\'\"\\\(\=0-9]/gi;
		if (RegExp.test(obj.value)) {
			// 특수문자 모두 제거    
			obj.value = obj.value.replace(RegExp , '');
		}
	}

</script>
