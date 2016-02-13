<div class="tags-wrapper usertags">
<?php
    foreach ($tags as $key => $tag) {  
        if ($authorID && $tag['ModelsTag']['user_id'] != $authorID)  {
            unset($tags[$key]);
        }      
     }
    $countTag = count($tags);
    $i = 0;    
    foreach ($tags as $key => $tag) :  
        $i++;
    ?>
<span class="tags"><?php echo $this->Html->link($tag['tag'], '/tag/' . $tag['id'] . '/' . $tag['ModelsTag']['model']); ?></span> (<?php echo $this->Html->link($tag['counter'], '/tag/' . $tag['id'] . '/' . $tag['ModelsTag']['model']); ?>)<?php if ($i<$countTag){?>,<?php }?>

<?php endforeach; ?>
</div>
