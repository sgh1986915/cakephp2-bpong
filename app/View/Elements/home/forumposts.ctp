
<div class="sbox">
	<h3  class='thin_h'>From the Forums</h3>
	<div class="overflow">
		<table cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>Topic</th>
					<th>Replies</th>
					<th>Last post</th>
				</tr>
			</thead>
			<tbody>
			  <?php
				$i   = 0;
				$all = count($forumtopics);
				
				foreach ($forumtopics as $forumtopic):
					$class = '';
					if ($i++ % 2 == 0) {
						$class = ' class="alt"';
					}
				
					$pagenum = ceil ( ($forumtopic ['Forumtopic'] ['repliescounter'] + 1) / 10 );
				?>

				<tr <?php echo $class;?>>
					<td><?php 	echo $this->Html->link($this->Text->truncate($forumtopic['Forumtopic']['name'], 30), array('controller'=> 'forumposts', 'action'=>'index', $forumtopic['Forumtopic']['slug_to_topic'])); ?></td>
					<td><span style="font-weight:normal"><?php echo $forumtopic['Forumtopic']['repliescounter']; ?></td>
					<td><?php echo $this->Time->niceShort($forumtopic['Lastpost']['created']);?> by <a href="/u/<?php echo $forumtopic ['Lastpostuser']['lgn'];?>"><?php echo $forumtopic ['Lastpostuser']['lgn'];?></a>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<!-- EOF sbox -->
