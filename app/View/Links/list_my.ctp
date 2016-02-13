<?php 
$this->Paginator->options(array('url' => $this->passedArgs)); 
$total = $this->Paginator->counter(array('format' => '%count%'));
?>
<h2>Your Links</h2>
<?php if (!empty($links)) {?>
    <?php 
    $i = 0;
    foreach ($links as $link) {
    $i++;
    ?>
    <b><?php echo $link['Link']['description'];?></b><br/>
    <a href="http://<?php echo str_replace('http://', '', $link['Link']['url']);?>"><?php echo $link['Link']['title'];?></a>
    <br/><br/>
<?php } ?>
<?php } else {?>
	No Links
<?php }?>
	<br class='clear'/>	<br class='clear'/>
<?php if($this->Paginator->numbers()): ?>
	<br/><?php echo $this->Paginator->numbers();?><br/>
<?php endif;?>


