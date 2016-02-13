<?php /*?>
<div class="input text">
<label for="setTags">User's Tags</label>    
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
<?php echo $this->Html->link($tag['tag'], '/tag/' . $modelName . '/' . $tag['tag']); ?> <?php echo $this->Html->link('x', '/tags/deleteUsers/' . $tag['ModelsTag']['id'], null, 'Are you sure you want to delete this Tag?'); ?><?php if ($i<$countTag){?>,<?php }?>

<?php endforeach; ?>
</div>
<?php */ ?>