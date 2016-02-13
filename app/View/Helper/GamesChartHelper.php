<?php
class GamesChartHelper extends AppHelper {

	function usersShow($chartInfo) {
		if (!empty($chartInfo['teams']) && !empty($chartInfo['games'])) {
			foreach ($chartInfo['games'] as $game) {
				$cupdif = $game['games']['cupdif'];
				if (isset($chartInfo['teams'][$game['games']['winningteam_id']])) {
					$winner = 1;	
				} else {
					$winner = 0;
					$cupdif = $cupdif*-1;
				}
				echo $cupdif;
				
			}	
		} else {

		}
		//pr($chartInfo);
	}

}

?>