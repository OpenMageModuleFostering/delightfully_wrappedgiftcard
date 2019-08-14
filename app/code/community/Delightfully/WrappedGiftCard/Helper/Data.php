<?php
class Delightfully_WrappedGiftCard_Helper_Data extends Mage_Core_Helper_Abstract {

	
	public function extendOptionsWithDeliveryMethod($options) {
		
		$optionLabels = Mage::helper('giftvoucher')->getGiftVoucherOptions(); //get all translated label names
		
		$found = false;
		
		//find the option with translated name matching send_friend internal name
		foreach ($options as $index=>$value) {
			if ($options[$index] && $options[$index]['label'] == $optionLabels['send_friend']) {
				$found = true;
			}
		}
		$extraOptions = array();
		if(!$found) {
			$extraOptions[] = array('label' => $optionLabels['send_friend'],'value' => 0); //show default if no options set
		}
		$options = array_merge($extraOptions,$options);
		
		//rewrite option value for send_friend option
		foreach ($options as $index=>$value) {
			if ($options[$index] && $options[$index]['label'] == $optionLabels['send_friend']) {
				$sendval = $options[$index]['value'];
		
				if($sendval == 1) {
					$options[$index]['value'] = __('Email to Friend');
				} else if($sendval == 2) {
					$options[$index]['value'] = __('Virtual Gift Wrap (after checkout)');
				} else {
					$options[$index]['value'] = __('Email to Purchaser');
				}
				$found = true;
			}
		}
		
		return $options;
	}
	
	public function addItemFilter($collection, $itemId){
		if ($collection->hasFlag('add_item_filer') && $collection->getFlag('add_item_filer')) return $collection;
		$collection->setFlag('add_item_filer',true);
		 
		$collection->getSelect()->joinLeft(
				array('history'	=> $collection->getTable('giftvoucher/history')),
				'main_table.giftvoucher_id = history.giftvoucher_id',
				array('order_item_id')
		)->where('history.order_item_id = ?',$itemId)
		->where('history.action = ?',Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE);
		 
		return $collection;
	}

	//TODO: Remove (dupe)
	public function getVoucherCodes($item) {
			
		if ($options = $item->getProductOptionByCode('info_buyRequest'))
		foreach (Mage::helper('giftvoucher')->getGiftVoucherOptions() as $code=>$label)
		if ($options[$code])
			$result[] = array(
					'label'	=> $label,
					'value'	=> $options[$code],
					'option_value'	=> $options[$code],
			);
		$collection = Mage::getModel('giftvoucher/giftvoucher')->getCollection();
		
		$giftVouchers = $collection->addItemFilter($item->getId());
		
		
		
		if ($giftVouchers->getSize()){
			$giftVouchersCode = array();
			foreach ($giftVouchers as $giftVoucher){
				$currency = Mage::getModel('directory/currency')->load($giftVoucher->getCurrency());
				$balance = $giftVoucher->getBalance();
				if ($currency) $balance = $currency->format($balance,array(),false);
				$giftVouchersCode[] = $giftVoucher->getGiftCode().' ('.$balance.') ';
			}
			$codes = implode(' ',$giftVouchersCode);
			$result[] = array(
					'label'	=> $this->__('Gift Card Code'),
					'value'	=> $codes,
					'option_value'	=> $codes,
			);
		}
		//error_log(print_r($result,TRUE));
		return $result;
	}

	
	public function getVoucherCodesForItem($item) {
			
		if ($options = $item->getProductOptionByCode('info_buyRequest'))
		/*
		foreach (Mage::helper('giftvoucher')->getGiftVoucherOptions() as $code=>$label)
			if (array_key_exists($code,$options))
			$result[] = array(
					'label'	=> $label,
					'value'	=> $options[$code],
					'option_value'	=> $options[$code],
			);
		*/
		$itemId = $item->getId();
		if(!$itemId) {
			$itemId = $item->_data['item_id'];
		}
		$collection = Mage::getModel('giftvoucher/giftvoucher')->getCollection();
		$giftVouchers = $collection->addItemFilter($itemId);
		if ($giftVouchers->getSize()){
			$giftVouchersCode = array();
			foreach ($giftVouchers as $giftVoucher){
				$giftVouchersCode[] = $giftVoucher->getGiftCode();
			}
		}
		//error_log(print_r($result,TRUE));
		return $giftVouchersCode;
	}
	
	public function getSenderParamsForItem($item) {
			
		$params = array();
			
	
		$giftVouchers = Mage::getModel('giftvoucher/giftvoucher')->getCollection()->addItemFilter($item->getId());
		if ($giftVouchers->getSize()){
			foreach ($giftVouchers as $giftVoucher){
				$params['from'] = $giftVoucher->getCustomerEmail();
				$params['sender'] = $giftVoucher->getCustomerName();

				$params['amount'] = $giftVoucher->getBalanceFormated();
				$params['value'] = $giftVoucher->getBalance();
				
			}
		}
		//error_log(print_r($result,TRUE));
		return $params;
	}
	
	public function makeAuthorizeCallWithParams($params) {
		
		$protocol = "https";
		$host = "delightfully.com";
		
		$method = "GET";
		$authpath = "/api/partner/authorize";
		
		
		$query = array();
		
		
		foreach ($params as $param=>$value)
		{
			if($param != 'merchant') {
				//replace tilde in case any urls require literal ~ to function despite RFC 1738
				$param = str_replace('%7E', '~', rawurlencode($param));
				$value = str_replace('%7E', '~', rawurlencode($value));
				$query[] = $param.'='.$value;
			}
		}
		$query = implode('&', $query);
		$request = $protocol.'://'.$host.$authpath.'?'.$query;
		$response = @file_get_contents($request);
		//error_log(print_r($response,TRUE));
		return $response;
	} 
	
	
	public function makeWrapURL($params) {

		$protocol = "https";
		$host = "delightfully.com";
		
		$wrappath = "/partner/wrap";
		
		return $protocol.'://'.$host.$wrappath.'?merchant='.rawurlencode($params['merchant']).'&transaction='.rawurlencode($params['transaction']);
	}
	
	public function getBaseAPIParams() {

		
		
		$authkey = Mage::getStoreConfig('delightfully_options/account/key');
		$merchant = Mage::getStoreConfig('delightfully_options/account/merchant');
		$redeem =  Mage::getStoreConfig('delightfully_options/product/redeem_url');
		
		//possible auto-generation of redeem url
		//$redeem = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		
		if($redeem) {
			$params['redeem'] = $redeem;
		}
		
		$params['key'] = $authkey;
		$params['merchant'] = $merchant;

		return $params;
	}
}
