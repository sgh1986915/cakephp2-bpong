<h2>BPONG.COM Authorization </h2>
<strong>Please select type of Authorization:</strong>
<br/>
	<br/>
	<b>1.</b> Do you already have a BPONG.COM account?  If so, we recommend that you link your existing BPONG account with your Twitter account on the BPONG site by authorizing below.  This will allow you to sign into the BPONG.COM website with either your BPONG account information and/or your Twitter account information.
	<br/><br/>
	  <?php echo $this->Form->create('User',array('id'=>'TWLogin','name'=>'Login','url'=>'/users/twitter_connect_finish/login/'));?>
	  <?php
			echo $this->Form->input('userlogin',array('legend'=>'','width'=>'100','label'=> 'User Name or Email','value'=>''));	?>
	  <?php echo $this->Form->input('userpwd',array('type'=>'password','label'=>'Password'));	?>
	<div style='margin-top:10px;'><a href="#" onclick='return $("#TWLogin").submit();'><img src="/img/buttons/authorize.gif" border="0"></a></div>
	  </form>
	<br/><br/>
	
	<b>2. Create new Bpong.com user</b>
	<br/>
	<?php echo $this->Form->create('User',array('id'=>'TWNew','name'=>'new','url'=>'/users/twitter_connect_finish/new/'));?>
	<?php echo $this->Form->input('email',array('legend'=>'','width'=>'100','label'=> 'Email','value'=>''));?>
	<div style='margin-top:10px;'><a href="#" onclick='return $("#TWNew").submit();'><img src="/img/buttons/create_authorize.gif" border="0"></a><br/></div>
	</form>

<div class='clear'></div>
<br/><br/><br/>