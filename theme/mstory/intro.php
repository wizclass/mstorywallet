<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>MSTORY WALLET</title>
		<style>
			body{
				overflow-y: hidden;
			}
			.container {
				margin:0;
				padding:0;
				width:100%;
				display:block;
				height:100vh;
				background:#000 url('<?=G5_THEME_URL?>/img/launcher.png') no-repeat center;
				background-size:cover;
			}
			#btnDiv {
				display: none;
				text-align: center;
				position:absolute;
				bottom: 80px;
				width:100%;
				padding:0 30px;
				z-index:1000;
			}
			.btn_ly{
				width:100%;
				text-align:center;
				margin:0 auto;
			}
			#myProgress {
				width: 100%;
			}
			#myBar {
				width: 1%;
				height: 2px;
			}
			.animate-bottom {
				position: relative;
				-webkit-animation-name: animatebottom;
				-webkit-animation-duration: .6s;
				animation-name: animatebottom;
				animation-duration: .6s
			}
			.intro_title {
				color:white;position:fixed;bottom:10px;text-align:center;width:100%;
			}
			.intro_title p {
				line-height:26px;letter-spacing:0;
			}
			.login_btn {
				color: #0c3879 !important;
				border: 1px solid #fff !important;
				background: #f8fbff;
				padding: 16px;
				font-weight: bold !important;
			}
			.login_btn:hover {
				background: #d7e8ff;
				border-color: #d7e8ff !important;
			}
			.signup_btn {
				color: white !important;
				border: 0.5px solid #fff !important;
				background: linear-gradient(157deg, rgba(10,125,206,0.83) 48%, rgba(8,52,117,0.83));
				padding: 16px;
				font-weight: bold !important;
			}
			.signup_btn:hover {
				background: rgba(12, 56, 121, 1);
				border-color: #002964 !important;
			}
			@-webkit-keyframes animatebottom {
				from { bottom:70px; opacity:0 }
				60%, 100% { bottom:80px; opacity:1 }
			}
			@keyframes animatebottom {
				from{ bottom:70px; opacity:0 }
				60%, 100% { bottom:80px; opacity:1 }
			}
			@media (min-width: 767px) {
				body{background:#0b0c13}
				.container{width:550px;margin:0 auto;}
				#btnDiv{width:550px;}
				.intro_title{width:550px;}
			}
		</style>
	</head>	
	<body>
		<div class="container">
			<div id="myBar"></div>
			<div id="btnDiv" class="animate-bottom">
				<div class='btn_ly'>
					<? include_once(G5_THEME_PATH.'/_include/lang.php'); ?>
					<a href="/bbs/login_pw.php" class="btn btn_wd login_btn">LOG IN</a>
					<a href="/bbs/register_form.php" class="btn btn_wd signup_btn">SIGN UP</a>
				</div>
			</div>
		</div>
		<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER; ?>"></script>
		<script>
			var myVar;
			var maintenance = "<?=$maintenance?>";

			$(document).ready(function(){
				move();
			});
			

			function temp_block(){
				commonModal("Notice",' 방문을 환영합니다.<br />사전 가입이 마감되었습니다.<br />가입하신 회원은 로그인 해주세요.<br /><br />Welcome to One-EtherNet.<br />Pre-subscription is closed.<br />If you are a registered member,<br />please log in.',220);
			}

			function showPage() {
				document.getElementById("myBar").style.display = "none";
				document.getElementById("btnDiv").style.display = "block";
			}

			function move() {
				var elem = document.getElementById("myBar");
				var width = 1;
				var id = setInterval(frame, 2);
				function frame() {
				if (width >= 100) {
					clearInterval(id);

					if(maintenance == 'N'){
					showPage();
					}
				} else {
					width++;
					elem.style.width = width + '%';
				}
				}
			}

			function auto_login(){

				if(typeof(web3) == 'undefined'){
					window.location.href = "/bbs/login_pw.php";
				}

				window.ethereum.enable().then((err) => {

				web3.eth.getAccounts((err, accounts) => {
					if(accounts){
					$.ajax({
						url: "/bbs/login_check.php",
						async: false,
						type: "POST",
						dataType: "json",
						data:{
							trust : "trust",
							ether : accounts
						},
						success: function(res){
							if(res.result == "OK"){
								window.location.href = "/page.php?id=structure";

							}

							if(res.result == "FAIL"){
								alert("EHTEREUM ADDRESS is not registered. Please Sign In or Sign Up.");
								window.location.href = "/bbs/login_pw.php";
							}

							if(res.result == "ERROR"){
								alert("ERROR");
							}
						}
					});
				}
				})
			});
			}
		</script>
	</body>
</html>