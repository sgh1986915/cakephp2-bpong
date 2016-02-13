<?php

/* USPS Class - a PHP class to interact with the U.S. Postal Service's Web Tools APIs and retrieve real-time shipping quotes.
This class can handle domestic and international shipping requests. It requires DOMXML and cURL to interface with the USPS and
move through the returned XML document. Feel free to use this class in your programs as you see fit, it is freeware. */

class USPS
{
	var $user_id;
	var $password;
	var $api;
	var $request_xml;
	var $package_index = 0;
	var $current_result = array();

	var $country_list = array(
	"United States",
	"Zimbabwe");

	var $submit_url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";

	function USPS($user_id, $password, $api = 'RateV2')
	{
		if(empty($user_id) || empty($password)) return false;
		else {
			$this->user_id = $user_id;
			$this->password = $password;
			$this->api = $api;
			$this->package_index = '1ST';
			$this->request_xml = "<$api" . "Request USERID='".$user_id."' PASSWORD='".$password."'>";
		}
	}

	function reset()
	{
		$this->api = '';
		$this->current_result = '';
		$this->request_xml = '';
		$this->package_index = 0;
	}

	function add_package($attribs = '')
	{
		if(!is_array($attribs)) return false;

		//Check to make sure array has required values for API
		if($this->api == 'RateV2') {
			if(!$attribs['service'] || !$attribs['zip_origin'] || !$attribs['zip_dest'] || !$attribs['pounds'] || !$attribs['ounces'] || !$attribs['size']) return false;

			//Check service type
			if(empty($attribs['service'])) return false;
			else {
				switch(strtolower($attribs['service']))
				{
					case 'express':
					case 'first class':
					case 'priority':
					case 'parcel':
					case 'bpm':
					case 'library':
					case 'media':
					case 'all':
						break;
					default:
						return false;
				}
			}

			//Check ZIP codes
			if(!isset($attribs['zip_origin'])) return false;
			if(!isset($attribs['zip_dest'])) return false;

			//Check weight
			if(!isset($attribs['pounds'])) return false;
			if(!isset($attribs['ounces'])) return false;

			//Check container for Express and Priority
			if(strtolower($attribs['service']) == 'express' || strtolower($attribs['service']) == 'priority')
			{
				if(!isset($attribs['container'])) return false;
				else {
					switch(strtolower($attribs['container']))
					{
						case 'flat rate envelope':
						case 'flat rate box':
							break;
						default:
							return false;
					}
				}
			}

			//Check size
			if(!$attribs['size']) return false;
			else {
				switch(strtolower($attribs['size']))
				{
					case 'regular':
					case 'large':
					case 'oversize':
						break;
					default:
						return false;
				}
			}

			//Check machinable for parcel post
			if(strtolower($attribs['service']) == 'parcel') {
				if(empty($attribs['machinable'])) return false;
			}

			//Add the package to the XML request
			$this->request_xml .= '<Package ID="' . $this->package_index . '">';
			$this->package_index++;
			$this->request_xml .= '<Service>' . strtoupper($attribs['service']) . '</Service>';
			$this->request_xml .= '<ZipOrigination>' . $attribs['zip_origin'] . '</ZipOrigination>';
			$this->request_xml .= '<ZipDestination>' . $attribs['zip_dest'] . '</ZipDestination>';
			$this->request_xml .= '<Pounds>' . $attribs['pounds'] . '</Pounds><Ounces>' . $attribs['ounces'] . '</Ounces>';
			if(strtolower($attribs['service']) == 'express' || strtolower($attribs['service']) == 'priority')
				$this->request_xml .= '<Container>' . $attribs['container'] . '</Container>';
			$this->request_xml .= '<Size>' . mb_convert_case($attribs['size'], MB_CASE_TITLE) . '</Size>';
			if(strtolower($attribs['service']) == 'parcel' || strtolower($attribs['service']) == 'all')
				$this->request_xml .= '<Machinable>' . $attribs['machinable'] . '</Machinable>';
			$this->request_xml .= '</Package>';
		}

		else if($this->api == 'IntlRate')
		{
			if(!$attribs['pounds']) return false;
			if(!$attribs['ounces']) return false;

			if(!$attribs['mail_type']) return false;
			else {
				switch(strtolower($attribs['mail_type']))
				{
					case 'package':
					case 'postcards or aerogrammes':
					case 'matter for the blind':
					case 'envelope':
						break;
					default:
						return false;
				}
			}

			if(!isset($attribs['country'])) return false;
			if(!in_array($attribs['country'], $this->country_list)) return false;

			//Add the package to the XML request
			$this->request_xml .= '<Package ID="' . $this->package_index . '">';
			$this->package_index++;
			$this->request_xml .= '<Pounds>' . $attribs['pounds'] . '</Pounds><Ounces>' . $attribs['ounces'] . '</Ounces>';
			$this->request_xml .= '<MailType>' . $attribs['mail_type'] . '</MailType><Country>' . $attribs['country'] . '</Country>';
			$this->request_xml .= '</Package>';
		}

		else if($this->api == 'Verify')
		{   if(isset($attribs['zip'])){
				$zip=$attribs['zip'];
		        if(strlen($zip)==9||strlen($zip)==10){
			    	 $attribs['zip5']=substr($zip, 0, 5);
			    	 $attribs['zip4']=substr($zip, -4);

			    }elseif(strlen($zip)==5){
			    	$attribs['zip5']=$zip;
			    }elseif(strlen($zip)==4){
			    	$attribs['zip4']=$zip;
			    }
		    }
			$this->request_xml="<AddressValidateRequest USERID='".$this->user_id."' PASSWORD='".$this->password."'>";
			$this->request_xml .= '<Address ID="' . $this->package_index . '">';
			$this->package_index++;
			$this->request_xml .= '<Address1>' . $attribs['address1'] . '</Address1><Address2>' . $attribs['address2'] . '</Address2>';
			$this->request_xml .= '<City>' . $attribs['city'] . '</City><State>' . $attribs['state'] . '</State>';
			$this->request_xml .= '<Zip5>' . $attribs['zip5'] . '</Zip5><Zip4>' . $attribs['zip4'] . '</Zip4>';
			$this->request_xml .= '</Address>';
			$this->request_xml .='</AddressValidateRequest>';
		}
		else if ($this->api == 'ZipCodeLookup')
		{
			//Add the package to the XML request
			$this->request_xml .= '<Address ID="' . $this->package_index . '">';
			$this->package_index++;
			$this->request_xml .= '<Address1>' . $attribs['address1'] . '</Address1><Address2>' . $attribs['address2'] . '</Address2>';
			$this->request_xml .= '<City>' . $attribs['city'] . '</City><State>' . $attribs['state'] . '</State>';
			$this->request_xml .= '</Address>';
		}
		else if ($this->api == 'CityStateLookup')
		{
			//Add the package to the XML request
			$this->request_xml .= '<ZipCode ID="' . $this->package_index . '">';
			$this->package_index++;
			$this->request_xml .= '<Zip5>' . $attribs['zip5'] . '</Zip5>';
			$this->request_xml .= '</ZipCode>';
		}

		return true;
	}

	function submit_request()
	{

		//Create a cURL instance and retrieve XML response
		//if(!is_callable("curl_exec")) die("USPS::submit_request: curl_exec is uncallable");
		$ch = curl_init($this->submit_url);
		$this->request_xml;
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "API=" . $this->api . "&XML=" . $this->request_xml);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$return_xml = curl_exec($ch);

		if($this->api == 'Verify'){			$this_answer=$this->_get_xml_array($return_xml);
			if(isset($this_answer['AddressValidateResponse']['Address']['0']['Error'])){
				$answer=$this_answer['AddressValidateResponse']['Address']['0']['Error']['0'];				$answer['Answer']='error';
			}else{				$answer=$this_answer['AddressValidateResponse']['Address']['0'];
				$answer['Answer']='address';
				if(isset($answer['Zip5'])&&isset($answer['Zip4'])&&$answer['Zip4']&&$answer['Zip5']){					$answer['Zip']=$answer['Zip5'].'-'.$answer['Zip4'];				}else{
					if(isset($answer['Zip5'])&&$answer['Zip5']){						$answer['Zip']=$answer['Zip5'];					}
					if(isset($answer['Zip4'])&&$answer['Zip4']){
						$answer['Zip']=$answer['Zip4'];
					}
				}
			}

		}


		return $answer;

	}


	####################################
	function get_rates($package_id = 0)
	{
		if($this->current_result[$package_id]['Error']) return $this->current_result[$package_id]['Error']['Description'];

		if($this->api == 'RateV2')
			return $this->current_result[$package_id]['Postage'];
		else if($this->api == 'IntlRate')
		{
			//SvcDescription and Postage
			$result = array();

			foreach($this->current_result[$package_id]['Service'] as $service)
			{
				$key = $service['SvcDescription'];
				$result[$key] = $service['Postage'];
			}

			return $result;
		}
		else return false;
	}

	function get_prohibitions($package_id)
	{
		if($this->api == 'IntlRate') return $this->current_result[$package_id]['Prohibitions'];
		else return false;
	}

	function get_restrictions($package_id)
	{
		if($this->api == 'IntlRate') return $this->current_result[$package_id]['Restrictions'];
		else return false;
	}

	function get_observations($package_id)
	{
		if($this->api == 'IntlRate') return $this->current_result[$package_id]['Observations'];
		else return false;
	}

	function get_areas_served($package_id)
	{
		if($this->api == 'IntlRate') return $this->current_result[$package_id]['AreasServed'];
		else return false;
	}
    /**
    * _get_xml_array($data)
    *
    * This is adds the contents of the return xml into the array for easier processing.
    *
    * @access    private
    * @param    string    $data this is the string of the xml data
    * @return    Array
    */
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

      $name = $values[$i]['tag'];
      $array[$name] = @$values[$i]['attributes'];
      $array[$name] = $this->__get_xml_array($values, $i);

      return $array;
    }
    /**
    * __get_xml_array($values, &$i)
    *
    * This is adds the contents of the return xml into the array for easier processing.
    *
    * @access    private
    * @param    array    $values this is the xml data in an array
    * @param    int    $i    this is the current location in the array
    * @return    Array
    */
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
}

?>