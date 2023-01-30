<?php
$menubar = 1;
include_once('./_common.php');
// $title = '아이디 찾기';

include_once(G5_THEME_PATH . '/_include/head.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');


?>

<html>



<body class="bf-login">

    <div class="find_wrap container mt-3">
        <h1 class="title">아이디찾기</h1>
        <div class="result_wrap mt-3">
            <p>고객님께서 가입하신 아이디입니다.</p>
            <p class="find_id">abc@naver.com</p>
        </div>
        <!-- <div class="notice-red" id="notice_phone" style="display:none;"></div>

        <div id="timer_auth" class="position-relative mt-4 mb-5" style="">
            <div class='timer-down' id='timer_down'></div>
            <input type="text" id='hp_auth' class="b_radius_10 border" placeholder="인증번호 입력">
        </div>

        <div id="pw_form" style="">
            <input type="password" id='auth_pw' class="b_radius_10 border" placeholder='비밀번호 재설정' data-i18n="[placeholder]find_pw.비밀번호 재설정">
            <input type="password" id='re_auth_pw' class="b_radius_10 border" placeholder='비밀번호 재설정 확인' data-i18n="[placeholder]find_pw.비밀번호를 재설정 확인">
            <input type="button" class="btn btn_wd btn-agree btn--blue b_radius_10 wd" id='confirm_pw' value="확인" data-i18n="[value]find_pw.확인">
        </div>

        <div class="notice-red" id="notice_password" style="display:none;"></div> -->

        <div class="gnb_dim"></div>
        <div class="id_submit_wrap mt-3">
            <a href="page.php?id=find_pw" class="btn btn_wd btn--gray wd main_btn">비밀번호 찾기</a>
            <a href="/bbs/login_pw.php" class="btn btn_wd btn--gray wd login_btn">로그인 하기</a>
        </div>
        
    </div>

</body>

</html>
<script>
    $(document).ready(function() {
        if($('#wrapper').parent().hasClass('bf-login') == true ) {
            $('#wrapper').css('margin-left','0px').css('color','#000');
        }
    });

    $(".top_title h3").hide();
</script>
