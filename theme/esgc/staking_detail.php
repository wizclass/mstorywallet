<?
    include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    $menubar =1;
    include_once(G5_THEME_PATH.'/_include/gnb.php');
    
    $title = 'staking_history';
    $val = $_REQUEST;
    $od_id = $val['od_id'];
    $sql = "SELECT * from g5_shop_order WHERE od_id = '{$od_id}' order by od_id desc limit 0,1";
    $this_od = sql_fetch($sql);

    $item_no = $this_od['od_app_no'];
    $staking_name = "스테이킹";
    $end_years = $this_od['od_invoice_time'];
    $handled_my_staking = shift_auto($this_od['od_cart_price'],ASSETS_CURENCY);
    $expiry_item = $this_od['od_refund_price'] > 0 ? "finish_staking" : "proceeding";
?>

<style>
    .notice_wrap {width: calc( 100% - 30px );}
</style>

<main>
    <div class='container pt20'>
            <div class='staking_card_fill_wrap <?=$expiry_item?> staking'>
                <div class="card_fill fill_card<?=$item_no?>"></div>    
                <div class="staking_card my_staking border_card<?=$item_no?>" data-id="<?=$od_id?>">
                    <div class="staking_left_wrap">
                        <p class="item_character text_color<?=$item_no?>">
                            <?=$this_od['od_tax_mny']?><span class='percent_marker'>%</span> 
                            /
                            <?=$this_od['pay_end']/12?><span class='year_marker'>년</span>
                        </p>

                        <p class="date"><?=$this_od['od_date']?>~<?=$end_years?></p>
                    </div>

                    <P class='divide dark'></P>
                    <div class="staking_right_wrap">
                        <p class="value text_color<?=$html_index?>"><?=$handled_my_staking?></p>
                        <p class="currency"><?=ASSETS_CURENCY?></p>
                    </div>
                </div>
                
                <div class="info_wrap">
                    <? echo $expiry_item === 'finish_staking'? '<p class="finish_staking_text">참여 완료된 스테이킹입니다.</p>' : '' ?>
                    <div class="">
                        <span>스테이킹 상품번호</span>
                        <span><?=$this_od['od_id']?></span>
                    </div>
                    <div class="quantity_wrap">
                        <span><?=$staking_name?> 수량</span>
                        <span class="values"><?=shift_auto($this_od['od_cart_price'],ASSETS_CURENCY)?> 
                            <label class='currency'><?=ASSETS_CURENCY?></label>
                        </span>
                    </div>
                    <div class="date_wrap">
                        <span><?=$staking_name?> 기간</span>
                        <span><?=$this_od['od_date']?> ~ <?=$this_od['od_invoice_time']?></span>
                    </div>
                    <div class="num_wrap">
                        <span><?=$staking_name?> 수익률</span>
                        <span><?=$this_od['od_tax_mny']?>%</span>
                    </div>
                    <div class="fee_wrap">
                        <span><?=$staking_name?> 수수료</span>
                        <span><?=shift_auto($this_od['od_tax_flag'],ASSETS_CURENCY);?> <?=ASSETS_CURENCY?></span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 스테이킹 수익금</span>
                        <span>+ <?=shift_auto($this_od['od_settle_case'] == "ETH" ? $this_od['pay_acc_eth'] : $this_od['pay_acc'],$this_od['od_settle_case'])?> <?=$this_od['od_settle_case']?></span>
                    </div>
                    <div class="sum_price_wrap">
                        <span>총 지급 회차</span>
                        <span><?=$this_od['pay_count']?> / <?=$this_od['pay_end']?></span>
                    </div>
                </div>
            </div>


        <div class="col-sm-12 col-12 content-box round history_detail mb20">
            <div class="box-header">
                수익금 지급내역
            </div>

            <div class="box-body">
                <?
                $staking_history_sql = "select * from {$g5['mining']} where mb_id = '{$member['mb_id']}' and shop_order_id = {$this_od['od_id']}";
                $staking_history_result = sql_query($staking_history_sql);
                while($staking_history_row = sql_fetch_array($staking_history_result)){
                ?>
                    
                    <div class="history_box">
                        <div class='hist_con deposit_history'>
                            
                            <div class="hist_con_row1 deposit">
                                <div class="hist_left">
                                    <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
                                </div>
                                <div class="hist_mid">
                                    <p class="hist_date"><?=$staking_history_row['day']?></p>
                                </div>
                                <div class="hist_right">
                                    <p class='hist_value'>+ <?=shift_auto($staking_history_row['mining'],$staking_history_row['currency'])?>  <span class="currency"><?= $staking_history_row['currency'] ?></span></p>
                                    <!-- <p class="hist_won"><?=$staking_history_row['currency'] == "ETH" ? number_format(floor($staking_history_row['mining'] * $coin['eth_krw'])) : number_format(floor($staking_history_row['mining'] * $coin['esgc_krw']))?> <?=BALANCE_CURENCY?></p>     -->
                                </div>
                            </div>                           
                        </div> 
                    </div>
                <?}?>
            </div>
        </div>
</main>


<div class="gnb_dim"></div>
</section>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>