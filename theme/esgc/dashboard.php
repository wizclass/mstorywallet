<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');
	include_once(G5_PATH.'/util/package.php');
    include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

	login_check($member['mb_id']);
    $member_info = sql_fetch("SELECT * FROM g5_member_info WHERE mb_id ='{$member['mb_id']}' order by date desc limit 0,1 ");

    $deposit_withdraw_sql = "(select txhash as credit, coin,amt,create_dt,'deposit' as states, status from {$g5['deposit']} where mb_id = '{$member['mb_id']}') 
    union all 
    (select addr as credit, coin, amt_total AS amt ,create_dt, 'withdraw' as states, status from {$g5['withdrawal']} where mb_id = '{$member['mb_id']}') 
    order by create_dt desc limit 0,3";
    
    $deposit_withdraw_result = sql_query($deposit_withdraw_sql);
    $deposit_withdraw_cnt = sql_num_rows($deposit_withdraw_result);

    $shop_order_result = get_my_staking($member['mb_id'],3);
    $shop_order_cnt = sql_num_rows($shop_order_result);

        // 입금 OR 출금
    if ($_GET['view'] == 'withdraw') {
        $view = 'withdraw';
        $history_target = $g5['withdrawal'];
    } else {
        $view = 'deposit';
        $history_target = $g5['deposit'];
    }

    $sql_common = "from {$g5['giftcard_history_table']} ";

    $sql = " select * " . $sql_common;
    $sql .= " where mb_id = '{$member['mb_id']}' and check_expiry_states <= 0 and update_date = '0000-00-00 00:00:00'";
    $sql .= " order by expiry_date asc limit 0,3";
    $giftcard_list = sql_query($sql);

    $giftcard_list_cnt = sql_num_rows($giftcard_list);

    // 테이블의 전체 레코드수만 얻음
    $sql = " select count(*) as cnt " . $sql_common;
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];
?>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/default.css">
<script src="<?=G5_URL?>/js/common.js"></script>


<?php
		if(defined('_INDEX_')) { // index에서만 실행
			include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
		}
 
	?>


<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>

<main>
    <div class='container dashboard'>
        <div class="nav_wrap">
            <?if($mb_lvl > 0){?>
                <a class="nav1" href="/page.php?id=staking">스테이킹</a>
            <?}?>
            <a class="nav2" href="/page.php?id=giftcard_purchase">상품권</a>
            <a class="nav3" href="/page.php?id=mywallet">입출금</a>
        </div>
        <?php 
            include_once(G5_THEME_PATH.'/status_card.php');
        ?>
        <?if($mb_lvl > 0){?>
        <h1 class="main_title mt40">마이 스테이킹 현황 <a class="page_move_btn" href="page.php?id=staking">스테이킹 바로가기 <img src="<?=G5_THEME_URL?>/img/arrow_right.png" alt=""> </a></h1>
        <div class="staking_status_wrap staking">

            <?php 
                while($row = sql_fetch_array($shop_order_result)){
                    $html_index = $row['od_app_no'];      
                    $end_years = $row['od_invoice_time'];

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

                        <p class="date"><?=$row['od_date']?> ~ <?=$end_years?></p>
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

            <?php if($shop_order_cnt == 0 ) { ?>
            <div class="null_data">
                현재 참여중인 스테이킹이 없습니다.
            </div>
            <?php } else {
            ?>

            <?php } ?>
        </div>

        <?}?>
        <h1 class="main_title mt40">사용 가능한 상품권 <a class="page_move_btn" href="/page.php?id=giftcard_purchase">상품권 구매하기 <img src="<?=G5_THEME_URL?>/img/arrow_right.png" alt=""> </a></h1>
        <div class="gift_box_wrap">
            <div class="swiper-container giftcard_swiper">
                <div class="swiper-wrapper">
                <?php
                for ($i=0; $row=sql_fetch_array($giftcard_list); $i++) {
                    $price_won = shift_auto($row['price_won'],BALANCE_CURENCY);
                ?>
                    <div class="swiper-slide slide_cont <?php echo $i == $total_count - 1 ? 'last_slide' : '' ?>">
                        <div class="gift_card_wrap">
                            <div class="product_cash_wrap">
                                <img src="<?=G5_THEME_URL?>/img/gift_logo.svg" alt="">
                                <div class="product_cash_box">
                                    <p class="product_text">에코페이 포인트<br>상품권</p>
                                    <p class="product_price"><?= $price_won ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="gift_card_sub_wrap">
                            <p>에코페이 포인트<br><?= $price_won ?>원 상품권</p>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                
                </div>
            </div>
            <div class="giftcard_more_btn">
                <a href="/page.php?id=giftcard_purchase">+</a>
            </div>
        </div>
        <?php if ($giftcard_list_cnt <= 0) { ?>
            <div class="null_data">현재 보유한 상품권이 없습니다.</div>
        <? } ?>
        <h1 class="main_title mt40" style="margin-bottom: 0">최근 입출금 내역<a class="page_move_btn" href="javascript:page_scroll_move('/page.php?id=mywallet&target=history_nav_wrap')">입출금 내역보기 <img src="<?=G5_THEME_URL?>/img/arrow_right.png" alt=""></a></h1>
    </div>
    <div class="container" style="margin-top:-10px">
        <div class="history_box" style="background: none">
            <?php if ($deposit_withdraw_cnt <= 0) { ?>
                <div class="null_data">최근 입출금 내역이 없습니다.</div>
            <? } ?>
            <div class='hist_con deposit_history'>
                <?php while ($row = sql_fetch_array($deposit_withdraw_result)) { 
                    $sign = $row['states'] == "deposit" ? ($row['amt'] >= 0 ? "+" : "") : "-";
                    $receipt_coin = $row['coin'] == "ETH" ? shift_auto($row['amt']) : shift_auto($row['amt'],ASSETS_CURENCY);
                    ?>
                    <div class='hist_con' style="background-color: transparent; margin-bottom: 10px">
                        <div class="hist_con_row1 <?=$row['states']?>">
                            <div class="hist_left">
                                <img src="<?=G5_THEME_URL?>/img/<?=$row['states']?>.svg" alt="">
                            </div>
                            <div class="hist_mid">
                                <p class="tx_id"><?=retrun_tx_func(Decrypt($row['credit'],$secret_key,$secret_iv),$row['coin'])?></p>      
                                <p class="hist_date"><?= $row['create_dt'] ?></p>
                                <p class="process_result">처리결과</p>
                            </div>
                            <div class="hist_right">
                                <p class='hist_value'><?=$sign?> <?= $receipt_coin ?> <span class="currency"><?= $row['coin'] ?></span></p>
                                <p class="hist_won"></p>    
                                <span class="process_result"><? string_shift_code($row['status']) ?></span>    
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</main>

<script>
    window.onload = function() {
        switch_func("<?= $view ?>");

        if(<?= $member['reg_tr_password_change'] ?> == 0 ) {
            dialogModal('','<strong>고객님의 원활한 출금을 위해<br>로그인 비밀번호와 핀번호를 변경해주세요.</strong>','tr_password');
        }
    }

    function switch_func(n) {
        $('.loadable').removeClass('active');
        $('#' + n).toggleClass('active');
    }

    function switch_func_paging(n) {
        $('.loadable').removeClass('active');
        $('#' + n).toggleClass('active');
        window.location.href = window.location.pathname + "?id=mywallet&'<?= $qstr ?>'&page=1&view=" + n;
    }

    $('.my_staking').on('click',function(){
        var order_id = $(this).data("id");
        window.location.href=`/page.php?id=staking_detail&cate=staking&od_id=${order_id}`;
    });
    
    function page_scroll_move(url) {
        location.href = url;
    }
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>