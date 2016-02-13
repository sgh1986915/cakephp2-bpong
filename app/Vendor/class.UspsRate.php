<?php
class UspsRate {

    var $server = "";
    var $user = "";
    var $pass = "";
    var $service = "";
    var $dest_zip;
    var $orig_zip;
    var $pounds;
    var $ounces;
    var $container = "None";
    var $size = "REGULAR";
    var $machinable='';
    var $country = "USA";
    var $international = 0;
    // Need only this types: - Type:MY code
	var $usps_types=array(
	'Priority Mail'=>'usps1',
	//,'Express Mail'=>'usps2'
	//'Global Express Guaranteed'=>'usps3',
	'Express Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International' => 'usps4',
	'Priority Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International' => 'usps5',
	'First-Class Mail&lt;sup&gt;&amp;reg;&lt;/sup&gt; International Package**' => 'usps7'	
	//'USPS GXG Envelopes'=>'usps6',		
	);

    function setServer($server) {
        $this->server = $server;
    }

    function setUserName($user) {
        $this->user = $user;
    }
    
    function country_list() {
      $list = array('AF' => 'Afghanistan',
                    'AL' => 'Albania',
                    'DZ' => 'Algeria',
                    'AD' => 'Andorra',
                    'AO' => 'Angola',
                    'AI' => 'Anguilla',
                    'AG' => 'Antigua and Barbuda',
                    'AR' => 'Argentina',
                    'AM' => 'Armenia',
                    'AW' => 'Aruba',
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'AZ' => 'Azerbaijan',
                    'BS' => 'Bahamas',
                    'BH' => 'Bahrain',
                    'BD' => 'Bangladesh',
                    'BB' => 'Barbados',
                    'BY' => 'Belarus',
                    'BE' => 'Belgium',
                    'BZ' => 'Belize',
                    'BJ' => 'Benin',
                    'BM' => 'Bermuda',
                    'BT' => 'Bhutan',
                    'BO' => 'Bolivia',
                    'BA' => 'Bosnia-Herzegovina',
                    'BW' => 'Botswana',
                    'BR' => 'Brazil',
                    'VG' => 'British Virgin Islands',
                    'BN' => 'Brunei Darussalam',
                    'BG' => 'Bulgaria',
                    'BF' => 'Burkina Faso',
                    'MM' => 'Burma',
                    'BI' => 'Burundi',
                    'KH' => 'Cambodia',
                    'CM' => 'Cameroon',
                    'CA' => 'Canada',
                    'CV' => 'Cape Verde',
                    'KY' => 'Cayman Islands',
                    'CF' => 'Central African Republic',
                    'TD' => 'Chad',
                    'CL' => 'Chile',
                    'CN' => 'China',
                    'CX' => 'Christmas Island (Australia)',
                    'CC' => 'Cocos Island (Australia)',
                    'CO' => 'Colombia',
                    'KM' => 'Comoros',
                    'CG' => 'Congo (Brazzaville),Republic of the',
                    'ZR' => 'Congo, Democratic Republic of the',
                    'CK' => 'Cook Islands (New Zealand)',
                    'CR' => 'Costa Rica',
                    'CI' => 'Cote d\'Ivoire (Ivory Coast)',
                    'HR' => 'Croatia',
                    'CU' => 'Cuba',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'DJ' => 'Djibouti',
                    'DM' => 'Dominica',
                    'DO' => 'Dominican Republic',
                    'TP' => 'East Timor (Indonesia)',
                    'EC' => 'Ecuador',
                    'EG' => 'Egypt',
                    'SV' => 'El Salvador',
                    'GQ' => 'Equatorial Guinea',
                    'ER' => 'Eritrea',
                    'EE' => 'Estonia',
                    'ET' => 'Ethiopia',
                    'FK' => 'Falkland Islands',
                    'FO' => 'Faroe Islands',
                    'FJ' => 'Fiji',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'GF' => 'French Guiana',
                    'PF' => 'French Polynesia',
                    'GA' => 'Gabon',
                    'GM' => 'Gambia',
                    'GE' => 'Georgia, Republic of',
                    'DE' => 'Germany',
                    'GH' => 'Ghana',
                    'GI' => 'Gibraltar',
                    'GB' => 'Great Britain and Northern Ireland',
                    'GR' => 'Greece',
                    'GL' => 'Greenland',
                    'GD' => 'Grenada',
                    'GP' => 'Guadeloupe',
                    'GT' => 'Guatemala',
                    'GN' => 'Guinea',
                    'GW' => 'Guinea-Bissau',
                    'GY' => 'Guyana',
                    'HT' => 'Haiti',
                    'HN' => 'Honduras',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IS' => 'Iceland',
                    'IN' => 'India',
                    'ID' => 'Indonesia',
                    'IR' => 'Iran',
                    'IQ' => 'Iraq',
                    'IE' => 'Ireland',
                    'IL' => 'Israel',
                    'IT' => 'Italy',
                    'JM' => 'Jamaica',
                    'JP' => 'Japan',
                    'JO' => 'Jordan',
                    'KZ' => 'Kazakhstan',
                    'KE' => 'Kenya',
                    'KI' => 'Kiribati',
                    'KW' => 'Kuwait',
                    'KG' => 'Kyrgyzstan',
                    'LA' => 'Laos',
                    'LV' => 'Latvia',
                    'LB' => 'Lebanon',
                    'LS' => 'Lesotho',
                    'LR' => 'Liberia',
                    'LY' => 'Libya',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MO' => 'Macao',
                    'MK' => 'Macedonia, Republic of',
                    'MG' => 'Madagascar',
                    'MW' => 'Malawi',
                    'MY' => 'Malaysia',
                    'MV' => 'Maldives',
                    'ML' => 'Mali',
                    'MT' => 'Malta',
                    'MQ' => 'Martinique',
                    'MR' => 'Mauritania',
                    'MU' => 'Mauritius',
                    'YT' => 'Mayotte (France)',
                    'MX' => 'Mexico',
                    'MD' => 'Moldova',
                    'MC' => 'Monaco (France)',
                    'MN' => 'Mongolia',
                    'MS' => 'Montserrat',
                    'MA' => 'Morocco',
                    'MZ' => 'Mozambique',
                    'NA' => 'Namibia',
                    'NR' => 'Nauru',
                    'NP' => 'Nepal',
                    'NL' => 'Netherlands',
                    'AN' => 'Netherlands Antilles',
                    'NC' => 'New Caledonia',
                    'NZ' => 'New Zealand',
                    'NI' => 'Nicaragua',
                    'NE' => 'Niger',
                    'NG' => 'Nigeria',
                    'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
                    'NO' => 'Norway',
                    'OM' => 'Oman',
                    'PK' => 'Pakistan',
                    'PA' => 'Panama',
                    'PG' => 'Papua New Guinea',
                    'PY' => 'Paraguay',
                    'PE' => 'Peru',
                    'PH' => 'Philippines',
                    'PN' => 'Pitcairn Island',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'QA' => 'Qatar',
                    'RE' => 'Reunion',
                    'RO' => 'Romania',
                    'RU' => 'Russia',
                    'RW' => 'Rwanda',
                    'SH' => 'Saint Helena',
                    'KN' => 'Saint Kitts (St. Christopher and Nevis)',
                    'LC' => 'Saint Lucia',
                    'PM' => 'Saint Pierre and Miquelon',
                    'VC' => 'Saint Vincent and the Grenadines',
                    'SM' => 'San Marino',
                    'ST' => 'Sao Tome and Principe',
                    'SA' => 'Saudi Arabia',
                    'SN' => 'Senegal',
                    'YU' => 'Serbia-Montenegro',
                    'SC' => 'Seychelles',
                    'SL' => 'Sierra Leone',
                    'SG' => 'Singapore',
                    'SK' => 'Slovak Republic',
                    'SI' => 'Slovenia',
                    'SB' => 'Solomon Islands',
                    'SO' => 'Somalia',
                    'ZA' => 'South Africa',
                    'GS' => 'South Georgia (Falkland Islands)',
                    'KR' => 'South Korea (Korea, Republic of)',
                    'ES' => 'Spain',
                    'LK' => 'Sri Lanka',
                    'SD' => 'Sudan',
                    'SR' => 'Suriname',
                    'SZ' => 'Swaziland',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'SY' => 'Syrian Arab Republic',
                    'TW' => 'Taiwan',
                    'TJ' => 'Tajikistan',
                    'TZ' => 'Tanzania',
                    'TH' => 'Thailand',
                    'TG' => 'Togo',
                    'TK' => 'Tokelau (Union) Group (Western Samoa)',
                    'TO' => 'Tonga',
                    'TT' => 'Trinidad and Tobago',
                    'TN' => 'Tunisia',
                    'TR' => 'Turkey',
                    'TM' => 'Turkmenistan',
                    'TC' => 'Turks and Caicos Islands',
                    'TV' => 'Tuvalu',
                    'UG' => 'Uganda',
                    'UA' => 'Ukraine',
                    'AE' => 'United Arab Emirates',
                    'UY' => 'Uruguay',
                    'UZ' => 'Uzbekistan',
                    'VU' => 'Vanuatu',
                    'VA' => 'Vatican City',
                    'VE' => 'Venezuela',
                    'VN' => 'Vietnam',
                    'WF' => 'Wallis and Futuna Islands',
                    'WS' => 'Western Samoa',
                    'YE' => 'Yemen',
                    'ZM' => 'Zambia',
                    'ZW' => 'Zimbabwe');

      return $list;
    }
    function setPass($pass) {
        $this->pass = $pass;
    }

    function setService($service) {
        /* Must be: Express, Priority, or Parcel */
        $this->service = $service;
    }

    function setDestZip($sending_zip) {
        /* Must be 5 digit zip (No extension) */
        if(strlen($sending_zip)>5){
        	$sending_zip=substr($sending_zip, 0, 5);
        }

        $this->dest_zip = $sending_zip;
    }

    function setOrigZip($orig_zip) {    	if(strlen($orig_zip)>5){
        	$orig_zip=substr($orig_zip, 0, 5);
        }
        $this->orig_zip = $orig_zip;
    }

    function setWeight($pounds, $ounces=0) {
        /* Must weight less than 70 lbs. */
        $this->pounds = $pounds;
        $this->ounces = $ounces;
    }

    function setContainer($cont) {
        $this->container = $cont;
    }

    function setSize($size) {
        $this->size = $size;
    }

    function setMachinable($mach) {
        /* Required for Parcel Post only, set to True or False */
        $this->machinable = $mach;
    }

    function setCountry($country) {
    	$usps_countries = $this->country_list();
        $this->country = $usps_countries[$country];
    }

    function getPrice() {

        if(!$this->international){
            // may need to urlencode xml portion
            $str = $this->server. "?API=RateV2&XML=<RateV2Request%20USERID=\"";
            $str .= $this->user . "\"%20PASSWORD=\"" . $this->pass . "\"><Package%20ID=\"0\"><Service>";
            $str .= $this->service . "</Service><ZipOrigination>" . $this->orig_zip . "</ZipOrigination>";
            $str .= "<ZipDestination>" . $this->dest_zip . "</ZipDestination>";
            $str .= "<Pounds>" . $this->pounds . "</Pounds><Ounces>" . $this->ounces . "</Ounces>";
            $str .= "<Container>" . urlencode($this->container) . "</Container><Size>" . $this->size . "</Size>";
            $str .= "<Machinable>" . $this->machinable . "</Machinable></Package></RateV2Request>";

        }
        else { 
            $str = $this->server. "?API=IntlRate&XML=<IntlRateRequest%20USERID=\"";
            $str .= $this->user . "\"%20PASSWORD=\"" . $this->pass . "\"><Package%20ID=\"0\">";
            $str .= "<Pounds>" . $this->pounds . "</Pounds><Ounces>" . $this->ounces . "</Ounces>";
            $str .= "<MailType>Package</MailType><Country>".urlencode($this->country)."</Country></Package></IntlRateRequest>";

        }

        $ch = curl_init();
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $str);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // grab URL and pass it to the browser
        $ats = curl_exec($ch);

        // close curl resource, and free up system resources
        curl_close($ch);
        $ERROR=0;
        $array = $this->GetXMLTree($ats);
        //$xmlParser->printa($array);
        if(isset($array['ERROR'])) { // If it is error
            $error = new error();
            $error->number = $array['ERROR'][0]['NUMBER'][0]['VALUE'];
            $error->source = $array['ERROR'][0]['SOURCE'][0]['VALUE'];
            $error->description = $array['ERROR'][0]['DESCRIPTION'][0]['VALUE'];
            $error->helpcontext = $array['ERROR'][0]['HELPCONTEXT'][0]['VALUE'];
            $error->helpfile = $array['ERROR'][0]['HELPFILE'][0]['VALUE'];
            $this->error = $error;
            $ERROR=1;
        } else if(isset($array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'])) {
            $error = new error();
            $error->number = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['NUMBER'][0]['VALUE'];
            $error->source = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['SOURCE'][0]['VALUE'];
            $error->description = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['DESCRIPTION'][0]['VALUE'];
            $error->helpcontext = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['HELPCONTEXT'][0]['VALUE'];
            $error->helpfile = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['HELPFILE'][0]['VALUE'];
            $this->error = $error;
            $ERROR=1;
        } else if(isset($array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'])){ //if it is international shipping error
            $error = new error($array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR']);
            $error->number = $array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['NUMBER'][0]['VALUE'];
            $error->source = $array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['SOURCE'][0]['VALUE'];
            $error->description = $array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['DESCRIPTION'][0]['VALUE'];
            $error->helpcontext = $array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['HELPCONTEXT'][0]['VALUE'];
            $error->helpfile = $array['INTLRATERESPONSE'][0]['PACKAGE'][0]['ERROR'][0]['HELPFILE'][0]['VALUE'];
            $this->error = $error;
            $ERROR=1;
        } else if(isset($array['RATEV2RESPONSE'])){ // if everything OK
            //print_r($array['RATEV2RESPONSE']);
            $this->zone = $array['RATEV2RESPONSE'][0]['PACKAGE'][0]['ZONE'][0]['VALUE'];
            foreach ($array['RATEV2RESPONSE'][0]['PACKAGE'][0]['POSTAGE'] as $value){
                $price = new price();
                $price->mailservice = $value['MAILSERVICE'][0]['VALUE'];
                $price->rate = $value['RATE'][0]['VALUE'];
                $this->list[] = $price;
            }
        } else if (isset($array['INTLRATERESPONSE'][0]['PACKAGE'][0]['SERVICE'])) { // if it is international shipping and it is OK
            foreach($array['INTLRATERESPONSE'][0]['PACKAGE'][0]['SERVICE'] as $value) {
                $price = new intPrice();
                $price->id = $value['ATTRIBUTES']['ID'];
                $price->pounds = $value['POUNDS'][0]['VALUE'];
                $price->ounces = $value['OUNCES'][0]['VALUE'];
                $price->mailtype = $value['MAILTYPE'][0]['VALUE'];
                $price->country = $value['COUNTRY'][0]['VALUE'];
                $price->rate = $value['POSTAGE'][0]['VALUE'];
                $price->svccommitments = $value['SVCCOMMITMENTS'][0]['VALUE'];
                $price->svcdescription = $value['SVCDESCRIPTION'][0]['VALUE'];
                $price->maxdimensions = $value['MAXDIMENSIONS'][0]['VALUE'];
                $price->maxweight = $value['MAXWEIGHT'][0]['VALUE'];
                $this->list[] = $price;
            }

        }
        if(!$ERROR&&isset($this->list)){
	        $prices=array();
	        foreach($this->list as $price){
	        	if($this->international){
	        		$service = $price->svcdescription;
	        	}else{
	        		$service = $price->mailservice;
	        	}
	        	//echo $service;echo "<br/>";
				if(isset($this->usps_types[$service])){
					  $prices[$this->usps_types[$service]]=$price->rate;
				}
			}
		}else{			$prices['error']='USPS Error!';		}
        return $prices;
    }
    function GetChildren($vals, &$i)
    {
        $children = array();


        if (isset($vals[$i]['value']))
            $children['VALUE'] = $vals[$i]['value'];


        while (++$i < count($vals))
        {
            switch ($vals[$i]['type'])
            {
                case 'cdata':
                    if (isset($children['VALUE']))
                        $children['VALUE'] .= $vals[$i]['value'];
                    else
                        $children['VALUE'] = $vals[$i]['value'];
                    break;

                case 'complete':
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']])-1;

                        if (isset($vals[$i]['value']))
                            $children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value'];
                        else
                            $children[$vals[$i]['tag']][$index]['VALUE'] = '';
                    } else {
                        if (isset($vals[$i]['value']))
                            $children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value'];
                        else
                            $children[$vals[$i]['tag']][]['VALUE'] = '';
    		}
                    break;

                case 'open':
                    if (isset($vals[$i]['attributes'])) {
                        $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
                        $index = count($children[$vals[$i]['tag']])-1;
                        $children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index],$this->GetChildren($vals, $i));
                    } else {
                        $children[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);
                    }
                    break;

                case 'close':
                    return $children;
            }
        }
    }

    function GetXMLTree($xml)
    {
        $data = $xml;

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $data, $vals, $index);
        xml_parser_free($parser);

        //print_r($index);

        $tree = array();
        $i = 0;

        if (isset($vals[$i]['attributes'])) {
    	$tree[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
    	$index = count($tree[$vals[$i]['tag']])-1;
    	$tree[$vals[$i]['tag']][$index] =    array_merge($tree[$vals[$i]['tag']][$index], $this->GetChildren($vals, $i));
        }
        else
            $tree[$vals[$i]['tag']][] = $this->GetChildren($vals, $i);

        return $tree;
    }

    function printa($obj) {
        global $__level_deep;
        if (!isset($__level_deep)) $__level_deep = array();

        if (is_object($obj))
            print '[obj]';
        elseif (is_array($obj)) {
            foreach(array_keys($obj) as $keys) {
                array_push($__level_deep, "[".$keys."]");
                $this->printa($obj[$keys]);
                array_pop($__level_deep);
            }
        }
        else print implode(" ",$__level_deep)." = $obj\n";
    }
}
class error
{
    var $number;
    var $source;
    var $description;
    var $helpcontext;
    var $helpfile;
}
class price
{
    var $mailservice;
    var $rate;
}
class intPrice
{
    var $id;
    var $rate;
}
?>