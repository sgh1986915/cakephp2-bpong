<div class="rstext">
	<h3  class='thin_h'>From the NBPL Blogs</h3>
	<div class="row">
		<h4><?php echo $blogPosts['lastPost']['Blogpost']['title'];?></h4>
		<div class="postinfo">
			Posted: <?php echo $this->Time->niceShort($blogPosts['lastPost']['Blogpost']['created']);?> by <a href="/u/<?php echo $blogPosts['lastPost']['User']['lgn'];?>"><?php echo $blogPosts['lastPost']['User']['lgn'];?></a>
			<br /> 
			Last modified: <?php echo $this->Time->niceShort($blogPosts['lastPost']['Blogpost']['modified']);?>
		</div>
			<?php if (!empty($blogPosts['lastPost']['Image']['0']['filename'])):?>
			<a href="/nation/beer-pong-blog"><img src="<?php echo   IMG_MODELS_URL . "/thumbsBig_".$blogPosts['lastPost']['Image']['0']['filename']; ?>" class="img" alt="" border="0"/></a>
			<?php endif;?>
			<p>
				<?php echo $this->Formater->stringCut($blogPosts['lastPost']['Blogpost']['description'], '160', '... <a style="" href="/nation/beer-pong-blog/view/' . $blogPosts['lastPost']['Blogpost']['slug'] . '">more</a>');?>			
			</p>
	<div class='clear' style='height:9px;'></div>			
	<a href="/nation/beer-pong-blog/view/<?php echo $blogPosts['lastPost']['Blogpost']['slug'];?>">Comments:</a>&nbsp;<?php echo $blogPosts['lastPost']['Blogpost']['comments'];?>&nbsp;|&nbsp;<a href="/nation/beer-pong-blog">View all posts</a>
	</div>
	<!-- EOF row -->

</div>
<!-- EOF rstext -->
