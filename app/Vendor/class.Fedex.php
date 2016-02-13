<?php
/*
  Remake of the osCommerce, Open Source E-Commerce Solutions
  By Oleg D.
*/


  class Fedex{
    var $enabled, $meter;
    var $cart_total, $cart_total_per_one, $cart_weight, $cart_weight_per_one, $cart_shipping_num_boxes, $cart_qty;
	var $testing,$total_weight;
	var $weight_units='LBS';
	var $sender_state,$sender_zip,$sender_country;
	var $dest_state,$dest_zip,$dest_country,$dest_company,$international;

// class constructor
    function Fedex() {

      $this->testing = 1;
      $this->meter = '';
      $this->account = '';

      // SENDER ADDRESS - TEST
	  //$this->sender_state='KY';
	  //$this->sender_zip='40299';
      //$this->sender_country='US';
      // DESTINATION ADDRESS - TEST
      //$this->dest_state='MD';
      //$this->dest_zip='20770-1441';
      //$this->dest_country='US';
      //$this->dest_company='';

      $this->total_weight=1;
      $this->pack_nums=1;      
      $this->pack_weight=1;  
      
      //////////////////////
      $this->enabled = true;
      $this->international=0;


	  //end

// You can comment out any methods you do not wish to quote by placing a // at the beginning of that line
// If you comment out 92 in either domestic or international, be
// sure and remove the trailing comma on the last non-commented line

      $this->domestic_types = array(
             '01' => 'Priority (by 10:30AM, later for rural)',
             '03' => '2 Day Air',
             '05' => 'Standard Overnight (by 3PM, later for rural)',
             '06' => 'First Overnight',
             '20' => 'Express Saver (3 Day)',
             '90' => 'Home Delivery',
             '92' => 'Ground Service'
             );

      $this->international_types = array(
             //'01' => 'International Priority',
             //'03' => 'International Economy',
             //'06' => 'International First',
             //'90' => 'Home Delivery',
             '92' => 'International Ground Service'
             );

    }

// class methods
    function quote($method = '') {
      global $shipping_weight, $shipping_num_boxes, $cart, $offer_order;

      if (defined("SHIPPING_ORIGIN_COUNTRY")) {
        $countries_array = tep_get_countries(SHIPPING_ORIGIN_COUNTRY, true);

        $this->country = $countries_array['countries_iso_code_2'];

      }
      /*
      $SHIPPING_BOX_WEIGHT=1;
      $SHIPPING_BOX_PADDING=0;
      $this->cart_weight_per_one = $this->total_weight;
      if ($SHIPPING_BOX_WEIGHT >= $this->cart_weight_per_one*$SHIPPING_BOX_PADDING/100) {
      	$this->cart_weight_per_one = $this->cart_weight_per_one+$SHIPPING_BOX_WEIGHT;
      }
      else {
      	$this->cart_weight_per_one = $this->cart_weight_per_one + ($this->cart_weight_per_one*$SHIPPING_BOX_PADDING/100);
      }

	  $this->_setPackageType('01');
      //$this->_setPackageType('01');

      if ($this->packageType == '01' && $this->cart_weight_per_one < 1) {
        $this->_setWeight(1);
      } else {
      */
      $this->_setPackageType('01');
      $weight = $this->total_weight + ($this->pack_nums *  $this->pack_weight);
      // ONLY 150 LBS IN PACKAGE !!!!!!!!!!!!
      if ($weight > 150) {
          $addition_packs_num = ceil($weight/'150') - 1;   
          $addition_packs_num = 2;
          $this->pack_nums = $this->pack_nums + $addition_packs_num;         
          $weight = $weight + ($addition_packs_num *  $this->pack_weight);
      }
      //echo $weight; echo "<br/>";
      $this->_setWeight($weight);

      $fedexQuote = $this->_getQuote();

      return $fedexQuote;
    }

    function _setService($service) {
      $this->service = $service;
    }

    function _setWeight($pounds) {
      if ($this->weight_units == 'OZ') {
      	$pounds = $pounds / 16; // oz -> lbs
      }
      if ($this->weight_units == 'KGS') {
      	$pound = $pont / 35.2739619; //oz -> kg
      }
      $this->pounds = sprintf("%01.1f", $pounds);
    }

    function _setPackageType($type) {
      $this->packageType = $type;
    }

    function _setInsuranceValue($order_amount) {
        $this->insurance = sprintf("%01.2f",$order_amount);
        //$this->insurance=0;
    }

    function _AccessFedex($data) {

      if (!$this->testing) {
        $this->server = 'gateway.fedex.com/GatewayDC';
      } else {
        $this->server = 'gatewaybeta.fedex.com/GatewayDC';
      }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, 'https://' . $this->server);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Referer: ",
                                                   "Host: " . $this->server,
                                                   "Accept: image/gif,image/jpeg,image/pjpeg,text/plain,text/html,*/*",
                                                   "Pragma:",
                                                   "Content-Type:image/gif"));
        //echo $data;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $reply = curl_exec($ch);
        curl_close ($ch);

        return $reply;
    }

    function _ParseFedex($data) {
      $current = 0;
      $length = strlen($data);
      $resultArray = array();
      while ($current < $length) {
        $endpos = strpos($data, ',', $current);
        if ($endpos === FALSE) { break; }
        $index = substr($data, $current, $endpos - $current);
        $current = $endpos + 2;
        $endpos = strpos($data, '"', $current);
        $resultArray[$index] = substr($data, $current, $endpos - $current);
        $current = $endpos + 1;
      }
    return $resultArray;
    }

    function _getQuote() {
      global $offer_order, $customer_id, $sendto;
     
      $data = '0,"25"'; // TransactionCode
      $data .= '10,"' . $this->account . '"'; // Sender fedex account number
      $data .= '498,"' . $this->meter . '"'; // Meter number
      $data .= '8,"' . $this->sender_state . '"';
      $orig_zip = str_replace(array(' ', '-'), '', $this->sender_zip);
      $data .= '9,"' . $orig_zip . '"'; // Origin postal code
      $data .= '117,"' . $this->sender_country . '"'; // Origin country
      $dest_zip = str_replace(array(' ', '-'), '', $this->dest_zip);
      $data .= '17,"' . $dest_zip . '"'; // Recipient zip code
      if ($this->dest_country == "US" || $this->dest_country == "CA" || $this->dest_country == "PR") {
        $state = $this->dest_state; // Recipient state
        if ($state == "QC") $state = "PQ";
        $data .= '16,"' . $state . '"'; // Recipient state
      }
      $data .= '50,"' . $this->dest_country . '"'; // Recipient country
      $data .= '75,"' . $this->weight_units . '"'; // Weight units

      if ($this->weight_units == "KGS") {
        $data .= '1116,"C"'; // Dimension units
      } else {
        $data .= '1116,"I"'; // Dimension units
      }
      //
      if ($this->pack_nums > 1) {
        $data .= '116,"' . $this->pack_nums . '"';          
      }

      //echo "<br/>";     echo "!". $this->pounds . "!";echo   $this->pack_nums;echo "<br/>";

      $data .= '1401,"' . $this->pounds . '"'; // Total weight
      $data .= '1529,"1"'; // Quote discounted rates
      /*
      if ($this->insurance > 0) {
        $data .= '1415,"' . $this->insurance . '"'; // Insurance value
        $data .= '68,"USD"'; // Insurance value currency
      }
      */

      if ($this->dest_company == '') {
        $data .= '440,"Y"'; // Residential address
      }else {
        $data .= '440,"N"'; // Business address, use if adding a residential surcharge
      }
      $data .= '1273,"' . $this->packageType . '"'; // Package type
      $data .= '1333,"1"'; // Drop of drop off or pickup
      //echo $this->packageType.'-';
      $data .= '99,""'; // End of record
      $fedexData = $this->_AccessFedex($data);
      if (strlen($fedexData) == 0) {
        $this->error_message = 'No data returned from Fedex, perhaps the Fedex site is down';
        return array('error' => $this->error_message);
      }
      $fedexData = $this->_ParseFedex($fedexData);
      $i = 1;
      if ($this->sender_country == $this->dest_country) {
        $this->international = 0;
      } else {
        $this->international= 1;
      }
      $rates = NULL;
      while (isset($fedexData['1274-' . $i])) {
      	//$this->international = 0;  /// Try to fix problem with International;
        if ($this->international) {
        	/*	
          if (isset($this->international_types[$fedexData['1274-' . $i]])) {
          	  echo "<pre>";
          	  print_r($fedexData);
          	  exit;	
              if (isset($fedexData['3058-' . $i])) {
                $rates[$fedexData['1274-' . $i] . $fedexData['3058-' . $i]] = $fedexData['1528-' . $i];
              } else {
                $rates[$fedexData['1274-' . $i]] = $fedexData['1528-' . $i];
              }
          }
			*/
          if (isset($this->international_types[$fedexData['1274-' . $i]])) {
              if (isset($fedexData['3058-' . $i])) {
                $rates[$fedexData['1274-' . $i] . $fedexData['3058-' . $i]] = $fedexData['1419-' . $i];
              } else {
                $rates[$fedexData['1274-' . $i]] = $fedexData['1419-' . $i];
              }
          }
        } else {
          if (isset($this->domestic_types[$fedexData['1274-' . $i]])) {
              if (isset($fedexData['3058-' . $i])) {
                $rates[$fedexData['1274-' . $i] . $fedexData['3058-' . $i]] = $fedexData['1419-' . $i];
              } else {
                $rates[$fedexData['1274-' . $i]] = $fedexData['1419-' . $i];
              }
          }
        }
        $i++;
      }
      if(isset($rates['902'])){      	$rates['90']=$rates['902'];
      	unset($rates['902']);      }
      if(isset($rates['922'])){
      	$rates['92']=$rates['922'];
      	unset($rates['922']);
      }

      if(!empty($rates)){
	  	asort($rates);
	  }

      return ((sizeof($rates) > 0) ? $rates : false);
    }

  }
?>