<? 
	include_once(G5_THEME_PATH.'/_include/head.php'); 
	$temp_id = get_cookie("ck_ca_id", $mb_id, time() + 86400*31);
?>
<style>
#wrapper{
	background:#f8fbff;
	margin: 0 auto;
}

</style>
<section id="wrapper">
	<div class="v_center">
		<div class="login_wrap">
			<div class="logo_login_div">
				<img src="<?=G5_THEME_URL?>/img/logo_.svg" alt="LOGO">
				<?if(strpos($url,'adm')){echo "<br><span class='adm_title'>For Administrator</span>";}?>
			</div>

			<form name="flogin" method="post" id="login_form">
					<input type="hidden" id="url" name="url" value="<?=$url?>">
				<div>
					<label for="u_name"><span>아이디</span></label>
					<input type="text" name="mb_id" id="u_name" placeholder="아이디" value="<?=$temp_id?>"/>

				</div>
				<div>
					<label for="u_pw"><span >비밀번호</span></label>
					<input type="password" name="mb_password" id="u_pw" style="line-height:22px;" placeholder="비밀번호 입력" onkeyup="press(event)"/>
				</div>

				<div class="check_ty01" style='display:flex; align-items:center; padding: 6px'>
					<input type="checkbox" id="showPw" >
					<label for="showPw" style="margin:0">비밀번호 표시</label>
				</div>
			
				<!-- <div style='text-align:left'>
					<input type="checkbox" name="auto_login"  style="width:auto" id="login_auto_login" checked >
					<label for="login_auto_login" class="auto_login" style="display:inline-block">자동로그인</label>
				</div> -->
				

				<div class="login_btn_bottom">
					<a href="/page.php?id=find_id_by_email" class='desc' style="text-decoration: none">아이디 찾기</a>
					<a href="/page.php?id=find_pw" class='desc find_pw' style="text-decoration: none">비밀번호 찾기</a>
					<div class="login_btn_wrap">
						<button type="button" class="btn wd main_btn" onclick="flogin_submit();" rerender="form"><span>로그인</span></button>
						<a href="/bbs/register_form.php" class="btn wd btn_secondary"><span>회원 가입</span></a>
					</div>
					<!-- <a href="javascript:temp_block();" class="btn btn_wd btn_default"><span data-i18n="login.신규 회원 등록하기">Create new account</span></a> -->
				</div>
			</form>

			
		</div>
		
	</div>

	<div class='footer'>
		<p class='copyright'><?=copyright?></p>
	</div>
	
</section>

<style>
	#showPw {
		width: 18px;
		height: 18px;
		border: 1px solid #767676;
		border-radius: 3px;
		margin-right: 8px;
		position: relative;
	}

	#showPw:checked {
		background-color: #00a2ff;
		border-color: #00a2ff;
	}

	#showPw + label {
		font-size: 13px;
	}

	#showPw:checked::after {
		content: '✔';
		color: #fff;
		position: absolute;
		left: 50%;
		top: -2px;
		transform: translateX(-50%);
		font-size: 12px;
	}
</style>

<script type="text/javascript">


	$('#showPw').on('click', function() {
		if($('#showPw').hasClass('active')) {
			$('#u_pw').attr("type", "password");
			$('#showPw').removeClass('active');
		} else {
			$('#u_pw').attr("type", "text");
			$('#showPw').addClass('active');
		}
	});

	function press(e){
		if(e.keyCode == 13){
			flogin_submit();
			e.preventDefault();
		}
	}

	function flogin_submit(){
		// $('form[name=flogin]').submit();

		$.ajax({
			url : '/bbs/login_check.php',
			type : "POST",
			dataType : "json",
			async : false,
			cache : false,
			data : {
				mb_id: document.querySelector('#u_name').value,
				mb_password: document.querySelector('#u_pw').value,
				url: "<?=$login_url?>"
			},
			success : function(res) {
				if(res.code != "200"){
					dialogModal("", res.msg, 'warning');
					
					return false;
				}
				location.href = res.url;
			},
			error: function(e) {
				const json = JSON.stringify(e);
				alert(json);
			}
			})

		return false;
		}


	
	function showhelp(){
		$('.helpmail').toggle();
	}

	function press(f){
		if(event.keyCode == 13){
			flogin_submit();
		}
	}
	
	function temp_block(){
		commonModal("Notice",'방문을 환영합니다.<br />사전 가입이 마감되었습니다.<br />가입하신 회원은 로그인 해주세요.<br /><br />Welcome to One-EtherNet.<br />Pre-subscription is closed.<br />If you are a registered member,<br />please log in.',220);
	}
</script>