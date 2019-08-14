<?php
class Delightfully_WrappedGiftCard_Helper_Giftvoucher_Data extends Magestore_Giftvoucher_Helper_Data {
	
	//Get the list of labels we want to display data for
	public function getGiftVoucherOptions() {
		$optionLabels = parent::getGiftVoucherOptions();
		$options1['send_friend'] = $this->__('Delivery Method');
		
		return array_merge($options1,$optionLabels);
	}
	
}