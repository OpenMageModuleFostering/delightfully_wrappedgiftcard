<?php

class Delightfully_WrappedGiftCard_Model_Giftvoucher extends Magestore_Giftvoucher_Model_Giftvoucher {

	/*obnoxious lengths to get something that should have been saved in gifvoucher model to begin with*/
	public function getDeliveryType() {
		$giftCardId = $this->getId(); //may have not been created yet.		
		
		//get card history by giftvoucher_id
		$history = Mage::getResourceModel('giftvoucher/history_collection')
		->addFieldToFilter('giftvoucher_id', $giftCardId)
		->addFieldToFilter('action', Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE)
		->getFirstItem();
		
		//get original order item from order_item_id in history entry
		$orderItem = Mage::getModel('sales/order_item')->load($history->getOrderItemId());
		
		if (!$history->getOrderIncrementId() || !$history->getOrderItemId()) {
			//error_log("Failed to find delivery type from history");
			return -1;						
			
		}
		$requestInfo = $orderItem->getProductOptionByCode('info_buyRequest');
		//error_log("Delivery type: " . print_r($requestInfo['send_friend'],TRUE));
		return $requestInfo['send_friend'];
	}
	
	
	public function sendEmailToRecipient() {	
		$dtype = $this->getDeliveryType();
		if($dtype < 2) {
			return parent::sendEmailToRecipient();
		} else {
			//error_log('skipping recipient email for send_friend: '.$dtype);
			return 1;
		}
	}
	
    public function sendEmail(){
    	
		$dtype = $this->getDeliveryType();
		
		//Mage::log($dtype,null,'dly.log');
		
		if($dtype < 2) {
			return parent::sendEmail();
		} else {
			//dummy send
		/*							
	    	$store = Mage::app()->getStore($this->getStoreId());
	    	$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
	    	$mailSent = 0;
	    	
			if ($this->getRecipientEmail()){
			*/
				$mailSent += $this->sendEmailToRecipient();
	    	/*
	    	}
	    	*/
	    	$this->setEmailSent($mailSent);
	    	//$translate->setTranslateInline(true);
	    	return $this;	
    		
		}
    }
    
}