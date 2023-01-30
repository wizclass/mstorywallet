<?php
class ServiceBroker
{
	var $bin ; 
	var $configFile ;
	
	var $reqMsg ;
	var $resMsg ;
	var $tag ;
	
	function ServiceBroker($bin, $configFile)
	{
		$this->tag = new MessageTag();
		$this->bin = $bin ;
		$this->configFile = $configFile ;
	}
	
	function getReqMsg()
	{
		return $reqMsg ;
	}

	function setReqMsg($reqMsg)
	{
		$this->reqMsg = $reqMsg ;
	}
	
	function getResMsg()
	{
		return $this->resMsg ;
	}

	function setResMsg($resMsg)
	{
		$this->resMsg = $resMsg ;
	}
	
	function invoke($serviceCode)
	{
		$this->resMsg = new Message();
		$cmd = sprintf("%s \"%s\" \"%s\" \"%s\" ", $this->bin, $this->configFile, $serviceCode, base64_encode($this->reqMsg->toString()));
	
		$retMsg = exec($cmd) or die("ERROR:899900");	
		
		$retCode = substr($retMsg,0,5);
		if(!strcmp($retCode, "ERROR")) {
			$this->resMsg->setVersion($this->reqMsg->getVersion());
			$this->resMsg->setMerchantId($this->reqMsg->getMerchantId());
			$this->resMsg->setServiceCode($this->reqMsg->getServiceCode());
			$this->resMsg->setCommand((string)((int)$this->reqMsg->getCommand() + 1));
			$this->resMsg->setOrderId($this->reqMsg->getOrderId());
			$this->resMsg->setOrderDate($this->reqMsg->getOrderDate());  
			
			$this->resMsg->put($this->tag->RESPONSE_CODE, substr($retMsg,6,4)); 
			$this->resMsg->put($this->tag->RESPONSE_MESSAGE, "API ����!!"); 
			$this->resMsg->put($this->tag->DETAIL_RESPONSE_CODE, substr($retMsg,10,2)); 
			$this->resMsg->put($this->tag->DETAIL_RESPONSE_MESSAGE, $this->getErrorMessage(substr($retMsg,6,6)));    
		}
		else {
			$this->resMsg->setData($retMsg);
		}
	}
	
	function invokeMessage($serviceCode, $msg)
	{
		$this->resMsg = new Message();
		$cmd = sprintf("%s \"%s\" \"%s\" \"%s\" ", $this->bin, $this->configFile, $serviceCode, $msg);
		
		$retMsg = exec($cmd) or die("ERROR:899900");	

		$retCode = substr($retMsg,0,5);
		if(!strcmp($retCode, "ERROR")) {
			$this->resMsg->setVersion($this->reqMsg->getVersion());
			$this->resMsg->setMerchantId($this->reqMsg->getMerchantId());
			$this->resMsg->setServiceCode($this->reqMsg->getServiceCode());
			$this->resMsg->setCommand((string)((int)$this->reqMsg->getCommand() + 1));
			$this->resMsg->setOrderId($this->reqMsg->getOrderId());
			$this->resMsg->setOrderDate($this->reqMsg->getOrderDate());  
			
			$this->resMsg->put($this->tag->RESPONSE_CODE, substr($retMsg,6,4)); 
			$this->resMsg->put($this->tag->RESPONSE_MESSAGE, "API ����!!"); 
			$this->resMsg->put($this->tag->DETAIL_RESPONSE_CODE, substr($retMsg,10,2)); 
			$this->resMsg->put($this->tag->DETAIL_RESPONSE_MESSAGE, $this->getErrorMessage(substr($retMsg,6,6)));    
		}
		else {
			$this->resMsg->setData($retMsg);
		}
	}
	
	function getErrorMessage($errorCode)
	{
		if(!strcmp($errorCode,"080000")) {
			return "API Command ����!!";
		}
		else if(!strcmp($errorCode,"080001")) {
			return "�޽��� �Ľ� ����!!";
		}
		else if(!strcmp($errorCode,"082001")) {
			return "���� ���� ����!!";
		}
		else if(!strcmp($errorCode,"082002")) {
			return "���� Ÿ�Ӿƿ� ����!!";
		}
		else if(!strcmp($errorCode,"083001")) {
			return "�޽��� ��ȣȭ ����!!";
		}
		else if(!strcmp($errorCode,"083002")) {
			return "�޽��� ��ȣȭ ����!!";
		}
		else if(!strcmp($errorCode,"899900")) {
			return "�˼� ���� ����!!";
		}
		else if(!strcmp($errorCode,"899901")) {
			return "API ���� �߻�!!";
		}
	}
	
} 
?>
