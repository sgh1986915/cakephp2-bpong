</form>
<script type="text/javascript">
	$(document).ready(function() {	
		tb_init('a.thickbox, area.thickbox, input.thickbox');
	});
	function ShowHideAssign(id) {
		$("#"+id).slideToggle('slow', function() {   } );
	}
	function  DeletePackage(packageID){
		if (confirm("Are you sure, you want to delete?")) {
			$("#form_loader").show();	
			//AJAX call for deleting Package
					$.post("/packages/delete/<?php echo $modelName; ?>/<?php echo $modelID; ?>/"+packageID
		               ,{
		               		packageID: packageID
		                }
		               ,function(response){
		               		if (response==""){
		               			$('#PackagesInformation').load("/packages/view/<?php echo $modelName.'/'.$modelID?>",{cache: false},function(){$('#form_loader').hide();});
		                	} else {
		                		alert(response);
		                	}
		                });
		}
	}

		function  DeletePackageDetail(detailID){	
			if (confirm("Are you sure, you want to delete?")) {
			$("#form_loader").show();	
			//AJAX call for deleting packageDetail
						$.post("/packagedetails/delete/<?php echo $modelName; ?>/<?php echo $modelID; ?>/"+detailID
			               ,{
			               		detailID: detailID
			                }
			               ,function(response){
			       				$("#form_loader").hide();
			               		if (response==""){
			                		$("#detail_"+detailID).hide("slow");
			                	} else {
			                		alert(response);
			                	}
			                });
			}
		}
	
</script>
<?php if(!empty($this->request->data)): ?>
<table id="address" cellspacing="0" style="width:640px !important;">
	<?php foreach ($this->request->data as $package):?>
    	<tr id="<?php echo "package_".$package['Package']['id'] ?>">
    		<td style="font-weight:bold;" width="55%"><?php echo $package['Package']['name']; echo ife(!empty($package['Package']['is_hidden'])," <font color='#ff0000'><SUP>Hidden</SUP></font>","") ?></td>
    		<td>	<a href="/packages/edit/<?php echo $modelName."/".$modelID."/".$package['Package']['id'];?>?&inlineId=EditPackage&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditPackage" title="Edit Package" >Edit</a>
    			&nbsp;
    			<a href="javascript: DeletePackage(<?php echo $package['Package']['id']; ?>);" >Delete</a>
    			&nbsp;
    			<a href="/packagedetails/add/<?php echo $modelName."/".$modelID."/".$package['Package']['id'];?>?&inlineId=EditPackage&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditPackage" title="Edit Package" >New Details</a>
			</td>
            </tr>
			<?php if ( !empty ($package['Packagedetail'] ) ) :?>
			<tr>	
				<td colspan="2">
					<table class="whitebg" cellspacing="0">
						 <?php foreach ($package['Packagedetail']  as $details):?>
						 			<tr id="<?php echo "detail_".$details['id'] ?>">
						 			<td><?php echo $this->Time->niceDate($details['start_date'] )?></td>
						 			<td><?php echo $this->Time->niceDate($details['end_date'] )?></td>
						 			<td><?php echo $details['price'] ?></td>
						 			<td><?php echo $details['price_team'] ?></td>
						 			<td>	<a href="/packagedetails/edit/<?php echo $modelName."/".$modelID."/".$package['Package']['id']."/".$details['id'];?>?&inlineId=EditPackage&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="EditPackage" title="Edit Package" >Edit</a>
                            			&nbsp;
                            			<a href="javascript: DeletePackageDetail(<?php echo $details['id']; ?>);" >Delete</a>
                        			</td>
						 			</tr>
						 <?php endforeach; ?> 
					</table>			
				</td>
			</tr>
			<?php endif; ?>
			<!-- HIDDEN PACKAGE -->
			<?php if (!empty($package['Package']['is_hidden'])):?>
    		<tr >
    			<td style="font-weight:bold;" width="55%">
    				Assigned Users
    			</td>
    			<td>    			
    			<a href="/packages/assignUser/<?php echo $modelName."/".$modelID."/".$package['Package']['id'];?>?&inlineId=AssignUser&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="AssignUser" title="Assign new user" >Assign new User</a>
    			<!-- <a href="javascript: ShowHideAssign('<?php echo "assignUser_".$package['Package']['id'] ?>');" >Assign new User</a> -->
    			</td>
    		</tr>
    		<?php if (!empty($package['User'])):?>
    		<tr><td colspan="2"> 
    		<table>
            	<tr>
            		<th nowrap="nowrap">Email</th>
            		<th nowrap="nowrap">Login</th>
            		<th nowrap="nowrap">First Name</th>
            		<th nowrap="nowrap">Last Name</th>
            		<th nowrap="nowrap">&nbsp;</th>
            	</tr>
            	<?php foreach ($package['User'] as $user): ?>
            	<tr>
            		<td><?php echo $user['email'] ?></td>
            		<td><?php echo $user['lgn'] ?></td>
            		<td><?php echo $user['firstname'] ?></td>
            		<td><?php echo $user['lastname'] ?></td>
            		<td><a  onclick="return confirm(&#039;Are you sure you want to remove?&#039;);"  href="/packages/removeUser/<?php echo $modelName."/".$modelID."/".$package['Package']['id']."/".$user['id'];?>">Remove</a></td>
            	</tr>
            	<?php endforeach; ?>
            </table>
    		</td></tr>
    		<?php endif; ?>	
        <?php endif;?>  
			<!-- EOF HIDDEN PACKAGE -->
			
    <?php endforeach; ?>
      
    </table>

<div style='clear:both; margin-bottom:10px;display:none;' id='form_loader'>    
	<img src="/img/loader_verify.gif" border="0" />
</div>

<?php endif; ?>
<a href="/packages/add/<?php echo $modelName."/".$modelID;?>?&inlineId=NewPackage&amp;height=500&amp;width=400&amp;modal=true;" class="thickbox" id="NewPackage" title="New Package" >New Package</a>
