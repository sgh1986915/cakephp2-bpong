<div class="tags-wrapper othertags">
<?php
    foreach ($tags as $key => $tag) {  
        if ($tag['ModelsTag']['user_id'] == $authorID)  {
            unset($tags[$key]);
        }      
     }
    $countTag = count($tags);
    $i = 0;    
    foreach ($tags as $key => $tag) :  
        $i++;
    ?>
    
<span class="tags"><?php echo $this->Html->link($tag['tag'], '/tag/' . $tag['id'] . '/' . $tag['ModelsTag']['model']); ?></span>  (<?php echo $this->Html->link($tag['counter'], '/tag/' . $tag['id'] . '/' . $tag['ModelsTag']['model']); ?>)<?php if ($i<$countTag){?>,<?php }?>

<?php endforeach; ?>
</div>

<?php if (isset($userSession['id']) && $userSession['id'] != VISITOR_USER):?>
<a title="Add your tags" class="thickbox add_link" href="/tags/ajaxAddUsers/<?php echo $modelName;?>/<?php echo $modelID;?>/<?php echo $authorID;?>/?height=300&amp;width=264&amp;modal=true;">Add Tags</a>
<?php endif;?>
