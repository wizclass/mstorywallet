<?php
$menubar = 1;
include_once(G5_THEME_PATH . '/_include/head.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
// include_once(G5_THEME_PATH.'/_include/lang.php');

if ($nw['nw_enroll'] == 'Y') {
} else {
	alert("현재 서비스를 이용할수없습니다.");
}

$service_term = get_write("g5_write_agreement", 1);
$private_term = get_write("g5_write_agreement", 2);
$marketing_term = get_write("g5_write_agreement", 3);

// 추천인링크 타고 넘어온경우
if ($_GET['recom_referral']) {
	$recom_sql = "select mb_id,mb_nick from g5_member where mb_no = '{$_GET['recom_referral']}'";
	$recom_result = sql_fetch($recom_sql);
	$mb_recommend = $recom_result['mb_id'];

	if ($recom_result['mb_nick'] != '') {
		$mb_center = $mb_recommend;
	}
}
?>

<link href="<?= G5_THEME_URL ?>/css/scss/enroll.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v5.2.0/js/all.js"></script>
<script src="<?= G5_URL ?>/js/certify.js"></script>
<script>
	var recommned = "<?= $mb_recommend ?>";
	var recommend_search = false;

	var center_search = false;

	if (recommned) {
		recommend_search = true;
	}

	/*추천인, 센터멤버 등록*/
	function getUser(etarget, type) {
		var target = etarget;
		if (type == 1) {
			var target_type = "#referral";
		} else if (type == 2) {
			var target_type = "#center";
		} else {
			var target_type = "#director";
		}
		console.log(target + ' === ' + type);

		$.ajax({
			type: 'POST',
			url: '/util/ajax.recommend.user.php',
			data: {
				mb_id: $(target).val(),
				type: type
			},
			success: function(data) {
				var list = JSON.parse(data);

				if (list.length > 0) {
					$(target_type).modal('show');
					var vHtml = $('<div>');

					$.each(list, function(index, obj) {
						// vHtml.append($("<div>").addClass('user').html(obj.mb_id));

						if (type == 2) {
							if (obj.mb_level > 0) {
								vHtml.append($("<div style='text-indent:-999px'>").addClass('user').html(obj.mb_id));
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							} else {
								vHtml.append($("<div style='color:red;text-indent:-999px'>").addClass('non_user').html(obj.mb_id));
								vHtml.append($("<label style='color:red'>").addClass('mb_nick').html(obj.mb_nick));
							}
						} else {
							if (obj.mb_level >= 0) {
								vHtml.append($("<div>").addClass('user').html(obj.mb_id));
							} else {
								vHtml.append($("<div style='color:red;>").addClass('non_user').html(obj.mb_id));
							}
						}
						/* 
						if (obj.mb_level > 0) {
							

							if(type == 2){
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							}

						} else {
							if(type == 2){
								
							}else{
								vHtml.append($("<div style='color:red;>").addClass('non_user').html(obj.mb_id));
							}
						} */


					});

					$(target_type + ' .modal-body').html(vHtml.html());
					first_select();


					/* 첫번째 선택되어있게 */
					function first_select() {
						$(target_type + ' .modal-body .user:nth-child(1)').addClass('selected');

						if (type == 2) {
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html())
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
						} else {
							$(target).val($(target_type + ' .modal-body .user.selected').html());
						}
					}


					$(target_type + ' .modal-body .user').click(function() {
						// console.log('user click');
						$(target_type + ' .modal-body .user').removeClass('selected');
						$(target + ' .modal-body .user').removeClass('selected');
						$(this).addClass('selected');
					});


					$(target_type + ' .modal-footer #btnSave').click(function() {

						if (type == 2) {
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html());
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
							center_search = true;
						} else {
							$(target).val($(target_type + ' .modal-body .user.selected').html());
							recommend_search = true;
							$('#reg_mb_center').val($(target_type + ' .modal-body .user.selected').html());
						}
						$(target).attr("readonly", true);
						$(target_type).modal('hide');
					});

				} else {

					dialogModal('처리 결과', '해당되는 회원이 없습니다.', 'failed');
				}
			}
		});

	} ///*추천인등록*/
</script>

<div class="v_center" style="padding:0;">

	<div class="enroll_wrap" style="padding:0;">
		<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" style="padding:1rem" method="post" enctype="multipart/form-data" autocomplete="off">
			<input type='hidden' name="cert_type" value="">
			<input type='hidden' name="cert_no" value="">

			<!-- 추천인 정보 -->
			<p class="check_appear_title mt10"><span>추천인정보</span></p>
			<section class='referzone'>
				<div class="btn_input_wrap">
					<input type="text" name="mb_recommend" id="reg_mb_recommend" value="<?= $mb_recommend ?>" required placeholder="추천인 아이디" />
					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check " onclick="getUser('#reg_mb_recommend',1);"><span>검색</span></button>
					</div>
				</div>
			</section>

			<p class="check_appear_title mt40"><span>개인 정보 & 인증</span></p>
			<div>
				<!-- <input type="text" name="mb_hp"  id="reg_mb_hp" class='cabinet'  pattern="[0-9]*" style='padding:15px' required  placeholder="휴대폰번호"/>
				<span class='cabinet_inner' style=''>※'-'를 제외한 숫자만 입력해주세요</span> -->


				<!-- <div class='in_btn_ly'><input type="button" id='win_hp_cert' class='btn_round check hp_cert' value="휴대폰 본인인증" style="width:80px;"></div> -->
				<?php if (REGISTER_USEPASS) { ?>
					<input type="button" id='win_hp_cert' class='btn btn_wd btn_primary hp_cert' value="휴대폰 본인인증" style="width:100%;">
					<input type="text" name="mb_name" style='padding:15px;display:none;' id="reg_mb_name" required placeholder="이름" />
					<input type="text" name="mb_hp" id="reg_mb_hp" class='hp_cert' style='padding:15px;display:none;' readonly placeholder="휴대폰번호" />
					<input type="email" name="mb_id" class='cabinet' style='padding:15px' id="reg_mb_id" required placeholder="아이디 (이메일형식)" />
					<span class='cabinet_inner' style=''>※이메일형식으로 입력해주세요</span>
					<div class='in_btn_ly'><input type="button" id='EmailChcek' class='btn_round check' value="이메일 인증"></div>
				<?php } else { ?>
					<input type="text" name="mb_name" style='padding:15px;' id="reg_mb_name" required placeholder="이름" />
					<div class="" style="display: flex; align-items: center; margin-top: 15px">
						<div class="nation_number_wrap">
							<select id="nation_number" name="nation_number" required>
								<option value="1">1</option>
								<option value="81">81</option>
								<option value="82" selected>82</option>
								<option value="84">84</option>
								<option value="86">86</option>
								<option value="62">62</option>
								<option value="63">63</option>
								<option value="66">66</option>
							</select>
						</div>
						<input type="text" name="mb_hp" id="reg_mb_hp" class='hp_cert' style='padding:15px; margin-top: 0px;' required placeholder="휴대폰번호" />
					</div>
					
					<input type="text" name="mb_id" style='padding:15px' id="reg_mb_id" required placeholder="아이디" />
					<input type="email" name="mb_email" class='cabinet' style='padding:15px' id="reg_mb_email" required placeholder="이메일" />
					<span class='cabinet_inner' style=''>※이메일형식으로 입력해주세요</span>
					<div class='in_btn_ly'><input type="button" id='EmailChcek' class='btn_round check' value="이메일 인증"></div>
				<?php } ?>
			</div>


			<ul class="clear_fix pw_ul mt20">
				<li>
					<input type="password" name="mb_password" id="reg_mb_password" minlength="4" maxlength="12" placeholder="로그인 비밀번호" />
					<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="4" maxlength="12" placeholder="로그인 비밀번호 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;'>비밀번호 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pm_1">4자 이상 12자 이하</li>
						<li class="x_li" id="pm_3">영문+숫자+특수문자</li>
						<li class="x_li" id="pm_5">비밀번호 비교</li>
					</ul>
				</li>
				<li style='margin-left:5px'>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password" name="reg_tr_password" placeholder="출금비밀번호(핀코드)" />
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password_re" name="reg_tr_password_re" placeholder="출금비밀번호(핀코드) 확인" />

					<strong><span class='mb10' style='display:block;font-size:13px;'>핀코드 설정 조건</span></strong>
					<ul>
						<li class="x_li" id="pt_1">6 자리</li>
						<li class="x_li" id="pt_3">숫자</li>
						<li class="x_li" id="pt_2">핀코드 비교</li>
					</ul>
				</li>
			</ul>



			<!--
			<hr>
			<div class="agreement_btn"> <button type="button" class="agreeement_show btn"><span data-i18n='register.회원가입 약관보기'>Read Terms and Conditions</span></button></div>
			-->

			<p class="check_appear_title mt40"><span>회원가입 약관동의 </span></p>
			<div class="mt20">
				<div class="term_space">
					<input type="checkbox" id="service_checkbox" class="checkbox-style-square term_none" name="term_required">
					<label for="service_checkbox" style="width:25px;height:25px;">
						<span style='margin-left:10px;line-height:30px;'><?= $service_term['wr_subject'] ?> 동의 (필수)</span>
						<a id="service" href="javascript:collapse('#service');" style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="service_term" class="term_textarea term_none"><?= $service_term['wr_content'] ?></textarea>
				</div>



				<div class="term_space">
					<input type="checkbox" id="private_checkbox" class="checkbox-style-square term_none" name="term_required">
					<label for="private_checkbox" style="width:25px;height:25px;">
						<span style='margin-left:10px;line-height:30px;'><?= $private_term['wr_subject'] ?> 동의 (필수)</span>
						<a id="private" href="javascript:collapse('#private');" style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="private_term" class="term_textarea term_none"><?= $private_term['wr_content'] ?></textarea>
				</div>


				<div class="term_space">
					<input type="checkbox" id="marketing_checkbox" class="checkbox-style-square term_none" name="mb_sms" value="1">
					<label for="marketing_checkbox" style="width:25px;height:25px;">
						<span style='margin-left:10px;line-height:30px;'><?= $marketing_term['wr_subject'] ?> 동의 (선택)</span>
						<a id="marketing" href="javascript:collapse('#marketing');" style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
					<textarea id="marketing_term" class="term_textarea term_none"><?= $marketing_term['wr_content'] ?></textarea>
				</div>

			</div>


			<div class="btn2_wrap " style='width:100%;height:60px; display: flex'>
				<input class="btn btn_double btn_secondary btn_cancle" type="button" value="취소">
				<input class="btn btn_double btn_primary submit main_btn" type="button" onclick="fregisterform_submit();" value="신규 회원 등록하기">
			</div>


		</form>
	</div>

</div>

</section>

<div class="gnb_dim"></div>



<script>
	$(function() {
		$(".top_title h3").html("<span style='font-size:16px;line-height:16px;'>신규 회원등록</span>");

		onlyNumber('reg_mb_hp');
		$('.cabinet').on('click', function() {
			$(this).next().css('display', 'contents');
		});

		$('.cabinet').on('mouseout', function() {
			$(this).next().css('display', 'none');
		});

		$('.btn_cancle').on('click', function() {
			dialogModal("회원가입 취소", "입력했던 내용을 모두 삭제하고 초기화면으로 돌아갑니다.", 'warning');
			$('#modal_return_url').on('click', function() {
				location.href = g5_url;
			});
		});

		/*이메일 체크*/
		$('#EmailChcek').on('click', function() {
			if(<?=REGISTER_USEPASS?>) {
				var email = $('#reg_mb_id').val();
			} else var email = $('#reg_mb_email').val();
			
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
					if (email_check) {
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

	//휴대폰 인증
	/* var mb_hp_check = false;
	document.querySelector('#hp_check').addEventListener('click',function(){
		dialogModal("휴대폰번호 인증", "휴대폰번호 인증이 완료되었습니다.", 'success');
		mb_hp_check = true;

	}) */

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

		if (pw.length < 4) {
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


	$(function() {
		mb_hp_check = true;
		var pageTypeParam = "pageType=register";

		<?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
			var params = "";
			// 휴대폰인증
			$("#win_hp_cert").click(function() {
				if (!cert_confirm())
					if (!cert_confirm()) return false;
				params = "?" + pageTypeParam;
				<?php
				switch ($config['cf_cert_hp']) {
					case 'kcb':
						$cert_url = G5_OKNAME_URL . '/hpcert1.php';
						$cert_type = 'kcb-hp';
						break;
					case 'kcp':
						$cert_url = G5_KCPCERT_URL . '/kcpcert_form.php';
						$cert_type = 'kcp-hp';
						break;
					default:
						echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");';
						echo 'return false;';
						break;
				}
				?>

				certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>" + params);
				return;
			});
		<?php } ?>
	});

	// 인증체크
	function cert_confirm() {
		var val = document.fregisterform.cert_type.value;
		var type;

		switch (val) {
			case "ipin":
				type = "아이핀";
				break;
			case "hp":
				type = "휴대폰";
				break;
			default:
				return true;
		}

		if (confirm("이미 " + type + "으로 본인확인을 완료하셨습니다.\n\n이전 인증을 취소하고 다시 인증하시겠습니까?"))
			return true;
		else
			return false;
	}


	// submit 최종 폼체크
	function fregisterform_submit() {
		var f = $('#fregisterform')[0];


		// 이름
		if (f.mb_name.value == '' || f.mb_name.value == 'undefined') {
			dialogModal('이름입력확인', '이름을 확인해주세요', 'warning');
			return false;
		}

		//아이디 중복체크
		// if (check_id == 0) {
		// 	dialogModal('ID 중복확인', '아이디 중복확인을 해주세요.', 'warning');
		// 	return false;
		// }

		//추천인 검사
		if (f.mb_recommend.value == '' || f.mb_recommend.value == 'undefined') {
			dialogModal('추천인정보 확인', "<strong>추천인 아이디 검색하여 목록에서 선택해주세요.</strong>", 'warring');
			return false;
		}
		if (!recommend_search) {
			dialogModal('추천인정보 확인', "<strong>추천인 아이디 검색하여 목록에서 선택해주세요.</strong>", 'warring');
			return false;
		}

		//추천인이 본인인지 확인
		if (f.mb_id.value == f.mb_recommend.value) {
			commonModal('조직 관계 입력 확인', '<strong> 자신을 추천인으로 등록할수없습니다. </strong>', 80);
			f.mb_recommend.focus();
			return false;
		}

		// 연락처
		if (f.mb_hp.value == '' || f.mb_hp.value == 'undefined' || !mb_hp_check) {
			dialogModal('휴대폰번호확인', '휴대폰 번호가 잘못되거나 누락되었습니다.', 'warning');
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

		if(<?=REGISTER_USEPASS?>) {
				var user_email = $('#reg_mb_id').val();
			} 
		else var user_email = $('#reg_mb_email').val();

		// 메일인증 체크
		$.ajax({
			type: "POST",
			url: "/mail/check_mail_for_register.php",
			cache: false,
			async: false,
			dataType: "json",
			data: {
				user_email: user_email
			},
			success: function(res) {
				if (res.result == "OK") {
					f.submit();
				} else {
					dialogModal("이메일 인증", res.res, 'failed');

				}

			},
			error: function(e) {
				console.log(e)
			}
		});
	}

	function chkChar(obj) {
		var RegExp = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+┼<>@\#$%&\'\"\\\(\=0-9]/gi;
		if (RegExp.test(obj.value)) {
			// 특수문자 모두 제거    
			obj.value = obj.value.replace(RegExp, '');
		}
	}
</script>