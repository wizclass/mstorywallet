<?php
include_once('./_common.php');
include_once('./adm.wallet.php');

check_admin_token();


if ($_POST['act_button'] == "신규등록") {

    auth_check($auth[$sub_menu], 'w');

    // 마지막 상품코드 조회
    $result = sql_fetch("select * from {$g5['g5_shop_giftcard_table']} order by gt_id desc limit 1");

    $card_num = date("Ymds",time()) . sprintf('%02d', rand(0, 99));
                 
    $sql = "insert into {$g5['g5_shop_giftcard_table']} 
        (gt_id, gt_name, gt_time) VALUES ('".$card_num."', '', '".G5_TIME_YMDHIS."')";
    sql_query($sql);

    goto_url("./gift_card.php?page=$page");

} else if ($_POST['act_button'] == "선택수정") {

    if (!count($_POST['chk'])) {
        alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
    }

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $gt_price = conv_number($_POST['gt_price'][$k]);
        $gt_coin = conv_number($_POST['gt_coin'][$k]);
                 
        $sql = "update {$g5['g5_shop_giftcard_table']}
        set gt_name        = '{$_POST['gt_name'][$k]}',
            gt_price       = '{$gt_price}',
            gt_coin        = '{$gt_coin}',
            gt_valid       = '{$_POST['gt_valid'][$k]}',
            gt_update_time       = '".G5_TIME_YMDHIS."',
            gt_fee       = '{$_POST['gt_fee'][$k]}',
            gt_order       = '{$_POST['gt_order'][$k]}',
            gt_use         = '{$_POST['gt_use'][$k]}'
            where gt_id    = '{$_POST['gt_id'][$k]}'";
        sql_query($sql);
    }
    
    goto_url("./gift_card.php?page=$page");

} else if ($_POST['act_button'] == "선택삭제") {

    if (!count($_POST['chk'])) {
        alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
    }

    if ($is_admin != 'super')
        alert('상품 삭제는 최고관리자만 가능합니다.');

    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $sql = "delete from {$g5['g5_shop_giftcard_table']}
        where gt_id      = '{$_POST['gt_id'][$k]}'";
        sql_query($sql);
    }

    goto_url("./gift_card.php?sca=$sca&amp;sst=$sst&amp;sod=$sod&amp;sfl=$sfl&amp;stx=$stx&amp;page=$page");
} else if ($_POST['act_button'] == "사용처리") {
    if (!count($_POST['chk'])) {
        alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
    }

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
                 
        $sql = "update {$g5['giftcard_history_table']} 
        set coupon = '사용완료',
            check_expiry_states = '1',
            update_date = '".G5_TIME_YMDHIS."'
            where origin_coupon = '{$_POST['origin_coupon'][$k]}'";
        sql_query($sql);
    }
    
    goto_url("./giftcard_orderlist.php?expiry={$expiry}&used={$used}&fr_date={$fr_date}&to_date={$to_date}");
    
} else if ($_POST['act_button'] == "개별사용") {
   
    $code = "300";
    $msg = "잘못된 접근입니다.";
                 
    $sql = "update {$g5['giftcard_history_table']} 
    set coupon = '사용완료',
        check_expiry_states = '1',
        update_date = '".G5_TIME_YMDHIS."'
        where idx = '{$_POST['gt_idx']}'";
    $update_result = sql_query($sql);

    if($update_result){
        $code = "200";
    }

    echo json_encode(array("code"=>$code, "msg"=>$msg));
}

?>
