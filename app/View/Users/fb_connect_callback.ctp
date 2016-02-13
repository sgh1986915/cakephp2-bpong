<h2>BPONG.COM Authorization </h2>
<strong>Please select type of Authorization:</strong>
<br/>
<?php if (!empty($likeUserInfo)):?>
	<br/>
	BPONG recommends that if you choose option #1 below so that your BPONG account will be merged with your Facebook account.  
	This will allow you to sign into the BPONG.COM website with either your BPONG account information and/or your Facebook account information.
	<br/><br/>
	<b>1. Authorize me as BPONG.COM user <a href="/u/<?php echo $likeUserInfo['User']['lgn'];?>" TARGET="_blank"><?php echo $likeUserInfo['User']['lgn']?></a></b> (email: <?php echo $likeUserInfo['User']['email']?>)
	<br/>
	<div style='margin-top:10px;'><a href="/users/fb_connect_finish/user/<?php echo $likeUserInfo['User']['id']?>"><img src="/img/buttons/authorize.gif" border="0"></a></div>
	<br/><br/>
	
	<b>2. Authorize me as another BPONG.COM user: </b>
	<br/>
	  <?php echo $this->Form->create('User',array('id'=>'FBLogin','name'=>'Login','url'=>'/users/fb_connect_finish/login/'));?>
	  <?php
			echo $this->Form->input('userlogin',array('legend'=>'','width'=>'100','label'=> 'User Name or Email','value'=>''));	?>
	  <?php echo $this->Form->input('userpwd',array('type'=>'password','label'=>'Password'));	?>
	<div style='margin-top:10px;'><a href="#" onclick='return $("#FBLogin").submit();'><img src="/img/buttons/authorize.gif" border="0"></a></div>
	  </form>
	<br/><br/>
	
	<b>3. Create new BPONG.COM user:</b>
	<br/>
	<?php echo $this->Form->create('User',array('id'=>'FBNew','name'=>'new','url'=>'/users/fb_connect_finish/new/' . $likeUserInfo['User']['id']));?>
	<?php echo $this->Form->input('email',array('legend'=>'','width'=>'100','label'=> 'Email','value'=>''));?>
	<div style='margin-top:10px;'><a href="#" onclick='return $("#FBNew").submit();'><img src="/img/buttons/create_authorize.gif" border="0"></a></div>
	</form>
<?php else:?>
	<br/>
	<b>1.</b> Do you already have a BPONG.COM account?  If so, we recommend that you link your existing BPONG account with your Facebook account on the BPONG site by authorizing below.  This will allow you to sign into the BPONG.COM website with either your BPONG account information and/or your Facebook account information.
	<br/><br/>
	  <?php echo $this->Form->create('User',array('id'=>'FBLogin','name'=>'Login','url'=>'/users/fb_connect_finish/login/'));?>
	  <?php
			echo $this->Form->input('userlogin',array('legend'=>'','width'=>'100','label'=> 'User Name or Email','value'=>''));	?>
	  <?php echo $this->Form->input('userpwd',array('type'=>'password','label'=>'Password'));	?>
	<div style='margin-top:10px;'><a href="#" onclick='return $("#FBLogin").submit();'><img src="/img/buttons/authorize.gif" border="0"></a></div>
	  </form>
	<br/><br/>
	
	<b>2. Create new Bpong.com user</b>
	<br/>
	<div style='margin-top:10px;'><a href="/users/fb_connect_finish/new/"><img src="/img/buttons/create_authorize.gif" border="0"></a><br/></div>
<?php endif;?>

<div class='clear'></div>
<br/><br/><br/>