<?php
if($member['mb_id'] == 'admin'){
$menu["menu700"] = array (
	array('700000', ' 입금/출금/매출', ''.G5_ADMIN_URL.'/adm.deposit_request.php', '0'),	
	// array('700100', ' 주문/구매내역', ''.G5_ADMIN_URL.'/shop_admin/orderlist.php', '0'),	
	array('700600', '입금 요청 내역', G5_ADMIN_URL.'/adm.deposit_request.php', 'bbs_board'),
	array('700400', '출금 요청 내역', G5_ADMIN_URL.'/adm.withdrawal_request.php', 'bbs_board'),
	array('700050', '입출금설정', G5_ADMIN_URL.'/bonus/wallet.config.php', 'sst_order_stats'),
	array('700010', '코인 시세(UPbit)', ''.G5_ADMIN_URL.'/etc/coin_swap.php', 'coin_swap' , 1),
	/*
	array('700100', '구매 통계', G5_ADMIN_URL.'/shop_admin/sale1.php', 'sst_order_stats'),
	array('700200', '구매 내역', G5_ADMIN_URL.'/shop_admin/orderlist.php', 'scf_order', 1),
	array('700300', '구매내역', G5_ADMIN_URL.'/shop_admin/cart_list.php', 'scf_order', 1),
	array('700600', '입금 요청 내역', G5_ADMIN_URL.'/adm.deposit_request.php', 'bbs_board'),
	array('700400', '출금 요청 내역', G5_ADMIN_URL.'/adm.withdrawal_request.php', 'bbs_board'),
	array('700400', '마이닝 출금 요청 내역', G5_ADMIN_URL.'/adm.withdrawal_request_mining.php', 'bbs_board'),
	array('700010', '코인 시세(UPbit)', ''.G5_ADMIN_URL.'/etc/coin_swap.php', 'coin_swap' , 1),
	array('700400', '코인(포인트)전환 내역', G5_ADMIN_URL.'/config_change.php', 'bbs_board'),
	array('700500', '코인 송금', G5_ADMIN_URL.'/config_wallet.php', 'bbs_board'),
    array('700400', '출금 요청 내역 (ETH)', G5_ADMIN_URL.'/config_withdrawal_eth.php', 'bbs_board'),
	array('700100', ' 입금 멤버 내역', ''.G5_ADMIN_URL.'/adm.eos.incom.enable.php', 'eos incom'),
	array('700200', ' 전체 수집 내역', ''.G5_ADMIN_URL.'/adm.eos.incom.php', 'eos incom all'),
	array('700300', '출금 요청 검토', G5_ADMIN_URL.'/withdrawal_batch.php', 'bbs_board')
	*/
);
}else{
	$menu["menu700"] = array (
	array('700000', '입금/출금/매출', ''.G5_ADMIN_URL.'/adm.deposit_request.php', '0'),	
	// array('700100', '주문/구매내역', ''.G5_ADMIN_URL.'/shop_admin/orderlist.php', '0'),	
	array('700600', '입금 요청 내역', G5_ADMIN_URL.'/adm.deposit_request.php', 'bbs_board'),
	array('700400', '출금 요청 내역', G5_ADMIN_URL.'/adm.withdrawal_request.php', 'bbs_board'),
	// array('700400', '마이닝 출금 요청 내역', G5_ADMIN_URL.'/adm.withdrawal_request_mining.php', 'bbs_board'),
	array('700050', '입출금설정', G5_ADMIN_URL.'/bonus/wallet.config.php', 'sst_order_stats'),
	array('700010', '코인 시세(UPbit)', ''.G5_ADMIN_URL.'/etc/coin_swap.php', 'coin_swap' , 1),
	);
}

?>