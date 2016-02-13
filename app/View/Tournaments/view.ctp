<h2><?php echo($tournament['Tournament']['name']); ?></h2>

<?php if ($tournament['Tournament']['signup_required']>0): ?>
    <a href="<?php echo SECURE_SERVER ?>/signups/Tournament/<?php echo($tournament['Tournament']['slug']); ?>">
    <img src="<?php echo STATIC_BPONG?>/img/signup_now.png" border="0"> </a>
<?php endif; ?>
<br />
<strong>Start date:</strong><?php echo($this->Time->niceDate($tournament['Tournament']['start_date'])); ?><br />
<strong>End date:</strong><?php echo($this->Time->niceDate($tournament['Tournament']['end_date'])); ?><br />
<?php echo($tournament['Tournament']['description']); ?><br />
<br />
<?php if ( 1!=1): ?>
<div class="clear"></div>
<div><h1> Events: </h1>

  <?php foreach ( $tournament['Event'] as $event) :?>
	  Name:<?php echo $event['name'] ?><a href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/signups/Event/"><img src="<?php echo STATIC_BPONG?>/img/signup_now.png" border="0"></a><BR>
	  Start date:<?php echo($this->Time->nice($event['start_time'])); ?><br />
      End date:<?php echo($this->Time->nice($event['end_time'])); ?><br />
  <?php endforeach; ?>

  </div>
  <?php endif; ?>
