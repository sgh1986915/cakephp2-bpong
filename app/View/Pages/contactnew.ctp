   
<?php echo  $this->Html->css(array(STATIC_BPONG.'/css/accordion/ui.accordion.css',STATIC_BPONG.'/css/accordion/ui.theme.css'))
                  .$this->Html->script(array(STATIC_BPONG.'/js/jquery.ui.min.js',STATIC_BPONG.'/js/ui.accordion.js'))
 ?>
   
  
  
 <script type="text/javascript">
      $(document).ready(function(){

         $("#accordion").accordion({
        	autoHeight: false,
        	active: <?php echo $accordionActive;?> 
        });

        $("#AdvertingAccordion").accordion({
            autoHeight: false,
            active: <?php echo $advertingActive;?> 
            });

        $("#MerchandiseAccordion").accordion({
        	fillSpace: true,
        	active: <?php echo $merchandiseActive;?>
        });
        
      });
  </script>
 
 <div id="accordion">
 
 	<h3><a href="#">Adverting/Marketing</a></h3>
	<div  >
		 <!-- Sub accordion -->
			<div id="AdvertingAccordion"  > 
					<h3><a href="#">Sponsorship inquiries relating to your company sponsoring a BPONG/WSOBP event</a></h3>
	               	<div >
                	    <?php echo $this->element('contact/simple',array('name'=>"Sponsorship"));?>
                	</div>
					<h3><a href="#">Advertising, marketing, or cross-promotional proposals for BPONG to consider</a></h3>
                	<div >
                	     <?php echo $this->element('contact/simple',array('name'=>"Advertising"));?>
                	</div>                	
			</div>
		<!-- EOF accordion -->	
	</div>
 
 <!-- ================================== -->
	<h3><a href="#">Merchandise Sales</a></h3>
	<div>
		 <!-- Sub accordion -->
			<div id="MerchandiseAccordion">
					<h3><a href="#">Pre-Sale Inquiries</a></h3>
                	<div>
                	       <?php echo $this->element('contact/simple',array('name'=>"PreSale"));?>
                	</div>
					<h3><a href="#">Problems with Orders</a></h3>
                	<div>
                	  <?php echo $this->element('contact/withorder',array('name'=>"ProblemswithOrders"));?>
                	</div>
                	
                	<h3><a href="#">Cancel or Change a Merchandise Order (may not be possible if order has already shipped)</a></h3>
                	<div>
                	      <?php echo $this->element('contact/withorder',array('name'=>"CancelOrder"));?>
                	</div>
                	
                	<h3><a href="#">Drop Ship Program</a></h3>
                	<div>
	                	<?php echo $this->element('contact/simple',array('name'=>"DropShipProgram"));?>
                	</div>
                	
                	<h3><a href="#">Affiliate Program</a></h3>
                	<div>
                	      <?php echo $this->element('contact/simple',array('name'=>"AffiliateProgram"));?>
                	</div>
                	
                	<h3><a href="#">Retail Sales</a></h3>
                	<div>
                			For inquiries related to purchasing BPONG products in bulk for resale
                			<?php echo $this->element('contact/simple',array('name'=>"Retail"));?>
                	</div>
			 </div>		
		<!-- EOF accordion -->
	</div> 
	<h3><a href="#">Tournament Results Issues</a></h3>
	<div> 
		<p>
		Tournament organizers aren't perfect, and it's possible that some of your game results were entered incorrectly, you were assigned to a team by mistake, etc. Please give us as much information as you can about the issue including a link to the affected tournament or team, and we'll do our best to resolve it.
		</p>
		<?php echo $this->element('contact/simple',array('name'=>"Results")); ?>
	</div>

	<h3><a href="#">Tournaments, including The World Series of Beer Pong and BPONG Tour</a></h3>
	<div>
			<p>
			Please use this form if you are interested in running a WSOBP Satellite Tournament:
		</p>
		    <?php echo $this->element('contact/simple',array('name'=>"Tournaments"));?>                		
    </div>
	
	<h3><a href="#">Tournament sign-up issues, billing issues relating to tournaments, sign-up changes, and questions relating to tournaments</a></h3>
	<div>
		
		<?php echo $this->element('contact/simple',array('name'=>"Signup"));?>
	</div>
	
	<h3><a href="#">Public Relations</a></h3>
	<div>
		<p>
		Please fill out the form for all public relation and press/media inquiries:
		</p>
		<?php echo $this->element('contact/simple',array('name'=>"PublicRelations"));?>
	</div>	
	
	
	<h3><a href="#">Rules Questions</a></h3>
	<div>
		<p>Please ask all rule-related questions on the BPONG forums</p>
	</div>	

	<h3><a href="#">Website</a></h3>
	<div>
		<p>Bug report, website registration issues, etc.</p>
	     <?php echo $this->element('contact/simple',array('name'=>"Website"));?>
	</div>

	<h3><a href="#">Miscellaneous/Other</a></h3>
	<div>
		<p>
		Cras dictum. Pellentesque habitant morbi tristique senectus et netus
		et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in
		faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia
		mauris vel est.
		</p>
		<?php echo $this->element('contact/simple',array('name'=>"Other"));?>
	</div>
	
</div>
 