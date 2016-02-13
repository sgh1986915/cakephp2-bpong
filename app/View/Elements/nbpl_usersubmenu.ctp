<?php if (isset($LoggedMenu) && !empty($LoggedMenu)): ?>
<ul>
								<li><a href="/u/<?php echo $userSession['lgn'];?>">Your profile</a></li>
								<li><a href="<?php echo MAIN_SERVER;?>/logout/">Logout</a></li>
			                    <li><a href="<?php echo MAIN_SERVER;?>/signups/mySignups">Signups</a></li>
			                    <li><a href="<?php echo MAIN_SERVER;?>/payments/myPayments">Payments</a></li>
			                    <li><a href="<?php echo MAIN_SERVER;?>/events/my">Events</a></li>
			                    <li><a href="<?php echo MAIN_SERVER;?>/venues/my">Venues</a></li>
			                    <li><a href="<?php echo MAIN_SERVER;?>/nation/beer-pong-teams/myteams">Teams</a></li>
			                    <?php if($userSession['promocodes']) : ?>
			                    	<li><a href="<?php echo MAIN_SERVER;?>/promocodes/showAssigned">Promocodes</a></li>
			                    <?php endif; ?>		                    
			                    <?php if($CSpanel) : ?>
			                    	<li><a href="<?php echo MAIN_SERVER;?>/cs/">CS Panel</a></li>
			                    <?php endif; ?>
			                    <li><a href="<?php echo MAIN_SERVER;?>/organizations/my">Organizations</a></li>
	
	
					
</ul>
<?php if (isset($AdminMenu) && !empty($AdminMenu)): ?>
								<div style='border-bottom:1px solid #E0E3E8;height:10px;width:100%;clear:both;width:100%;'>
				                <ul style='clear:both;padding-top:12px;'>
				                	<li><a href="<?php echo MAIN_SERVER;?>/users">Users</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/accessions">Permissions</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/statuses">User Statuses</a></li>

		                    		<li><a href="<?php echo MAIN_SERVER;?>/mailtemplates">@ Templates</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/payments">Payments</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/promocodes">Promocodes</a></li>

				                    <li><a href="<?php echo MAIN_SERVER;?>/events/all">Events</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/signups/showAllSignups">Signups</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/eventfeatures">Eventfeatures</a></li>

				                    <li><a href="<?php echo MAIN_SERVER;?>/venues">Venues</a></li>
				                    <li><a href="<?php echo MAIN_SERVER;?>/nation/beer-pong-teams/show-all-teams">Teams</a></li>
									<li><a href="<?php echo MAIN_SERVER;?>/rooms">Rooms</a></li>

				                 	<li><a href="<?php echo MAIN_SERVER;?>/history">History</a></li>
				                 	<li><a href="<?php echo MAIN_SERVER;?>/slides">Slider</a></li>
				                 	<li><a href="<?php echo MAIN_SERVER;?>/reports">Reports</a></li>
				                 	
                                    <li><a href="<?php echo MAIN_SERVER;?>/KnowledgeTopics/index">Knowledge Base</a></li>
				                 	<li><a href="<?php echo MAIN_SERVER;?>/Accessions/clear_cache">Clear Cache</a></li>
				                 	<li><a href="<?php echo MAIN_SERVER;?>/pages/cron_links">Cron Links</a></li>

				                 	<li><a href="<?php echo MAIN_SERVER;?>/logs">Logs</a></li>
				                 	<li><a href="<?php echo MAIN_SERVER;?>/Softwareregs">Soft. Registrations</a></li>             
                                    <li><a href="<?php echo MAIN_SERVER;?>/pages/update_stats">Update Stats</a></li>
                                    <li><a href="<?php echo MAIN_SERVER;?>/users/merge_accounts">Merge Accounts</a></li>                                    
                                 </ul>
				                 </div>
<?php endif;?>

<?php endif;?>
