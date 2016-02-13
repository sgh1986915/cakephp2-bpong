<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'User')); ?>
<div class="users index">
<br/>
<?php echo $this->Form->create('User',array('id'=>'UserFilter','name'=>'UserFilter', 'url' => '/Users/show_all', 'class' => 'userfilter'));?>
    <fieldset>
		<?php echo $this->Form->input('UserFilter.lgn', array('label' => 'Login:'));?>
		<?php echo $this->Form->input('UserFilter.searchby',array('type' => 'select','label'=>'Search By:','options' => array('AND'=>'AND','OR'=>'OR')));?>
		<?php echo $this->Form->input('UserFilter.firstname', array('label' => 'First Name:'));?>
		<?php echo $this->Form->input('UserFilter.lastname', array('label' => 'Last Name:'));?>
		<div class='submit'>
			<input type="submit" value="Filter"/>
		</div>
    <?php /*?><table>
        <tr>
        	<td><div class="input text"><?php echo $this->Form->input('UserFilter.lgn', array('label' => 'Login', 'div' => false));?></div></td>
        	<td><?php echo $this->Form->input('UserFilter.searchby',array('type' => 'select','label'=>'Search By','options' => array('AND'=>'AND','OR'=>'OR')));?></td>
        </tr>
       <tr>
       		<td><?php echo $this->Form->input('UserFilter.firstname', array('label' => 'First Name'));?></td>
        	<td><?php echo $this->Form->input('UserFilter.lastname', array('label' => 'Last Name', 'div' => false));?></td>
        </tr>

       <tr>
       		<td><?php echo $this->Form->input('UserFilter.provincestate_id',array('type' => 'select','label'=>'State','options' => $states, 'style' => 'width:220px;'));?></td>
        	<td><?php echo $this->Form->input('UserFilter.city',array('label'=>'City')); ?></td>
        </tr>


    </table>
    </fieldset>
  <?php echo $this->Form->end('Filter');?><?php */ ?>
  	<div class="clear"></div>
  	</fieldset>
  </form>
  <table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><strong>Avatar</strong></th>
      <th><?php echo $this->Paginator->sort('Login', 'lgn', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Name', 'firstname', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Created', 'created', array('sorter' => true));?></th>
    </tr>
    <?php
$i = 0;
foreach ($users as $user):
  $class = null;
  if ($i++ % 2 != 0) {
    $class = ' class="gray"';
  }
?>
    <tr<?php echo $class;?>>
      <td style='padding:3px 0px;'><?php if ($user['User']['avatar']):?><?php echo $this->Image->avatar($user['User']['avatar']);?><?php endif;?></td>
      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
      <td><?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?> </td>
      <td><?php  echo $this->Time->niceShort($user['User']['created'])?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php echo $this->element('simple_paging');?>
</div>
