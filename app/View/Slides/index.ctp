<div class="slides index">
    <h2><?php echo __('Slides'); ?></h2>
    <table cellpadding="0" cellspacing="0">
	<tr>
	    <th>#</th>
	    <th>title</th>
	    <th>url</th>
	    <th>ordering</th>
	</tr>
	<?php
	$i = 0;
	if (!empty($slides)) :
	    foreach ($slides as $slide):
		$class = null;
		if ($i++ % 2 == 0) {
		    $class = ' class="altrow"';
		}
	    ?>
		<tr<?php echo $class; ?>>
		    <td><?php echo $this->Html->link($i, array('action' => 'edit', $i)); ?></td>
		    <td><?php echo $this->Html->link($slide['Slide']['title'], array('action' => 'edit', $slide['Slide']['id'])); ?></td>
		    <td><?php echo $slide['Slide']['url']; ?>&nbsp;</td>
		    <td class="ordering">
			<span>
			    <?php if ($i > 1) : ?>
				<a href="slides/move/<?php echo $slide['Slide']['id'] ?>/up" title="up"><?php echo $this->Html->image('order-arrow-up.png'); ?></a>
			    <?php endif; ?>
			    &nbsp;
			</span>
			<span>
			    <?php if ($i < count($slides)) : ?>
				<a href="slides/move/<?php echo $slide['Slide']['id'] ?>/down" title="down"><?php echo $this->Html->image('order-arrow-down.png'); ?></a>
			    <?php endif; ?>
			    &nbsp;
			</span>
		    </td>
		</tr>
	    <?php endforeach; ?>
	<?php endif; ?>
    </table>
</div>