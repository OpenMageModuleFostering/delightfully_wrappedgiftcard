<?php
class Delightfully_WrappedGiftCard_Block_Adminhtml_Giftvoucher extends Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher {
	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'delightfully_wrappedgiftcard';
	}
}