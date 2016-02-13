<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/flashembed.min.js"></script>
<script type="text/javascript">
 flashembed("video",
        {
            src:'<?php echo STATIC_BPONG?>/files/FlowPlayerLight.swf',
            width: <?php echo $videoWidth - 0; ?>,
            height: <?php echo $videoHeight - 10; ?>,
            wmode: 'opaque'
        },

        {config: {
            autoPlay: true,
            autoBuffering: true,
            initialScale: 'orig',
            loop: false,
            autoRewind: true,
            videoFile: '<?php echo STATIC_BPONG?>/files/<?php echo $videoFile; ?>'    
        }}
    );
 function close_button(){
     tb_remove();
 }
</script>
<button onclick="close_button();"></button>
<div id="video"></div>