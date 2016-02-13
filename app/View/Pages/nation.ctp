 <img src="<?php echo STATIC_BPONG?>/img/nation_main.gif" alt="Nation" class="left" style="margin:0px -7px;" />
<div class="store" style="height:153px"> <a href="<?php echo Router::url(array('controller' => 'StoreCategories', 'action' => 'front_show','beer-pong-tables')); ?>"><img src="<?php echo STATIC_BPONG?>/img/buy_now_banner.jpg" alt="Store" border="0" /></a></div>
<!-- *********** EOF store *********** -->
<a href="http://www.facebook.com/BPONG"><img src="/img/bpong_facebook.jpg" alt="" border="0" class="right" style="margin:0px -6px 0px 0px;" /></a> <br />
<div class="clear"></div>
<div class="redlinenation"></div>
<div class="fright" style='float:right;'>
  <div class="pongtable">
    <h6>Random Table</h6>
    <?php
		echo $this->Html->image(IMG_MODELS_URL ."/" . $pongtable['Tableimage']['filename'], array ('class'=>'random_table'));
	?>
    <div class="pongtable2">
      <?php
	echo $this->Html->link('View All', array('controller' => 'pongtables', 'action' => 'index')) . "&nbsp;";
	echo $this->Html->link('Submit', "mailto:tables@bpong.com");
	?>
      <br />
      <?php
	echo $pongtable['Pongtable']['description'];
	?>
    </div>
    <!-- eof pongtable -->
  </div>
  <?php /*?><div class="games"> <a href="javascript:void(0);" onclick="window.open('/game/multi_demo.php', 'Multi_Window', 'width=380, height=574, left=0, top=0, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, toolbar=no, fullscreen=no')"  ><img src="<?php echo STATIC_BPONG?>/img/pic.gif" alt="multi" width="123px" height="30px" border="0" /></a> <a href="javascript:void(0);" onclick="window.open('/game/playgame.php', 'Game_Window', 'width=360, height=420, left=0, top=0, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, toolbar=no, fullscreen=no')" ><img src="<?php echo STATIC_BPONG?>/img/pic.gif" alt="single" width="123px" height="30px" border="0" /></a> </div>
	<?php */ ?>
</div>
<br />
<div class="nation_blog">
  <!-- Bpong Blog content -->
  <h2><?php echo $blogPost['Blogpost']['title']; ?></h2>
  <small>Posted: <?php echo $this->Time->niceShort($blogPost['Blogpost']['created']); ?> by <?php echo $this->Html->link($blogPost['User']['lgn'], array('controller' => 'users', 'action'=>'view',$blogPost['User']['lgn'])); ?> </small>
  <p> <?php echo $this->Text->truncate($blogPost['Blogpost']['description'],600,array('ending' => '&nbsp'.$this->Html->link('read more', Router::url(array('controller' => 'blogposts'))), 'html' => true)); ?> </p>
  <!-- Bpong Blog content END -->
</div>
<div style="float:left" class="nation_topics">
  <table>
    <tr>
      <td>Active Forum Threads</td>
      <td>Author</td>
      <td>Views</td>
      <td>Replies</td>
      <td>Last post</td>
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
	$slug_to_topic = $this->Forumlinks->generate_last_post_url_for_branch ( $forumtopic ['Forumtopic'] ['slug'], $forumtopic ['Forumbranch'] ['lft'], $forumtopic ['Forumbranch'] ['rght'] );

?>
    <tr<?php echo $class;?> id="topic_<?php echo $forumtopic['Forumtopic']['id']; ?>">
      <td style="font-size:10px"><?php
                	 	echo $this->Html->link(html_entity_decode($forumtopic['Forumtopic']['name']), array('controller'=> 'forumposts', 'action'=>'index', $slug_to_topic));
                	 ?>
      </td>
      <td align="center" valign="middle"><?php
					echo $this->Html->link($forumtopic['User']['lgn'], array('controller'=> 'users', 'action'=>'view', rawurlencode(htmlentities($forumtopic ['User'] ['lgn'], ENT_QUOTES, "UTF-8"))), array(), false, false)
            	?>
      </td>
      <td align="center" valign="middle"><span style="font-weight:normal"><?php echo $forumtopic['Forumtopic']['viewcounter']; ?></span> </td>
      <td align="center" valign="middle"><span style="font-weight:normal"><?php echo $forumtopic['Forumtopic']['repliescounter']; ?></span> </td>
      <td class="latest"><?php
				if (!empty($forumtopic['Lastpost']['id'])) {
					echo $this->Html->link($forumtopic['Lastpostuser']['lgn'], array('controller'=> 'forumposts', 'action'=>'index', $slug_to_topic, "page:" . $pagenum, "#post_" . $forumtopic['Lastpost']['id']), array(), false, false);
				}
			?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <div style="text-decoration:underline; padding:0 0 5px 30px; margin-top:-9px; color:#d61c20">
    <?php
	echo $this->Html->link("View All Topics", array('controller' => 'forumbranches', 'action' => 'index'));
?>
  </div>
</div>
