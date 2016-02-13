<h2>Submit Content</h2>
<hr/>
<span class='red'><strong>Success!</strong></span>
<br/>
Your content has been submitted! It will now be listed in the BPONG feeds. What now? You can:
<br/>
<?php if ($type =='link') {?>
<?php /*?>
<br/><span class='red'>&bull;</span> View your links by accessing the <a href="/links/listMy/" >direct link</a> <?php */ ?>
<?php } else { ?>
<?php if (!$toJunk):?>
<br/><span class='red'>&bull;</span> View your submission by accessing the <a href="/Albums/show_<?php echo $type;?>/<?php echo $albumID;?>" >direct link</a>
<?php endif;?>
<?php }?>
<br/><span class='red'>&bull;</span> View all your submission in your <a href="/u/<?php echo $userSession['lgn'];?>">profile</a>
<br/><span class='red'>&bull;</span> Submit <a href="/submit/">more stuff</a>