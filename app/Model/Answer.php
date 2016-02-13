<?php
class Answer extends AppModel
{
    var $name = 'Answer';

    function getAnswers($model = null, $model_id = null, $user_id = null ) 
    {
        $criteria = array();
        if(!empty($model)) {
            $criteria[] = "Answer.model = '$model'";
        }
        if(!empty($model_id)) {
            $criteria[] = "Answer.model_id = $model_id";
        }
        if(!empty($user_id)) {
            $criteria[] = "Answer.user_id = $user_id";
        }
        $criteria = implode(" AND ", $criteria);
        
        $sql = "SELECT * 
				FROM answers as Answer
				LEFT JOIN options AS Options ON (Answer.option_id = Options.id)
				WHERE $criteria";
        
        return $this->query($sql);            
    }
    
}
?>