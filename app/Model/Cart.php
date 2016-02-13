<?php
class Cart extends AppModel
{

    var $name = 'Cart';
    var $useTable = false; 
    /**
* 
     * Select only 'store' Countries.
     * @author Oleg D.
     */
    function cartsCountries($Address, $cart)
    {

        $product_list = '';
        foreach ($cart['products'] as $product) {
            $product_list .= $product['slot_id'].',';
        }
        $product_list = substr($product_list, 0, -1);
        $countries = $Address->query(
            '
		SELECT DISTINCT(countries.id), countries.name  FROM store_slots_countries sc 
		LEFT JOIN countries ON sc.country_id = countries.id
		WHERE slot_id IN(' . $product_list . ') AND country_id > 0'
        );
        
        $shipCountries = Set::combine($countries, '{n}.countries.id', '{n}.countries.name');
        
        return $shipCountries;
    }
    /*
    * Calculate quantity of not Promocodes products
    * @author Oleg D
    */
    function freePromoQuantity($product) 
    {

        $promoQuant = 0;
        $quantity = $product['quantity'];
        $freeProduct = 0;
        if(!isset($product['discounts']) || empty($product['discounts'])) {
            if(isset($product['promocodes'])) {
                foreach ($product['promocodes'] as $promocode) {
                    $promoQuant += $promocode['quantity'];
                }
            }
            $freeProduct = $quantity - $promoQuant;        
        } else {
            $freeProduct = 0;            
        }

        return $freeProduct;
    }
    /*
    * Check - was used promocode or not.
    * @author Oleg D
    */
    function promocodeUsed($promocodeID, $products) 
    {
        $used = 0;
        foreach ($products as $product){
            if(isset($product['promocodes'][$promocodeID])) {
                $used = 1;        
            }
        }
        return $used;
    }
        
}
?>