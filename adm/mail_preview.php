<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check($auth[$sub_menu], 'r');

$se = sql_fetch("select ma_subject, ma_content from {$g5['mail_table']} where ma_id = '{$ma_id}' ");

$subject = $se['ma_subject'];

?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo G5_VERSION ?> 메일발송 테스트</title>
</head>

<body>

<h1><?php echo $subject; ?></h1>

<p>
    <?php echo $content; ?>
</p>

<p>
    <strong>주의!</strong> 이 화면에 보여지는 디자인은 실제 내용이 발송되었을 때 디자인과 다를 수 있습니다.
</p>

</body>
</html>