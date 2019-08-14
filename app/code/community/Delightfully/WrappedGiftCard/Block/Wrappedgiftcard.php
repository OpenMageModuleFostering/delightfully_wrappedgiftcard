<?php 
class Delightfully_WrappedGiftCard_Block_Wrappedgiftcard extends Mage_Checkout_Block_Onepage_Success {
    public function getOrder() {
        return Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId());    
    }

    public function getOrderedProduct($ordered_product) {
        return Mage::getModel('catalog/product')->load($ordered_product->getProductId());
    }

    public function getStoreName() {
        return Mage::getStoreConfig('general/store_information/name');
    }

    public function countWrappedCardsInOrderItems($items) {
    	$prescan = 0;
    	foreach ( $items as $item ) {
    		if($this->isWrappedGiftCard($item)) {		
				$prescan = $prescan + $this->countGiftCodes($item);
    		}
    	}
    	return $prescan;
    }
    
    public function countGiftCodes($item) {
    	if($this->isWrappedEnterpriseGiftCard($item)) {
    		return 1;
    	}
    	if($this->isWrappedMagestoreGiftVoucher($item)) {
    		return $this->countMagestoreGiftVoucherCodes($item);
    	} 
    	return 0;
    }
    
    public function countMagestoreGiftVoucherCodes($item) {
    	$dhelper = Mage::helper('delightfully_wrappedgiftcard');
    	return count($dhelper->getVoucherCodesForItem($item));
    }
    
    public function isWrappedEnterpriseGiftCard($item) {
    	//check if matches by DLYGC SKU
    	return preg_match('/DLYGC/',$item->_data['sku']);
    }

	public function isAnyMagestoreGiftVoucher($item) {
		$prodtype = $item->_data['product_type'];
    	return ('giftvoucher' == $prodtype); 
	}

    public function isWrappedMagestoreGiftVoucher($item) {
    	//check if matches by type
    	$prodtype = $item->_data['product_type'];
    	if('giftvoucher' == $prodtype) { 
    		//check if voucher gift has Delightfully wrapping selected.
    		$opts = $item->getProductOptionByCode('info_buyRequest');
    		if($opts['send_friend'] == 2) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function isWrappedGiftCard($item) {
    	if($this->isWrappedEnterpriseGiftCard($item)) { 
    		return true;
    	}
    	if($this->isWrappedMagestoreGiftVoucher($item)) {
    		return true;
    	}
    	return false;
    }
    
    public function getAPIParamsForItem($item,$transaction,$count,$codenum) {
    	if($this->isWrappedEnterpriseGiftCard($item)) {
    		return $this->getAPIParamsForEnterpriseGiftCard($item,$transaction,$count,$codenum);
    	}
    	if($this->isWrappedMagestoreGiftVoucher($item)) {
    		return $this->getAPIParamsForMagestoreGiftVoucher($item,$transaction,$count,$codenum);
    	}
    	return array();
    }
    
    public function getAPIParamsForEnterpriseGiftCard($item,$transaction,$count,$codenum) {
    	
    	
    	$dhelper = Mage::helper('delightfully_wrappedgiftcard');
    	$params = $dhelper->getBaseAPIParams();
    	
    	$opt = $item->getProductOptions();
    	$code = "PENDING";
    	$codes = $opt['giftcard_created_codes'];
    	if($codes[$codenum]) $code = $codes[$codenum];
    	
    	$params['sender'] = $opt['giftcard_sender_name'];
    	$params['recipient'] = $opt['giftcard_recipient_name'];
    	$params['from'] = $opt['giftcard_sender_email'];
    	$params['to'] = $opt['giftcard_recipient_email'];
    	$params['message'] = $opt['giftcard_message'];
    	$params['code'] = $code;

    	$txn = "".$transaction . "_" . $count . "_" . $code;
    	$params['transaction'] = $txn;
    	return $params;
    }
    
    public function getAPIParamsForMagestoreGiftVoucher($item,$transaction,$count, $codenum) {
    	
     	
    	$dhelper = Mage::helper('delightfully_wrappedgiftcard');
    	
    
		$description = Mage::getStoreConfig('delightfully_options/product/gift_description');
    	
    	$params = $dhelper->getBaseAPIParams();
    	 
    	
    	$opt = $item->getProductOptions();
    	$code = "PENDING";
    	$codes = $dhelper->getVoucherCodesForItem($item);
    	$sender = $dhelper->getSenderParamsForItem($item);
    	 
    	if($codes[$codenum]) $code = $codes[$codenum];//can be multiple codes in same item
    	 
    	$params['sender'] = $sender['sender'];
    	$params['recipient'] = $opt['info_buyRequest']['recipient_name'];
    	$params['from'] = $sender['from'];
    	$params['to'] = $opt['info_buyRequest']['recipient_email'];
    	$params['message'] = $opt['info_buyRequest']['message'];
    	$params['code'] = $code;
     	$txn = "".$transaction . "_" . $count . "_" . $code;
    	$params['transaction'] = $txn;
    	$paramas['value'] = $sender['value'];
    	$params['description'] = $sender['amount'] . " " .$description;
    	$params['schedule'] = $opt['info_buyRequest']['day_to_send'];
    	return $params;
    }
    
}
