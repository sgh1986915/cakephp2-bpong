<?php
	$submissionsCount = $lastLinks['lastCount'] + $lastImages['lastCount'] + $lastVideos['lastCount'];
	if (!empty($lastImages['lastImage']['Image']['modified'])) {
		$imageTimeStamp = strtotime($lastImages['lastImage']['Image']['modified']);
	} else {
		$imageTimeStamp = 0;
	}
	if (!empty($lastVideos['lastVideo']['Video']['modified'])) {
		$videoTimeStamp = strtotime($lastVideos['lastVideo']['Video']['modified']);
	} else {
		$videoTimeStamp = 0;
	}
	if ($videoTimeStamp > $imageTimeStamp) {
		$lastSubmission = 'video';
	} else {
		$lastSubmission = 'image';
	}
?>
<div class="simplecol">
	<ul class="userstat">
		<li>
			<h4><a href="/all_submissions/">New Submissions</a></h4>
				<?php if ($submissionsCount > 0):?>
			    <a href="/all_submissions/" class="uibox subscr hmiddle_text"><?php echo $submissionsCount;?></a>
			    <?php else:?><a href="/all_submissions/" class="uibox subscr hlittle_text">Latest Submissions</a><?php endif;?>
		</li>
		<li>
			<h4><a href="/events/results/">New Topurnament Results</a></h4>
				<?php if ($lastEventResults > 0):?>
			    <a href="/events/results/" class="uibox res hmiddle_text"><?php echo $lastEventResults;?></a>
			    <?php else:?><a href="/events/results/" class="uibox res hlittle_text">Latest Results</a><?php endif;?>
		</li>
		<li>
			<h4><a href="/events">Upcoming Tournaments</a></h4>
				<?php if ($lastEvents['lastCount'] > 0):?>
			    <a href="/events/results/" class="uibox upc hmiddle_text"><?php echo $lastEvents['lastCount'];?></a>
			    <?php else:?><a href="/events/results/" class="uibox upc hlittle_text">Latest Tournaments</a><?php endif;?>
		</li>
		<li>
			<h4><a href="/submit">Upload your own content</a></h4>
			<a href="/submit" class="uibox cont">
				Upload
				<span>your own content</span>
			</a>
		</li>
	</ul>
</div>