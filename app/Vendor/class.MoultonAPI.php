<?php
/**
  *  Moulton class API
  *  @author Oleg D.
*/
class MoultonAPI{
	
	var $testing = 0;	var $shippingAddress = array();
	var $products = array();
	var $attributes = array();
	
	var $send_order_url = 'https://www.moultonordervision.com/Ws/ORDAPI.asmx';	var $send_order_url_test = 'http://qcmoultonordervision.com/Ws/ORDAPI.asmx';
  
  /**
   *  Block Send XML order to Moulton
   *  @author Oleg D.
   */
  function send_order() {
	  $result = array();
	  $xml_order = $this->build_order_xml();
	  $xml_order = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2", $xml_order);
	  $xml_order = str_replace('&', '&amp;', $xml_order);
	  echo $xml_order;	
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_order);
	  if($this->testing){
	  	curl_setopt($ch, CURLOPT_URL, $this->send_order_url_test);
	  }else{	  	curl_setopt($ch, CURLOPT_URL, $this->send_order_url);	  }
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

	  $moulton_answer = curl_exec($ch);
	  curl_close($ch);
	  
	  include 'xmlparser.class.php';
	  $Parser = new XmlParser();
	  $result = $Parser->xml2array($moulton_answer);


	  return $result;
  }


  	/**
   	*  build XML order to Moulton
  	*  @author Oleg D.
   	*/
  	function build_order_xml() {
		$date = date('Y-m-d');
		$time = date('H:i:s');

		$xml_order = 
			'<?xml version="1.0" encoding="iso-8859-1" ?> 
			 <Order>
			  <OrderHeader>
				 <GROUP_CODE>POST</GROUP_CODE> 
				 <CASH_BAT>8696</CASH_BAT> 
				 <SEQUENCE_NUMBER>4444</SEQUENCE_NUMBER> 
				 <USER>POST</USER> 
				 <DATE_ORD>02/16/2011</DATE_ORD> 
				 <CL_NO>GT</CL_NO> 
				 <CSOURCE>EDREAMZ</CSOURCE> 
				 <CMEDIA>INTERNET</CMEDIA> 
				 <CREDCD>4H00036454534774</CREDCD> 
				 <EXPDT>0412</EXPDT> 
				 <PROJECT>proj1</PROJECT> 
				 <CARRIER /> 
				 <SVC_TYPE /> 
				 <AMTPAY /> 
				 <CHECK_NO /> 
				 <BANK-NO /> 
				 <MICRCODE /> 
				 <PAY_TYPE>C</PAY_TYPE> 
				 <NUM_PYMNTS>2</NUM_PYMNTS> 
				 <EMAIL>BROWNC13@ATT.NET</EMAIL> 
				 <COMPANY /> 
				 <F_NAME>GARY</F_NAME> 
				 <L_NAME>BROWN</L_NAME> 
				 <ADDR_1>10961 ELIOTTI ST</ADDR_1> 
				 <ADDR_2 /> 
				 <CITY>ORLANDO</CITY> 
				 <ST>FL</ST> 
				 <ZIP>32832</ZIP> 
				 <PHONE>1234567890</PHONE> 
				 <PHONE_EXT>1111</PHONE_EXT> 
				 <COUNTRY_CODE>US</COUNTRY_CODE> 
				 <OPT_IN /> 
				 <OPT_OUT /> 
				 <ADJ_CODE /> 
				 <AMT_DISC /> 
				 <MISC /> 
				 <SALE_AMOUNT /> 
				 <SHIPPING_HANDLING_AMOUNT /> 
				 <TAX_AMOUNT /> 
				 <TOTAL_CHARGE_AMOUNT /> 
				 <CREDIT_CARD_DISCOUNT_AMOUNT /> 
				 <ITEM_DISCOUNT_PERCENT /> 
				 <CREDIT_CARD_DISCOUNT_PERCENT /> 
				 <PURCHASE_ORDER /> 
				 <SERIES_INTERVAL>0</SERIES_INTERVAL> 
				 <SRC_CD>BRPOST</SRC_CD> 
				 <CVV2 /> 
				 <BILL_TO_COMPANY /> 
				 <BILL_TO_L_NAME>SETZER</BILL_TO_L_NAME> 
				 <BILL_TO_F_NAME>GARY</BILL_TO_F_NAME> 
				 <BILL_TO_ADDR_1>124 W WALNUT AVE</BILL_TO_ADDR_1> 
				 <BILL_TO_ADDR_2 /> 
				 <BILL_TO_CITY>MOUNT HOLLY</BILL_TO_CITY> 
				 <BILL_TO_ST>NC</BILL_TO_ST> 
				 <BILL_TO_ZIP>28120</BILL_TO_ZIP> 
				 <BILL_TO_COUNTRY_CODE>US</BILL_TO_COUNTRY_CODE> 
				 <UNIQUE-ID /> 
			  </OrderHeader>
			  <OrderDetail>
				  <LineItem>
					<QUANTITY_ORDERED>2</QUANTITY_ORDERED> 
					 <OFFER_CODE>RUSHSH</OFFER_CODE> 
					 <OFFER_DESCRIPTION>PRIORITY PROCESSING</OFFER_DESCRIPTION> 
					 <SELL_SIGN /> 
					 <UNIT_PRICE /> 
					 <HANDL_SIGN /> 
					 <UNIT_SHIPPING_HANDLING_AMOUNT /> 
					 <CLIENT_SKU /> 
					 <TAXABLE_FLAG /> 
					 <CONTINUITY_FLAG>N</CONTINUITY_FLAG> 
					 <UNIQUE_ID /> 
				 </LineItem>
			 </OrderDetail>
			</Order>';
		
		return $xml_order;
	}
}
?>