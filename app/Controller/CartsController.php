<?php

class CartsController extends AppController
{

    var $name = 'Carts';
    var $uses = array('Cart','StoreProduct','StoreOrder','Image', 'Promocode', 'StoreSlot','StoreOrdersPackage','StoreProductattribute','StoreSlotsProductattribute','StoreProductsProductattribute','StoreWarehouse','StoreProductsWarehouse','Address','Provincestate','StoreDiscount');
    var $components = array(
       'StoreCalculator'
    );
    /**
* 
     * Add Product To Cart and Redirect to Back URL
     *
     * @author Oleg D.
     * @param $product_id Product ID
     * @param $from  from what bage made action ADD
     *  $from = 1 StoreSlots->front_show
     *  $from = 2 Carts/manage/
     *
     */
    function add_from($product_id=0,$from=1,$quantity=1,$discount_id=null) 
    {
        if (!$product_id) {
            $this->Session->setFlash(__('Invalid id of the Product'), 'flash_error');
            return $this->redirect('/');
        }
        // Discounts calculations
        if($discount_id) {
            $cart=$this->Session->read('storecart');
            $user_id=$this->getUserID();
            $this->StoreDiscount->recursive=-1;
            $this->StoreProduct->recursive=-1;
            $cartDiscounted=0;
            $thisDiscount=$this->StoreDiscount->find('first', array('conditions'=>array('id'=>$discount_id)));
            $thisDiscount=$thisDiscount['StoreDiscount'];


            $usersLimit=$thisDiscount['users_limit'];
            $discountGroupID = $thisDiscount['group_id'];
            $groupsLimit=$thisDiscount['groups_limit'];
            $wasDiscountedGroup=$thisDiscount['was_discounted'];

            $wasDiscountedUser=$this->StoreDiscount->wasDiscounted($discount_id, $user_id);
            $products_price=$this->StoreProduct->field('price', array('id'=>$product_id));
            $discountAmount=$this->StoreDiscount->discountAmount($thisDiscount['discount'], $thisDiscount['discount_type'], $products_price);

            $thisDiscountLimit=$this->StoreDiscount->calculateLimit($usersLimit, $groupsLimit, $wasDiscountedUser, $wasDiscountedGroup);
            $discounts['id']=$discount_id;
            $discounts['amount']=$discountAmount;
            $discounts['group_id']=$discountGroupID;
            $discounts['limit']=$thisDiscountLimit;
        }else{
            $discounts=null;
        }
        // EOF Discounts calculations
        //echo "<pre>";
        //print_r($discounts);
        //exit;
        $quantity=intval($quantity);
        if(!$quantity) {
            $quantity=1;
        }
        $params=$this->__productParams($product_id);
        for($i=1;$i<=$quantity;$i++){
            $this->__add($product_id, $params, $discounts);
        }

        // Redirect Options:
        if($from==1) {
            $product=$this->StoreProduct->find('all', array('conditions'=>array('StoreProduct.id='.$product_id)));
            if(isset($product[0]['StoreSlot']['slug'])) {
                $back_url='/store/product/'.$product[0]['StoreSlot']['slug'].'/'.$product_id;
                $this->Session->setFlash(__('Product "'.$product[0]['StoreProduct']['name'].'" has been added to the cart.'), 'flash_success');
                return $this->redirect('/shopping_cart');
            }else{

                $this->Session->setFlash(__('Product "'.$product[0]['StoreProduct']['name'].'" has been added to the cart.'), 'flash_success');
                return $this->redirect('/shopping_cart');
            }
            exit;
        }elseif($from=2) {
            return $this->redirect('/shopping_cart');
        }
    }


    /**
* 
     * Clean the Cart
     *
     * @author Oleg D.
     *
     */
    function delete_all() 
    {
        $this->Session->delete('storecart');
        $this->Session->delete('storeorders');

        exit;
    }
    /**
* 
     *  AJAX
     *  Alert if Total Stock - Order Quantity>= Low Stock
     *  @author Oleg D.
     *
     */
    function confirmStockAjax($product_id, $quantity = 0) 
    {
        $this->layout=false;
        Configure::write('debug', 0);
        if ($this->RequestHandler->isAjax()) {
            $stock=$this->StoreCalculator->productsStock($product_id);
            $total_stock=$stock['total'];
            $low_stock=$stock['low_stock'];
            $needed=$total_stock-$quantity;
            if($needed<=$low_stock&&$needed>=0) {
                echo 'low';
            }elseif($needed<0) {
                $backordersProps = $this->StoreProduct->allowBackorder($product_id);
                $response['use_backorders'] = $backordersProps['use_backorders'];
                $response['restock_date'] = $backordersProps['restock_date'];
                if($response['use_backorders']) {
                    echo 'out';
                } else {
                    if($backordersProps['restock_date']) {
                        echo $backordersProps['restock_date'];
                    } else {
                        echo 'notbackorders';
                    }
                }
            } else {
                echo "ok";
            }
        }
        exit;
    }
    /**
* 
     *  Check Total Stock - Order Quantity>= Low Stock
     *  @author Oleg D.
     *
     */
    function __inStock($product_id,$quantity) 
    {
         $stock=$this->StoreCalculator->productsStock($product_id);
        $total_stock=$stock['total'];
        $low_stock=$stock['low_stock'];

        $needed=$total_stock-$quantity;
        if($needed<=$low_stock&&$needed>=0) {
            return 'low';
        }elseif($needed<0) {
            return 'out';
        }
    }
    /**
* 
     * Manage items in the cart.
     * Main Checkout container
     *
     * @author Oleg D.
     */
    function manage($action=null) 
    {
        $this->Access->checkAccess('Cart', 'u');
        $this->storecategoriesMenu();
        if($action=='recalculation') {
            $this->Session->setFlash(__('Sorry! Some Prices or Discounts have changed. They have been recalculated, so you can continue checkout'), 'flash_notice');
            return $this->redirect('/shopping_cart');
        }

        $cart = $this->Session->read('storecart');
        $total_weight = 0;
        $total_price = 0;
        $price = 0;
        $total_discount = 0;
        $products=array();
        $promocodes=array();
        $promocode = 0;
        $productIds = array();
        $usingDiscount = 0;

        if(isset($this->request->data['Promocode']['value']) && $this->request->data['Promocode']['value']) {
            $promocode = $this->request->data['Promocode']['value'];
        }

        $this->StoreProduct->recursive=-1;
        if($cart&&!empty($cart)&&!empty($cart['products'])) {
            if(!empty($cart['products'])) {
                foreach($cart['products'] as $cart_product){
                    $id=$cart_product['id'];
                    $this_discount=0;
                    $this->StoreProduct->id=$id;

                    $slot_id=$this->StoreProduct->field('slot_id', array('id'=>$id));
                    $slug=$this->StoreSlot->field('slug', array('id='.$slot_id));

                    $weight=$this->StoreProduct->field('weight')*$cart_product['quantity'];
                    $products[$id]['id']=$id;
                    $price=$this->StoreProduct->field('price', array('id'=>$id));
                    $products[$id]['quantity']=$cart_product['quantity'];
                    $products[$id]['price']=$price;
                    $products[$id]['name']=$this->StoreProduct->field('name', array('id'=>$id));
                    $products[$id]['link']='/store/product/'.$slug.'/'.$id;
                    $products[$id]['image'] = $this->Image->field('filename', array('model' => 'StoreProduct', 'model_id' => $id, 'is_deleted' => 0));
                    $products[$id]['weight']=$this->StoreProduct->field('weight', array('id'=>$id));
                    if(isset($cart_product['discounts'])&&!empty($cart_product['discounts'])) {
                        $usingDiscount = 1;
                        foreach($cart_product['discounts'] as $dkey=>$discount){
                            $this_discount+=($discount['amount']*$discount['quantity']);

                        }
                        $total_discount+=$this_discount;
                        $products[$id]['discount']=$this_discount;

                    }
                    $this_discount = 0;
                    if(isset($cart_product['promocodes'])&&!empty($cart_product['promocodes'])) {

                        // delete promocodes if we using discount
                        if ($usingDiscount) {
                                 unset($cart['products'][$id]['promocodes']);
                                 $this->Session->write('storecart', $cart);
                        } else {
                            foreach($cart_product['promocodes'] as $pkey=>$promocode){
                                $this_discount += ($promocode['amount'] * $promocode['quantity']);
                                if (!isset($promocodes[$pkey]['amount'])) {
                                    $promocodes[$pkey]['total_price'] = 0;
                                }
                                $promocodes[$pkey]['total_price'] += ($promocode['amount'] * $promocode['quantity']);
                                $promocodes[$pkey]['code'] = $this->Promocode->field('code', array('Promocode.id' => $pkey));
                            }
                            $total_discount+=$this_discount;
                            $products[$id]['discount']=$this_discount;
                        }
                    }
                } 
            }

        }else{
            $cart=0;
        }
        $this->set('user_id', $this->getUserID());
           $this->set('products', $products);
           $this->set('total_discount', $total_discount);
           $this->set('promocodes', $promocodes);
           $this->set('usingDiscount', $usingDiscount);




    }
    /**
* 
     * Logging information before login
     *
     * @author Oleg D.
     */
    function login() 
    {
        $this->storecategoriesMenu();

    }

    /**
* 
     * Add Product To Cart
     * @author Oleg D.
     *
     */
    function __add($product_id,$params=array(),$discount=null) 
    {
        if (!$product_id) {
            exit('Product ID error!');
        }
        $cart=$this->Session->read('storecart');
        if(!isset($cart['weight'])) {
            $cart['weight']=0;
        }
        if(!empty($params)) {
            $product_price=$params['price'];
            $product_weight=$params['weight'];
        }else{
            $params=$this->__productParams($product_id);
        }
        $product_price=$params['price'];
        $product_weight=$params['weight'];

        if($product_weight) {
            $cart['weight']+=$product_weight;
        }

        $product_cart['id']=$product_id;
        $product_cart['slot_id']=$this->StoreProduct->field('slot_id', 'id='.$product_id);
        $product_cart['price']=$product_price;
        if(!isset($cart['items'])) {
            $cart['items']=0;
        }
        $cart['items']++;

        if(isset($cart['products'][$product_id])) {
            $product_cart['quantity']=$cart['products'][$product_id]['quantity']+1;
            if(isset($cart['products'][$product_id]['discounts'])) {
                $product_cart['discounts']=$cart['products'][$product_id]['discounts'];
            }
            if(isset($cart['products'][$product_id]['promocodes'])) {
                $product_cart['promocodes']=$cart['products'][$product_id]['promocodes'];
            }
        }else{
            $product_cart['quantity']=1;
        }

           $cart['products'][$product_id]=$product_cart;

           // Save discount
        if($discount) {
            $cartDiscounted=0;
            $discount_id=$discount['id'];
            if(isset($cart['products'])&&!empty($cart['products'])) {
                foreach($cart['products'] as $dproduct){
                    if(isset($dproduct['discounts'][$discount_id]['quantity'])) {
                        $cartDiscounted+=$dproduct['discounts'][$discount_id]['quantity'];
                    }
                }
            }
            if(isset($cart['products'][$product_id]['discounts'][$discount_id])) {
                if (($cart['products'][$product_id]['discounts'][$discount_id]['limit']>$cart['products'][$product_id]['discounts'][$discount_id]['quantity']
                    && $cart['products'][$product_id]['discounts'][$discount_id]['limit']>$cartDiscounted)
                    ||$cart['products'][$product_id]['discounts'][$discount_id]['limit']=='unlimited'
                ) {

                    $cart['products'][$product_id]['discounts'][$discount_id]['quantity']+=1;
                }
            }else{
                $cart['products'][$product_id]['discounts'][$discount_id]=$discount;
                $cart['products'][$product_id]['discounts'][$discount_id]['quantity']=1;
            }
        }
           $this->Session->write('storecart', $cart);
           return $product_cart;

    }
    /**
* 
     * Delete Product from Cart
     * @author Oleg D.
     *
     */
    function __del($product_id,$params=array(),$all=0) 
    {
        $this->layout=false;
        $cart=$this->Session->read('storecart');

        if(!isset($cart['weight'])) {
            $cart['weight']=0;
        }

        if(!empty($params)) {
            $product_price=$params['price'];
            $product_weight=$params['weight'];
        }else{
            $params=$this->__productParams($product_id);
        }
        $product_price=$params['price'];
        $product_weight=$params['weight'];

        if(!isset($cart['items'])) {
            $cart['items']=0;

        }

        // delete all product
        if($all) {
            $cart['items']-=$cart['products'][$product_id]['quantity'];
            if($product_weight) {
                $cart['weight']-=$product_weight*$cart['products'][$product_id]['quantity'];
            }
            unset($cart['products'][$product_id]);

        }else{
            if(isset($cart['products'][$product_id])&&$cart['products'][$product_id]['quantity']>0) {

                // If we have DISCOUNTS for this product
                if(isset($cart['products'][$product_id]['discounts'])&&!empty($cart['products'][$product_id]['discounts'])) {
                    // calculate discounts quantity
                    $discount_quantity=0;
                    foreach($cart['products'][$product_id]['discounts'] as $discount_id=>$this_discount){
                        $discount_quantity+=$this_discount['quantity'];
                    }
                    // if all not discounted products already deleted
                    if($cart['products'][$product_id]['quantity']<=$discount_quantity) {
                        foreach($cart['products'][$product_id]['discounts'] as $discount_id=>$this_discount){
                            if($this_discount['quantity']>1) {
                                $cart['products'][$product_id]['discounts'][$discount_id]['quantity']--;
                            }else{
                                unset($cart['products'][$product_id]['discounts'][$discount_id]);
                            }
                            break;
                        }
                    }
                }
                // EOF DISCOUNTS
                // If we have PROMOCODES for this product
                if(isset($cart['products'][$product_id]['promocodes'])&&!empty($cart['products'][$product_id]['promocodes'])) {
                    // calculate discounts quantity
                    $discount_quantity=0;
                    foreach($cart['products'][$product_id]['promocodes'] as $promocode_id=>$this_promocode){
                        $promocode_quantity+=$this_promocode['quantity'];
                    }
                    // if all not discounted products already deleted
                    if($cart['products'][$product_id]['quantity']<=$promocode_quantity) {
                        foreach($cart['products'][$product_id]['promocodes'] as $promocode_id=>$this_promocode){
                            if($this_promocode['quantity']>1) {
                                $cart['products'][$product_id]['promocodes'][$promocode_id]['quantity']--;
                            }else{
                                unset($cart['products'][$product_id]['promocodes'][$discount_id]);
                            }
                            break;
                        }
                    }
                }
                // EOF PROMOCODES
                $cart['products'][$product_id]['quantity']--;
                $cart['items']--;
                if($product_weight) {
                    $cart['weight']-=$product_weight;
                }
                $cart['products'][$product_id]['id']=$product_id;
                $cart['products'][$product_id]['price']=$product_price;



            }else{

                unset($cart['products'][$product_id]);
            }

            if($cart['products'][$product_id]['quantity']<1) {
                unset($cart['products'][$product_id]);
            }

        }
        if($cart['items']<1) {
            $cart['items']='0';
        }
        if($cart['weight']<0) {
            $cart['weight']=0;
        }


        $this->Session->write('storecart', $cart);

        return $cart['products'][$product_id];

    }
    /**
     * Delete Product from Cart by AJAX (only 1 product)
     * @author Oleg D.
     */
    function check_promocodes_conditions() 
    {

        $amount = $this->StoreOrder->cartAmount() + $this->StoreOrder->cartDiscount();
        $cart = $this->Session->read('storecart');
        $foundError = 0;
        foreach ($cart['products'] as $productID => $product) {
            if (isset($product['promocodes'])) {
                foreach ($product['promocodes'] as $promocodeID => $promocode) {
                    if ($promocode['threshold'] > $amount) {
                        unset($cart['products'][$productID]['promocodes'][$promocodeID]);
                        $foundError = 1;
                        $this->Session->setFlash('Your promocode has been deleted from the Cart, because subtotal of order is < $' . sprintf("%01.2f", ($promocode['threshold'])), 'flash_notice');
                    }
                }
            }
        }
        $this->Session->write('storecart', $cart);
        return $foundError;
    }

    /**
* 
     * Delete Product from Cart by AJAX (only 1 product)
     * @author Oleg D.
     */

    function del_ajax($product_id) 
    {
        $this->layout=false;
        Configure::write('debug', 0);
        if ($this->RequestHandler->isAjax()) {

            $params=$this->__productParams($product_id);
            $product_values=$this->__del($product_id, $params);

            $cart=$this->Session->read('storecart');
            if(isset($product_values['quantity'])) {
                $response['this_quantity']=$product_values['quantity'];
                $response['this_tdiscount']=0;
                if(isset($cart['products'][$product_id]['discounts'])&&!empty($cart['products'][$product_id]['discounts'])) {
                    foreach($cart['products'][$product_id]['discounts'] as $discount){
                        $response['this_tdiscount']+=($discount['quantity']*$discount['amount']);
                    }
                }
                if(isset($cart['products'][$product_id]['promocodes'])&&!empty($cart['products'][$product_id]['promocodes'])) {
                    foreach($cart['products'][$product_id]['promocodes'] as $promocode){
                        $response['this_tdiscount']+=($promocode['quantity']*$promocode['amount']);
                    }
                }
                $response['this_tdiscount']=sprintf("%01.2f", $response['this_tdiscount']);
                $response['this_tcost']=sprintf("%01.2f", (($product_values['quantity']*$product_values['price'])-$response['this_tdiscount']));


            }else{
                $response['this_quantity']='0';
                $response['this_tcost']=sprintf("%01.2f", 0);
            }
            $promocodeError = $this->check_promocodes_conditions();

            $response=$this->__make_totals($response);
            $response['stock']='';
            $response['stock']='';
            $response['promocode_error']=$promocodeError;
            $response['stock']=$this->__inStock($product_id, $product_values['quantity']);

            if($response['stock']=='out') {
                $response['products_stock']=$this->StoreProduct->field('name', array('StoreProduct.id ='.$product_id));
            }
            exit($this->Json->encode($response));
        }
    }
    /**
* 
     * Delete Product from Cart by AJAX (all quantity)
     * @author Oleg D.
     */

    function product_delAjax($product_id) 
    {
        $this->layout=false;
        Configure::write('debug', 0);
        if ($this->RequestHandler->isAjax()) {

            $params=$this->__productParams($product_id);
            $product_values=$this->__del($product_id, $params, 1);

            $cart=$this->Session->read('storecart');

            $promocodeError = $this->check_promocodes_conditions();

            $response=$this->__make_totals($response);
            $response['promocode_error']=$promocodeError;


            exit($this->Json->encode($response));
        }
    }
    /**
* 
     * Add Product To Cart by AJAX
     *
     * @author Oleg D.
     * @param $from  from what bage made action ADD
     *
     */
    function add_ajax($product_id=0,$quantity = 0) 
    {
        if (!$product_id) {
            exit('Product ID error');
        }
        $this->layout=false;
        Configure::write('debug', 0);
        if ($this->RequestHandler->isAjax()) {
            $quantity=intval($quantity);
            if(!$quantity) {
                $quantity=1;
            }
            $params=$this->__productParams($product_id);
            for($i=1;$i<=$quantity;$i++){
                $product_values=$this->__add($product_id, $params);
            }
            $cart=$this->Session->read('storecart');

            $response['this_quantity']=$product_values['quantity'];

            $response['stock']='';
            $response['this_tdiscount']=0;
            if(isset($cart['products'][$product_id]['discounts'])&&!empty($cart['products'][$product_id]['discounts'])) {
                foreach($cart['products'][$product_id]['discounts'] as $discount){
                    $response['this_tdiscount']+=($discount['quantity']*$discount['amount']);
                }
            }
            if(isset($cart['products'][$product_id]['promocodes'])&&!empty($cart['products'][$product_id]['promocodes'])) {
                foreach($cart['products'][$product_id]['promocodes'] as $promocode){
                    $response['this_tdiscount']+=($promocode['quantity']*$promocode['amount']);
                }
            }
            $response['this_tdiscount']=sprintf("%01.2f", $response['this_tdiscount']);
            $response['this_tcost']=sprintf("%01.2f", (($product_values['quantity']*$product_values['price'])-$response['this_tdiscount']));
            $response['products_stock']='';
            $response['stock']=$this->__inStock($product_id, $product_values['quantity']);

            if($response['stock']=='out') {
                $response['products_stock']=$this->StoreProduct->field('name', array('StoreProduct.id ='.$product_id));
            }
            $response=$this->__make_totals($response);
            exit($this->Json->encode($response));
        }
        exit;
    }
    /**
* 
     * Prepare Totals values for checkout footer
     *
     * @author Oleg D.
     *
     */
    function __make_totals($response)
    {

        $cart=$this->Session->read('storecart');
        // Delere Shipping-Handlings if user made changes in the Cart
        $cart['shippingTotal']=0;
        $cart['handlingTotal']=0;
        $cart['salesTax']=0;
        $this->Session->write('storecart', $cart);

         $orderAmount=$this->StoreOrder->cartAmount();
         $orderTotal=$this->StoreOrder->cartTotal();
         $shippTotal=$this->StoreOrder->cartHandlingShipping();
         $salesTax=$this->StoreOrder->cartTax();
         $discount=$this->StoreOrder->cartDiscount();

        $response['cart_items']=$cart['items'];
        $response['order_tax']=$salesTax;
        $response['cart_tcost']=sprintf("%01.2f", $orderTotal);
        $response['cart_tdiscount']=sprintf("%01.2f", $discount);
        $response['order_amount']=sprintf("%01.2f", $orderAmount);
        $response['order_total']=sprintf("%01.2f", $orderTotal);
        $response['weight_total']=sprintf("%01.2f", $cart['weight']);

        if(isset($shippTotal)&&$shippTotal>0) {
            $response['ship_total']=sprintf("%01.2f", $shippTotal);
        }else{
            $response['ship_total']='Cost Not Calculated';
        }



        return $response;
    }
    /**
* 
     * AJAX
     * @author Oleg D
     * reclculate cart after changes
     */
    function recalculateAjax() 
    {

            $this->layout=false;
            Configure::write('debug', 0);
            $cart=$this->Session->read('storecart');
        $stock='';
        if ($this->RequestHandler->isAjax()) {
            $products=$_REQUEST['products'];
            $response='';
            foreach($products as $product_id=>$quantity){

                $params=$this->__productParams($product_id);

                if($quantity>$cart['products'][$product_id]['quantity']) {
                    $iterations=$quantity-$cart['products'][$product_id]['quantity'];

                    for($i=0;$i<$iterations;$i++){
                        $this->__add($product_id, $params);
                    }
                    if($this->__inStock($product_id, $quantity)=='out') {
                        $stock.=$product_id.',';
                    }
                }elseif($quantity==$cart['products']['products'][$product_id]['quantity']) {
                    //Nothing
                }elseif($quantity<$cart['products'][$product_id]['quantity']) {
                    $iterations=$cart['products'][$product_id]['quantity']-$quantity;
                    for($i=0;$i<$iterations;$i++){
                        $this->__del($product_id, $params);
                    }
                    if($this->__inStock($product_id, $quantity)=='out') {
                        $stock.=$product_id.',';
                    }
                }

            }
            $promocodeError = $this->check_promocodes_conditions();

            $cart=$this->Session->read('storecart');
            $response=$this->__make_totals($response, $cart);
            $response['promocode_error']=$promocodeError;

            if($stock) {
                $stock=substr($stock, 0, -1);
                $pNames=$this->StoreProduct->find('all', array('conditions'=>array('StoreProduct.id IN('.$stock.')'),'fields'=>array('name')));
                $stock='';
                foreach($pNames as $pName){
                    $stock.='"'.$pName['StoreProduct']['name'].'", ';
                }
                $stock=substr($stock, 0, -2);
            }
            $response['stock_products']=$stock;
            $products=array();
            foreach($cart['products'] as $product){
                $products[$product['id']]=sprintf("%01.2f", $product['quantity']*$product['price']);
            }
            $discounts=array();
            foreach($cart['products'] as $product){
                if(isset($product['discounts'])&&!empty($product['discounts']) || isset($product['promocodes'])) {
                    $this_discount=0;
                    if(isset($product['discounts'])&&!empty($product['discounts'])) {
                        foreach($product['discounts'] as $discount){
                            $this_discount+=($discount['quantity']*$discount['amount']);
                        }
                    }
                    $this_discount=0;
                    if(isset($product['promocodes'])&&!empty($product['promocodes'])) {
                        foreach($product['promocodes'] as $promocode){
                            $this_discount+=($promocode['quantity']*$promocode['amount']);
                        }
                    }
                    $discounts[$product['id']]=sprintf("%01.2f", $this_discount);
                }
            }

            $response['products']=$products;
            $response['discounts']=$discounts;
            exit($this->Json->encode($response));
        }

        exit;
    }
    /*
    * Get product Params
    * @author Oleg D
    */
    function __productParams($product_id)
    {

        $this->StoreProduct->id=$product_id;
        $params['price']=$this->StoreProduct->field('price');
        $params['weight']=$this->StoreProduct->field('weight');

        return $params;
    }

    /*
    * Add promocode action
    * @author Oleg D
    */
    function addPromocode()
    {
        $cart = $this->Session->read('storecart');
        if (isset($this->request->data['Promocode']['value'])) {
            $coupon = $this->request->data['Promocode']['value'];
            // Checking Promocode
            $error = '';
            $errors[1] = "Such coupon does not exist.";
            $errors[2] = "This coupon has already been used.";
            $errors[3] = "This coupon has expired.";
            $errors[5] = "This coupon can not be used for this action.";
            $promocodeUse = 0;

            $conditions = array('code'=>$coupon,'Promocode.is_deleted'=>0);
            $this->recursive = -1;
            $couponInfo = $this->Promocode->find('first', array('conditions'=>$conditions));
            if (empty($couponInfo)) {
                $error = $errors[1];
            }

            if(isset($couponInfo['Promocode']['id'])) {
                $promocodeID = $couponInfo['Promocode']['id'];
            } else {
                $promocodeID = 0;
            }
            if (!empty($couponInfo['Promocode']['threshold']) && $couponInfo['Promocode']['threshold'] > 0) {
                $amount = $this->StoreOrder->cartAmount();
                if ($couponInfo['Promocode']['threshold'] > $amount) {
                    $error = 'If subtotal of order is > $' . sprintf("%01.2f", ($couponInfo['Promocode']['threshold'])) . ', coupon is valid.  Otherwise, coupon is not good.';
                }
            }

            if (!$error && intval($couponInfo['Promocode']['uses_count']) >= intval($couponInfo['Promocode']['number_of_uses'])) {
                $error = $errors[2];
            }
            if (!$error && !empty($couponInfo['Promocode']['expiration_date'])) {
                $conditions['expiration_date >'] = date('Y-m-d H:i:s');
                $this->Promocode->recursive = -1;
                $couponInfo = $this->Promocode->find('first', array('conditions'=>$conditions));
                if (empty($couponInfo)) {
                    $error = $errors[3];
                }
            }

            if(!$error && $this->Cart->promocodeUsed($promocodeID, $cart['products'])) {
                $error = $errors[2];
            }

            if (!$error) {
                $canUses = intval($couponInfo['Promocode']['number_of_uses']) - intval($couponInfo['Promocode']['uses_count']);
                $this->Promocode->PromocodesAssigment->recursive = -1;
                $findAssigments = $this->Promocode->PromocodesAssigment->find('all', array('conditions'=>array('model' => array('StoreCategory', 'StoreSlot', 'StoreProduct', 'All'), 'promocode_id' => $couponInfo['Promocode']['id'])));

                $assigments = array();
                $allProducts = 0;
                foreach ($findAssigments as $assign) {
                    $assign = $assign['PromocodesAssigment'];
                    $assigments[$assign['id']]['slots'] = array();
                    $assigments[$assign['id']]['products'] = array();
                    $model_id = $assign['model_id'];
                    if ($assign['model'] == 'StoreSlot') {
                        $assigments[$assign['id']]['slots'][$model_id] = $model_id;
                        if($model_id == -1) {
                            $allProducts = 1;
                        }
                    } elseif ($assign['model'] == 'StoreProduct') {
                        $assigments[$assign['id']]['products'][$model_id] = $model_id;
                        if($model_id == -1) {
                            $allProducts = 1;
                        }

                    } elseif ($assign['model'] == 'StoreCategory') {
                        $this->StoreProduct->StoreSlot->StoreCategory->recursive = -1;
                        $slots = $this->StoreProduct->StoreSlot->StoreSlotsCategory->find('list', array('fields' => array('slot_id', 'slot_id'), 'conditions' => array('category_id' => $model_id )));
                        $assigments[$assign['id']]['slots'] = $slots;
                        if($model_id == -1) {
                            $allProducts = 1;
                        }
                    }
                    if($allProducts) {
                        $slots = $this->StoreProduct->StoreSlot->StoreSlotsCategory->find('list', array('fields' => array('slot_id', 'slot_id')));
                        $assigments[$assign['id']]['slots'] = $slots;
                    }
                }
                foreach ($assigments as $assigmentID => $assigment) {
                    if ($canUses > 0) {
                        foreach ($cart['products'] as $product){
                            $productID = $product['id'];
                            $slotID = $product['slot_id'];

                            if(isset($assigment['slots'][$slotID]) || isset($assigment['products'][$productID])) {
                                $freePromoQuantity = $this->Cart->freePromoQuantity($product);

                                $promoQuantity = 0;
                                if ($canUses >= $freePromoQuantity) {
                                    $promoQuantity = $freePromoQuantity;
                                } else {
                                    $promoQuantity = $canUses;
                                }
                                //echo $assigmentID . '-' . $canUses . ' :: '. $productID . '-' . $freePromoQuantity . ' | ' . $promoQuantity;
                                $canUses -= $promoQuantity;
                                $productPromo = array();
                                $productPromo['id'] = $promocodeID;
                                //pr($couponInfo['Promocode']);
                                $productPromo['amount'] = $this->Promocode->calculateDiscountAmount($couponInfo['Promocode'], $product['price']);
                                $productPromo['quantity'] = $promoQuantity;
                                $productPromo['threshold'] = $couponInfo['Promocode']['threshold'];
                                $cart['products'][$productID]['promocodes'][$promocodeID] = $productPromo;

                            }
                        }
                    }
                }

                $this->Session->write('storecart', $cart);
            } else {
                $this->Session->setFlash(__($error), 'flash_error');
            }
        }
        return $this->redirect('/shopping_cart');

    }
    /*
    * Add promocode action
    * @author Oleg D
    */
    function deletePromocode($promocodeID)
    {
        $cart = $this->Session->read('storecart');
        foreach ($cart['products'] as $productID => $product) {
            if (isset($product['promocodes'][$promocodeID])) {
                unset($cart['products'][$productID]['promocodes'][$promocodeID]);
            }

        }

        $this->Session->write('storecart', $cart);

        $this->Session->setFlash('Promocode has been deleted from the Cart', 'flash_success');
        return $this->redirect('/shopping_cart');

    }
}
?>