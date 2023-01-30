<?php
$menubar = 1;
include_once('./_common.php');
$title = '아이디 찾기';

include_once(G5_THEME_PATH . '/_include/head.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
?>


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

<script src="<?=G5_URL?>/js/certify.js"></script>

<body class="bf-login">
        <form id="ffindform" name="ffindform" action="" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="cert_type" value="">
        <input type='hidden' name="cert_no" value="">
        
        
        <div class="find_wrap container mt-3">
            <h1 class="title"><?=$title?></h1>
            <p class="id_sub_title mt-3"><span>실명/휴대폰 번호 인증</span>으로<br>가입하신 아이디를 찾습니다.</p>
            <div class="hp_form mt-4" id="hp_form">
              <input type="button" id="win_hp_cert" class='btn btn_wd btn_primary' value="휴대폰 본인인증" >
            </div>
        </div>
        
        </form>

    <div class="gnb_dim"></div>
</body>


<script>
	$(function() {
        var pageTypeParam = "pageType=find";
		
        <?php if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
		var params = "";

        // 휴대폰인증
        $("#win_hp_cert").on("click",function() {
            
            if(!cert_confirm())
                if(!cert_confirm()) return false;
            	params = "?" + pageTypeParam;
            <?php
            switch($config['cf_cert_hp']) {
                case 'kcb':
                    $cert_url = G5_OKNAME_URL.'/hpcert1.php';
                    $cert_type = 'kcb-hp';
                    break;
                case 'kcp':
                    $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
                    $cert_type = 'kcp-hp';
                    break;
                default:
                    echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");';
                    echo 'return false;';
                    break;
            }
            ?>

			certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
            return;
        });
        <?php } ?>
    });

    // 인증체크
    function cert_confirm()
    {
        var val = document.ffindform.cert_type.value;
        var type;

        switch(val) {
            case "ipin":
                type = "아이핀";
                break;
            case "hp":
                type = "휴대폰";
                break;
            default:
                return true;
        }

        if(confirm("이미 "+type+"으로 본인확인을 완료하셨습니다.\n\n이전 인증을 취소하고 다시 인증하시겠습니까?"))
            return true;
        else
            return false;
    }
</script>
    