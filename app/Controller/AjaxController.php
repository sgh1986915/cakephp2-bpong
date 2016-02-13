<?php
class AjaxController extends AppController
{
    var $name = 'Ajax';
    var $layout = false;
    
    
    function videoplayer() 
    {
        Configure::write('debug', 0);

        $video_file = $this->request->params["url"]["video"];
        $video_width = $this->request->params["url"]["width"];
        $video_height = $this->request->params["url"]["height"];

        if (!$this->RequestHandler->isAjax() || empty($video_file)) {
            exit();
        }
        
        $this->set(compact("video_file", "video_width", "video_height")); 
    }
    
}
?>
