Teammates:<br />
<?php foreach ($teammates as $teammate): ?>
<a href="<?php echo MAIN_SERVER.'/u/'.$teammate['User']['lgn']?>"><?php echo $teammate['User']['lgn'];?></a><?php echo ' - '.$teammate['User']['firstname'].' '.$teammate['User']['lastname']; ?><br />
<?php endforeach; ?>