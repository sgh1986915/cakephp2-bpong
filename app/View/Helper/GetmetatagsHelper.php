<?php


App::import('Model', 'Metatag');

class GetmetatagsHelper extends Helper {

    var $helpers = array('Session');

    var $AutoAdd = 1; //if 1 then auto store new row to the DB

    var $IgnoreAdmin = 1;//ignore admin methods

    var $UnUsedActions = array();//check here actions which should not  be checked by the method Get

    var $DefaultTitle = '';

    var $DefaultKeywords = '';

    var $DefaultAuthor = '';

    var $DefaultDescription = '';

    var $UsedURLControlers = array('Pages');//array of  controlers which have to get meta using URL for others controllers URL not used


	function get(
				  $title = '',
	              $in_name = '',//controller name
	              $in_action = '',//controller method
	              $in_url = ''// current URl
	){

	    if ( empty($in_name) && !empty($this->request->params['controller']) ) {
	       $in_name = $this->request->params['controller'];
	    }//if

	    if ( empty($in_action) && !empty($this->request->params['action']) ) {
	       $in_action = $this->request->params['action'];
	    }//if

	    $out = array(
    	    'title'       => $this->DefaultTitle,
    	    'keywords'    => $this->DefaultKeywords,
    	    'author'      => $this->DefaultAuthor,
    	    'description' => $this->DefaultDescription
	    );

	    if(!empty($in_name) && !empty($in_action)){

	        if(!empty($in_action) && in_array($in_action, $this->UnUsedActions)) {
	           return $out;
	        }//if

	        $search = array();
	        $lngID=$this->Session->read('User.Lang.id');
	        if (!$lngID) $lngID = 1;
	        $search['language_id'] = $lngID;

	        $objMetatag = new Metatag();
            $objMetatag->unbindModel(array('belongsTo' => array('Language')));

	        if(in_array($in_name, $this->UsedURLControlers) && !empty($in_url)) {
	           $search['url'] = $in_url;
	        }//if

	        if(!empty($in_name)) {
	           $search['name'] = $in_name;
	        }//if

	        if(!empty($in_action)) {
	           $search['action'] = $in_action;
	        }//if

	        $arrData = $objMetatag->find($search, null, null, 0);

	        if(!empty($arrData['Metatag'])){
	            $out['title'] = empty($arrData['Metatag']['title']) ? $this->DefaultTitle : $arrData['Metatag']['title'];
	            $out['keywords']=empty($arrData['Metatag']['keywords'])?$this->DefaultKeywords:$arrData['Metatag']['keywords'];
	            $out['author']=empty($arrData['Metatag']['author'])?$this->DefaultAuthor:$arrData['Metatag']['author'];
	            $out['description']=empty($arrData['Metatag']['description'])?$this->DefaultDescription:$arrData['Metatag']['description'];
	        } elseif($this->AutoAdd){

	            if($this->IgnoreAdmin){
	                $split_url=split("/",$in_url);
	                if(count($split_url)>2 && $split_url[1]=='admin') {
	                   return $out;
	                }//if
	            }//if

	            $arrData=array(
    	            'url'=>$in_url,
    	            'name'=>$in_name,
    	            'action'=>$in_action,
    	            'language_id'=>$lngID,
    	            'title'=>'Page Title',
    	            'keywords'=>'',
    	            'author'=>'',
    	            'description'=>''
	            );

	            $objMetatag->save($arrData);
	        }//if
	    }//if


	    $tagsOut = '';

	    if ( !empty($out['title']) ) {
            $tagsOut .= '<title>' . (!empty($title) ? $title : $out['title']) . '</title>';
	    }//if

	    if ( !empty($out['keywords']) ) {
            $tagsOut .= '<meta name="keywords" content="' . $out['keywords'] . '">';
	    }//if

	    if ( !empty($out['description']) ) {
            $tagsOut .= '<meta name="description" content="' . $out['description'] . '">';
	    }//if

	    if ( !empty($out['author']) ) {
            $tagsOut .= '<meta name="author" content="' . $out['author'] . '">';
	    }//if

	    return $tagsOut;
	}//eof get


}//Metatag

