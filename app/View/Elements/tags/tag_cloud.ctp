<?php 
// initialization:
// controller: $this->set('cloudTags', $this->Album->Tag->getCloudTags('Image')); 
// view:  echo $this->element('/tags/tag_cloud', array('url' => '/Images/tag/'));
?>
<?php if (!empty($cloudTags)):?>
<div class = 'tag-cloud'>
<ul>
    <?php foreach ($cloudTags as $tagName => $tag): ?>
    <li style='font-size:<?php echo $tag['size'];?>% !important;'>
    <?php if ($url):?>
        <?php echo $this->Html->link($tag['name'], $url . $tag['name']); ?>
    <?php else:?>
        <?php echo $tag['name']; ?>
    <?php endif;?>    
    </li>          
    <?php endforeach;?>
</ul>
</div>
<?php endif;?>