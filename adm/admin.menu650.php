<?php
if($member['mb_id'] == 'admin'){
$menu['menu650'] = array (
        
    // array('650000', '상품권관리', ''.G5_ADMIN_URL.'/gift_card.php','gift_card'),
    // array('650100', '상품권관리', ''.G5_ADMIN_URL.'/gift_card.php','gift_card'),
    // array('650150', '상품권 구매내역', ''.G5_ADMIN_URL.'/giftcard_orderlist.php','gift_card_history'),
	// array('650160', '상품권 통계', G5_ADMIN_URL.'/shop_admin/sale2.php', 'sst_order_stats'),


    // array('650000', '조직도보기', ''.G5_ADMIN_URL.'/member_tree.php','allowance_sett'),
    // array('650100', '조직도(트리) 보기', ''.G5_ADMIN_URL.'/member_tree.php', 'bbs_board'),
    // array('650200', '조직도(박스) 보기', ''.G5_ADMIN_URL.'/member_org.php', 'bbs_board'),
    // array('650200', '조직도2 보기', ''.G5_ADMIN_URL.'/member_random_org.php', 'bbs_board')
	
);
}else{
    $menu['menu650'] = array (
        // array('650000', '상품권관리', ''.G5_ADMIN_URL.'/gift_card.php','gift_card'),
        // array('650100', '상품권관리', ''.G5_ADMIN_URL.'/gift_card.php','gift_card'),
        // array('650150', '상품권 구매내역', ''.G5_ADMIN_URL.'/giftcard_orderlist.php','gift_card_history'),
	    // array('650160', '상품권 통계', G5_ADMIN_URL.'/shop_admin/sale2.php', 'sst_order_stats'),

        // array('650000', '조직도보기', ''.G5_ADMIN_URL.'/member_tree.php','allowance_sett'),
        // array('650100', '조직도(트리) 보기', ''.G5_ADMIN_URL.'/member_tree.php', 'bbs_board'),
        // array('650200', '조직도(박스) 보기', ''.G5_ADMIN_URL.'/member_org.php', 'bbs_board'),
        // array('650200', '조직도2(제타플러스) 보기', ''.G5_ADMIN_URL.'/member_random_org.php', 'bbs_board')
    );
}
?>