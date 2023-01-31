<?php
include_once('./_common.php');

include_once(G5_THEME_PATH.'/_include/head.php');

if (isset($_SESSION['ss_mb_reg']))
	$mb = get_member($_SESSION['ss_mb_reg']);

// 회원정보가 없다면 초기 페이지로 이동
if (!$mb['mb_id'])
	goto_url(G5_URL);
?>


<style>
    .refferal{background:#f1f1f1;width:60%;margin:20px auto;padding:10px;border-radius:15px;}
    .refferal span{display:block}
    .refferal .btn{background:black;color:white;}
    .logo-login-div {margin: 70px 0 60px; text-align: center;}

    .btn--blue {
	background: #384b97;
	color: midnightblue;
}

.btn.btn-agree {
	position: fixed;
	bottom: 20px;
	left: 15px;
	width: calc(100% - 30px);
}

.btn_wd{width:100%;padding:10px 15px;display:block;}

.register-result {
	text-align: center;
}

.register-result .title {
	font-size: 32px;
}

.register-result .title + p {
	font-size: 18px;
	padding-bottom: 20px;
	width: 70%;
	margin: 0 auto;
	border-bottom: 1px solid;
}

body{
    height:0%
}

</style>
<body>

    <div class="register-result">
        <div class="container">
            <div class="logo-login-div">
                <!-- <img src="<?=G5_THEME_URL?>/img/logo.png" width="190" alt="LOGO"> -->
                <img src="<?=G5_THEME_URL?>/img/logo_.svg" width="190" alt="LOGO">
                <?if(strpos($url,'adm')){echo "<br><span class='adm_title'>For Administrator</span>";}?>
            </div>
        </div>
        <p class="title mb-5">회원가입 완료</p>
        <p class="mb-3">회원가입이 완료되었습니다.</p>
        <p>로그인후 <br> 모든 서비스를 이용가능합니다.</p>

        <a href="/"  class="btn btn_wd btn-agree btn_primary text-white">로그인</a>
    </div>

</body>

