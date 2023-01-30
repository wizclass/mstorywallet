<?php
@extract($_REQUEST);

//---------------------------------------
// Include
//---------------------------------------
include G5_SHOP_PATH."/billgate/php/config.php";
include G5_SHOP_PATH."/billgate/php/class/Message.php";
include G5_SHOP_PATH."/billgate/php/class/MessageTag.php";
include G5_SHOP_PATH."/billgate/php/class/ServiceCode.php";
include G5_SHOP_PATH."/billgate/php/class/Command.php";
include G5_SHOP_PATH."/billgate/php/class/ServiceBroker.php";


$today	= mktime(); 
$today_time = date('YmdHis', $today);

//parameter
$orderDate 	= $today_time;
$orderId = $od_id;
$transactionId = $od['od_tno'];	//취소건의 거래번호
$amount = $od['od_receipt_price'];    //취소건의 금액

//---------------------------------------
//Create Instance
//---------------------------------------
$reqMsg = new Message(); 
$resMsg = new Message(); 
$tag = new MessageTag();
$svcCode = new ServiceCode(); 
$cmd = new Command(); 
$broker = new ServiceBroker($COMMAND, $CONFIG_FILE);

//---------------------------------------
//Header 
//---------------------------------------
$reqMsg->setVersion("0100"); 
$reqMsg->setMerchantId($serviceId); 
$reqMsg->setServiceCode($svcCode->CREDIT_CARD); 
$reqMsg->setCommand($cmd->CANCEL_SMS_REQUEST); 
$reqMsg->setOrderId($orderId); 
$reqMsg->setOrderDate($orderDate);

//---------------------------------------
//Body 
//---------------------------------------
if($transactionId != NULL) 
	$reqMsg->put($tag->TRANSACTION_ID, $transactionId);                              
if($amount != NULL) 
	$reqMsg->put($tag->DEAL_AMOUNT, $amount);   

//---------------------------------------
//Request
//---------------------------------------
$broker->setReqMsg($reqMsg); 
$broker->invoke($svcCode->CREDIT_CARD); 
$resMsg = $broker->getResMsg();

//---------------------------------------
//Response 
//---------------------------------------
$msg = $resMsg->get($tag->RESPONSE_MESSAGE); 

$RESPONSE_CODE = $resMsg->get($tag->RESPONSE_CODE);
$RESPONSE_MESSAGE = $resMsg->get($tag->RESPONSE_MESSAGE);
$DETAIL_RESPONSE_CODE = $resMsg->get($tag->DETAIL_RESPONSE_CODE);
$DETAIL_RESPONSE_MESSAGE = $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link href="css/css_admin.css" rel="stylesheet" type="text/css">
<link href="css/css_01.css" rel="stylesheet" type="text/css">
<head>
<!-- 키 방어 코드 -->
<script type="text/javascript" src="js/comm.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">	
<table width="500" border="0" cellpadding="0"	cellspacing="0">
	<tr> 
	  <td height="25" style="padding-left:10px" class="title01">신용카드 &gt; <b>가맹점 Return Url</b></td>
	</tr>
	<!--title-->
	<tr>
		<td height="54" background="images/manager_title01.gif" style="padding-left: 65px; padding-top: 18px"><font size="3"><strong>가맹점 Return Url</strong></font></td>
	</tr>
	<!--title-->
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><!--본문테이블 시작--->
		<table width="450" border="0" cellpadding="4" cellspacing="1" bgcolor="#B0B0B0">	
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>가맹점 아이디</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $serviceId ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>주문번호</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $orderId ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>주문 일시</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $orderDate ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>거래번호</b></td> 
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $transactionId ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>응답코드</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo iconv("EUC-KR","UTF-8",$RESPONSE_CODE) ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>응답메시지</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo iconv("EUC-KR","UTF-8",$RESPONSE_MESSAGE) ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>상세응답코드</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo iconv("EUC-KR","UTF-8",$DETAIL_RESPONSE_CODE) ?></b></td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>상세응답메시지</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo iconv("EUC-KR","UTF-8",$DETAIL_RESPONSE_MESSAGE) ?></b></td>								
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>