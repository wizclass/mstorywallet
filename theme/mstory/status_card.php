<div class="asset_status_wrap">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="status_card_wrap esgc">
                    <h5>ESGC 자산 현황</h5>
                    <div class="quantity_wrap">
                        <p class="esgc_quantity"><?=$shift_total_token_balance?> <span class="currency"><?=ASSETS_CURENCY?></span></p>
                        <!-- <p class="price"><?=$total_token_balance_krw?> <?=BALANCE_CURENCY?></p> -->
                    </div>
                    <div class="link_btn_wrap">
                        <a class="deposit" href="javascript:link('deposit','esgc');">입금</a>
                        <a class="withdraw" href="javascript:link('withdraw','esgc');">출금</a>
                    </div>
                </div>
            </div>
            <?if($memlev > 1){?>
            <div class="swiper-slide last_slide">
                <div class="status_card_wrap eth">
                    <h5>ETH 자산 현황</h5>
                    <div class="quantity_wrap">
                        <p class="esgc_quantity"><?=$shift_total_eth_balance?> <span class="currency"><?=WITHDRAW_CURENCY?></span></p>
                        <!-- <p class="price"><?=$total_eth_balance_krw?> <?=BALANCE_CURENCY?></p> -->
                    </div>
                    <div class="link_btn_wrap">
                        <a href="javascript:link('withdraw','eth');">출금</a>
                    </div>
                </div>
            </div>
            <?}?>
        </div>
    </div>
</div>

<script>
    $(function() {
        let giftcard_cnt = <?=$giftcard_list_cnt?>;

        esgc_swiper('.giftcard_swiper',giftcard_cnt == 1 ? 1 : (giftcard_cnt > 1) ? 1.2 :  "", 25, (giftcard_cnt == 3) ? 100 : 0);
        esgc_swiper('.asset_status_wrap .swiper-container',<?php echo ($memlev > 1) ? "1.2" : "1"?> ,25);

        $(".link_btn_wrap a").on('click',function(e) {
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
        })
    })

    function esgc_swiper(target,showNum,margin,afterOffset) {
        if(target == ".asset_status_wrap .swiper-container") {
            var swiper = new Swiper(`${target}`, {
                spaceBetween: margin,
                slidesPerView: showNum,
                loop: false,
                variableWidth: true,
                loopAdditionalSlides: 1,
                on: {
                    slideChangeTransitionStart: function() {

                        if(this.realIndex == 0) {
                            $('.loadable').removeClass('active');
                            $('#deposit').toggleClass('active');
                        } else if(this.realIndex == 1) {
                            $('.loadable').removeClass('active');
                            $("#deposit").removeClass('active');
                            $("#withdraw").toggleClass('active');
                            $('.esgc .link_btn_wrap a:nth-child(1)').addClass('active').siblings().removeClass('active');
                        } 
                    },
                }
            });
        } else {
            var swiper = new Swiper(`${target}`, {
                spaceBetween: margin,
                slidesPerView: showNum,
                loop: false,
                variableWidth: true,
                slidesOffsetAfter : afterOffset, 
                on: {
                    activeIndexChange: function() {
                        if(this.realIndex == 2) {
                            $('.giftcard_more_btn').addClass('giftcard_active')
                        } else {
                            $('.giftcard_more_btn').removeClass('giftcard_active');
                        }
                    },
                }
            });
        }
    }
    
    function link(type,target) {
        var url = location.href;
        if(url.indexOf("mywallet") != -1) {
            switch_func(type)
        } else {
            location.href = `page.php?id=mywallet&view=${type}&target=${target}`;
        }
    }

</script>




