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
        <span class="content_box5 sub_title"><img class="caution" src="<?=G5_THEME_URL?>/img/caution.png" alt="">투자 전 KYC 인증을 반드시 완료 해주시기 바랍니다.</span>

        <?php for($i = 0; $i < $shop_items_cnt; $i++){
            if($shop_items[$i]['it_brand'] == "ETH" && $member['mb_level'] < 2) continue;
        ?>
        <div class="card_border"></div>
        <a href="javascript:check_kyc('<?=$shop_items[$i]['it_id']?>')">
            <div class="content_box3 staking_card recruit_staking card<?=$i+1?>">
                <img src="<?=G5_THEME_URL?>/img/card_logo2.png" alt="">
                <div class="staking_left_wrap">
                    <p class="percent"><?=$shop_items[$i]['it_option_subject']?></p>
                </div>
                <div class="staking_right_wrap">
                    <p class="currency"><?=$shop_items[$i]['it_brand']?></p>
                </div>
            </div>
        </a>
        <?php } ?>

        <div class="line"></div>
        <h1 class="main_title">참여 스테이킹</h1>
        <div class="staking_status_wrap">
            <?php for($i = 0; $i < $row = sql_fetch_array($shop_order_result); $i++){
                $html_index = $i + 1;    
                $years = $row['pay_end']/12;
                $end_years = date("Y-m-d", strtotime("+{$years} years"));
                $paid_acc = $member['swaped'] > 0 || $row['od_settle_case'] == "ETH" ? $row['pay_acc_eth'] : number_format(floor($row['pay_acc']));
                $paid_currency = $member['swaped'] > 0 ? WITHDRAW_CURENCY : $row['od_settle_case'];
                $staking_name = "스테이킹";
                $handled_my_staking = number_format(floor($row['od_cart_price']));
            ?>
            
            <!-- <div class="content_box3 staking_card card<?=$html_index?>" >
                <div class="staking_left_wrap">
                    <p class="percent"><?=$row['od_name']?></p>
                    <p class="date"><?=$row['od_date']?>~<?=$end_years?></p>
                </div>
                <div class="staking_right_wrap">
                    <p class="value"><?=$handled_my_staking?></p>
                    <p class="currency"><?=ASSETS_CURENCY?></p>
                </div>
            </div> -->
            

            <a data-toggle="collapse" href="#participation_staking<?=$html_index?>" role="button" aria-expanded="false" aria-controls="participation_staking<?=$html_index?>">
                <div class="content_box3 staking_card card<?=$html_index?>">
                    <div class="staking_left_wrap">
                        <p class="percent"><?=$row['od_name']?></p>
                        <p class="date"><?=$row['od_date']?>~<?=$end_years?></p>
                    </div>
                    <div class="staking_right_wrap">
                        <p class="value"><?=$handled_my_staking?></p>
                        <p class="currency"><?=ASSETS_CURENCY?></p>
                    </div>
                </div>
            </a>

            <div class="participation_staking card<?=$html_index?> collapse" id="participation_staking<?=$html_index?>">
                <div class="info_wrap">
                    <div class="quantity_wrap">
                        <span><?=$staking_name?> 수량</span>
                        <span class="values"><?=$handled_my_staking?><label class='currency'><?=ASSETS_CURENCY?></label></span>
                    </div>
                    <div class="date_wrap">
                        <span><?=$staking_name?> 기간</span>
                        <span><?=$row['od_date']?>~<?=$end_years?></span>
                    </div>
                    <div class="num_wrap">
                        <span><?=$staking_name?> 수익률</span>
                        <span><?=$row['od_tax_mny']?>%</span>
                    </div>
                    <div class="fee_wrap">
                        <span><?=$staking_name?> 수수료</span>
                        <span><?=number_format(floor($row['od_tax_flag']));?> <?=ASSETS_CURENCY?></span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 스테이킹 수익금</span>
                        <span>+ <?=$paid_acc?> <?=$paid_currency?></span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 지급 회차</span>
                        <span><?=$row['pay_count']?> / <?=$row['pay_end']?></span>
                    </div>
                </div>
                
                <div class="history_box">
                    <div class='hist_con deposit_history'>
                        <?php
                            $staking_history_sql = "select * from {$g5['mining']} where mb_id = '{$member['mb_id']}' and shop_order_id = {$row['od_id']}";
                            $staking_history_result = sql_query($staking_history_sql);
                            for($j = 0; $j < $staking_history_row = sql_fetch_array($staking_history_result); $j++){
                        ?>
                        <div class="hist_con_row1 deposit">
                            <div class="hist_left">
                                <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
                            </div>
                            <div class="hist_mid">
                                <p class="hist_date"><?=$staking_history_row['day']?></p>
                            </div>
                            <div class="hist_right">
                                <p class='hist_value'>+ <?=$staking_history_row['currency'] == "ETH" ? $staking_history_row['mining'] : floor($staking_history_row['mining'])?>  <span class="currency"><?= $staking_history_row['currency'] ?></span></p>
                                <!-- <p class="hist_won"><?=$staking_history_row['currency'] == "ETH" ? number_format(floor($staking_history_row['mining'] * $coin['eth_krw'])) : number_format(floor($staking_history_row['mining'] * $coin['esgc_krw']))?> <?=BALANCE_CURENCY?></p>     -->
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
            <?php } ?>
            <!-- <div class="more"><a href="">+ 더보기</a></div> -->

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
  
  function check_kyc(item){
    let kyc_cert = Number("<?=$member['kyc_cert']?>");

    if(kyc_cert <= 0){
        dialogModal('KYC 인증 미등록/미승인 ', "<strong> KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을 진행해주세요<br><a href='/page.php?id=profile' class='btn btn-primary'>KYC인증</a></strong>", 'warning');
        return false;
    }

    window.location.href=`/page.php?id=staking_application&it_id=${item}`;
  }


  $(document).ready(function(){
    $('.staking_card').on('click',function(){
        console.log();

    }); 
  });
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
