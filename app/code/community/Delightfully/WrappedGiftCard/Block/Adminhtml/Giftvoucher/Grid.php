<?php


class Delightfully_WrappedGiftCard_Block_Adminhtml_Giftvoucher_Grid extends Magestore_Giftvoucher_Block_Adminhtml_Giftvoucher_Grid {
	protected function _prepareColumns()
	{
		 
		$this->addColumn('giftvoucher_comments', array(
				'header'	=> Mage::helper('giftvoucher')->__('Comments'),
				'align'	 =>'left',
				'index'	 =>'giftvoucher_comments',
				'filter_index'    => 'main_table.giftvoucher_comments'
		));
		 
		//$this->addExportType('*/*/exportCsv', Mage::helper('giftvoucher')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('giftvoucher')->__('XML'));
	
		return parent::_prepareColumns();
	}
}