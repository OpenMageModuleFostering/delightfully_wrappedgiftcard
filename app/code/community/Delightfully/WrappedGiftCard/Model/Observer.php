<?php

/**
 * This stub prevents email from being sent to recipients of a gift card that has an SKU containing DLYGC.
 * Attempt a future-proof against later core changes -- overwrite all emails with $replace_email before call to generateGiftCardAccounts
 * and replace emails after.
 */

class Delightfully_WrappedGiftCard_Model_Observer extends Enterprise_GiftCard_Model_Observer

{

	
  /**
   * Generate gift card accounts after order save
   *
   * @param Varien_Event_Observer $observer
   * @return Enterprise_GiftCard_Model_Observer
   */
  public function generateGiftCardAccounts(Varien_Event_Observer $observer)
  {

	$replace_email = "concierge@delightfully.com";

	//change the destination email before we run code that sends giftcard email
	$emails = array();

	$order = $observer->getEvent()->getOrder();
	$requiredStatus = Mage::getStoreConfig(Enterprise_GiftCard_Model_Giftcard::XML_PATH_ORDER_ITEM_STATUS, $order->getStore());
  
	foreach ($order->getAllItems() as $item) {
	  if ($item->getProductType() == Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD) {
		if(preg_match('/DLYGC/',$item->getSku())) {
		  Mage::log("Modifying email address for gift card notification"); 

		  $options = $item->getProductOptions();

		  array_unshift($emails,$options['giftcard_recipient_email']);

		  $options['giftcard_recipient_email'] = $replace_email;

		  $item->setProductOptions($options);

		}	  
	  }
	}
	  

	//Now call the superclass to perform default giftcard creation
	parent::generateGiftCardAccounts($observer);



	//revert to user's intended email for later use
	foreach ($order->getAllItems() as $item) {
	  if ($item->getProductType() == Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD) {
		if(preg_match('/DLYGC/',$item->getSku())) {
		  Mage::log("Restoring email addresses"); 

		  $options = $item->getProductOptions();

		  $options['giftcard_recipient_email'] = array_pop($emails);

		  $item->setProductOptions($options);
		  $item->save();
		}	  
	  }
	}
  }
}