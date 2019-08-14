<?php
class Delightfully_WrappedGiftCard_Block_Cart_Item extends Magestore_Giftvoucher_Block_Cart_Item {

	//shim to show friendly version of product options in cart instead of internal send_friend code
	public function getProductOptions(){
		$options = parent::getProductOptions(); //get default relevant translated option labels & values
		
		$dhelper = Mage::helper('delightfully_wrappedgiftcard');
		
		return $dhelper->extendOptionsWithDeliveryMethod($options); //edit options to include correct names for delivery options
	}
}