<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/votes/votes.js'></script>
<script type="text/javascript">
	$(document).ready(function() {
		customPreLoadPiece("/albums/image_albums_list/0/Organization/<?php echo $organization['Organization']['id'];?>","#imageAlbumsList", 'paginationImageAlbums', 'imageAlbumsLoader');
		customPreLoadPiece("/albums/video_albums_list/0/Organization/<?php echo $organization['Organization']['id'];?>","#videoAlbumsList", 'paginationVideoAlbums', 'videoAlbumsLoader');
	});
</script>

<div id="tab-photos">
	<h2>Photo Albums</h2>
	<div id="imageAlbumsList"><?php echo $this->requestAction('/albums/image_albums_list/Organization/' . $organization['Organization']['id']); ?></div>
	<div class='imageAlbumsLoader' style='height:10px;' class='clear'></div>
	
	<div class='clear'></div>
	
	<h2>Video Albums</h2>	
	<div id="videoAlbumsList"><?php echo $this->requestAction('/albums/video_albums_list/Organization/' . $organization['Organization']['id']);  ?></div>
	<div class='videoAlbumsLoader' style='height:10px;' class='clear'></div>
</div>