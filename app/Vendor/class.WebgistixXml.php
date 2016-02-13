<?php
/**
	FCI tracking for Bpong
	author Oleg D.
**/
class WebgistixXml{

  var $customerID ='3';
  var $password ='Webgistix';
  var $username ='Webgistix';
  /// For Send Order
  var $order_url='http://www.webgistix.com/XML/API.asp';
  var $order_test_url='http://www.webgistix.com/XML/shippingTest.asp';
  var $testing=1;
  var $ship = array();
  var $products = array();
  var $attributes= array();
  /// For Track Order
  var $track_url='http://www.webgistix.com/XML/TrackingSvc.asp';
  var $WebgistixOrder;

  /**
   *  Block Send XML order to Webgistix
   *  Author Oleg D.
   */
  function send_order() {
	  $result = array();
	  $xml_order = $this->build_order_xml();
	  $xml_order = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2", $xml_order);
	  $xml_order = str_replace('&', '&amp;', $xml_order);

	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_order);
	  if($this->testing==1){
	  	curl_setopt($ch, CURLOPT_URL, $this->order_test_url);
	  }else{	  	curl_setopt($ch, CURLOPT_URL, $this->order_url);	  }
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

	  $fci_answer = curl_exec($ch);
	  curl_close($ch);

	  $result_array = $this->_get_xml_array($fci_answer);
	  $result_array['xml_text']=$xml_order;
	  return $result_array;
  }
  /**
   *  Block Track XML order to Webgistix
   *  Author Oleg D.
   */
  function track_order() {
	  $result = array();
	  $xml_order = $this->build_track_xml();
	  $xml_order = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2", $xml_order);
	  $xml_order = str_replace('&', '&amp;', $xml_order);

	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_order);
	  curl_setopt($ch, CURLOPT_URL, $this->track_url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
	  $web_answer = curl_exec($ch);
	  curl_close($ch);

	  $result_array = $this->_get_xml_array($web_answer);
	  return $result_array;
  }
  /**
   *  update INVENTORY for Webgistix
   *  Author Oleg D.
   */
  function update_inventory($items) {
	  $result = array();
	  $xml_order = $this->build_inventory_xml($items);
	  $xml_order = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2", $xml_order);
	  $xml_order = str_replace('&', '&amp;', $xml_order);

	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_order);
	  //curl_setopt($ch, CURLOPT_URL, 'http://localhost/curl/index.php');
	  curl_setopt($ch, CURLOPT_URL, 'http://www.webgistix.com/XML/GetInventoryDetails.asp');
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
	  $web_answer = curl_exec($ch);
	  curl_close($ch);

	  $result_array = $this->_get_xml_array($web_answer);
	  return $result_array;
  }
    function _get_xml_array($data){

      $values = array();
      $index = array();
      $array = array();
      $parser = xml_parser_create();
      xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
      xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
      xml_parse_into_struct($parser, $data, $values, $index);
      xml_parser_free($parser);

      $i = 0;
      $name='';
	  if(isset($values[$i]['tag'])){
      	$name = $values[$i]['tag'];
	  }

      $array[$name] = @$values[$i]['attributes'];
      $array[$name] = $this->__get_xml_array($values, $i);

      return $array;
    }
    function __get_xml_array($values, &$i)
    {
      $child = array();
      if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']);

      while (++$i < count($values))
      {
        switch ($values[$i]['type'])
        {
          case 'cdata':
            array_push($child, $values[$i]['value']);
          break;

          case 'complete':
            $name = $values[$i]['tag'];
            if(isset($values[$i]['value']))
                $child[$name]= $values[$i]['value'];

            if(isset($values[$i]['attributes']))
                if($values[$i]['attributes']){
                  $child[$name] = $values[$i]['attributes'];
                }
          break;

          case 'open':
            $name = $values[$i]['tag'];
            $size = @sizeof($child[$name]);
            if(isset($values[$i]['attributes'])){
                if($values[$i]['attributes']){
                     $child[$name][$size] = $values[$i]['attributes'];
                      $child[$name][$size] = $this->__get_xml_array($values, $i);
                 }
             }
            else
            {
                  $child[$name][$size] = $this->__get_xml_array($values, $i);
            }
          break;

          case 'close':
            return $child;
          break;
        }
      }
      return $child;
    }

  	/**
   	*  Block Send XML order to Webgistix
  	*  Author Oleg D.
   	*/
  	function build_order_xml($offer=null, $order=null) {
		$date = date('Y-m-d');
		$time = date('H:i:s');

		$attributes=$this->attributes;
		$ship=$this->ship;
		$products=$this->products;

		// TESTING VALUES:
		/*
		$attributes['email']='dikusaro@list.ru';
		$attributes['shipping_method']='FedEx Ground';
		$attributes['PurchaseOrderNumber']=uniqid(); // orderID-packageID

		$ship['name']='Oleg Dikusar';
		$ship['address1']='2200 Ampere Drice';
		$ship['address2']='';
		$ship['city']='Louisville';
		$ship['state_code']='KY';
		$ship['zip']='40299';
		$ship['phone']='502 214 4337';
		$ship['country_name']='United States';


		$products[1]['sku']='gfdg';
		$products[1]['quantity']='2';
		*/
		$xml_order=
		'<?xml version="1.0"?>'.
		'<OrderXML>'.
		'<Password>'.$this->password.'</Password>'.
		'<CustomerID>'.$this->customerID.'</CustomerID>'.
		'<Order>'.
		'<ReferenceNumber>'.$attributes['PurchaseOrderNumber'].'</ReferenceNumber>'.
		'<Company></Company>'.
		'<Name>'.$ship['name'].'</Name>'.
		'<Address1>'.$ship['address1'].'</Address1>'.
		'<Address2>'.$ship['address2'].'</Address2>'.
		'<Address3>'.$ship['address3'].'</Address3>'.
		'<City>'.$ship['city'].'</City>'.
		'<State>'.$ship['state_code'].'</State>'.
		'<ZipCode>'.$ship['zip'].'</ZipCode>'.
		'<Country>'.$ship['country_name'].'</Country>'.
		'<Email>'.$attributes['email'].'</Email>'.
		'<Phone>'.$ship['phone'].'</Phone>'.
		'<ShippingInstructions>'.$attributes['shipping_method'].'</ShippingInstructions>'.
		'<OrderComments>www.Bpong.com Order</OrderComments>'.
		'<Approve>1</Approve>';
		foreach($products as $product){
			$product_fci_sku=$product['sku'];
			$product_quantity=$product['quantity'];
			$xml_order .=
				'<Item>'.
					'<ItemID>'.$product_fci_sku.'</ItemID>'.
					'<ItemQty>' . $product_quantity . '</ItemQty>'.
				'</Item>';
		 };
		    // echo !!!
		 $xml_order.='</Order></OrderXML>';
		 return $xml_order;
	}
  /**
   *  Block Track XML order to Webgistix
   *  Author Oleg D.
   */
  function build_track_xml() {
  	$numbers=$this->WebgistixOrder;
	$xml_order='<?xml version="1.0"?>'.
	'<TrackingXML>'.
		'<Username>'.$this->username.'</Username>'.
		'<Password>'.$this->password.'</Password>'.
		'<Customer>'.$this->customerID.'</Customer>';
		foreach($numbers as $key=>$number)
		$xml_order.='<Tracking>'.
		'<Order>'.$number.'</Order>'.
		'</Tracking>';

	$xml_order.='</TrackingXML>';
	return $xml_order;
  }
  /**
   *  Inventory XML
   *  Author Oleg D.
   */
  function build_inventory_xml($items) {
	$xml_text='<?xml version="1.0"?>'.
	'<InventoryXML>'.
		'<Password>'.$this->password.'</Password>'.
		'<CustomerID>'.$this->customerID.'</CustomerID>';
		foreach($items as $itemID=>$prodWarID){
		$xml_text.='<Item>'.
		'<ItemID>'.$itemID.'</ItemID>'.
		'</Item>';
		}

	$xml_text.='</InventoryXML>';

	return $xml_text;
  }
}
?>