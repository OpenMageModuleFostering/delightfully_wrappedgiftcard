<?php
class Delightfully_WrappedGiftCard_Model_Giftvoucher_Price extends Magestore_Giftvoucher_Model_Product_Price
{
/*
	protected function _applyOptionsPrice($product, $qty, $finalPrice){
		$finalPriceOut = $finalPrice;
		try {			
			$finalPriceOut = parent::_applyOptionsPrice($product,$qty,$finalPrice);
		} catch (Exception $e) {
			Mage::log('Unable to get parent options price.', null, 'dly.log');
			Mage::logException($e);
			Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}	
		//apply fees (based on Delightfully-specific options)
		$wrappingFee = -1;

		try {
 			$wrappingFee = Mage::getStoreConfig('delightfully_options/product/wrap_price');
 		} catch (Exception $e) {
 		Mage::logException($e);
		}
		if(!$wrappingFee || $wrappingFee == -1) {
			$wrappingFee = 0.0;
			Mage::log('no wrapping fee setting found', null, 'dly.log');
			Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}
		$emailFee = 0.00;
		$selfFee = 0.00;
		
		$sendMethodOption = 0;
		
		try {
			$sendMethodOption = $product->getCustomOption('send_friend');
		} catch (Exception $e) {
			Mage::log('no delivery method found', null, 'dly.log');
			Mage::logException($e);
			Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}
			
		if ($sendMethodOption){
			$method = $sendMethodOption->getValue();
			if($method == 2) {
				$finalPriceOut = $finalPriceOut + $wrappingFee;
			} else if($method == 1) {
				$finalPriceOut = $finalPriceOut + $emailFee;
			} else if($method == 0) {
				$finalPriceOut = $finalPriceOut + $selfFee;
			}
		}
		
		return $finalPriceOut;
	}
}
*/
    // get final price for catalog rule
    public function getFinalPrice($qty=null, $product) {

		$finalPriceOut = 0;
		try {			        	
			$finalPriceOut = parent::getFinalPrice($qty, $product);
		} catch (Exception $e) {
			//Mage::log('Unable to get parent options price.', null, 'dly.log');
			//Mage::logException($e);
			//Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}	
		//apply fees (based on Delightfully-specific options)
		$wrappingFee = -1;

		try {
 			$wrappingFee = Mage::getStoreConfig('delightfully_options/product/wrap_price');
 		} catch (Exception $e) {
 			//Mage::logException($e);
		}
		if(!$wrappingFee || $wrappingFee == -1) {
			$wrappingFee = 0.0;
			//Mage::log('no wrapping fee setting found', null, 'dly.log');
			//Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}
		$emailFee = 0.00;
		$selfFee = 0.00;
		
		$sendMethodOption = 0;
		
		try {
			$sendMethodOption = $product->getCustomOption('send_friend');
		} catch (Exception $e) {
			//Mage::log('no delivery method found', null, 'dly.log');
			//Mage::logException($e);
			//Mage::log(array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')), null, 'dly.log');
		}
			
		if ($sendMethodOption){
			$method = $sendMethodOption->getValue();
			if($method == 2) {
				$finalPriceOut = $finalPriceOut + $wrappingFee;
			} else if($method == 1) {
				$finalPriceOut = $finalPriceOut + $emailFee;
			} else if($method == 0) {
				$finalPriceOut = $finalPriceOut + $selfFee;
			}
		}
		
		return $finalPriceOut;
	}
}