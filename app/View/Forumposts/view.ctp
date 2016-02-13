<?php
	$this->pageTitle = 'Beer Pong Forums' . $this->Forumlinks->last_title( $this->request->params['pass'] ) . " | BPONG.COM";
?>
<?php
	echo $this->Html->css ( array ('bbcode' ) );
?>
	<?php
			 $paginate_params = array(
										  'url' => array(
												  $this->request->data['Forumtopic']['forumbranch_id']
												, $this->request->data['Forumtopic']['forumtopic_id']
												)
										, 'escape' => false
									);
	?>
<div class="forumposts index">
<?php
/**
 * Generate Navigation link by Forumlinks helper
 */
	echo $this->Forumlinks->generateforumlinks($this->request->data['Forumtopic']['forumbranch_id'], $this->request->data['Forumtopic']['forumtopic_id']);
?>
<div class="rightbox">
<div class="rightbox_top">&nbsp;</div>
<?php echo $this->element('forumAdvert'); ?>
<div class="rightbox_bottom">&nbsp;</div>
</div>
        <div class="forumbox">
        	 <table class="tableforum" cellpadding="0" cellspacing="0" border="0">
               <tr class="tableheader">
                    <td width="100" style="text-align:left;" class="tdleft">
                       Author
                    </td>
                    <td width="400" style="text-align:left;" class="tdright">
                       Message
                    </td>
                </tr>
<?php
$i = 0;
foreach ($forumposts as $index => $forumpost):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
            <tr<?php echo $class;?> id="post_<?php echo $forumpost["Forumpost"]["id"]?>">
                    <td align="left" valign="top" class="odd latest">
                        <strong><?php echo $forumpost['User']['lgn']; ?></strong><br />
                        	Joined: <?php echo $this->Time->niceShort($forumpost['User']['created']); //<br />Posts: 17?>
                    </td>
                    <td class="even" valign="top">
                    <ul>
                    	<li class="posted">
                        	<?php
                        		$quote = $this->Html->image(STATIC_BPONG."/img/quote.gif", array('alt' => 'Quote', 'class' => 'right'));
                        		echo $this->Html->link($quote, array('action'=>'add', $this->request->data['Forumtopic']['forumbranch_id'], $this->request->data['Forumtopic']['forumtopic_id'], $forumpost["Forumpost"]["id"]), array('escape' => false), false, false);
                        		unset($quote);
                        	?>
                        	<p>
                        		Posted: <?php echo $this->Time->niceShort($forumpost['Forumpost']['created']); ?>
                        		<br />
                        		Post subject:
                        		<?php echo $forumpost['Forumtopic']['name']; ?>
                        	</p>
                        </li>
                        <li class="message">
                        	<?php echo $this->Bbcode->convert_bbcode( $forumpost['Forumpost']['text'] ); ?>
                            <br />
                            <?php
								$editimage = $this->Html->image(STATIC_BPONG."/img/smalledit.gif",array('style' => 'padding:5px;', 'alt' => 'edit'));
	                            	$deleteimage = $this->Html->image(STATIC_BPONG."/img/smalldelete.gif",array('style' => 'padding:5px;', 'alt' => 'delete'));
	                            	if($Updated=='ALL' || ($Updated=='OWNER' && $userID==$forumpost['Forumpost']['user_id'])):
	                            		echo $this->Html->link( $editimage, array( 'action'=> 'edit', $forumpost['Forumpost']['id']), array('escape' => false), false, false);
	                            	endif;

                            	if ($index != 0) {
	                            	if($Deleted=='ALL' || ($Deleted=='OWNER' && $userID==$forumpost['Forumpost']['user_id'])):
	                            		echo $this->Html->link( $deleteimage, array( 'action'=> 'delete', $forumpost['Forumpost']['id']), array('escape' => false), "Are you sure want to delete this post?", false);
	                            	endif;

                            	}
                            	unset($deleteimage);
	                            unset($editimage);
                           	?>
                        </li>
                        <li class="top">

                        	<?php
                        		$profileimage = $this->Html->image(STATIC_BPONG."/img/profile.gif", array('alt' => 'Profile', 'class' => 'left'));
                        		echo $this->Html->link($profileimage, array('controller' => 'users', 'action' => 'view', rawurlencode(htmlentities($forumpost['User']['lgn'], ENT_QUOTES, "UTF-8"))), array('escape' => false), false, false);
                        		unset( $profileimage );
                        	?>

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
</div>

<?php if( $this->Paginator->params['paging']['Forumpost']['pageCount'] > 1 ): ?>
	<div class="paging">
		<?php echo $this->Paginator->prev($this->Html->image(STATIC_BPONG.'/img/prev.gif', array('alt' => 'prev')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	 	<?php echo $this->Paginator->numbers($paginate_params);?>
		<?php echo $this->Paginator->next($this->Html->image(STATIC_BPONG.'/img/next.gif', array('alt' => 'next')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
		<?php unset($paginate_params); ?>
	</div>
<?php endif; ?>

<div class="actions">
	<ul>
		<li><span class="backbtn"><?php echo $this->Html->link('Back to topics', array('controller'=> 'forumtopics', 'action'=>'index', $this->request->data['Forumtopic']['forumbranch_id']), array('class'=>'backbtn')); ?></span></li>
		<?php if($Created): ?>
		<li><span class="addbtn"><?php echo $this->Html->link('Reply', array('action'=>'add', $this->request->data['Forumtopic']['forumbranch_id'], $this->request->data['Forumtopic']['forumtopic_id']), array('class'=>'addbtn')); ?></span></li>
		<?php endif; ?>
	</ul>
</div>
