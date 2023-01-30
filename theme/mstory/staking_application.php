<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_THEME_PATH . '/_include/breadcrumb.php');

$it_id = isset($_GET['it_id']) ? $_GET['it_id'] : false;

if(!$it_id){
    echo "<script>window.history.back()</script>";
    exit; 
}

$get_shop_items = get_shop_item(null,$it_id);
$this_item = $get_shop_items[0];

if(count($get_shop_items) <= 0){
    echo "<script>window.history.back()</script>";
    exit; 
}

?>
<main>
    <div class='container staking staking_application pt20'>
        <h1 class="main_title">스테이킹 신청</h1>
        <div class="box_ty01">
            <div class="card_border border_card<?=$this_item['it_order']?>"></div>
            <div class="staking_card recruit_staking fill_card<?=$this_item['it_order']?>" data-id="<?=$this_item['it_id']?>">
                <p class='img_marker'><img src="<?=G5_THEME_URL?>/img/card_logo2.png" alt=""></p>
                <p class="item_character">
                    <?=$this_item['it_supply_point']?><span class='percent_marker'>%</span> 
                    /
                    <?=$this_item['it_point']?><span class='year_marker'>년</span>
                </p>
                <P class='divide'></P>
                <p class="currency"><?=$this_item['it_brand']?></p>
            </div>
            <h1 class="main_title2 mt20">스테이킹 수량 입력</h1>
            <div class="input_wrap">
                <input type="text" placeholder="0" id="staking_quantity" inputmode="numeric">
                <label class='currency-right'> <?= ASSETS_CURENCY ?></label>
            </div>
            <span class="sub_title"><img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt="">
                최소 참여 수량은 <?=number_format($this_item['it_price'])?> <?= ASSETS_CURENCY ?> 입니다.
            </span>
            <div class="staking_input_wrap">
                <div class="fee_top_wrap">
                    <span class="title">수수료</span>
                    <p>
                        <span class="value" id="staking_fee">0</span>
                        <label class='currency-right'><?= ASSETS_CURENCY ?></label>
                    </p>
                </div>
                <div class="fee_bottom_wrap">
                    <span class="title">총 스테이킹 수량(수수료 포함)</span>
                    <p>
                        <span class="value" id="total_staking">0</span>
                        <label class='currency-right'><?= ASSETS_CURENCY ?></label>
                    </p>
                </div>
            </div>
            <h1 class="main_title2">
                <span>전체동의</span>
                <div class="agree_wrap">
                    <input type="checkbox" name="all_check" id="all_check">
                    <label for="all_check" class="chk_all"></label>
                </div>
            </h1>
            <div class="agree_content_wrap">
                <div class="text_wrap">
                    <img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt=""><span>스테이킹 상품 투자 유의사항 확인<a href="#" class="content_all_view">약관보기</a></span>
                    <div class="agree_btn_wrap">
                        <input type="checkbox" name="check1" id="check1">
                        <label for="check1" class="chk_all"></label>
                    </div>
                </div>
                <div class="text_wrap">
                    <img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt=""><span>사용중인 지갑의 비밀번호 등 주요 정보를 분실시 복구가 <b>절대 불가능</b>합니다.</span>
                    <div class="agree_btn_wrap">
                        <input type="checkbox" name="check2" id="check2">
                        <label for="check2" class="chk_all"></label>
                    </div>
                </div>
                <div class="text_wrap">
                    <img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt=""><span>스테이킹 신청 직후부터 취소 <b>불가</b>합니다.</span>
                    <div class="agree_btn_wrap">
                        <input type="checkbox" name="check3" id="check3">
                        <label for="check3" class="chk_all"></label>
                    </div>
                </div>
            </div>
            <div class="submit_wrap">
                <a class="btn wd main_btn2" id="staking_btn">스테이킹 신청</a> 
            </div>
        </div>
    </div>
</main>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

<script>
    $('#staking_quantity').change(()=>{
        let staking_quantity = conv_number($('#staking_quantity').val());
        let fee = Number(staking_quantity) * (Number("<?=$this_item['it_cust_price']?>") * 0.01);
        let total_staking_quantity = staking_quantity - fee;
        $('#staking_fee').text(Price(fee));
        $('#total_staking').text(Price(total_staking_quantity));
    })

    $('.content_all_view').on('click',function(e) {
        e.preventDefault();
        const html = `
            <div class="modal_content">
                <p>스테이킹 상품 투자 유의사항</p>
                <p class="title">
                    1. 주식회사 지엘에코팜은 스테이킹에 관한 수익을 보장하지
                    않으며, 가상 프로젝트나 밸리데이터의 사정에 따라 손실이
                    발생할 수도 있습니다.
                </p>
                <p class="title">
                    2. 스테이킹 신청한 디지털 자산은 스테이킹 목적으로 고객님
                    의 ESG Chain 지갑에서 출금 처리되며, 보유자산 및 출금 가능
                    자산에서 제외 됩니다. 고객님은 여전히 해당 디지털 자산에
                    대한 권리를 보유하고 있으며, 주식회사 지엘에코팜은 고객님
                    을 위하여 스테이킹 업무를 처리합니다.
                </p>
                <p class="title">
                    3. 스테이킹 신청이후, 스테이킹 완료 및 보상 발생 시작까지
                    대기기간이 발생 할 수 있습니다. 스테이킹 신청 전에 스테이킹
                    대기기간을 확인해 주시기 바랍니다.
                </p>
                <p class="title">
                    4. 스테이킹 완료 후 자산을 찾을 때, 대기 기간을 반드시 확인해
                    주시기 바랍니다.
                </p>
                <p class="title">
                    5. 보상 수익은 매월 정기 지급됩니다.
                </p>
                <p class="title">
                    6. 스테이킹 신청 직후부터 취소가 불가합니다.
                </p>
            </div>
        `;

        show_alert_terms(html)
    })
        
        $('#all_check').click(function(){
            var checked = $('#all_check').is(':checked');
            
            if(checked) {
                $('input:checkbox').prop('checked',true);
            } else {
                $('input:checkbox').prop('checked',false);
            }
        });

        $('#check1, #check2, #check3').on('click',function() {
            if($('#all_check').is(":checked") == true) {
                $('#all_check').prop('checked',false);
            }

            if($('#check1').prop('checked') == true && $('#check2').prop('checked') == true && $('#check3').prop('checked') == true) {
                $('#all_check').prop('checked',true)
            }
        });
       
    	//패키지구매처리
        $('#staking_btn').on('click', function() {
            let token = "<?=ASSETS_CURENCY?>";
            let total_balance = Number('<?=$total_token_balance?>');
            let staking_quantity = conv_number($('#staking_quantity').val());
            let min_quantity = <?=$get_shop_items[0]['it_price']?>;
            let it_id = "<?=$it_id?>";

            if(staking_quantity < min_quantity){
                dialogModal('',`최소 스테이킹 ${token} 수량은 ${Price(min_quantity)} 입니다.`, 'warning');
                return false;
            }

            if (total_balance < staking_quantity) {
                dialogModal('',`보유하고 계신 ${token} 수량이 부족합니다.`, 'warning');
                return false;
            }
            
            if($("#check1").prop('checked') == false || $("#check2").prop('checked') == false || $("#check3").prop('checked') == false) {
                dialogModal('','약관동의를 체크해 주세요', 'warning');
                return false;
            }

            $.ajax({
                type: "POST",
                url: "/util/upstairs_proc.php",
                async: false,
                cache: false,
                dataType: "json",
                data: {
                    "od_cart_price" : staking_quantity,
                    "od_tno": it_id
                },
                success: function(res) {
                    if(res.code == "200"){
                        // dialogModal(res.msg, res.msg, 'success');
                        dialogModal('', "<b>스테이킹 신청 완료</b> <br> 스테이킹 신청이 완료되었습니다.", 'success');

                        $('.closed').click(function(){
                                window.location.href = '/page.php?id=staking';
						});
                    }else{
                        dialogModal(res.msg, 'warning');
                    }
                    // console.log(res);
                },
                error: function(e) {
                    // dialogModal('회원님 죄송합니다. 현재 시스템의 장애로 인한 문제가 발생하였습니다. 계속 문제가 발생한다면 관리자에 문의해주세요.','warning');
                }
            });
        });
</script>