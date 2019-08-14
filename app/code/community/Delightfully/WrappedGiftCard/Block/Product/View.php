<?php
class Delightfully_WrappedGiftCard_Block_Product_View extends Magestore_Giftvoucher_Block_Product_View {

	public function getGiftAmount($product){
		return parent::getGiftAmount($product);
	}

}