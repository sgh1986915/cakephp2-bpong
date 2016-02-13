<?php
/**
 * CakePHP UPS
 * For calculating shipping rates from UPS.
 *
 *  $rate = $this->Ups->getRate(array(
 *         'Weight' => '34'
 *  ));
 */
class UpsComponent extends Component
{
    var $accessKey    = 'EC759088968C2E98';
    var $userId       = 'BPONG00';
    var $password     = 'N>K3pmD3NR';
    var $upsUrl       = 'https://www.ups.com/ups.app/xml/Rate';
    var $handlingFee  = 0;
    var $response;

    var $defaults     = array(
        'ShipperZip'        => '94901',
        'ShipperCountry'    => 'US',
        'ShipFromZip'        => '94901',
        'ShipFromCountry'   => 'US',
        'ShipToZip'         => '94901',
        'ShipToCountry'     => 'US',

        'ShipperNumber'        => 'BPONG00',
    
        'PickupType'        => '01',
        'PackagingType'        => '02',

        'WeightUnit'        => 'LBS',
        'Weight'            => '1',
       
        'DimensionsUnit'    => 'IN',
        'DimensionsLength'    => '1',
        'DimensionsHeight'    => '1',
        'DimensionsWidth'    => '1',
    );
    /*
    PickupType values are:
    "01" – Daily Pickup
    "03" – Customer Counter
    "06" – One Time Pickup
    "07" – On Call Air
    "11" – Suggested Retail Rates
    "19" – Letter Center
    "20" – Air Service Center
		
		
    PackagingType values are:
    ‘01’ = UPS Letter,
    ‘02’ = Customer Supplied Package,
    ‘03’ = Tube,
    ‘04’ = PAK,
    ‘21’ = UPS Express Box,
    ‘24’ = UPS 25KG Box,
    ‘25’ = UPS 10KG Box
    ‘30’ = Pallet
    2a = Small Express Box
    2b = Medium Express Box
    2c = Large Express Box
    */
    /**
     * STARTUP
     *
     * @param $controller
     *
     * TODO: Allow all options to be set from controller,
     *  so user doesn't have to modify component.
     */
    function startup(&$controller, $options=array()) 
    {
        $this->defaults = array_merge((array)$this->defaults, (array)$options);
    } // startup

    /**
     * GET RATE
     * @param $data
     * @return int | false
     */
    function getRate($data=null) 
    {
        // MUST BE ABOVE 1LB
        if ($data['Weight'] < .1) { $data['Weight'] = .1; 
        }
             
        $response = $this->request($data);
        
        // Print Real Result
        //echo "<pre/>";
        //print_r($response);
        
        $upsRates = array();
        if (!empty($response['RatingServiceSelectionResponse']['RatedShipment'])) {
            
            // One rate result
            if (!isset($response['RatingServiceSelectionResponse']['RatedShipment']['0']) && $response['RatingServiceSelectionResponse']['RatedShipment']['TotalCharges']) {            
                $results[0] = $response['RatingServiceSelectionResponse']['RatedShipment'];
            }else {
                $results = $response['RatingServiceSelectionResponse']['RatedShipment'];
            }
    
            foreach ($results as $rate) {
                if (!empty($rate['TotalCharges']['MonetaryValue']) && !empty($rate['Service']['Code'])) {
                    $upsRates[$rate['Service']['Code']] = $rate['TotalCharges']['MonetaryValue'];
                }        
            }        
        }
        return $upsRates;
    } // calculate

    /**
     * REQUEST
     *
     * @param  $data
     * @return array
     *
     * TODO: Use Cake HttpSocket
     */
    function request($data=null) 
    {
        App::import('Core', 'Xml');
        $xml = $this->buildRequest($data);
        $ch = curl_init($this->upsUrl);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $res = curl_exec($ch);
        $res = strstr($res, '<?'); // REMOVES HEADERS
        $xml = new Xml($res);
        $this->response = $xml->toArray();
        return $this->response;
    } // request

    /**
     * BUILD REQUEST
     *
     * @param  $data
     * @return str
     *
     * TODO: Error check data
     */
    function buildRequest($data=array()) 
    {
        $this->defaults = array_merge((array)$this->defaults, (array)$data);

        return "<?xml version=\"1.0\"?>
		<AccessRequest xml:lang=\"en-US\">
		    <AccessLicenseNumber>$this->accessKey</AccessLicenseNumber>
		    <UserId>$this->userId</UserId>
		    <Password>$this->password</Password>
		</AccessRequest>
		<?xml version=\"1.0\"?>
		<RatingServiceSelectionRequest xml:lang=\"en-US\">
		    <Request>
			<TransactionReference>
			    <CustomerContext>Bare Bones Rate Request</CustomerContext>
			    <XpciVersion>1.0001</XpciVersion>
			</TransactionReference>
			<RequestAction>Rate</RequestAction>
			<RequestOption>shop</RequestOption>" . /// NOTE !!!!!!!!! For all available services use "shop" for some - "Rate"
        "</Request>
		<PickupType>
		    <Code>".$this->defaults['PickupType']."</Code>
		</PickupType>
		<Shipment>
		    <Shipper>
			<Address>
			    <PostalCode>".$this->defaults['ShipperZip']."</PostalCode>
			    <CountryCode>".$this->defaults['ShipperCountry']."</CountryCode>
			</Address>
		    <ShipperNumber>".$this->defaults['ShipperNumber']."</ShipperNumber>
		    </Shipper>
		    <ShipTo>
			<Address>
			    <PostalCode>".$this->defaults['ShipToZip']."</PostalCode>
			    <CountryCode>".$this->defaults['ShipToCountry']."</CountryCode>
			<ResidentialAddressIndicator/>
			</Address>
		    </ShipTo>
		    <ShipFrom>
			<Address>
			    <PostalCode>".$this->defaults['ShipFromZip']."</PostalCode>
			    <CountryCode>".$this->defaults['ShipFromCountry']."</CountryCode>
			</Address>
		    </ShipFrom>
		    <Package>
			<PackagingType>
			    <Code>".$this->defaults['PackagingType']."</Code>
			</PackagingType>" . 
        /*<Dimensions>
        <UnitOfMeasurement>
        <Code>".$this->defaults['DimensionsUnit']."</Code>
        </UnitOfMeasurement>
        <Length>".$this->defaults['DimensionsLength']."</Length>
        <Width>".$this->defaults['DimensionsWidth']."</Width>
        <Height>".$this->defaults['DimensionsHeight']."</Height>
        </Dimensions>*/
        "<PackageWeight>
			    <UnitOfMeasurement>
				<Code>".$this->defaults['WeightUnit']."</Code>
			    </UnitOfMeasurement>
			    <Weight>".number_format($this->defaults['Weight'], 2, '.', '')."</Weight>
			</PackageWeight>
		    </Package>
		</Shipment>
		</RatingServiceSelectionRequest>";
    } // buildRequest
    
    /**
     * UPS tracking 
     * @author Oleg D.
     */
    function upsTrack($trackingNumber) 
    {  
        $data ="<?xml version=\"1.0\"?>  
	            <AccessRequest xml:lang='en-US'>  
	                    <AccessLicenseNumber>$this->accessKey</AccessLicenseNumber>  
	                    <UserId>$this->userId</UserId>  
	                    <Password>$this->password</Password>  
	            </AccessRequest>  
	            <?xml version=\"1.0\"?>  
	            <TrackRequest>  
	                    <Request>  
	                            <TransactionReference>  
	                                    <CustomerContext>  
	                                            <InternalKey>blah</InternalKey>  
	                                    </CustomerContext>  
	                                    <XpciVersion>1.0</XpciVersion>  
	                            </TransactionReference>  
	                            <RequestAction>Track</RequestAction>  
	                    </Request>  
	            <TrackingNumber>$trackingNumber</TrackingNumber>  
	            </TrackRequest>";  
        $ch = curl_init("https://www.ups.com/ups.app/xml/Track");  
        curl_setopt($ch, CURLOPT_HEADER, 1);  
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
        $result=curl_exec($ch);  
        // echo '<!-- '. $result. ' -->';  
        $data = strstr($result, '<?');  
        $xml_parser = xml_parser_create();  
        xml_parse_into_struct($xml_parser, $data, $vals, $index);  
        xml_parser_free($xml_parser);  
        $params = array();  
        $level = array();  
        foreach ($vals as $xml_elem) {  
            if ($xml_elem['type'] == 'open') {  
                if (array_key_exists('attributes', $xml_elem)) {  
                    list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);  
                } else {  
                      $level[$xml_elem['level']] = $xml_elem['tag'];  
                }  
            }  
            if ($xml_elem['type'] == 'complete') {  
                $start_level = 1;  
                $php_stmt = '$params';  
                while($start_level < $xml_elem['level']) {  
                    $php_stmt .= '[$level['.$start_level.']]';  
                    $start_level++;  
                }  
                $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';  
                eval($php_stmt);  
            }  
        }  
        curl_close($ch);  
        return $params;  
    }   
} // Ups
