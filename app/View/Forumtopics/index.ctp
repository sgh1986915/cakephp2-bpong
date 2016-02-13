<script>
function movebranch( node_to_move, where_to_move, node_type ) {
	if (node_to_move == where_to_move) return false;


	$("#" + node_type + "_" + node_to_move).find("#ForumMovePosition").hide();
	$("#" + node_type + "_"+node_to_move).find("img").show();//[name='loading'

	$.post("/forumbranches/ajaxmovebranch",
				{ 'data[nodeID]'		:	node_to_move
				, 'data[wheretomoveID]'	:	where_to_move
				, 'data[nodetype]'		:	node_type }
			, function(response){
				setTimeout("finishMoveNode('" + escape(response) + "','" + node_type + "','" + node_to_move + "' )", 400);
			});
}

function finishMoveNode(response, node_type, node_id, oldselectbox ){
	response = unescape(response);

	if ( response == 'error' ) {
		$("#" + node_type + "_"+node_id).find("img").hide();
		$("#" + node_type + "_" + node_id).find("#ForumMovePosition").show();

		alert('Error while moving. Please try again.');
	} else {
		//alert('All Ok.');
		//location.reload(true);
		$("#" + node_type + "_"+node_id).slideUp("slow");

	}

}

</script>

<div class="forumtopics index">

<div class="rightbox">
<div class="rightbox_top">&nbsp;</div>
<?php echo $this->element('forumAdvert'); ?>
<div class="rightbox_bottom">&nbsp;</div>
</div>
<?php $paginate_params = array('url' => array($this->request->data['Forumbranches']['id']), 'escape' => false);?>

<?php
	echo $this->Forumlinks->generateforumlinks($this->request->data['Forumbranches']['id']);
?>

<div class="forumbox">
<?php if( $this->Paginator->params['paging']['Forumtopic']['pageCount'] > 1 ): ?>
<div class="paging">
		<?php echo $this->Paginator->prev($this->Html->image(STATIC_BPONG.'/img/prev.gif', array('alt' => 'prev')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	 	<?php echo $this->Paginator->first(3, $paginate_params, null, array('class'=>'disabled'));?>
 	 	<?php echo $this->Paginator->numbers( $paginate_params );?>
        <?php echo $this->Paginator->last(3, $paginate_params, null, array('class'=>'disabled'));?>
		<?php echo $this->Paginator->next($this->Html->image(STATIC_BPONG.'/img/next.gif', array('alt' => 'next')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
</div>
<?php endif; ?>
<?php
	$leftroundclass = ' class="topicleft"';
	$rightroundclass = ' class="topicright"';
	if (!empty($subbranches) || !empty($forumtopics)) {
		if (!empty($subbranches)) :
		$leftroundclass = '';
		$rightroundclass = '';
?>


<table class="tableforum" cellpadding="0" cellspacing="0" border="0">
    	<tr class="tableheader">
        	<td colspan="2" style="text-align:left; padding-left:15px;" width="300" class="tdleft">
            	Forum
            </td>
            <td width="50">
            	Topics
            </td>
            <td width="50">
            	Posts
            </td>
            <td width="100" class="tdright">
            	Last post
            </td>
        </tr>
<!--        <tr class="header">
        	<td colspan="5">
            	<h2>BPONG.COM</h2>
            </td>
        </tr> -->
<?php
$i = 0;
$all = count($subbranches);
foreach ($subbranches as $forumbranch):
	$class = ' class="even';
	if ($i++ % 2 == 0) {
		$class = ' class="odd';
	}
	if ($i >= $all) {
		$class .= ' last';
	}
	$class .='"';

	if (isset($forumbranch ['Lastpost']['id'])) {
		$pagenum = ceil ( ($forumbranch ['Lastpost'] ['Forumtopic'] ['repliescounter'] + 1) / 10 );
	} else {
		$pagenum = 1;
	}
	?>
	<tr<?php echo $class;?> id="branch_<?php echo $forumbranch['Forumbranch']['id']; ?>">
		<td align="center" valign="middle" width="40">
			<img src="<?php echo STATIC_BPONG?>/img/newposts.gif" alt="New posts" />
		</td>
		<td width="260">
			<h3>
				<?php
					echo $this->Html->link($forumbranch['Forumbranch']['name'], array('controller'=>'forumtopics','action'=>'index', $forumbranch['Forumbranch']['id']));
				?>
			</h3>
			<p class="announce">
				<?php
					echo $forumbranch['Forumbranch']['description'];
				?>
			</p>
            <br />
            			<?php
                                	$editimage = $this->Html->image(STATIC_BPONG."/img/smalledit.gif",array('style' => 'padding:5px;', 'alt' => 'edit'));
	                            	$deleteimage = $this->Html->image(STATIC_BPONG."/img/smalldelete.gif",array('style' => 'padding:5px;', 'alt' => 'delete'));

	                            	if($UpdatedForum=='ALL' || ($UpdatedForum=='OWNER' && $userID==$forumbranch['Forumbranch']['user_id'])):
	                            		echo $this->Html->link( $editimage, array( 'controller' => 'forumbranches','action'=> 'edit', $forumbranch['Forumbranch']['id']), array(), false, false);
	                            	endif;
	                            	if($DeletedForum=='ALL' || ($DeletedForum=='OWNER' && $userID==$forumbranch['Forumbranch']['user_id'])):
	                            		echo $this->Html->link( $deleteimage, array( 'controller' => 'forumbranches', 'action'=> 'delete', $forumbranch['Forumbranch']['id']), array(), sprintf('Are you sure you want to delete "%s"?', $forumbranch['Forumbranch']['name']), false);
	                            	endif;

	                            	unset($deleteimage);
	                            	unset($editimage);

	                            	if($UpdatedForum=='ALL' || ($UpdatedForum=='OWNER' && $userID==$forumbranch['Forumbranch']['user_id'])):
	                            	   //remove current subforum from dropdown list
	                            	   $current_options = $alltrees[$forumbranch['Forumbranch']['id']];
	                            	   echo $this->Form->select("Forum.move_position", $current_options, $forumbranch['Forumbranch']['parent_id'], array('escape' => false, 'style'=>'width: 100%;', 'onchange' => 'movebranch(' . $forumbranch['Forumbranch']['id'] . ', this.value, \'branch\');'), false);
	                            	   unset($current_options);
	                            	 endif;
	                            	?>


	                            	<?php
	                            	echo $this->Html->image(STATIC_BPONG."/img/loading.gif", array(	  'style' => 'display:none;'
	                            											, 'border' => '0'
	                            											, 'name' => 'loading' ));
            			?>


		</td>
		<td width="50" align="center" valign="middle">
			<?php echo $forumbranch['Forumbranch']['topiccounter']; ?>
		</td>
		<td width="50" align="center" valign="middle">
			<?php echo $forumbranch['Forumbranch']['postcounter']; ?>
		</td>
		<td width="100" class="latest">
			<?php
				if (!empty($forumbranch['Lastpost']['id'])) {
				echo $this->Time->niceShort($forumbranch['Lastpost']['modified'])
						. " by "
					    . $this->Html->link($forumbranch['Lastpost']['User']['lgn'], '/u/' . $forumbranch['Lastpost']['User']['lgn'])
						. $this->Html->link("View latest post", array('controller'=> 'forumposts', 'action'=>'index', $forumbranch['Forumbranch']['id'], $forumbranch['Lastpost']['forumtopic_id'], "page:" . $pagenum, "#post_" . $forumbranch ['Lastpost'] ['id']), array("class" => "viewlatest"), false, false);
				} else {
					echo "No post";
				}
			?>
		</td>
    </tr>
<?php endforeach; ?>
    </table>

<?php
	endif;
	if(!empty($forumtopics)):
?>
<br />

     <table class="tableforum" cellpadding="0" cellspacing="0" border="0">
       <tr class="header">
        	<td colspan="2" style="text-align:left; padding-left:15px;" width="250"<?php echo $leftroundclass;?>>
            	Topics            </td>
            <td width="50">
            	Replies            </td>
            <td width="80">
            	Author            </td>
            <td width="50">
            	Views            </td>
            <td width="100"<?php echo $rightroundclass; ?>>
            	Last post            </td>
        </tr>
<?php
$i = 0;
$all = count($forumtopics);
foreach ($forumtopics as $forumtopic):
	$class = ' class="even';
	if ($i++ % 2 == 0) {
		$class = ' class="odd';
	}
	if ($i >= $all) {
		$class .= ' last';
	}
	$class .='"';

	$pagenum = ceil ( ($forumtopic ['Forumtopic'] ['repliescounter'] + 1) / 10 );

?>
	<tr<?php echo $class;?> id="topic_<?php echo $forumtopic['Forumtopic']['id']; ?>">
        	<td align="center" valign="middle" width="250">
            	<img src="<?php echo STATIC_BPONG?>/img/newposts.gif" alt="New posts" />            </td>
            <td width="200">
            	<h3>
                	 <?php
                	 	echo $this->Html->link($forumtopic['Forumtopic']['name'], array('controller'=> 'forumposts', 'action'=>'index', $forumtopic['Forumtopic']['forumbranch_id'], $forumtopic['Forumtopic']['id']));
                	 ?>
                </h3>
       			<p class="announce">
					<?php
						echo $forumtopic['Forumtopic']['description'];
					?>
				</p>

                <br />
            			<?php
                                	$editimage = $this->Html->image(STATIC_BPONG."/img/smalledit.gif",array('style' => 'padding:5px;', 'alt' => 'edit'));
	                            	$deleteimage = $this->Html->image(STATIC_BPONG."/img/smalldelete.gif",array('style' => 'padding:5px;', 'alt' => 'delete'));

	                            	if($UpdatedTopic=='ALL' || ($UpdatedTopic=='OWNER' && $userID==$forumtopic['Forumtopic']['user_id'])):
	                            		echo $this->Html->link( $editimage, array( 'action'=> 'edit', $forumtopic['Forumtopic']['id']), array(), false, false);
	                            	endif;
	                            	if($DeletedTopic=='ALL' || ($DeletedTopic=='OWNER' && $userID==$forumtopic['Forumtopic']['user_id'])):
	                            		echo $this->Html->link( $deleteimage, array( 'action'=> 'delete', $forumtopic['Forumtopic']['id']), array(), sprintf('Are you sure you want to delete "%s"?', $forumtopic['Forumtopic']['name']), false);
	                            	endif;

	                            	unset($deleteimage);
	                            	unset($editimage);
	                            	if ( $MoveTopic=='ALL' || ($MoveTopic=='OWNER' && $userID==$forumbranch['Forumtopic']['forumbranch_id'])):
	                            	     echo $this->Form->select("Forum.move_position", $alltrees ['All'], $forumtopic['Forumtopic']['forumbranch_id'], array('escape' => false, 'style'=>'width: 100%;', 'onchange' => 'movebranch(' . $forumtopic['Forumtopic']['id'] . ', this.value, \'topic\');'), false);
	                            	endif;

	                            	echo $this->Html->image(STATIC_BPONG."/img/loading.gif", array(	  'style' => 'display:none;'
	                            											, 'border' => '0'
	                            											, 'name' => 'loading' ));

            			?>            </td>
            <td width="50" align="center" valign="middle">
            	<?php echo $forumtopic['Forumtopic']['repliescounter']; ?>            </td>
            <td width="80" align="center" valign="middle">
            	<?php
            		echo $this->Html->link($forumtopic['User']['lgn'], '/u/' . $forumtopic['User']['lgn']);
					?>

            	</td>
            <td width="50" align="center" valign="middle">
            	<?php echo $forumtopic['Forumtopic']['viewcounter']; ?>            </td>

            <td width="100" class="latest">
			<?php
				if (!empty($forumtopic['Lastpost']['id'])) {
				echo $this->Time->niceShort($forumtopic['Lastpost']['modified'])
						. " by "
						. $this->Html->link($forumtopic['Lastpost']['User']['lgn'], array('controller'=> 'users', 'action'=>'view', rawurlencode(htmlentities($forumtopic ['Lastpost'] ['User'] ['lgn'], ENT_QUOTES, "UTF-8"))), array('escape' => false), false, false)
						. $this->Html->link("Latest post", array('controller'=> 'forumposts', 'action'=>'index', $forumtopic['Forumtopic']['forumbranch_id'], $forumtopic['Forumtopic']['id'], "page:" . $pagenum, "#post_" . $forumtopic['Lastpost']['id']), array("class" => "viewlatest"), false, false);
				} else {
					echo "No post";
				}

			?>            </td>
	</tr>
<?php endforeach; ?>
    </table>
    <div class="clear"></div>
<?php if( $this->Paginator->params['paging']['Forumtopic']['pageCount'] > 1 ): ?>
<div class="paging">
		<?php echo $this->Paginator->prev($this->Html->image(STATIC_BPONG.'/img/prev.gif', array('alt' => 'prev')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
	 	<?php echo $this->Paginator->first(3, $paginate_params, null, array('class'=>'disabled'));?>
 	 	<?php echo $this->Paginator->numbers( $paginate_params );?>
        <?php echo $this->Paginator->last(3, $paginate_params, null, array('class'=>'disabled'));?>
		<?php echo $this->Paginator->next($this->Html->image(STATIC_BPONG.'/img/next.gif', array('alt' => 'next')), $paginate_params, null, array('class'=>'disabled', 'escape'=>false));?>
		<?php unset($paginate_params); ?>
</div>
<?php endif; ?>

<?php
	endif;
	} else {
?>
<div class="error">There is no subbranches and topics</div>
<?php
	}
?>
</div>
</div>
<div class="actions">
	<ul>
    	<li><span class="backbtn"><?php echo $this->Html->link('Forums', array('controller'=> 'forumbranches', 'action'=>'index'), array('class'=>'backbtn')); ?></span></li>
		<?php if($CreatedTopic): ?>
		<li><span class="addbtn"><?php echo $this->Html->link('Add topic', array('action'=>'add', $this->request->data['Forumbranches']['id']), array('class'=>'addbtn')); ?></span></li>
		<?php endif; ?>
		<?php if($CreatedForum): ?>
		<li><span class="addbtn"><?php echo $this->Html->link('Add forum', array('controller'=> 'forumbranches', 'action'=>'add', $this->request->data['Forumbranches']['id']), array('class'=>'addbtn')); ?></span></li>
		<?php endif; ?>
	</ul>
</div>