<?php
/**
	FCI tracking for Bpong
	author Oleg D.
**/
class FciXml{
  var $api_url='';
  var $depth = array();
  var $items_count = -1;
  var $current_array=array();
  var $attributes = array();
  var $ship = array();
  var $fci = array();
  var $products = array();

  var $main_dir='';
  var $arhive_dir='Archives';
  var $ftp_timeout = 5;
  var $ftp_address='';
  var $ftp_login='';
  var $ftp_password='';
  var $remove_files=1;
  var $track_limit=0;

  /**
   *  Block Send XML order to FCI
   *  Author Oleg D.
   */
  function send_fci_order() {

  	$result = array();
  	$result['status'] = 1;
  	$xml_order = $this->build_fci_order_xml();
	$xml_order = preg_replace("/(\s+)?(\<.+\>)(\s+)?/", "$2", $xml_order);
	$xml_order = str_replace('&', '&amp;', $xml_order);
 	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, $this->api_url);
  	curl_setopt($ch, CURLOPT_TIMEOUT, 40);
   	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_order);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   	curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);

    $fci_answer = curl_exec($ch);

    if (!$fci_answer) {
    	$class_result['accepted']=0;
        $class_result['reject_reason']='FCI API Connection error';
    	return $class_result;
    }

  	$result_array = $this->_get_xml_array($fci_answer);
  	$fci_result['result_array'] = $result_array;
  	$fci_result['result_xml'] = $fci_answer;
	$fci_result['request_xml'] = $xml_order;
	if (isset($fci_result['connection_error'])) {
     		$accepted = 0;
     		$reject_reason = $fci_result['connection_error_message'];
	} elseif (!isset($fci_result['result_array']['PurchaseOrderAcknowledgment']['AcknowledgmentType'])) {
     		$accepted = 0;
     		$reject_reason = $fci_result['result_xml'];
	} elseif ($fci_result['result_array']['PurchaseOrderAcknowledgment']['AcknowledgmentType'] == 'IR') {
     		$accepted = 0;
     		$reject_reason = $fci_result['result_array']['PurchaseOrderAcknowledgment']['OrderRejectionMessage'];
    } else {
    		$accepted = 1;
    		$reject_reason = '';
    }
    $class_result['accepted']=$accepted;
    $class_result['xml_text']=$xml_order;
    $class_result['reject_reason']=$reject_reason;


  	return $class_result;
  }
  /**
   *  Block Send XML order to FCI
   *  Author Oleg D.
   */
	function startElement($parser, $name, $attrs) {

		$current_element = $name;
		switch ($name) {
			case 'PurchaseOrderAcknowledgment':
				$this->current_array = 'PurchaseOrderAcknowledgment';
				break;
			case 'ShipmentNotice':
				$this->current_array = 'ShipmentNotice';
				break;
			case 'ShipToAddress':
				$this->current_array = 'ShipToAddress';
				break;
			case 'ShipFromAddress':
				$this->current_array = 'ShipFromAddress';
				break;
			case 'PurchaseOrderInformation':
				$this->current_array = 'PurchaseOrderInformation';
				break;
			case 'AcknowledgmentItemDetail':
				$this->current_array = 'ItemInformation';
				$this->items_count++;
				break;

		}

	   $this->depth[$parser]++;
	}
  /**
   *  Block Send XML order to FCI
   *  Author Oleg D.
   */
	function endElement($parser, $name)
	{

	   $this->depth[$parser]--;
	}
  /**
   *  Block Send XML order to FCI
   *  Author Oleg D.
   */
	function characterData($parser, $data) {
		global $result_array, $current_array, $current_element, $items_count;

		if ($this->current_array != 'ItemInformation'){
		    $result_array[$this->current_array][$current_element] = $data;
		} else {
		    $result_array[$this->current_array][$items_count][$current_element] = $data;
		}
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
   *  Block Send XML order to FCI
   *  Author Oleg D.
   */
  function build_fci_order_xml($offer=null, $order=null) {
  	$date = date('Y-m-d');
  	$time = date('H:i:s');

    $attributes=$this->attributes;
    $ship=$this->ship;
    $bill=$this->bill;
    $fci=$this->fci;

	// TESTING VALUES:
	/*
	$attributes['email']='dikusaro@list.ru';
	$attributes['shipping_method']='FedEx Ground';
	$attributes['handling_method']='bb';
	$attributes['PurchaseOrderNumber']=uniqid(); // orderID-packageID

	$ship['name']='Oleg Dikusar';
	$ship['address1']='2200 Ampere Drice';
	$ship['address2']='';
	$ship['city']='Louisville';
	$ship['state_code']='Kentucky';
	$ship['zip']='40299';
	$ship['country3']='USA';
	$ship['phone']='502 214 4337';
	$ship['country_name']='USA';

	$bill['name']='Oleg Dikusar';
	$bill['address1']='2200 Ampere Drice';
	$bill['address2']='';
	$bill['city']='Louisville';
	$bill['state_code']='Kentucky';
	$bill['zip']='40299';
	$bill['country3']='USA';
	$bill['phone']='502 214 4337';
	$bill['country_name']='USA';

	$fci['address1']='2200 Ampere Drive';
	$fci['city'] ='Louisville';
	$fci['state'] ='Kentucky';
	$fci['zip'] ='40299';
	$fci['country3'] ='USA';
	$fci['country_name']='USA';
	*/

  	switch ($attributes['handling_method']) {
  		case 'bb': $handling_type = 'B-B'; break;
  		case 'bc': $handling_type = 'B-C'; break;
  		default: $handling_type = 'B-C'; break;
  	}

  	$xml_order = '<?xml version="1.0" encoding="ISO-8859-1" ?>
	<PurchaseOrder>
		<FromParty>BPONG1</FromParty>
		<VendorID>BPONG1</VendorID>
		<VendorCustID>BPONG1</VendorCustID>
		<CompanyID>BPONG1</CompanyID>
		<PurchaseOrderNumber>'.$attributes['PurchaseOrderNumber'].'</PurchaseOrderNumber>
		<CustomerPurchaseOrderNumber />
		<PurchaseOrderDate>'. $date .'</PurchaseOrderDate>
		<PurchaseOrderTime>'. $time .'</PurchaseOrderTime>
		<ExpediteNote1></ExpediteNote1>
		<CostCenterID>'. $handling_type .'</CostCenterID>
		<SpecHand>'. $attributes['shipping_method'] .'</SpecHand>
		<ShippingInstructions1></ShippingInstructions1>
		<ShipToAddress>
			<AddressID />
			<Name></Name>
			<Attention>' . $ship['name'] . '</Attention>
			<Line1>' . $ship['address1'] . '</Line1>
			<Line2>' . trim($ship['address2'] . ' ' .$ship['address3']) . '</Line2>
			<City>' . $ship['city'] . '</City>
			<StateProvinceCode>' . $ship['state_code'] . '</StateProvinceCode>
			<PostalCode>' . $ship['zip'] . '</PostalCode>
			<CountryCode>' . $ship['country3'] . '</CountryCode>
			<Phone>' . $ship['phone'] . '</Phone>
			<CountryName>' . $ship['country_name'] . '</CountryName>
			<Email>' . $attributes['email'] . '</Email>
		</ShipToAddress>
		<BillToAddress>
			<AddressID />
			<Name></Name>
			<Attention>' . $bill['name'] . '</Attention>
			<Line1>' . $bill['address1'] . '</Line1>
			<Line2>' . trim($bill['address2'] . ' ' .$bill['address3']) . '</Line2>
			<City>' . $bill['city'] . '</City>
			<StateProvinceCode>' . $bill['state_code'] . '</StateProvinceCode>
			<PostalCode>' . $bill['zip'] . '</PostalCode>
			<CountryCode>' . $bill['country3'] . '</CountryCode>
			<Phone>' . $bill['phone'] . '</Phone>
			<CountryName>' . $bill['country_name'] . '</CountryName>
			<Email>' . $attributes['email'] . '</Email>
		</BillToAddress>
		<OrdByAddress>
			<Name></Name>
			<Attention>' . $bill['name'] . '</Attention>
			<Line1>' . $bill['address1'] . '</Line1>
			<City>' . $bill['city'] . '</City>
			<StateProvinceCode>' . $bill['state_code'] . '</StateProvinceCode>
			<PostalCode>' . $bill['zip'] . '</PostalCode>
			<CountryCode>' . $bill['country3'] . '</CountryCode>
			<Phone>' . $bill['phone'] . '</Phone>
			<CountryName>' . $bill['country_name'] . '</CountryName>
			<Email>' . $attributes['email'] . '</Email>
		</OrdByAddress>';
		$line_number=1;
		$products=$this->products;
		foreach($products as $product){
			$product_fci_sku=$product['sku'];
			$product_quantity=$product['quantity'];
			$product_price=$product['price'];
	  		$xml_order .= '
	  		<PurchaseOrderItemDetail>
				<LineNumber>' . $line_number . '</LineNumber>
				<VendorProductID>' . $product_fci_sku . '</VendorProductID>
				<OrderQuantity>' . $product_quantity . '</OrderQuantity>
				<SellPrice>' . $product_price . '</SellPrice>
			</PurchaseOrderItemDetail>';
		$line_number++;
	  	};

		$xml_order .= '
		</PurchaseOrder>';
		return $xml_order;
  }
  	/**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function ItemizeDir($contents, $remote_system) {
		$files = array();
		$folders = array();
		$inum=0;
		foreach ($contents as $ikey=>$item) {
			if($this->track_limit==0||$inum<$this->track_limit){
				switch ($remote_system){
					case 'Windows_NT':
						if (ereg("([0-9]{2})-([0-9]{2})-([0-9]{2}) +([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)", $item, $regs)) {
							if(!isset($regs[9])){
								$regs[9]='';
							}
							if ($regs[9] == '.') continue;
							if ($regs[3]<70) { $regs[3]+=2000; } else { $regs[3]+=1900; } // 4digit year fix
							$type = ($regs[7]=="<DIR>");
							$tmp_array['type'] = $type;
							$tmp_array['size'] = $regs[7];
							$tmp_array['month'] = $regs[1];
							$tmp_array['day'] = $regs[2];
							$tmp_array['year'] = $regs[3];
							$tmp_array['name'] = $regs[8];
							switch ($type) {
								case 0: $files[] = $tmp_array; break;
								case 1: $folders[] = $tmp_array; break;
							}
						}
						break;

					case 'UNIX':
					default:
						if (ereg("([-dl][rwxstST-]+).* ([0-9]*) ([a-zA-Z0-9]+).* ([a-zA-Z0-9]+).* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9])[ ]+(([0-9]{2}:[0-9]{2})|[0-9]{4}) (.+)", $item, $regs)) {
							if(!isset($regs[9])){
								$regs[9]='';
							}
							if ($regs[9] == '.') continue;
							$type = (int) strpos("-dl", $regs[1]{0});
							$tmp_array['type'] = $type;
							$tmp_array['rights'] = $regs[1];
							$tmp_array['user'] = $regs[3];
							$tmp_array['group'] = $regs[4];
							$tmp_array['size'] = $regs[5];
							$date = strtotime($regs[6]);
							$tmp_array['day'] = date("d", $date);
							$tmp_array['month'] = date("m", $date);
							if (strpos($regs[7], ':') || (strlen($regs[7]) == 5)) $tmp_array['year'] = $this->GetCurYear();
							else $tmp_array['year'] = $regs[7];
							$tmp_array['name'] = $regs[9];
							switch ($type) {
								case 0: $files[] = $tmp_array; break;
								case 1: $folders[] = $tmp_array; break;
							}
						}
						break;
				}
			}
			$inum++;
			unset($contents[$ikey]);
		}
		return $files;
	}
	 /**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function GetCurYear () {
		$now = getdate();
		return $now['year'];
	}
	 /**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function DeleteFile($conn_id, $fname) {
		if (!@ftp_delete($conn_id, $fname)) return false; else return true;
	}
	 /**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function ArchiveFile($conn_id, $fname, $new_filename) {
		if (@ftp_rename($conn_id, $fname, $new_filename)){
			@ftp_delete($conn_id, $fname) ;
			return true;
		}else{
			@ftp_delete($conn_id, $fname) ;
			return false;
		}
	}
	 /**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function GetFileContent($conn_id, $fname, $delim){
		ob_start();
		$fp = fopen('php://output', 'w');
		@ftp_fget($conn_id, $fp, $fname, FTP_BINARY);
		fclose($fp);
		$content = ob_get_clean();
		return $content;
	}
	 /**
  	 *  Block Track FCI
  	 *  Author Oleg D.
  	 */
	function track_fci($limit=50){
		$shipment_information=array();
		$conn_id = ftp_connect($this->ftp_address, 21, $this->ftp_timeout) or die("Couldn't connect to $this->ftp_address");
		$login_result = ftp_login($conn_id, $this->ftp_login, $this->ftp_password) or die("Couldn't log in to $this->ftp_address");
		$pwd = ftp_pwd($conn_id);
		$remote_system = ftp_systype($conn_id);
		switch ($remote_system){
			case 'Windows_NT':
				$delim = "\\";
				$pwd = str_replace('/', '\\', $pwd);
				break;

			case 'UNIX':
			default:
				$delim = "/";
				break;
		}
		ftp_pasv($conn_id, true);
		$ROOT_DIR = $pwd;

		if ($this->main_dir) {
			$cur_dir=$this->main_dir;
		} else {
			$cur_dir = $pwd;
		}

		switch ($remote_system){
			case 'Windows_NT':
				$cur_dir = str_replace('/', '\\', $cur_dir);
				$cur_dir = str_replace('\\\\', '\\', $cur_dir);
				break;
			case 'UNIX':
			default:
				$cur_dir = str_replace('\\', '/', $cur_dir);
				$cur_dir = str_replace('//', '/', $cur_dir);
				break;
		}

		if (substr($cur_dir, -2) == '..') {
			$cur_dir = substr_replace($cur_dir, '', -3);
			$cur_dir = substr($cur_dir, 0, strrpos($cur_dir, $delim));
			if ($cur_dir == '') $cur_dir = $ROOT_DIR;
		};
		$items = ftp_rawlist($conn_id, "-a ".$cur_dir);
		//echo "<pre>";
		//print_r($items);
		//exit;
		$files=$this->ItemizeDir($items, $remote_system);

		$processed_files = array();
		$shipment_information = array();
	    $f=1;
	    $iterations=0;
	    if(!empty($files)){
			foreach ($files as $file){
				if($iterations==$limit){
					break;
				}
				$iterations++;

				$file_name = $file['name'];
				$processed_files[$file_name]['success'] = 1;

				//ob_flush();
				$fci_shipping_report = $this->GetFileContent($conn_id, $cur_dir.'/'.$file_name, $delim);
				$result_answer = $this->_get_xml_array($fci_shipping_report);
				if ($result_answer === false) {
		 		    $processed_files[$file_name]['success'] = 0;
					continue;

				}else{
			        $result_array=array();
			        $result_array['ShipToAddress']=$result_answer['ShipmentNotice']['ShipToAddress'][0];
					unset($result_answer['ShipmentNotice']['ShipToAddress']);

					$result_array['ShipFromAddress']=$result_answer['ShipmentNotice']['ShipFromAddress'][0];
					unset($result_answer['ShipmentNotice']['ShipFromAddress']);

					$result_array['PurchaseOrderInformation']['PurchaseOrderNumber']=$result_answer['ShipmentNotice']['PurchaseOrderInformation'][0]['PurchaseOrderNumber'];
			        unset($result_answer['ShipmentNotice']['PurchaseOrderInformation'][0]['PurchaseOrderNumber']);

					$result_array['ItemInformation']=$result_answer['ShipmentNotice']['PurchaseOrderInformation'][0]['ItemInformation'];
			        unset($result_answer['ShipmentNotice']['PurchaseOrderInformation'][0]['ItemInformation']);
			        unset($result_answer['ShipmentNotice']['PurchaseOrderInformation']);
					$result_array['ShipmentNotice']=$result_answer['ShipmentNotice'];
				}

				$ShipmentID = $result_array['ShipmentNotice']['ShipmentID'];
				$FreightCarrier = $result_array['ShipmentNotice']['FreightCarrier'];
				$FreightService = $result_array['ShipmentNotice']['FreightService'];
				$BillOfLadingNumber = $result_array['ShipmentNotice']['BillOfLadingNumber'];
				$FreightAmount = $result_array['ShipmentNotice']['FreightAmount'];
				$ShipDate = $result_array['ShipmentNotice']['ShipDate'];
				$ShipTime = $result_array['ShipmentNotice']['ShipTime'];
				$SCACCode = $result_array['ShipmentNotice']['SCACCode'];
				$fci_order_number = $result_array['PurchaseOrderInformation']['PurchaseOrderNumber'];
				$res = explode('-', $fci_order_number);
				$res = explode('KY',$res['1']);
				$package_id = $res[0];
				if(isset($result_array['ItemInformation'])&&!empty($result_array['ItemInformation'])){
					foreach ($result_array['ItemInformation'] as $order_information){

						$LineNumber = $order_information['LineNumber'];
						$VendorProductID = $order_information['VendorProductID'];
						$CustomerProductID = $order_information['CustomerProductID'];
						$QuantityShipped = $order_information['QuantityShipped'];
						$UnitOfMeasure = $order_information['UnitOfMeasure'];
						$LabelTrackingNumber = $order_information['LabelInformation'][0]['LabelTrackingNumber'];
						$processed_files[$file_name]['success'] = 1;
						$shipment_information[$f]['package_number']=$fci_order_number;
						$shipment_information[$f]['tracking_number']=$LabelTrackingNumber;
						$shipment_information[$f]['file_name']=$file_name;
						$shipment_information[$f]['package_id'] = $package_id;
						$shipment_information[$f]['ShipToAddress'] = $result_array['ShipToAddress'];
					}
				}
				$f++;
			}
	    }
	    if(isset($processed_files)){
			foreach ($processed_files as $file_name => $data){
				if ($data['success']){
					if($this->remove_files){
						$this->ArchiveFile($conn_id, $cur_dir.'/'.$file_name, $this->arhive_dir.'/'.$file_name);
					}
				}
			}
	    }
		return $shipment_information;
	}
	 /**
  	 *  Update Inventory For FCI.
  	 *  Author Oleg D.
  	 */
	function update_inventory(){
		$conn_id = ftp_connect($this->ftp_address, 21, $this->ftp_timeout) or die("Couldn't connect to $this->ftp_address");
		$login_result = ftp_login($conn_id, $this->ftp_login, $this->ftp_password) or die("Couldn't log in to $this->ftp_address");
		$pwd = ftp_pwd($conn_id);
		$remote_system = ftp_systype($conn_id);
		switch ($remote_system){
			case 'Windows_NT':
				$delim = "\\";
				$pwd = str_replace('/', '\\', $pwd);
				break;

			case 'UNIX':
			default:
				$delim = "/";
				break;
		}
		ftp_pasv($conn_id, true);
		$ROOT_DIR = $pwd;

		if ($this->main_dir) {
			$cur_dir=$this->main_dir;
		} else {
			$cur_dir = $pwd;
		}

		switch ($remote_system){
			case 'Windows_NT':
				$cur_dir = str_replace('/', '\\', $cur_dir);
				$cur_dir = str_replace('\\\\', '\\', $cur_dir);
				break;
			case 'UNIX':
			default:
				$cur_dir = str_replace('\\', '/', $cur_dir);
				$cur_dir = str_replace('//', '/', $cur_dir);
				break;
		}

		if (substr($cur_dir, -2) == '..') {
			$cur_dir = substr_replace($cur_dir, '', -3);
			$cur_dir = substr($cur_dir, 0, strrpos($cur_dir, $delim));
			if ($cur_dir == '') $cur_dir = $ROOT_DIR;
		};
		$items = ftp_rawlist($conn_id, $cur_dir);
		$files=$this->ItemizeDir($items, $remote_system);
		//echo "<pre>";
		//print_r($items);
		//print_r($files);
		//print_r($exit);
		include('class.CSVparser.php');

		$parsed_files=array();
		$item=1;
		if(!empty($files)){
			foreach($files as $file){
				$file_name = $file['name'];
				$processed_files[$file_name]['success'] = 1;
				$csv = new CSVparser(',', "\r\n", '"');
				$file_conent = $this->GetFileContent($conn_id, $cur_dir.'/'.$file_name, $delim);
				//cell separator, row separator, value enclosure
			    //parse the string content
			    $csv->setContent($file_conent);
			   	//returns an array with the CSV data
			   	$parsed_files[$item]=$csv->getArray();
				if($this->remove_files){
						$this->ArchiveFile($conn_id, $cur_dir.'/'.$file_name, $this->arhive_dir.'/'.$file_name);
				}
				$item++;
				unset($csv);
			}
		}else{
			$parsed_files=array();
		}
		return $parsed_files;

	}
}

?>