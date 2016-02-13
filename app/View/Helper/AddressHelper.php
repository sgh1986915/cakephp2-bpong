<?php 

App::import('Model', 'Address');

class AddressHelper extends AppHelper {
    
	/**
    * Get lat lon by address
    * @author Oleg D.
    */
	function getLatLon($address, $updateAddress = 0) {
		$addressString = $city = $state = $country = '';
		if (!empty($address['address'])) {
			$addressString = $address['address'] . ' ' . $address['address2'] . ' ' . $address['address3'];	
			if (!empty($address['city'])) {
				$city = $address['city'];	
			}		
			if (!empty($address['Country']['name'])) {
				$country = $address['Country']['name'];	
			} elseif (!empty($address['country_name'])) {
				$country = $address['country_name'];	
			}
					
			if (!empty($address['Provincestate']['name'])) {
				$state = $address['Provincestate']['name'];	
			} elseif (!empty($address['state_name'])) {
				$state = $address['state_name'];	
			} 	
			
			App::import('Vendor', 'GoogleMapAPI', array('file' => 'class.GoogleMapAPI.php'));
			$map = new GoogleMapAPI();
			$map->setAPIKey(GOOGLE_MAP_KEY);
			$latLon = $map->getCoordsByAddress(trim($addressString), $city, $state, $country);
			
			if ($updateAddress && !empty($address['id']) && !empty($latLon['lat']) && !empty($latLon['lon'])) {
				$Address = new Address();
				$Address->save(array('id' => $address['id'], 'latitude' => $latLon['lat'], 'longitude' => $latLon['lon']));					
			}
			
			return $latLon;
		} else {
			
			return false;
		}
	}
	
}
?>