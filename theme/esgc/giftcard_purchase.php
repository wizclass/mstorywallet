<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_THEME_PATH . '/_include/breadcrumb.php');


$sql = "select * from {$g5['g5_shop_giftcard_table']}";
$sql .= " where gt_use = 1";
$sql .= " order by gt_order asc";
$giftcard_list = sql_query($sql);

$expiry_sql = "update wpurchase_giftcard_history set coupon = '기간만료', update_date = now() where idx in 
(select idx from wpurchase_giftcard_history where 
mb_id = '{$member['mb_id']}' and 
expiry_date <= date_add(curdate(), interval -1 day) and
check_expiry_states <= 0 and
update_date = '0000-00-00 00:00:00')";
sql_query($expiry_sql);

$sql = "select * from {$g5['giftcard_history_table']}";
$sql .= " where mb_id = '".$member['mb_id']."' order by check_expiry_states,expiry_date asc ";
$giftcard_bought = sql_query($sql);
$giftcard_bought_cnt = sql_num_rows($giftcard_bought);

?>

<main>
    <div class='container giftcard_purchase pt20'>
        <h1 class="main_title">상품권 구매</h1>
        <span class="sub_title">
            <img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt=""><?=ASSETS_CURENCY?>로만 구매할 수 있습니다.<br>
            <img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt="">상품권은 <span class="font_red">구매 후 취소 / 환불이 불가합니다.</span>
        </span>        
        <?php for($i = 0; $row = sql_fetch_array($giftcard_list); $i++ ) { 
            $gt_coin = shift_auto($row['gt_coin'],ASSETS_CURENCY);
            $gt_price = shift_auto($row['gt_price'],BALANCE_CURENCY);            
        ?>
        <div class="giftcard_box box_ty01">
            <a data-toggle="collapse" href="#purchase<?= $i ?>" role="button" aria-expanded="false" aria-controls="purchase" class="dp-flex">
                <div class="gift_card_wrap">
                    <div class="product_cash_wrap">
                        <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                        <div class="product_cash_box">
                            <p class="product_text">에코페이 포인트<br>상품권</p>
                            <p class="product_price"><?= $gt_price ?></p>
                        </div>
                    </div>
                </div>
                <div class="gift_card_sub_wrap">
                    <p>에코페이<br>포인트 상품권</p>
                    <p><?= $gt_price ?>원</p>
                </div>
            </a>
        </div>        
        <div class="collapse" id="purchase<?= $i ?>">
            <div class="box_ty01">
                <h1 class="main_title">상품권 구매</h1>
                <div class="gift_card_wrap">
                    <div class="product_cash_wrap">
                        <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                        <div class="product_cash_box">
                            <p class="product_text">에코페이 포인트<br>상품권</p>
                            <p class="product_price"><?= $gt_price ?></p>
                        </div>
                    </div>
                </div>
                <div class="pruchase_detail_title">
                    <p>에코페이몰</p>
                    <p>에코페이 포인트 <?= $gt_price ?>원 상품권</p>
                </div>
                <div class="box_ty01 info_box">
                    <div class="input_wrap period_wrap">
                        <span>유효기간</span>
                        <span>발행일로부터 <?= $row['gt_valid'] ?>일</span>
                    </div>
                    <div class="input_wrap2 use_wrap">
                        <span>사용처</span>
                        <span>에코페이몰</span>
                    </div>
                </div>
                <?php
                    $total_giftcard_price = shift_coin($row['gt_price']/$coin['esgc_krw'],BONUS_NUMBER_POINT);
                    $price_coin_fee = shift_coin($total_giftcard_price * ($row['gt_fee'] * 0.01),BONUS_NUMBER_POINT);
                    $price_coin = shift_auto($total_giftcard_price + $price_coin_fee,ASSETS_CURENCY);
                ?>
                <div class="box_ty01 info_box">
                    <div class="input_wrap2 sum_purchase_wrap">
                        <span>총 결제 수량 (수수료포함)</span>
                        <span class="value">
                            <?=$price_coin?> <span class="unit"><?=ASSETS_CURENCY?></span>
                        </span>
                    </div>
                </div>
                <p class="guide"><a data-toggle="collapse" href="#guide<?= $i ?>" role="button" aria-expanded="false" aria-controls="guide">이용안내<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
                <div class="collapse" id="guide<?= $i ?>">
                    <div class="guide_content">
                        <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">상품권 이용 안내</p><br>
                        <ul>
                            <li>
                                최대 교환 가능 금액은 1회 최대 200만원, 월 구입 한도는 1,000만원
                                입니다.<br><br>
                            </li>
                            <li>
                                해당 상품권의 유효기간은 발행일로부터 <?= $row['gt_valid'] ?>일이며, 유효기간 연장이
                                불가합니다.<br><br>
                            </li>
                            <li>
                                상품권은 구매 후 취소/환불이 불가합니다.<br><br>
                            </li>
                            <li>
                                구입한 상품권은 에코페이몰에서 즉시 등록하여 사용이 가능합니다.<br><br>
                            </li>
                            <li>
                                구입한 상품권 등록은 [에코페이몰] > [로그인] > [마이페이지] >
                                [교환권등록]에서 입력해 주시기 바랍니다.<br><br>
                            </li>
                            <li>
                                등록된 상품권의 전환 포인트는 등록일로부터 1년간 사용 가능합니다.<br><br>
                            </li>
                            <li>
                                해당 상품권은 타 상품권과 타 할인 혜택의 중복 사용이 불가합니다.<br><br>
                            </li>
                            <li>
                                해당 상품권은 사용이 완료되면 취소 또는 환불이 불가하오니 이용 시 이점 필히 참고 부탁드립니다.<br><br>
                            </li>
                            <li>
                                해당 상품권은 영수증 및 현금 영수증 발급이 불가합니다.<br><br>
                            </li>
                            <li>
                                비정상적 경로나 기타 방법을 통하여 상품권을 구입할 경우, 상품권
                                사용처에서 사용이 금지되거나 제한 될 수 있으니 주의 하시기 바랍니다.<br><br>
                            </li>
                            <li>
                                해당 상품권의 일련번호가 타인에게 노출되지 않도록 주의 하시기
                                바랍니다.<br><br>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="submit_wrap">
                    <a class="btn wd main_btn2 purchase" id="<?=$row['gt_id'] ?>" onclick="giftcard_modal(<?=$row['gt_id'] ?>)">구매하기</a>
                    <a class="btn wd main_btn3 deposit" href="javascript:link('deposit','esgc');">입금하기</a>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="line"></div>
        <h1 class="main_title">상품권 구매내역</h1>

        <?php for($i = 0; $row = sql_fetch_array($giftcard_bought); $i++) {
            $price_won = shift_auto($row['price_won'],BALANCE_CURENCY);
        
            if(strtotime($row['expiry_date'],date("Y-m-d")) >= strtotime(date("Y-m-d")) && $row['check_expiry_states'] < 1 && $row['update_date'] == "0000-00-00 00:00:00"){
        ?> 
        <a data-toggle="collapse" href="#giftcard_history<?= $i ?>" role="button" aria-expanded="false" aria-controls="giftcard_history">
            <div class="giftcard_history">
                <div class="gift_card_wrap">
                    <div class="circle circle1"></div>
                    <div class="circle circle2"></div>
                    <div class="product_cash_wrap">
                        <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                        <div class="product_cash_box">
                            <p class="product_text">에코페이 포인트<br>상품권</p>
                            <p class="product_price"><?= $price_won ?></p>
                        </div>
                    </div>
                </div>
                <div class="gift_card_sub_wrap">
                    <p class="status">사용가능</p>
                    <p class="until_date"><?= str_replace("-", ".", $row['expiry_date']) ?>까지</p>
                </div>
            </div>
        </a>
        <?php }else{ ?>
        <!-- 사용완료 card 템플릿 -->
        <a data-toggle="collapse" href="#giftcard_history" role="button" aria-expanded="false" aria-controls="giftcard_history">
            <div class="giftcard_history used_giftcard">
                <div class="gift_card_wrap">
                    <div class="circle circle1"></div>
                    <div class="circle circle2"></div>
                    <div class="product_cash_wrap">
                        <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                        <div class="product_cash_box">
                            <p class="product_text">에코페이 포인트<br>상품권</p>
                            <p class="product_price"><?= $price_won ?></p>
                        </div>
                    </div>
                </div>
                <div class="gift_card_sub_wrap">
                    <p class="status"><?=$row['coupon']?></p>
                </div>
            </div>
        </a>
        <?php } ?>

        <div class="collapse" id="giftcard_history<?= $i ?>">
            <div class="box_ty01">
                <div class="gift_card_wrap" style="margin-top:30px;">
                    <div class="product_cash_wrap">
                        <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                        <div class="product_cash_box">
                            <p class="product_text">에코페이 포인트<br>상품권</p>
                            <p class="product_price"><?= $price_won ?></p>
                        </div>
                    </div>
                </div>
                <div class="serial_number_wrap">
                    <p>일련번호</p>
                    <!-- <span class="serial_number"><?= str_replace(" ", "", $row['coupon']) ?></span> -->
                    <span class="serial_number"><?= $row['coupon'] ?></span>
                    <a class="copy btn wd" onclick="coupon_copy('<?= $row['coupon'] ?>')">복사하기</a>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if($giftcard_bought_cnt == 0) { ?>
        <div class="null_data">상품권 구매내역이 없습니다.</div>
        <? } ?>        
    </div>
</main>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
<script>
    $(".top_title h3").html("<p>상품권</p>");

    function giftcard_buy(id) {

        $.ajax({
            url: g5_url+'/util/giftcard_purchase_proc.php',
            type: 'POST',
            async: false,
            cache: false,
            data: {
                esgc_krw : <?=$coin['esgc_krw']?>,
                gt_id: id
            },
            dataType: 'json',
            success: function(result) {
                if(result.code == 200) {
                    dialogModal('상품권 구매', result.msg, 'success');
                    $('.closed').click(function(){
                        location.reload();
                    });
                } else if(result.code != 200) {
                    dialogModal('상품권 구매', result.msg, 'warning');
                    $('.closed').click(function(){
                        location.reload();
                    });
                }
            },
            error: function(e) {
                console.log(e);
            }
        });
    }
    function giftcard_modal(id) {
        confirmModal('상품권 구매 후 취소/환불이 불가합니다.<br>구매 하시겠습니까?', 'giftcard_buy', id);
    }

    $(".giftcard_box").click(function () {
        if($(this).next().attr("class") == "collapse show") {
            
        } else {
            $(".collapse").not(this).removeClass("show");
        }
    })

    function coupon_copy(text){
        const textarea = document.createElement("textarea");
        document.body.appendChild(textarea);
        textarea.value = text;
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        dialogModal('',`${text}<br>쿠폰번호가 복사되었습니다.`,'success')
    }

    function link(type,target) {
        var url = location.href;
        if(url.indexOf("mywallet") != -1) {
            switch_func(type)
        } else {
            location.href = `page.php?id=mywallet&view=${type}&target=${target}`;
        }
    }

    $(document).on('click','.guide a', function() {
        collapse_image_rotate(this)
    });

    function collapse_image_rotate(target) {
        collapse_state = $(target).attr('aria-expanded');

        (collapse_state == 'true') ? deg = '180' : deg =  '360';

        $(target).find("img").css({
        "transform": `rotateX(${deg}deg)`,
        "transition": "all .2s"
        });
    }
</script>