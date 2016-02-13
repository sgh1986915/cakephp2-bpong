<?php
class VotesController extends AppController
{

    var $name = 'Votes';

    /**
 * Vote
 * @author vovich
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @param unknown_type $point
 * @return JSON
 */    
    function voting($model,$modelId, $delta) 
    {
        Configure::write('debug', 0);
         $this->layout   = false;
         $result = array("error"=>"", "sum" => 0, "votes_plus" => 0,"votes_minus"=>0);
         $userId = $this->Access->getLoggedUserID(); 
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }     
        if ($userId == VISITOR_USER || !$userId) {
            $result['error'] = "Access error, please login.";        
        } elseif (!$this->Access->getAccess('Vote_' . $model, 'c')) {
            $result['error'] = "You can not vote for this ".$model."<BR> please logg in ";
        } else {
             $result['error'] = $this->Vote->canVote($model, $modelId, $userId);
        }           
         
        $data['model']     =  Sanitize::paranoid($model);
        $data['model_id'] =  Sanitize::paranoid($modelId); 
        $data['user_id']    =  $userId;
        $data['delta']     =  $delta; 
        if (Sanitize::paranoid($model) == 'Image') {
            Cache::delete('last_images');    
        } elseif (Sanitize::paranoid($model) == 'Video') {
            Cache::delete('last_images');                
        }
        if (empty($result['error'])) {
            $points =  $this->Vote->add($data);
            $result['votes_plus'] = $points['votes_plus'];
            $result['votes_minus'] = $points['votes_minus'];
            $result['sum'] = $points['votes_plus'] - $points['votes_minus'];          
        }  
       
        exit($this->Json->encode($result)); 
    }

}
?>