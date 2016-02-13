<?php
App::import('Sanitize');
class TournamentFeed extends AppModel
{

    var $name = 'TournamentFeed';
    var $useTable  = false;

    function getTournamentStatus($event_id)
    {

        $event_id = Sanitize::escape($event_id);

        $query = "SELECT numrounds," .
        "currentround," .
        "starttimeofnextround," .
        "gamescreated," . 
        "iscompleted," .
        "finalscreated " .
        "FROM events " .
        "WHERE id = '${event_id}'";

        $tourn_info_raw = $this->query($query);
        $tourn_info = $tourn_info_raw[0]['events'];

        return array(
        'number_rounds' => $tourn_info['numrounds'],
        'current_round' => $tourn_info['currentround'],
        'start_time_next_round' => '?????'
        );

    }

    function getTournamentTeams($event_id)
    {

        $event_id = Sanitize::escape($event_id);
    
        $query = "SELECT * " .
        "FROM teams_objects " .
        "WHERE model = 'Event' AND " .
        "model_id = '${event_id}' AND " .
        "status = 'Created'";
    
        $teams_info_raw = $this->query($query);

        //Parse and reformat
        $teams_info = array();
        foreach($teams_info_raw as $team_info){
            $team_info = $team_info['teams_objects'];
            $teams_info[] = array(
            'id' => $team_info['team_id'],
            'name' => $team_info['name'],
            'seed' => $team_info['seed'],
            'rank' => $team_info['rank'],
            'wins' => $team_info['wins'],
            'losses' => $team_info['losses'],
            'cup_diff' => $team_info['cupdif'],
            'in_finals' => $team_info['infinals'],
            'finals_seed' => $team_info['finalsseed']
            );
        }

        return $teams_info;
    }

    function getTournamentGames($event_id)
    {

        $event_id = Sanitize::escape($event_id);

        $query = "SELECT * " .
        "FROM games " .
        "WHERE event_id = '$event_id'";

        $games_info_raw = $this->query($query);

        //Parse and reformat
        $games_info = array();
        foreach($games_info_raw as $game_info){
            $game_info = $game_info['games'];

            $losing_team_id = ($game_info['team1_id'] == $game_info['winningteam_id'] ? $game_info['team2_id'] : $game_info['team1_id']);

            $games_info[] = array(
            'id' => $game_info['id'],
            'game_type' => $game_info['bracketname'],
            'game_number' => $game_info['gamenumber'],
            'round_number' => $game_info['round'],
            'table_number' => $game_info['table'],
            'team1_id' => $game_info['team1_id'],
            'team2_id' => $game_info['team2_id'],
            'picks_side_team_id' => $game_info['team2_id'],
            'shoots_first_team_id' => $game_info['team1_id'],
            'is_forfeit' => $game_info['isforfeit'],
            'winning_team_id' => $game_info['winningteam_id'],
            'losing_team_id' => $losing_team_id,
            'cup_diff' => $game_info['cupdif']
            );
        }

        return $games_info;    

    }

}
