<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./inc.member.class.php');

//������
if (!$m_id) $m_id      = "0010000288";


//class ���� ����
make_class();

// set üũ
$my_depth = get_depth($m_id);
echo "my_depth : ".$my_depth."<br>\n";

?>