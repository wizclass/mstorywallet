<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_THEME_PATH . '/_include/breadcrumb.php');

$shop_items = get_shop_item();
$shop_items_cnt = count($shop_items);

$shop_order_result = get_my_staking($member['mb_id']);
$shop_order_cnt = sql_num_rows($shop_order_result);
?>

<main>
    <div class='container staking pt20'>
        <h1 class="main_title">모집 스테이킹</h1>
        <span class="box_ty01 sub_title"><img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt="">투자 전 KYC 인증을 반드시 완료 해주시기 바랍니다.</span>

        <?php for($i = 0; $i < $shop_items_cnt; $i++){
            if($shop_items[$i]['it_brand'] == "ETH" && $member['mb_level'] < 2) continue;
        ?>
        <?if($i == 3){echo "<div class='horizon_divide'>ETH 스테이킹</div>";}?>

        <div class='staking_card_wrap box_ty01'>
            <div class="card_border border_card<?=$shop_items[$i]['it_order']?>"></div>
            <div class="staking_card recruit_staking fill_card<?=$shop_items[$i]['it_order']?>" data-id="<?=$shop_items[$i]['it_id']?>">
                <p class='img_marker'><img src="<?=G5_THEME_URL?>/img/card_logo2.png" alt=""></p>
                <p class="item_character">
                    <?=$shop_items[$i]['it_supply_point']?><span class='percent_marker'>%</span> 
                    /
                    <?=$shop_items[$i]['it_point']?><span class='year_marker'>년</span>
                </p>
                <P class='divide'></P>
                <p class="currency"><?=$shop_items[$i]['it_brand']?></p>
            </div>
        </div>
        <?php } ?>

        <div class="line"></div>

        <h1 class="main_title">참여 스테이킹</h1>
        <div class="staking_status_wrap">
            <?php while($row = sql_fetch_array($shop_order_result)){               
                $html_index = $row['od_app_no'];    
                $end_years = $row['od_invoice_time'];
                $staking_name = "스테이킹";
                $handled_my_staking = shift_auto($row['od_cart_price'],ASSETS_CURENCY);
                $expiry_item = $row['od_refund_price'] > 0 ? "finish_staking" : "proceeding";
            ?>

            <div class='staking_card_fill_wrap'>
                <div class="card_fill fill_card<?=$html_index?>"></div>    
                <div class="staking_card my_staking <?=$expiry_item?> border_card<?=$html_index?>" data-id="<?=$row['od_id']?>">
                    <div class="staking_left_wrap">
                        <p class="item_character text_color<?=$html_index?>">
                            <?=$row['od_tax_mny']?><span class='percent_marker'>%</span> 
                            /
                            <?=$row['pay_end']/12?><span class='year_marker'>년</span>
                        </p>
                        <p class="date"><?=$row['od_date']?>~<?=$end_years?></p>
                    </div>
                    <P class='divide dark'></P>
                    <div class="staking_right_wrap">
                        <p class="value text_color<?=$html_index?>"><?=$handled_my_staking?></p>
                        <p class="currency"><?=ASSETS_CURENCY?></p>
                    </div>
                    <?if($expiry_item === 'finish_staking'){echo '<span class="finish_staking_text">참여완료</span>';}?>
                </div>
            </div>
            <?php } ?>

            <?php if($shop_order_cnt <= 0){?>
            <div class="null_data">
                현재 참여중인 스테이킹이 없습니다.
            </div>
            <?php } ?>
        </div>
    </div>
</main>

<script>
    $(".top_title h3").html("<p>스테이킹</p>");

    $(function() {
        $('.recruit_staking').on('click',function(){
            let item = $(this).data('id');
            let kyc_cert = Number("<?=$member['kyc_cert']?>");

            if(kyc_cert <= 0){
                dialogModal('KYC 인증 미등록/미승인 ', "KYC 인증 미등록/미승인<br>KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을<br>진행해주세요", 'kyc_warning');
                return false;
            }

            window.location.href=`/page.php?id=staking_application&it_id=${item}`;
        })
    });

    $('.my_staking').on('click',function(){
        var order_id = $(this).data("id");

        window.location.href=`/page.php?id=staking_detail&cate=staking&od_id=${order_id}`;
    });  
</script>
<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>