
<h1>Edit Meta-tags</h1>

<form method="post" action="/metatags/save">
<div class="editcontent">
    <?php echo $this->Form->hidden('Metatag.id'); ?>
    <?php echo $this->Form->input(
                  'Metatag.name'
                 ,array(
                     'label' => 'Name: '
                  ,'div'   => array('class' => 'input')
                  ,'readonly' => true
                  ,'size'  => '50'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
                  'Metatag.action'
                 ,array(
                     'label' => 'Action: '
                  ,'div'   => array('class' => 'input')
                  ,'readonly' => true
                  ,'size'  => '50'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
                  'Metatag.title'
                 ,array(
                     'label' => 'Title: '
                  ,'div'   => array('class' => 'input')
                  ,'size'  => '50'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
                  'Metatag.keywords'
                 ,array(
                     'label' => 'Keywords: '
                  ,'div'   => array('class' => 'input')
                  ,'type'  => 'textarea'
                  ,'cols'  => '40'
                  ,'rows'  => '5'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
                  'Metatag.author'
                 ,array(
                     'label' => 'Author: '
                  ,'div'   => array('class' => 'input')
                  ,'size'  => '50'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
                  'Metatag.description'
                 ,array(
                     'label' => 'Description: '
                  ,'div'   => array('class' => 'input')
                  ,'type'  => 'textarea'
                  ,'cols'  => '40'
                  ,'rows'  => '5'
                  )//array
               )//input
    //input ?>
    <?php echo $this->Form->input(
              'Metatag.language_id'
              ,array(
                  'label'    => 'Language: '
                 ,'div'      => array('class' => 'input')
                 ,'type'     => 'select'
                 ,'options'  => $this->Language->availableLangs()
                 ,'selected' => $this->request->data['Metatag']['language_id']
                 ,'showEmpty' => false
              )//array
            );//input ?>
    <?php echo $this->Form->submit('Save', array('id' => 'save')) ?>
</div>
</form>

