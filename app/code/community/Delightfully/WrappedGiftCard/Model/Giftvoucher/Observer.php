<?php

class Delightfully_WrappedGiftCard_Model_Giftvoucher_Observer extends Magestore_Giftvoucher_Model_Observer

{
 public function orderPlaceBefore($observer) {
 	parent::orderPlaceBefore($observer); 
 	$order = $observer->getEvent()->getOrder();
 	//gift vouchers need order items to have been saved before giftvoucher is created in order placement. Multishipping needs this extra save.
 	$order->save();
 }

public function orderPlaceAfter($observer) {

	parent::orderPlaceAfter($observer);
	
	$order = $observer->getEvent ()->getOrder ();
	
	$orderIncrementId = $order->getIncrementId();
	//get card history entries for this order
	$giftHistories = Mage::getResourceModel('giftvoucher/history_collection')
	->addFieldToFilter('order_increment_id', $orderIncrementId);
	
	foreach ( $giftHistories as $gift ) {
		$voucherId = $gift->getGiftvoucherId();

		$orderItem = Mage::getModel('sales/order_item')->load($gift->getOrderItemId());
		
		$buyRequest = $orderItem->getProductOptionByCode('info_buyRequest');
		
		$giftVoucher = Mage::getModel('giftvoucher/giftvoucher')->load($voucherId);
	
		$methodText = "";
		$method = $buyRequest ['send_friend']; {
			if($method == 2) {
				$methodText = "Via Delightfully";
			} else if ($method == 1) {
				$methodText = "To friend via email";
			} else {
				$methodText = "Send to self";
			}
		}
		$oldComments = $giftVoucher->getData('giftvoucher_comments');
		if($oldComments) {
			$methodText = $oldComments."\n".$methodText;
		}
		$giftVoucher->setData ( 'giftvoucher_comments', $methodText )->save ();

		$gift->setData('comments', $methodText)->save();
		
	}
}
		
}