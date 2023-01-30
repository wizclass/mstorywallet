<?php
header('Content-Type: text/html; charset=euc-kr');
@extract($_REQUEST);
   
//---------------------------------------
// API Ŭ���� Include
//---------------------------------------
include "php/config.php";
include "php/class/Message.php";
include "php/class/MessageTag.php";
include "php/class/ServiceCode.php";
include "php/class/Command.php";
include "php/class/ServiceBroker.php";

//---------------------------------------
// API �ν��Ͻ� ����
//---------------------------------------
$reqMsg = new Message(); //��û �޽���
$resMsg = new Message(); //���� �޽���
$tag = new MessageTag(); //�±�
$svcCode = new ServiceCode(); //���� �ڵ�
$cmd = new Command(); //Command
$broker = new ServiceBroker($COMMAND, $CONFIG_FILE); //��� ���

//---------------------------------------
//Header ����
//---------------------------------------
$reqMsg->setVersion("0100"); //���� (0100)
$reqMsg->setMerchantId($SERVICE_ID); //������ ���̵�
$reqMsg->setServiceCode($svcCode->MOBILE); //�����ڵ�

if($SERVICE_TYPE == "0000") {  //�Ϲݰ����ΰ��
	$reqMsg->setCommand($cmd->AUTH_REQUEST); //���� ��û Command
}else if($SERVICE_TYPE == "1000") { //�ڵ������� ���
	$reqMsg->setCommand($cmd->AUTO_BILL_AGREE_AUTH_REQUEST); //���� ��û Command	 	
}	
$reqMsg->setOrderId($ORDER_ID); //�ֹ���ȣ
$reqMsg->setOrderDate($ORDER_DATE); //�ֹ��Ͻ�(YYYYMMDDHHMMSS)

//print_r($_POST);
//---------------------------------------
//Check RESPONSE_CODE
//---------------------------------------
$isSuccess = false;
if(!strcmp($RESPONSE_CODE, "0000")) { // ���� ������ ��� ����(����)��û
	//---------------------------------------
	//Check Sum
	//---------------------------------------
	if($CHECK_SUM){
		$temp = $SERVICE_ID.$ORDER_ID.$TRANSACTION_ID;
		//$temp = $SERVICE_ID.$ORDER_ID.$ORDER_DATE;
		$cmd = sprintf("%s \"%s\" \"%s\" \"%s\"", $COM_CHECK_SUM, "DIFF", $CHECK_SUM, $temp);
        //echo $temp;
        //echo $cmd;
        //exit;
		//$checkSum = exec($cmd) or die("ERROR:899900");	

		if($checkSum != 'SUC'){

			//---------------------------------------
			//Set Body
			//---------------------------------------
			if($TRANSACTION_ID != NULL) //�ŷ���ȣ
			        $reqMsg->put($tag->TRANSACTION_ID, $TRANSACTION_ID);
			if($CERT_NUMBER != NULL) //������ȣ
			        $reqMsg->put($tag->CERT_NUMBER, $CERT_NUMBER);
			if($USER_EMAIL != NULL) //���̸���
			        $reqMsg->put($tag->USER_EMAIL, $USER_EMAIL);			
			//---------------------------------------
			// Request
			//---------------------------------------
			$broker->setReqMsg($reqMsg); //��û �޽��� ����
			$broker->invoke($svcCode->MOBILE); //���� ��û
			$resMsg = $broker->getResMsg(); //���� �޽��� Ȯ��
			
			//---------------------------------------
			// Response 
			//---------------------------------------
			$RESPONSE_CODE = $resMsg->get($tag->RESPONSE_CODE);
			$RESPONSE_MESSAGE = $resMsg->get($tag->RESPONSE_MESSAGE);
			$DETAIL_RESPONSE_CODE = $resMsg->get($tag->DETAIL_RESPONSE_CODE);
			$DETAIL_RESPONSE_MESSAGE = $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE);
			

			if(!strcmp($resMsg->get($tag->RESPONSE_CODE), "0000")) {
			  $AUTH_AMOUNT = $resMsg->get($tag->AUTH_AMOUNT);
			  $SESSION_KEY = $resMsg->get($tag->SESSION_KEY);	
              $PROTOCOL_NUMBER = $resMsg->get($tag->PROTOCOL_NUMBER);				
			  
			  $isSuccess = true;
			}
		}
	}

}
if($isSuccess == true) {
	//---------------------------------------
	// ������ ���� - ���� �� ������ ��� ó�� 
	// 1. ������ �ֹ���ȣ �� �����þ� �ŷ���ȣ�� ���� ��� DB ����
	// 2. ���� ���� ������ ȣ��
	//---------------------------------------
?>
<html>
<head>
<script type="text/javascript">
<!--
function setBillgateResult() {
    try {
        opener.payment_return('<?=$TRANSACTION_ID?>', '<?=$AUTH_AMOUNT?>', '<?=$SERVICE_CODE?>');
        self.close();
    } catch (e) {
        alert(e.message);
    }
}

setBillgateResult();
//-->
</script>
</head>
</body>
</html>
<?php
}else {
	//---------------------------------------
	// ������ ���� - ���� �� ������ ��� ó�� 
	// 1. ������ �ֹ���ȣ �� �����þ� �ŷ���ȣ�� ���� ��� DB ����
	// 2. ���� ���� ������ ȣ��
	//---------------------------------------
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link href="css/css_admin.css" rel="stylesheet" type="text/css">
<link href="css/css_01.css" rel="stylesheet" type="text/css">
<head>
<!-- Ű ��� �ڵ� -->
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">	
<table width="500" border="0" cellpadding="0"	cellspacing="0">
	<tr> 
	  <td height="25" style="padding-left:10px" class="title01"> 
		# ������ġ &gt;&gt; �޴��� &gt; <b>������ Return Url</b></td>
	</tr>
	<!--�����丮-->
	<!--title-->
	<tr>
		<td height="54" background="images/manager_title01.gif"
			style="padding-left: 65px; padding-top: 18px"><font size="3"><strong>������ Return Url</strong></font></td>
	</tr>
	<!--title-->
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><!--�������̺� ����--->
		<table width="450" border="0" cellpadding="4" cellspacing="1" bgcolor="#B0B0B0">	
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>������ ���̵�</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $SERVICE_ID ?></b>
				</td>								
			</tr>
				<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>�ֹ���ȣ</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $ORDER_ID ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>�ֹ��Ͻ�</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $ORDER_DATE ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>�ŷ���ȣ</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $TRANSACTION_ID ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>�����ڵ�</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $RESPONSE_CODE ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>����޽���</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $RESPONSE_MESSAGE ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>�������ڵ�</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $DETAIL_RESPONSE_CODE ?></b>
				</td>								
			</tr>
			<tr>
				<td width="100" align="center" bgcolor="#F6F6F6"><b>������޽���</b></td>
				<td width="200" align="left" bgcolor="#FFFFFF">&nbsp;<b><?php echo $DETAIL_RESPONSE_MESSAGE ?></b>
				</td>								
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?php
}
?>