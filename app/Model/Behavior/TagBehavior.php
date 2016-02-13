<?php /**
 * Tag Behavior class file.
 *
 * Model Behavior to support tags.
 *
/**
 * Add tags behavior to a model.
 */
class TagBehavior extends ModelBehavior
{
    /**
     * Initiate behaviour for the model using specified settings.
     *
     * @param object $model    Model using the behaviour
     * @param array  $settings Settings to override for model.
     *
     * @access public
     */
    function setup(Model $model, $settings = array())
    {
        
    }

    function afterSave(Model $model, $created, $options = array())
    {     
        if (isset($model->data[$model->alias]['tags'])) {        
            $modelID = $model->id;
            $modelName = $model->name;
            $newTagsIDs = array();
            $oldTagsIDs = array();
                                    
            if (isset($_SESSION['loggedUser']['id'])) {
                $userID = $_SESSION['loggedUser']['id'];
            } else {
                $userID = 0;
            }
            
            $tagsConditions = array('ModelsTag.model' => $modelName, 'ModelsTag.model_id' => $modelID);        
            
            // delete only author tags
            if (isset($model->data[$model->alias]['tags_user_id']) && $model->data[$model->alias]['tags_user_id']) {
                $tagsConditions['user_id'] = $model->data[$model->alias]['tags_user_id'];    
            }
            
            $oldTags = $model->ModelsTag->find('all', array('conditions' => $tagsConditions));
            if (!empty($oldTags)) {     
                $oldModelTagsIDs = Set::combine($oldTags, '{n}.ModelsTag.id', '{n}.ModelsTag.id');
                $oldTagsIDs = Set::combine($oldTags, '{n}.ModelsTag.id', '{n}.ModelsTag.tag_id');        
        
                // delete all tags of this model object
                if (!empty($oldModelTagsIDs)) {
                    $model->ModelsTag->recursive = -1;
                    $model->ModelsTag->deleteAll(array('ModelsTag.id' => $oldModelTagsIDs), false);        
                }
            }
            $dataTags = '';
            if (isset($model->data[$model->alias]['tags'])) {
                $dataTags = trim($model->data[$model->alias]['tags']);
                unset($model->data[$model->alias]['tags']);
            }
            if ($dataTags) {
                $expTags = explode(',', $dataTags);    
                if(empty($expTags) && trim($dataTags)) {
                    $expTags[0] = $dataTags;
                }  
                if(!trim($expTags[count($expTags) - 1])) {
                    unset($expTags[count($expTags) - 1]);            
                }
                
                $expTags = $model->ModelsTag->deleteDubleTags($expTags);
                
                foreach ($expTags as $expTag) {
                    $tag = $model->Tag->parseTag($expTag);               
                    if ($tag) {
                            $thisTagID = $model->Tag->field('id', array('Tag.tag' => $tag));
                            
                        if (!$thisTagID) {
                            $saveTag['tag'] = $tag;
                            $saveTag['user_id'] = $userID;
                                
                            $model->Tag->create();
                            $model->Tag->save($saveTag);  
                            $thisTagID = $model->Tag->getLastInsertID();  
                        }
                            
                            $newTagsIDs[] = $thisTagID;
                            
                            $model->ModelsTag->create();
                            $saveModelsTag['tag_id'] = $thisTagID;
                            $saveModelsTag['model_id'] = $modelID;
                            $saveModelsTag['model'] = $modelName;
                            $saveModelsTag['user_id'] = $userID;
        
                            $model->ModelsTag->save($saveModelsTag);
                    }    
                }
            }

            // decrease tags counter
            $difOldTagsIDs = array_diff($oldTagsIDs, $newTagsIDs);
            if (!empty($difOldTagsIDs)) {
                $model->ModelsTag->query('UPDATE tags SET counter = counter - 1 WHERE id = ' . implode(' OR id = ', $difOldTagsIDs));                   
            }
            // increase tags counter        
            $difNewTagsIDs = array_diff($newTagsIDs, $oldTagsIDs); 
            if (!empty($difNewTagsIDs)) {
                $model->ModelsTag->query('UPDATE tags SET counter = counter + 1 WHERE id = ' . implode(' OR id = ', $difNewTagsIDs));                   
            }
        }                
        return true;
    }
    function beforeDelete(Model $model, $cascade = true)
    {
        $modelID = $model->id;
        $oldTags  = $model->ModelsTag->find('all', array('conditions' => array('model' => $model->name, 'model_id' => $modelID)));    
        if (!empty($oldTags)) {     
            $oldModelTagsIDs = Set::combine($oldTags, '{n}.ModelsTag.id', '{n}.ModelsTag.id');
            $oldTagsIDs = Set::combine($oldTags, '{n}.ModelsTag.id', '{n}.ModelsTag.tag_id');        
    
            // delete all tags of this model object
            if (!empty($oldModelTagsIDs)) {
                $model->ModelsTag->recursive = -1;
                $model->ModelsTag->deleteAll(array('ModelsTag.id' => $oldModelTagsIDs), false);        
            }
            if (!empty($oldTagsIDs)) {
                $model->ModelsTag->query('UPDATE tags SET counter = counter - 1 WHERE id = ' . implode(' OR id = ', $oldTagsIDs));                   
            }           
        }                            
    }
}

