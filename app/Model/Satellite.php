<?php
class Satellite extends AppModel
{
    var $name = 'Satellite';
    var $useTable = false;



    /**
    * Gets satellites tied with current tournament
    * @author abel
    * @param int                    $tID
    * @param varchar                $show
    * @param varchar                $tID
    * @param $isSatel - is satellite
    */
    function getSatellites($tID = null, $show = null, $state = null, $isSatel = 1)
    {
        if ($show == 'showactive') { $dating = ' AND end_date > now() '; 
        } else { $dating = ''; 
        }

        $location = '';

        if ($state) {
            $location = " AND provincestates.shortname = '".addslashes($state)."'";
        }

        return $this->query(
            "
			SELECT addresses.city,provincestates.shortname,events.name,events.slug,events.start_date,venues.name,venues.slug,venues.web_address
				FROM events
				INNER JOIN events_tournaments ON events_tournaments.event_id = events.id
				INNER JOIN venues ON venues.id = events.venue_id
				INNER JOIN addresses ON addresses.model_id = venues.id
				INNER JOIN provincestates ON provincestates.id = addresses.provincestate_id
				WHERE events_tournaments.tournament_id = $tID AND events_tournaments.is_satellite = $isSatel AND addresses.model = 'Venue'
				AND events.is_deleted = 0
				$dating $location
				ORDER BY events.start_date,provincestates.shortname,addresses.city,events.slug
		"
        );
    }


    /**
    * Gets states with satellites
    * @author abel
    * @param int     $tID
    * @param varchar $show
    * @param varchar $tID
    * @param int     $isSatel
    */
    function getStates($tID = null, $state = null,$isSatel = 1)
    {
        $locout = '';

        if ($state) {
            $locout = " AND provincestates.shortname <> '".addslashes($state)."'";
        }

        return $this->query(
            "
			SELECT DISTINCT provincestates.shortname
				FROM events
				INNER JOIN events_tournaments ON events_tournaments.event_id = events.id
				INNER JOIN venues ON venues.id = events.venue_id
				INNER JOIN addresses ON addresses.model_id = venues.id
				INNER JOIN provincestates ON provincestates.id = addresses.provincestate_id
				WHERE events_tournaments.tournament_id = $tID AND events_tournaments.is_satellite = $isSatel AND addresses.model = 'Venue'
				AND events.is_deleted = 0 AND (start_date >=NOW() OR end_date>=NOW())
				$locout
				ORDER BY provincestates.shortname
		"
        );
    }
}
?>
