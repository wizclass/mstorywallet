<?php
$menubar = 1;
include_once('./_common.php');
$title = '비밀번호 재설정';

include_once(G5_THEME_PATH . '/_include/head.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
?>

<html>

<style>
    .notice-red {
        color: red;
    }

    .top_title h3 {
        line-height: 20px;
        display: inline-block;
        width: auto;
        margin: 0 auto;
        padding-right: 13px;
        font-size: 15px !important;
    }

    .top_title {
        color: #000;
        text-align: center;
        box-sizing: border-box;
        padding: 15px 20px;
        /* box-shadow:0 1px 0px rgba(0,0,0,0.25) */
    }
</style>


<body class="bf-login">

    <div class="find_wrap container mt-3">
        <h1 class="title">비밀번호 찾기 | 재설정</h1>
        <div class="hp_form mt-4" id="hp_form">
            <input type="text" id="mb_name" class="b_radius_10 mb-2 border" placeholder="실명 입력">
            <input type="text" id="mb_id" class="b_radius_10 mb-2 border" placeholder="아이디 입력">
            <input type="button" class="btn btn_wd btn--gray main_btn" id="hp_button" value="인증번호 받기">
        </div>

        <div id="timer_auth" class="position-relative mt-4">
            <h5 class="sub_title">
                <div>인증번호 입력</div>
                <div class="timer_down_wrap">
                    <div class='timer-down' id='timer_down'>남은 시간 01:26</div>
                </div>
            </h5>
            
            <div class="auth_wrap mt-2">
                <input type="text" id='auth_number' class="b_radius_10 border" placeholder="이메일로 전송 받은 인증번호 입력">
                <button class="btn input_btn input_btn2 main_btn" id="auth_number_confirm">확인</button>
            </div>
            
        </div>
        <div class="line"></div>
        <div id="pw_form">
            <h5 class="sub_title">비밀번호 재설정</h5>
            <input type="password" id='auth_pw' class="b_radius_10 border mt-3" placeholder='영문+숫자+특수문자 조합 8~16자리'>
            <h5 class="sub_title">비밀번호 재설정 확인</h5>
            <input type="password" id='re_auth_pw' class="b_radius_10 border mt-3" placeholder='비밀번호 재입력'>
            <input type="button" class="btn btn_wd btn-agree btn--blue main_btn" id='confirm_pw' value="확인">
        </div>

        <div class="gnb_dim"></div>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        if($('#wrapper').parent().hasClass('bf-login') == true ) {
            $('#wrapper').css('margin-left','0px').css('color','#000');     
        }
    });
</script>
<script>
    $(".top_title h3").hide();

    $('#timer_auth').hide();
    $('#pw_form').hide();
    $('.line').hide();

    $('#hp_button').click(function() {

        if ($('#mb_name').val() == "" || $('#mb_id').val() == "") {
            dialogModal('','실명 또는 아이디를 입력해주세요.','find_warning');
            return;
        }

        $.ajax({

            url: "/mail/find_pw_mail.php",
            type: "POST",
            dataType: "json",
            async: false,
            cache: false,
            data: {
                mb_id: $('#mb_id').val(),
                mb_name : $('#mb_name').val()
            },
            complete: function(res) {
                var check = res.hasOwnProperty("responseJSON")

                if (check) {
                    dialogModal('','해당회원을 찾지 못했습니다.','find_warning');
                    return;
                } else {
                    dialogModal('','입력하신 아이디로 인증번호가 전송되었습니다. 이메일을 확인해주세요.','find_success');
                    let count;
                    $('#modal_return_url').click(function() {
                        count = count_down();
                    })

                    $('#auth_number_confirm').click(function(){
                        let auth_number = $('#auth_number').val();
                        if(auth_number.length == 6){
                            
                            $.ajax({
                                url:'/util/find_pw_proc.php',
                                type: "POST",
                                dataType: "json",
                                async: false,
                                cache: false,
                                data:{
                                    type : "auth_number_check",
                                    mb_id: $('#mb_id').val(),
                                    mb_name : $('#mb_name').val(),
                                    auth_number : auth_number
                                },
                                success : (res) => {
                                    if(res.code == "200"){
                                        clearInterval(count);
                                        dialogModal('','인증이 완료되었습니다.','success');
                                        $('#modal_return_url').click(function() {
                                            $('#timer_down').hide();
                                            $('#auth_number_confirm').attr('disabled',true);
                                            $('#auth_number').attr("readonly", true);
                                            $('#pw_form').show();
                                            $('.line').show();
                                        })
                                    }else{
                                        dialogModal('',res.msg,'find_warning');
                                    }
                                }
                            })
                        }
                    })            
                }
            }
        })
    })

    $('#confirm_pw').click(function() {
        var auth_pw = $('#auth_pw').val();
        var re_auth_pw = $('#re_auth_pw').val();
        var blank = /[\s]/g;
        var pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,16}$/;
        let auth_number = $('#auth_number').val();

        if (auth_pw == "" || re_auth_pw == "") {
            dialogModal('','비밀번호를 다시 입력해주세요.','find_warning');
            return false;
        }

        if (!pattern.test(auth_pw)) {
            dialogModal('','영문+숫자+특수 문자 조합을 사용하여 최소 8 자 이상 16 자리 이하 입력해주세요.','find_warning');
            return false;
        }

        if (auth_pw != re_auth_pw) {
            dialogModal('','비밀번호를 한번 더 입력해주세요.','find_warning');
            return;
        }

        if (blank.test(auth_pw) == true) {
            dialogModal('','비밀번호에 공백이 포함 되어있습니다.','find_warning');
            return false;
        }

        $.ajax({
            url: "/util/find_pw_proc.php",
            type: "POST",
            dataType: "json",
            async: false,
            data: {
                type: "change_password",
                auth_pw: encodeUnicode(auth_pw),
                mb_id : $('#mb_id').val(),
                mb_name : $('#mb_name').val(),
                auth_number : auth_number
            },
            success: function(res) {
                dialogModal('', res.msg, 'success');
                if (res.code == "200") {
                    $('#modal_return_url').click(function() {
                        location.replace('/bbs/login_pw.php');
                    })
                }
            }
        })


    })

 function encodeUnicode(str) {
	var unicodeString = '';
	for (var i=0; i < str.length; i++) {
		var theUnicode = str.charCodeAt(i).toString(16).toUpperCase();

		while (theUnicode.length < 4) {
			theUnicode = '0' + theUnicode;
		}

		theUnicode = '\\u' + theUnicode;
		unicodeString += theUnicode;
	}

	return unicodeString;
}

</script>