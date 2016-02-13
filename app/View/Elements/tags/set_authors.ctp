<?php 
    $tagsList = '';
    if (isset($this->request->data[$modelName]['tags']) && $this->request->data[$modelName]['tags']) {
        $tagsList = $this->request->data[$modelName]['tags'];
        
    } elseif (isset($tags) && !empty($tags)) {
            $tagsList = '';
            foreach ($tags as $key => $tag) { 
                if ($authorID && $tag['ModelsTag']['user_id'] != $authorID)  {
                    unset($tags[$key]);
                }      
             }
            foreach ($tags as $tag) {
                $tagsList.= $tag['tag'] . ', ';         
            }
    }
    if (!isset($label)) {
        $label = 'Tags';
    }
?>

<script type="text/javascript">
$(document).ready(function() {
    $("#setTags").autocomplete("/tags/autocomplete/<?php echo $modelName;?>", {
        		width: 320,
        		max: 4,
        		highlight: false,
        		multiple: true,
        		multipleSeparator: ", ",
        		scroll: true,
        		scrollHeight: 300
    });				   				   			   				   
});
</script>
<?php if(isset($authorID) && $authorID):?>
<?php echo $this->Form->hidden('tags_user_id', array('value' => $authorID)); ?>
<?php endif;?>
<div class="input text">
<?php if ($label):?>
<label for="setTags"><?php echo $label;?></label>
<div style='float:left;'>
    <?php endif;?>
    <?php echo $this->Form->input('tags', array('div' => false, 'value' => $tagsList, 'id' => 'setTags', 'label' => false, 'style' => 'width:300px;overflow: hidden;')); ?>
    <br/> separate multiple tags with commas
</div>
</div>
