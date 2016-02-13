<?php if ($video['Video']['is_processed']):?>
    
    <?php if ($video['Video']['is_file']):?>
    	<?php echo $this->Youtube->getVideoCode($video['Video']['youtube_id'], 810, 480);?>
    <?php elseif ($video['Video']['youtube_id']):?>
    	<?php echo $this->Youtube->getVideoCode($video['Video']['youtube_id'], 810, 480);?>
    <?php else:?>
        <?php echo $this->Youtube->fixVideoCode($video['Video']['code']);?>
    <?php endif; ?>
<?php else:?>
	Video is processing, please wait
<?php endif; ?>