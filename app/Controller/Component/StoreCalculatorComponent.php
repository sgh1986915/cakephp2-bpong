<?php
/**
 * @name StorecalculatorComponent
 * @author Oleg D.
 *
 * Package, Warehouses etc. calculations for Bpong store
 */

class StoreCalculatorComponent extends Component
{

    /**
* 
     * Calculate quantity of the product in stock, low stock etc.
     * @param $product_id Product ID
     *
     * @author Oleg D.
     *
     */
    function productsStock($product_id) 
    {
        $this->StoreSlotsCountry->recursive = 1;
        $objProduct=ClassRegistry::init('StoreProduct');
        $objProductsWarehouse=ClassRegistry::init('StoreProductsWarehouse');

        //$objProductsWarehouse = new StoreProductsWarehouse();
        $objProduct->recursive = -1;

        $low_stock=$objProduct->field('low_stock_quantity', array('id'=>$product_id));
        $pwarehouses=$objProductsWarehouse->find('all', array('conditions'=>array('product_id'=>$product_id,'StoreWarehouse.is_deleted<>1')));
        $quantity=0;
        if(!empty($pwarehouses)) {
            foreach($pwarehouses as $pwarehouse){
                $freeQuantity = $pwarehouse['StoreProductsWarehouse']['products_quantity'] - $pwarehouse['StoreProductsWarehouse']['set_aside_products_quantity'];
                if ($freeQuantity > 0) {
                    $quantity+= $freeQuantity;
                }
                $warequantity['warehouses'][$pwarehouse['StoreProductsWarehouse']['warehouse_id']]=($pwarehouse['StoreProductsWarehouse']['products_quantity'] - $pwarehouse['StoreProductsWarehouse']['set_aside_products_quantity']);
            }
        }
        $warequantity['total']=$quantity;
        $warequantity['low_stock']=$low_stock;

        return $warequantity;
    }

    /**
* 
     * Sorting Products To Warehouses and Back Order
     *
     * @author Oleg D.
     *
     */
    function sortToWarehouses($warehouses, $products, $compatibility)
    {
        
        // check warehouses product compatibility - we should send as many products (compatibility) from one warehouse
        foreach ($warehouses as $war_id => $warehouse) { 
            foreach ($warehouse['products_stock'] as $product_id => $warQuantity) {
                
                $warehouses[$war_id]['products_compatibility'][$product_id] = 0;
                
                if($warQuantity - $products[$product_id]['quantity'] >= 0) {        
                    foreach ($warehouse['products_stock'] as $next_product_id => $nextWarQuantity) {
                        if($nextWarQuantity - $products[$next_product_id]['quantity'] >= 0 && $product_id != $next_product_id) {    
                            if (!empty($compatibility[$products[$product_id]['slot_id']][$products[$next_product_id]['slot_id']])) {
                                $warehouses[$war_id]['products_compatibility'][$product_id] = $warehouses[$war_id]['products_compatibility'][$product_id] +1;    
                            }            
                        }                    
                    }        
                }
            }        
        }
        
        
        $war_stock=array();
        foreach($products as $product_id=>$product){
            $product_deleted=0;
            $product_qty = $products[$product_id]['quantity'];

            // Phase 1 search full quantity stock
            foreach($warehouses as $war_id => $warehouse) {
                if(isset($warehouses[$war_id]['products_stock'][$product_id])) {
                    
                    // check the best products compatibility we should send as many products (compatibility) from one warehouse
                       $isBestCompatibility = 1;
                    foreach ($warehouses as $next_war_id => $next_warehouse) {
                        if (isset($next_warehouse['products_compatibility'][$product_id]) && $next_warehouse['products_compatibility'][$product_id] > $warehouse['products_compatibility'][$product_id]) {
                            $isBestCompatibility = 0;
                            break;
                        }                  
                    }
                                   
                    if($isBestCompatibility) {
                        $war_stock = $warehouses[$war_id]['products_stock'];
                        if(isset($war_stock[$product_id]) && $war_stock[$product_id] > 0) {
    
                            $war_qty = $war_stock[$product_id];
                            
                            if($war_qty - $product_qty >= 0) {
                                $quantity=$war_qty - $product_qty;
                                $warehouses[$war_id]['products_stock'][$product_id] = $quantity;
                                $warehouses[$war_id]['products_order'][$product_id] = $product_qty;
                                if($war_qty-$product_qty==0) {
                                    unset($warehouses[$war_id]['products_stock'][$product_id]);
                                }
                                unset($products[$product_id]);
                                $product_deleted = 1;
                                $product_qty=0;
    
                                break;
                            }
    
                        }
                    }
                }
            }
            // Phase 2 search not full quantity stock
            if(!$product_deleted) {
                foreach($warehouses as $war_id => $warehouse){
                    if(isset($warehouses[$war_id]['products_stock'])) {
                        $war_stock=$warehouses[$war_id]['products_stock'];

                        if(isset($war_stock[$product_id])&&$war_stock[$product_id]>0) {

                            $war_qty=$war_stock[$product_id];

                            // delete empty product stock
                            if($war_stock[$product_id]<1) {
                                unset($warehouses[$war_id]['products_stock'][$product_id]);
                            }else{
                                $war_qty=$war_stock[$product_id];
                                if($war_qty-$product_qty<0) {
                                    $quantity=$product_qty-$war_qty;
                                    $products[$product_id]['quantity']=$product_qty=$quantity;
                                    $warehouses[$war_id]['products_order'][$product_id]=$war_qty;
                                    unset($warehouses[$war_id]['products_stock'][$product_id]);
                                    $war_qty=0;
                                }else{
                                    $quantity=$war_qty-$product_qty;
                                    $warehouses[$war_id]['products_stock'][$product_id]=$quantity;
                                    $warehouses[$war_id]['products_order'][$product_id]=$product_qty;
                                    unset($products[$product_id]);
                                    $product_deleted=1;
                                    $product_qty=0;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        $backorder['products']=$products;
        $sortResult=array('warehouses'=>$warehouses,'backorder'=>$backorder);

        return $sortResult;
    }
    /**
* 
     * Sorting Products in Warehouses to Packages
     *
     * @author Oleg D.
     *
     */
    function orderToPackages($warehouses,$products,$compatibility)
    {

        foreach($warehouses as $warehouse){
            $package_num=0;
            $war_id=$warehouse['war_id'];
            // delete internal not needed array 'products_stock'


            $warehouses[$war_id]['packages']=array();
            if(isset($warehouses[$war_id]['products_order'])) {
                $products_order=$warehouses[$war_id]['products_order'];
                foreach($products_order as $this_product_id=>$this_product_qty){

                    // maybe product was deleted form order, so:
                    if(isset($warehouses[$war_id]['products_order'][$this_product_id])) {
                        $this_product_qty;
                        $this_slot_id=$products[$this_product_id]['slot_id'];

                        // if comp. with itself
                        if(isset($compatibility[$this_slot_id][$this_slot_id])) {
                            $warehouses[$war_id]['packages'][$package_num][$this_product_id]=$this_product_qty;
                            unset($warehouses[$war_id]['products_order'][$this_product_id]);
                        }else{ // if not comp. with itself:

                            //echo $this_product_id.'-'.$warehouses[$war_id]['products_order'][$this_product_id];
                            $warehouses[$war_id]['packages'][$package_num][$this_product_id]=1;
                            $warehouses[$war_id]['products_order'][$this_product_id]--;
                            $this_product_qty=$warehouses[$war_id]['products_order'][$this_product_id];
                            for($i=0;$i<$this_product_qty;$i++){
                                $package_num++;
                                $warehouses[$war_id]['packages'][$package_num][$this_product_id]=1;
                                // if orders quantity = 0 unset array
                                $warehouses[$war_id]['products_order'][$this_product_id]--;
                                if($warehouses[$war_id]['products_order'][$this_product_id]==0) {
                                    unset($warehouses[$war_id]['products_order'][$this_product_id]);
                                }
                            }
                        }
                        // EOF if comp. with itself

                        // find Neighbors for backage
                        $this->__findNeighbors($warehouses, $compatibility, $products, $war_id, $package_num, $this_slot_id);
                        // EOF find Neighbors
                        $package_num++;
                    }
                }
                unset($warehouses[$war_id]['products_stock']);
                unset($warehouses[$war_id]['products_order']);
            }
            //delete not needed arrays
            unset($warehouses[$war_id]['products_stock']);
            unset($warehouses[$war_id]['products_order']);

        }

        return $warehouses;

    }
    /**
* 
     * Find Neighbors for current Backage
     *
     * @author Oleg D.
     *
     */
    function __findNeighbors(&$warehouses,$compatibility,$products,$war_id,$package_num,$this_slot_id)
    {
        $new_products_order=$warehouses[$war_id]['products_order'];
        foreach($new_products_order as $new_product_id=>$new_product_qty){
            $new_slot_id=$products[$new_product_id]['slot_id'];

            if(isset($compatibility[$this_slot_id][$new_slot_id])) {
                // if comp. with itself
                if(isset($compatibility[$new_slot_id][$new_slot_id])) {
                    $warehouses[$war_id]['packages'][$package_num][$new_product_id]=$new_product_qty;
                    unset($warehouses[$war_id]['products_order'][$new_product_id]);
                }else{
                    $warehouses[$war_id]['packages'][$package_num][$new_product_id]=1;
                    $warehouses[$war_id]['products_order'][$new_product_id]--;

                }
                // EOF if comp. with itself
            }else{ // if not comp. with itself se

            }
        }
    }
    /**
* 
     * Sorting Backorder Products to Warehouses and to Packages
     *
     * @author Oleg D.
     *
     */
    function backorderToPackages($orderWarehouses,$oldStock,$compatibility,$backorder)
    {
        // Sorting to Warehouses
        foreach($backorder as $product_id=>$backProducts){
            $slot_id=$backorder[$product_id]['slot_id'];
            $quantity=$backorder[$product_id]['quantity'];
            // if comp. with itself
            if(isset($compatibility[$slot_id][$slot_id])) {
                $its_compty=1;
            }else{
                $its_compty=0;
            }
            $countStock=count($oldStock);
            $backWarehouseId = 'looses';
            $i=1;
            // Find Warehouse for this Bckorders Product
            foreach($oldStock as $war_id=>$stock){
                if(isset($oldStock[$war_id][$product_id])) {
                    $backWarehouseId=$war_id;
                    break;
                }
                $i++;
            }
            // Add Backorders Package's to Warehouse
            if($its_compty) {
                $orderWarehouses[$backWarehouseId]['backorders'][][$product_id]=$quantity;

            }else{
                for($j=1;$j<=$quantity;$j++){
                    $orderWarehouses[$backWarehouseId]['backorders'][][$product_id]=1;
                }
            }

        }
        return $orderWarehouses;

    }
    /**
* 
     * Calculating of the Shipping price for all Packages/Warehouses
     *
     * @author Oleg D.
     *
     */
    function calculateShipping($orderWarehouses, $shippingGroups, $notUsingMethods, $weight_products, $ship_address)
    {
        set_time_limit(300);
        $errors = array();
        $shippingError = array();

        // Shipping Address address - DESTINATION
        $ship_state = $ship_zip = $ship_country = $ship_company = $ship_country_full = '';
          $ship_company='';  /// need to check !!!!

        if(isset($ship_address['Country']['iso2'])) {
            $ship_country=$ship_address['Country']['iso2'];
        }
        if(isset($ship_address['Country']['name'])) {
            $ship_country_full = $ship_address['Country']['name'];
        }

        if(isset($ship_address['Provincestate']['shortname'])) {
            $ship_state=$ship_address['Provincestate']['shortname'];
        }
        if(isset($ship_address['Address']['postalcode'])) {
              $ship_zip=$ship_address['Address']['postalcode'];
        }
          // EOF Shipping Address

          // Install shippings API classes

        $fedex_tag='fedex';
        $errors[$fedex_tag] = 0;

        $usps_tag='usps';
        $errors[$usps_tag] = 0;

        $ups_tag = 'ups';
        $errors[$ups_tag] = 0;
                        
        // EOF shipping classes installation

          $objStoreWarehouse=ClassRegistry::getObject('StoreWarehouse');
        $objStoreWarehouse->recursive = 1;
        // loop through all shipping groups (warehouse can be only in one group)
        foreach ($shippingGroups as $groupKye => $shippingGroup){

            $thisGroupCompanies = $shippingGroup['companies'];

            // loop through all warehouses of current shipping group
            foreach ($shippingGroup['warehouses'] as $war_id => $groupsWarehouse){
                $warehouse = $orderWarehouses[$war_id];

                // Warehouse Address - SENDER
                $ware_address = $objStoreWarehouse->find('first', array('conditions' => array('StoreWarehouse.id' => $war_id), 'fields' => array('Provincestate.shortname', 'StoreWarehouse.postalcode', 'Country.iso2')));
                $sender_state=$ware_address['Provincestate']['shortname'];
                $sender_zip=$ware_address['StoreWarehouse']['postalcode'];
                $sender_country=$ware_address['Country']['iso2'];

                if (!isset($warehouse['packages'])) { $warehouse['packages']=array(); 
                }
                if (!isset($warehouse['backorders'])) { $warehouse['backorders']=array(); 
                }
                $packagesNum = count($warehouse['packages']);
                $packages = $warehouse['packages'];
                $pachKey = $packagesNum + 1;
                $i = $j = 0;

                // make backorder as order (for usability)
                foreach ($warehouse['backorders'] as $backorderCopy) {
                    $packages[$pachKey]=$backorderCopy;
                    $pachKey++;
                }

                if (!empty($packages)) {
                    foreach ($packages as $pack_id => $prod){
                        $products = $prod['products'];
                        $package_weight = 0;
                        $packResultQuote = array();
                        foreach ($products as $product_id => $product_quantity) {
                            if (isset($weight_products[$product_id]['weight'])) {
                                $package_weight += $weight_products[$product_id]['weight'] * $product_quantity;
                            }
                        }
                        ////////////// SHIPPING METHODS /////////////////

                        // FEDEX calculations
                        if (isset($thisGroupCompanies[$fedex_tag]) && !$errors[$fedex_tag]) {
                            $packFedexQuote = $this->fedexReuest($ship_country, $ship_state, $ship_zip, $ship_company, $sender_state, $sender_zip, $sender_country, $package_weight);
                            if (!$packFedexQuote) {
                                $errors[$fedex_tag] = 1;
                                $packFedexQuote = array();
                            }else{
                                // Delete Not Using Shipping Methods for each warehouse
                                if(!empty($notUsingMethods[$war_id])) {
                                    foreach ($notUsingMethods[$war_id] as $nuMethodKey => $nuMethodValue) {
                                        if (isset($packFedexQuote[$nuMethodValue])) {
                                            unset($packFedexQuote[$nuMethodValue]);
                                        }
                                    }
                                }
                                $packResultQuote += $packFedexQuote;
                                $shippingGroups[$groupKye]['mix_shippings'][$fedex_tag][] = $packFedexQuote;
                            }
                        }
                        if ($errors[$fedex_tag]) {
                            $shippingGroups[$groupKye]['mix_shippings'][$fedex_tag] = array();
                            $packFedexQuote = array();
                        }
                        // EOF FEDEX calculations
                        
                        // UPS calculations
                        if (isset($thisGroupCompanies[$ups_tag]) && !$errors[$ups_tag]) {
                            $packUpsQuote = $this->upsRequest($ship_country, $ship_state, $ship_zip, $ship_company, $sender_state, $sender_zip, $sender_country, $package_weight);
                            if (!$packUpsQuote) {
                                $errors[$ups_tag] = 1;
                                $packUpsQuote = array();
                            }else{
                                // Delete Not Using Shipping Methods for each warehouse
                                if(!empty($notUsingMethods[$war_id])) {
                                    foreach ($notUsingMethods[$war_id] as $nuMethodKey => $nuMethodValue) {
                                        if (isset($packUpsQuote[$nuMethodValue])) {
                                            unset($packUpsQuote[$nuMethodValue]);
                                        }
                                    }
                                }
                                $packResultQuote += $packUpsQuote;
                                $shippingGroups[$groupKye]['mix_shippings'][$ups_tag][] = $packUpsQuote;
                            }
                        }
                        if ($errors[$ups_tag]) {
                            $shippingGroups[$groupKye]['mix_shippings'][$ups_tag] = array();
                            $packUpsQuote = array();
                        }
                        // EOF UPS calculations
                        
                        // USPS calculations
                        if (isset($thisGroupCompanies[$usps_tag]) && !$errors[$usps_tag]) {
                            $packUspsQuote = $this->uspsRequest($ship_country, $ship_country_full, $ship_state, $ship_zip, $ship_company, $sender_zip, $package_weight);
                            if (!$packUspsQuote) {
                                $errors[$usps_tag] = 1;
                                $packUspsQuote = array();
                            }else{
                                // Delete Not Using Shipping Methods for each warehouse
                                if(!empty($notUsingMethods[$war_id])) {
                                    foreach ($notUsingMethods[$war_id] as $nuMethodKey => $nuMethodValue) {
                                        if (isset($packUspsQuote[$nuMethodValue])) {
                                            unset($packUspsQuote[$nuMethodValue]);
                                        }
                                    }
                                }
                                $packResultQuote += $packUspsQuote;
                                $shippingGroups[$groupKye]['mix_shippings'][$usps_tag][] = $packUspsQuote;
                            }
                        }
                        if ($errors[$usps_tag]) {
                            $shippingGroups[$groupKye]['mix_shippings'][$usps_tag] = array();
                            $packUspsQuote = array();
                        }
                        // EOF USPS calculations
                        ///////////////// EOF SHIPPING METHODS ///////////
                        if($i >= $packagesNum) {

                            if (empty($packResultQuote)) {
                                $shippingError += $orderWarehouses[$war_id]['backorders'][$j]['products'];
                            }else{
                                $orderWarehouses[$war_id]['backorders'][$j]['packShipping']=$packResultQuote;
                            }

                            $j++;
                        }else{

                            if (empty($packResultQuote)) {
                                $shippingError += $orderWarehouses[$war_id]['packages'][$i]['products'];
                            }else{
                                $orderWarehouses[$war_id]['packages'][$i]['packShipping']=$packResultQuote;
                            }

                            $i++;
                        }
                    }
                }
            }
        }

        // Make list of real summ chippings companies/methods
        foreach ($shippingGroups as $groupKey => $shippingGroup){

            if (!empty($shippingGroup['mix_shippings'])) {
                foreach ($shippingGroup['mix_shippings'] as $shipCompanyName => $shippings){

                    foreach ($shippings as $shipKey => $shipMethods){

                        foreach ($shipMethods as $shipMethodKey => $shipMethodValue){

                            if (!isset($shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodKey])) {

                                $shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodKey]['amount'] = $shipMethodValue;
                                $shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodKey]['number'] = 1;
                            }else{

                                $shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodKey]['amount'] += $shipMethodValue;
                                $shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodKey]['number'] += 1;
                            }
                        }
                    }
                }
            }
            // checking - has all packages all shipping methods or not
            if (!empty($shippingGroups[$groupKey]['shippings'])) {
                foreach ($shippingGroups[$groupKey]['shippings'] as $shipCompanyName => $shipMethods){

                    $packsNum = count($shippingGroup['mix_shippings'][$shipCompanyName]);
                    foreach ($shipMethods as $shipMethodName => $shipMethod){
                        if ($shipMethod['number'] == $packsNum) {
                            $shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodName] = $shipMethod['amount'];
                        } else {
                            unset($shippingGroups[$groupKey]['shippings'][$shipCompanyName][$shipMethodName]);
                        }
                    }

                }
            }
            unset($shippingGroups[$groupKey]['mix_shippings']);
        }

        $result['orders'] = $orderWarehouses;
        $result['shippingError'] = $shippingError;
        $result['shippingGroups'] = $shippingGroups;
        unset($shippingGroups);
        unset($orderWarehouses);

        return $result;

    }
    /**
* 
     * Calculate Handling Price
     *
     * @author Oleg D.
     *
     */
    function calculateHandling($orderWarehouses,$productsHandling) 
    {

        $handlingTotal=0;

        foreach ($orderWarehouses as $warehouse) {
            $warehouseHandling = 0;
            if (isset($warehouse['war_id'])) {
                $warId = $warehouse['war_id'];
                $warHandling = $warehouse['handling'];
                if (!isset($warehouse['packages'])) { $warehouse['packages'] = array(); 
                }
                if (!isset($warehouse['backorders'])) { $warehouse['backorders'] = array(); 
                }
                $packagesNum = count($warehouse['packages']);
                $packages = $warehouse['packages'];
                $pachKey = $packagesNum + 1;

                foreach($warehouse['backorders'] as $backorderCopy){
                    $packages[$pachKey] = $backorderCopy;
                    $pachKey++;
                }
                $i = 0;
                $j = 0;
                if(!empty($packages)) {

                    foreach ($packages as $packId => $package) {
                        $packageHandling = 0;
                        $handlingType = '';
                        // Calculate B-C PRICING
                        if($warHandling['bc_handling']==1) {
                            $bcPrice=$this->__handlingBC($package, $warHandling, $productsHandling, $warId);
                        }else{
                            $bcPrice=0;
                        }
                        // Calculate B-B PRICING
                        if($warHandling['bb_handling']==1) {
                            $bbPrice=$this->__handlingBB($package, $warHandling, $productsHandling, $warId);
                        }else{
                            $bbPrice=0;
                        }
                        // Select B-B or B-C pricing
                        if($bbPrice&&$bcPrice) {
                            if($bbPrice<=$bcPrice) {
                                $packageHandling=$bbPrice;
                                $handlingType='bb';
                            }else{
                                $packageHandling=$bcPrice;
                                $handlingType='bc';
                            }
                        }elseif(!$bcPrice) {
                            $packageHandling=$bbPrice;
                            $handlingType='bb';
                        }elseif(!$bbPrice) {
                            $packageHandling=$bcPrice;
                            $handlingType='bc';
                        }
                        $warehouseHandling+=$packageHandling;
                        // Store handlings to packages and backorders
                        if ($i >= $packagesNum) {
                            $products = $orderWarehouses[$warId]['backorders'][$j];
                            unset($orderWarehouses[$warId]['backorders'][$j]);
                            $orderWarehouses[$warId]['backorders'][$j]['products']=$products;
                            $orderWarehouses[$warId]['backorders'][$j]['packHandling']['price']=$packageHandling;
                            $orderWarehouses[$warId]['backorders'][$j]['packHandling']['type']=$handlingType;
                            $j++;
                        } else {
                            $products = $orderWarehouses[$warId]['packages'][$i];
                            unset($orderWarehouses[$warId]['packages'][$i]);
                            $orderWarehouses[$warId]['packages'][$i]['products']=$products;
                            $orderWarehouses[$warId]['packages'][$i]['packHandling']['price']=$packageHandling;
                            $orderWarehouses[$warId]['packages'][$i]['packHandling']['type']=$handlingType;
                            $i++;
                        }
                        // EOF Make handly array

                    }
                }
                $handlingTotal+=$warehouseHandling;
                $orderWarehouses[$warId]['handlingPrice']=$warehouseHandling;
                unset($orderWarehouses[$warId]['handling']);
                unset($orderWarehouses[$warId]['distorder']);
                unset($products);

            }
        }
        $result['handlingTotal']=$handlingTotal;
        $result['orders']=$orderWarehouses;
        //echo "<pre>";
        //print_r($orderWarehouses);
        //exit;
        return $result;
    }
    /**
* 
     * Calculate BC Handling Price
     *
     * @author Oleg D.
     *
     */
    function __handlingBC($package,$warHandling,$productsHandling,$warId)
    {
        $bcFirst=$warHandling['bc_first'];
        $bcNext=$warHandling['bc_next'];
        $i=1;
        $bcPrice=0;
        //print_r($package);
        foreach($package as $productId=>$productQuantity){
            $specialFee=$productsHandling[$productId][$warId]['special_fee'];
            $additional_fee=$productsHandling[$productId][$warId]['additional_fee'];
            if($additional_fee>0) {
                $bcPrice+=($additional_fee*$productQuantity);
            }
            if($specialFee>0) {
                $bcPrice+=$productQuantity*$specialFee;
            }else{
                if($i==1) {
                    $bcPrice+=$bcFirst;
                    if($productQuantity>1) {
                        $bcPrice+=($productQuantity-1)*$bcNext;
                    }
                }else{
                    $bcPrice+=$productQuantity*$bcNext;
                }

                $i++;
            }
        }
        return $bcPrice;
    }
    /**
* 
     * Calculate BB Handling Price
     *
     * @author Oleg D.
     *
     */
    function __handlingBB($package,$warHandling,$productsHandling,$warId)
    {
        $skus=array();
        $bbFlatCharge=$warHandling['bb_flat_charge'];
        $bbEachLine=$warHandling['bb_each_line'];
        $bbPrice=0;
        foreach($package as $productId=>$productQuantity){
            $i=1;
            $specialFee=$productsHandling[$productId][$warId]['special_fee'];
            $additional_fee=$productsHandling[$productId][$warId]['additional_fee'];
            if($additional_fee>0) {
                $bbPrice+=($additional_fee*$productQuantity);
            }
            if(isset($productsHandling[$productId][$warId]['sku'])) {
                $thisSku=$productsHandling[$productId][$warId]['sku'];
            }else{
                $thisSku='my_custom_sku'.$productId;
            }
            if($specialFee>0) {
                $bbPrice+=$productQuantity*$specialFee;
            }else{
                if($i==1) {
                    $bbPrice+=$bbFlatCharge;
                }
                if(!isset($skus[$thisSku])) {
                    $bbPrice+=$bbEachLine;
                }
                $i++;
            }
            $skus[$thisSku]=1;
        }
        return $bbPrice;
    }
    /**
* 
     * Sorting Warehouses to Shipping Groups (By Shipping Companies)
     *
     * @author Oleg D.
     *
     */
    function sortShippingGroups($warehouses)
    {

        $shippingGroups = array();
        foreach ($warehouses as $war_id => $war){

            if (isset($warehouses[$war_id])) {
                // delete this warehouse from warehouses array
                unset($warehouses[$war_id]);
                $war_ship_count = count($war['shipCompanies']);
                $same_co = 0;
                $groupID = count($shippingGroups);

                $shippingGroups[$groupID]['companies'] = $war['shipCompanies'];
                $shippingGroups[$groupID]['warehouses'][$war_id] = $war_id;
                $shippingGroups[$groupID]['international'] = $war['international'];
                if (isset($shippingGroups[$groupID]['handlingPrice'])) {
                    $shippingGroups[$groupID]['handlingPrice'] += $war['handlingPrice'];
                }else{
                    $shippingGroups[$groupID]['handlingPrice'] = $war['handlingPrice'];
                }

                $country_id = $war['country_id'];
                foreach ($warehouses as $next_war_id => $next_war){

                    if (isset($warehouses[$next_war_id])) {

                        // firstly warehouses should hav one numbers of shipping companies and the same country_id
                        if ($war_ship_count == count($next_war['shipCompanies']) && $country_id == $next_war['country_id']) {

                            foreach ($war['shipCompanies'] as $war_shipco =>$empty_val){
                                if (isset($next_war['shipCompanies'][$war_shipco])) {
                                    $same_co++;
                                }
                            }
                            // all company aqual
                            if ($war_ship_count == $same_co) {
                                $shippingGroups[$groupID]['warehouses'][$next_war_id] = $next_war_id;
                                $shippingGroups[$groupID]['handlingPrice'] += $warehouses[$next_war_id]['handlingPrice'];
                                unset($warehouses[$next_war_id]);
                            }
                        }
                    }
                }
            }
        }

        return $shippingGroups;
    }
    /**
* 
     * Request FEDEX shippings
     * @author Oleg D.
     */
    function fedexReuest($ship_country, $ship_state, $ship_zip, $ship_company, $sender_state, $sender_zip, $sender_country, $package_weight)
    {

        App::import('Vendor', 'Fedex', array('file' => 'class.Fedex.php'));
        $Fedex = new Fedex();

        //$pounds = sprintf("%d", round($package_weight/16));   	
        $pounds = ceil($package_weight/16);
        
        if(FEDEX_TEST_MODE) {
            $Fedex->testing = 1;
            $Fedex->meter = FEDEX_TEST_METER;
            $Fedex->account = FEDEX_TEST_ACCOUNT;
        }else{
            $Fedex->testing = 0;
               $Fedex->meter = FEDEX_METER;
               $Fedex->account = FEDEX_ACCOUNT;
        }

        $Fedex->dest_state = $ship_state;
        $Fedex->dest_zip = $ship_zip;
        $Fedex->dest_country = $ship_country;
        $Fedex->dest_company = $ship_company;
        $Fedex->sender_state = $sender_state;
        $Fedex->sender_zip = $sender_zip;
        $Fedex->sender_country = $sender_country;
        $Fedex->total_weight = $pounds;

        $myFedexQuote = array();
        $fedex_error = 0;
        $fedexGet = 0;
        $fedexIteration = 0;
        $iterationsNum = 2; // Numbe of trying to ger request

        while($fedexGet == 0){
            $fedexQuote = $Fedex->quote();
            //echo "<pre/>"; print_r($fedexQuote);
            if (!empty($fedexQuote)) {
                foreach($fedexQuote as $this_code => $this_price){
                    $myFedexQuote[substr($this_code, 0, 2)] = $this_price;
                }

            }
            $fedexIteration++;
            if  (isset($myFedexQuote['90']) || isset($myFedexQuote['01']) || $fedexIteration > $iterationsNum) {
                $fedexGet=1;
            }
        }

        if (isset($myFedexQuote['error'])) {
            $fedex_error = 1;
        }

        if (!$fedex_error) {
            $result = $myFedexQuote;
        }else{
            $result = 0;
        }
        // show FEDEX quote info !!!!!!
        //echo 'pounds:' . $pounds; echo "<pre>"; print_r($myFedexQuote);
        //debug($result);
        return $result;

    }
    /**
* 
     * Request USPS shippings
     * @author Oleg D.
     */
    function uspsRequest($ship_country, $ship_country_full, $ship_state, $ship_zip, $ship_company, $sender_zip, $package_weight)
    {
        App::import('Vendor', 'UspsRate', array('file' => 'class.UspsRate.php'));
        $Usps= new UspsRate();

        $usps_error = 0;
        $myUspsQuote = array();
        $ozes = 0; 
        
        //$pounds = sprintf("%d", round($package_weight/16));
        //echo $pounds = ceil($package_weight/16);
        $pounds = intval($package_weight/16);
        $ozes = intval($package_weight - $pounds*16);
        
        // USPS needs no more than 150 lbs
        if ($pounds > 70) {
            return 0;
        }
        if ($ozes < 0) {
            $ozes = 0;
        }

        $Usps->setServer("http://Production.ShippingAPIs.com/ShippingAPI.dll");
        $Usps->setUserName(USPS_USERNAME);
        $Usps->setPass(USPS_PASSWORD);
        $Usps->setService("All");
        $Usps->setContainer("");
        if ($ship_country != 'US') {
            $Usps->international = 1;
            $Usps->setCountry($ship_country);
        }

        $Usps->setMachinable("False");
        $Usps->setSize("REGULAR");
        $Usps->setDestZip($ship_zip);
        $Usps->setOrigZip($sender_zip);
        $Usps->setWeight($pounds, $ozes);
        $myUspsQuote=$Usps->getPrice();
        
        //echo "<pre/>"; print_r($myUspsQuote);
        // show USPS quote info !!!!!!!!
        //echo 'pounds:' . $pounds; echo "<pre>"; print_r($myUspsQuote);

        if (isset($myUspsQuote['error'])) {
            $usps_error = 1;
        }

        if (!$usps_error) {
            $result = $myUspsQuote;
        }else{
            $result = 0;
        }
        return $result;
    }
    /**
     * Request UPS shippings
     * @author Oleg D.
     */
    function upsRequest($ship_country, $ship_state, $ship_zip, $ship_company, $sender_state, $sender_zip, $sender_country, $package_weight)
    {
        App::import('Component', 'Ups'); 
        $ObjUps= new UpsComponent();

        $ups_error = 0;
        $myUpsQuote = array();
        $pounds = $package_weight/16;
        
        // UPS needs no more than 150 lbs
        if ($pounds > 150) {
            return 0;
        }
        $upsProps['Weight'] = $pounds;
        $upsProps['ShipperCountry'] = $sender_country;
        
        $upsProps['ShipperZip'] = $sender_zip;
        $upsProps['ShipperCountry'] = $sender_country;
                
        $upsProps['ShipFromZip'] = $sender_zip;
        $upsProps['ShipFromCountry'] = $sender_country;
        
        $upsProps['ShipToZip'] = $ship_zip;
        $upsProps['ShipToCountry'] = $ship_country;

        //pr($upsProps);
        
        $myUpsQuote = $ObjUps->getRate($upsProps);

        //echo pr($myUpsQuote);

        return $myUpsQuote;
    }
}
?>