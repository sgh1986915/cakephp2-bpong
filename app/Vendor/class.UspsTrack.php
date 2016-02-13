<?php
class UspsTrack
{
	var $usps_user_name;
	function TrackNumberUSPS($tracking_number){
		$usps_req='';
		$usps_req .= '<TrackID ID="'.$tracking_number.'"></TrackID>';
		$track_Ret = $this->SendUSPSRequest($usps_req);
	
	
		if ($track_Ret === false) {
			$result_array['request_result'] = 0;
		} else {
			$result_array = $this->ParseUSPSReply($track_Ret);
		}
		return $result_array;
	}
	
	function SendUSPSRequest($s){
	     $url = "http://production.shippingapis.com/shippingapi.dll";
	
	     $path ='API=TrackV2&XML=<TrackRequest USERID="'.$this->usps_user_name.'">'.$s.'</TrackRequest>';
	
	     $ch = curl_init();
	     curl_setopt($ch, CURLOPT_URL,$url);
	     curl_setopt($ch, CURLOPT_POSTFIELDS, $path);
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	     curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	     curl_setopt($ch, CURLOPT_POST, TRUE);
	
	     $result = curl_exec ($ch);
	     curl_close($ch);
	     return $result;
	}
	
	
	function ParseUSPSReply($track_Ret){
		$result_array = array();
		$result_array['packages'][1]['summary'] = '';
		$result_array['packages'][1]['status'] = '';
		if (preg_match_all('/<TrackSummary>(.*)<\/TrackSummary>(<TrackDetail>(.*)<\/TrackDetail>){0,}/', $track_Ret, $res, PREG_SET_ORDER)){
			$global_summary = '';
			$delivery_status = '';
			foreach ($res as $rec) {
				$summary = $rec[1];
				$details = $rec[3];
				if ($details) $global_summary .= '<b>'.$details.'.</b><br>';
	   			$global_summary .= '<i><font color=blue>'.$summary.'</font></i><br>';
				if (strpos($summary, "Your item was delivered") !== false){
	   				$delivery_status = 'delivered';
	   			} elseif (strpos($summary, 'no record of that mail item')  !== false) {
	   				$result_array['packages'][1]['status'] = 'undelivered';	
	   			} else {
	   				$result_array['packages'][1]['status'] = 'ontheway';	   				
	   			}
			}
			$result_array['packages'][1]['summary'] = $global_summary;
			$result_array['packages'][1]['status'] = $delivery_status;
			$result_array['request_result'] = 1;
		}
	
		return $result_array;
	}
}

?>