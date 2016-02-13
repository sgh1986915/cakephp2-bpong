<?php
	$linkntitle = $this->Forumlinks->generate_link_and_title_for_posts ( $topicname, $branchleft, $branchright );
	$this->pageTitle = 'Beer Pong Forums' . $linkntitle['pagetitle'] . " | BPONG.COM";
	echo $this->Html->css ( array ('bbcode' ) );
?>
	<?php
			$slug_url =  implode("/", $this->request->params['pass']);
			$paginate_params = array(
										  'url' => array( $slug_url )
										, 'escape' => false
									);
	?>

<div class="forumposts index">
<?php
/**
 * Generate Navigation link by Forumlinks helper
 */
	//echo $this->Forumlinks->generateforumlinks ( $this->request->params['pass'] );
	echo $linkntitle['links'];
?>
<div class="rightbox">
<div class="rightbox_top">&nbsp;</div>
<?php echo $this->element('forumAdvert'); ?>
<div class="rightbox_bottom">&nbsp;</div>
</div>
        <div class="forumbox">
        	<?php if( $this->Paginator->params['paging']['Forumpost']['pageCount'] > 1 ): ?>
	<div class="paging">
		<?php echo $this->Paginator->prev($this->Html->image(STATIC_BPONG.'/img/prev.gif', array('alt' => 'prev')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	 	<?php echo $this->Paginator->first(3, $paginate_params, null, array('class'=>'disabled'));?>
 	 	<?php echo $this->Paginator->numbers( $paginate_params );?>
        <?php echo $this->Paginator->last(3, $paginate_params, null, array('class'=>'disabled'));?>
		<?php echo $this->Paginator->next($this->Html->image(STATIC_BPONG.'/img/next.gif', array('alt' => 'next')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	</div>
<?php endif; ?>

             <table class="tableforum" cellpadding="0" cellspacing="0" border="0">
               <tr class="tableheader">
                    <td width="100" style="text-align:center;" class="tdleft">
                       Author
                    </td>
                    <td width="400" style="text-align:left;" class="tdright">
                       Message
                    </td>
                </tr>
<?php
$i = 0;
$j = 0;
foreach ($forumposts as $index => $forumpost):
	$class = null;
	$j++;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
            <tr<?php echo $class;?> id="post_<?php echo $forumpost["Forumpost"]["id"]?>">
                    <td align="left" valign="top" class="odd latest">
                    		<div class='forum_user_info'>
							<?php echo $this->Image->avatar($forumpost['User']['avatar']);?><br/>
							<?php echo $this->Html->link($forumpost['User']['lgn'], '/u/' . $forumpost['User']['lgn']); ?>
                    		</div>
                        	<div class='forum_joined'>Joined: <?php echo date ('m.d.Y', strtotime($forumpost['User']['created']));?></div>
                        	<br/>
                        	<?php if ($forumpost['User']['birthdate'] && $forumpost['User']['birthdate'] != '0000-00-00'): ?>
							<span class='forum_stats_title'>Age: </span> <span class='forum_stats_value'><?php echo $this->Time->age($forumpost['User']['birthdate']); ?></span><br/>
							<?php endif; ?>
							<span class='forum_stats_title'>Total Games: </span> <span class='forum_stats_value'><?php echo intval($forumpost['User']['gameInfo']['losses'] + $forumpost['User']['gameInfo']['wins'])?></span><br/>
                           	<span class='forum_stats_title'>W/L %: </span> <span class='forum_stats_value'><?php echo 100 * $forumpost['User']['gameInfo']['average_wins'];?></span><br/>
                            <span class='forum_stats_title'>Avg CD: </span> <span class='forum_stats_value'><?php echo $forumpost['User']['gameInfo']['average_cupdif'];?></span><br/>
                            <br class='clear' />

							<?php if ($forumpost['User']['gameInfo']['wins'] || $forumpost['User']['gameInfo']['losses']):?>
                            	<div style='margin-left:25px;'><?php echo $this->element('/charts/user_pie_chart', array('chartIndex' => $j, 'winnings' => $forumpost['User']['gameInfo']['wins'], 'losses' => $forumpost['User']['gameInfo']['losses']));?></div>

                            <?php endif;?>
                            <div style='width:100%; text-align:center'>
                            <strong><a href="/users/stats/<?php echo $forumpost['User']['id'];?>">Full Stats</a></strong>
                            </div>
                            <?php /*?>
                            <a href="/users/stats/<?php echo $forumpost['User']['id'];?>"><?php echo $this->Html->image(STATIC_BPONG.'/img/stats_link.gif', array('alt' => 'next'));?></a>
                           <?php */ ?>
                           <br /><br />
                           	<?php
                        		/*$profileimage = $this->Html->image(STATIC_BPONG."/img/profile.gif", array('alt' => 'Profile', 'class' => 'left'));
                        		echo $this->Html->link($profileimage, array('controller' => 'users', 'action' => 'view', rawurlencode(htmlentities($forumpost['User']['lgn'], ENT_QUOTES, "UTF-8"))), array(), false, false);
                        		unset( $profileimage );*/
                        	?>
                    </td>
                    <td class="even" valign="top">
                    <ul>
                    	<li class="posted">
                        	<?php
                        		$quote = $this->Html->image(STATIC_BPONG."/img/quote.gif", array('alt' => 'Quote', 'class' => 'right'));
                        		echo $this->Html->link($quote, array('action'=>'add', $slug_url, $forumpost["Forumpost"]["id"]), array('escape' => false), false, false);
                        		unset($quote);
                        	?>
                        	<p>
                        		Posted: <?php echo $this->Time->niceShort($forumpost['Forumpost']['created']); ?>
                        		<br />
                        		Post subject:
                        		<?php echo $forumpost['Forumtopic']['name'];?>
                        	</p>
                        </li>
                        <li class="message">
                        	<?php
                                $texttodecode = html_entity_decode($this->Bbcode->convert_bbcode( $forumpost['Forumpost']['text'] ), ENT_QUOTES, 'UTF-8');
                                $texttodecode = html_entity_decode( $texttodecode, ENT_QUOTES, 'UTF-8' );
                                echo $texttodecode;
                               //function replaced to the add method of forumpost controller
                               // echo $this->Forumlinks->cut_long_words_from_post($texttodecode);
                        	?>
                            <br />
                            <?php
								$editimage = $this->Html->image(STATIC_BPONG."/img/smalledit.gif",array('style' => 'padding:5px;', 'alt' => 'edit', 'name' => 'smalledit'));
	                            	$deleteimage = $this->Html->image(STATIC_BPONG."/img/smalldelete.gif",array('style' => 'padding:5px;', 'alt' => 'delete', 'name' => 'smalldelete'));
	                            	if($Updated=='ALL' || ($Updated=='OWNER' && $userID==$forumpost['Forumpost']['user_id'])):
	                            		echo $this->Html->link( $editimage, array( 'action'=> 'edit', $slug_url, $forumpost['Forumpost']['id']), array('escape' => false), false, false);
	                            	endif;

	                            //Nobody can not delete first post of a topic
                            	if ($forumpost['Forumpost']['id'] != $firstPostId) {
	                            	if($Deleted=='ALL' || ($Deleted=='OWNER' && $userID==$forumpost['Forumpost']['user_id'])):
	                            		echo $this->Html->link( $deleteimage, array( 'action'=> 'delete', $slug_url, $forumpost['Forumpost']['id']), array('escape' => false), "Are you sure want to delete this post?", false);
	                            	endif;

                            	}
                            	unset($deleteimage);
	                            unset($editimage);
                           	?>
                        </li>
                        <li class="top">



                        		<?php
	                        		if ( $forumpost['Forumpost']['created'] != $forumpost['Forumpost']['modified'] ) :
	                        	?>
		                        	<span style="float: right;">
	                        			Last edited on <?php echo $this->Time->niceShort($forumpost['Forumpost']['modified']); ?>
		                        	</span>
	                        	<?php
	                        		endif;
                        		?>

                        </li>
                    </ul>
                    </td>
            </tr>
<?php endforeach; ?>
            </table>
        </div>
<?php if( $this->Paginator->params['paging']['Forumpost']['pageCount'] > 1 ): ?>
	<div class="paging">
		<?php echo $this->Paginator->prev($this->Html->image(STATIC_BPONG.'/img/prev.gif', array('alt' => 'prev')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	 	<?php echo $this->Paginator->first(3, $paginate_params, null, array('class'=>'disabled'));?>
 	 	<?php echo $this->Paginator->numbers( $paginate_params );?>
        <?php echo $this->Paginator->last(3, $paginate_params, null, array('class'=>'disabled'));?>
		<?php echo $this->Paginator->next($this->Html->image(STATIC_BPONG.'/img/next.gif', array('alt' => 'next')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
		<?php unset($paginate_params); ?>
	</div>
<?php endif; ?>
</div>



<div class="actions">
	<ul>
		<li><span class="backbtn"><?php echo $this->Html->link('Back to topics', array('controller'=> 'forumbranches', 'action'=>'index', $back_slug), array('class'=>'backbtn')); ?></span></li>
		<?php if($Created): ?>
		<li><span class="addbtn"><?php echo $this->Html->link('Reply', array('action'=>'add', $slug_url), array('class'=>'addbtn')); ?></span></li>
		<?php endif; ?>
	</ul>
</div>
<script type="text/javascript">
		//Correct a width of a big images loaded externally
		maxSize = 385;

		$('img').click(function(){
					if ($(this).css('max-width')=='385px') {
						window.open( this.src, 'imageView', 'toolbar=no, directories=no, location=no,  status=yes, menubar=no, resizable=no, scrollbars=no');
					}
		});
		//eof correcting

</script>
