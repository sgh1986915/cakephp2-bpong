
<h1>Edit Language</h1>

<form method="post" action="">
<div id="saveform">
 
    <?php echo $this->Form->input('Language.name',array(
         'label' => 'Name'
        ,'size'  => '35'
    ))//input ?>
    
    <?php echo $this->Form->input('Language.code', array(
         'label' => 'Code'
        ,'size'  => '5'
    ))//input ?>
    
    <?php echo $this->Form->input('Language.nationalname', array(
         'label' => 'National name'
        ,'size'  => '35'
    ))//input ?>

    <div class="clear"></div>
    
    <?php echo $this->element('submit-cancel') ?>

</div>
</form>
