<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

  <style type="text/css" media="all">@import "/css/style.css";</style>
  <link rel="stylesheet" type="text/css" href="/css/thickbox.css" />

  <!--[if lte IE 7]>
  <link rel="stylesheet" type="text/css" media="screen" href="/css/ie.css"/>
  <![endif]-->

  <!--[if lt IE 7]>
  <link rel="stylesheet" type="text/css" media="screen" href="/css/ie6.css"/>
  <![endif]-->

  <style type="text/css" media="print">@import "/css/print.css";</style>
  
  <script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/jquery.js"> </script>
  <script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/basic.js"> </script>
  <script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/thickbox.js"></script>

  
  
</head>
<body id="home">
<div id="main">
  <div id="logo" style="background-image: url(<?php echo STATIC_BPONG?>/img/content/results_delivered.png);" >
    <a href="/"><img src="<?php echo STATIC_BPONG?>/img/logo.png" onmouseover="this.src = '<?php echo STATIC_BPONG?>/img/logo_over.png'" onmouseout="this.src = '<?php echo STATIC_BPONG?>/img/logo.png'" alt="Reservation Maestro" /></a>
  </div>
  
  <div id="content">
     <ul id="menu" class="collapse">
      <li>
        <a class="link-parent collapse" style="cursor:pointer;">Home</a>

        <ul>
          <li><a  href="/pages/about">About Us</a></li>
          <li><a  href="/pages/contact">Contact Us</a></li>
          <li><a  href="/faq">FAQ</a></li>
          <li><a  href="/">Home</a></li>
          <li><a  href="/news">News</a></li>		  
        </ul>

      </li>
	  <li>
        <a class="link-parent collapse" style="cursor:pointer;">Who are you?</a>
        <ul>
          <li><a  href="/faq/restaurateur">Restaurateur?</a></li>
          <li><a  href="/faq/webportal">Website Manager?</a></li>
          <li><a  href="/faq/consumer">Consumer?</a></li>

        </ul>
	  			  			  	  	  
	  <!-- HOW DO I menu -->
	  	  <!-- end of how do I -->
	  
	<!-- RESTAURANTS  -->
		  	<!-- EOF Restaurants-->
	<!-- Admin PARTNERS  -->
		  		      <li>
		        <a class="link-parent collapse" style="cursor:pointer;">Partners</a>

		        <ul>
		          <li><a  href="/partners/management_overview">Partners</a></li>
		          <!--li><a  href="/faq/webportal">Website Manager?</a></li>
		          <li><a  href="/faq/consumer">Consumer?</a></li-->
		        </ul>
		      </li>
		  	<!-- EOF Admin PARTNERS-->
	<!-- WEB PARTNERS  -->
		  	<!-- EOF WEB PARTNERS -->

	<!-- SALES PARTNERS -->	  
			   
	<!-- EOF SALES PARTNERS -->	  
	<!-- PERSONS -->	  
		  	<!-- EOF PERSONS-->
	
	</ul>
    <div id="central">
	      <div id="user-options">
        <form id="select-language" name="selectlanguage" method="post">
          <label for="language">Choose your language</label>
		  		  <select class="lang_form" id="lng" name="lng" onchange="document.selectlanguage.submit();" disabled="disabled">

		  		  
		                <option value="1" selected="selected"  >English</option>			
		  		  
		                <option value="2"  >français</option>			
		  		  
		                <option value="3"  >español</option>			
		  	
          </select>
		  
        </form>
								<div id="login-block">
			<strong>
			Hello <span id='LoginUserName'>Mark</span>! <a href="/users/login">(not<span id='LoginUserName2'> Mark</span>?)</a>

			</strong><br />
			           Last login was:<br/>
			July 3rd. 2008			</div>
	        <a href="/users/logout" class="link-button"><span>Log out</span></a>			
		      </div> <!-- user-options -->
      <div id="inner-central">
   
                  <script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">
    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
		theme_advanced_toolbar_location : "top",
		content_css : "/css/stylesheet.css", 
        plugins : "ibrowser",
        theme_advanced_buttons3_add : "ibrowser",
		force_p_newlines: false,
		extended_valid_elements : "script[charset|defer|language|src|type]",				
        convert_urls : false
    });
</script> 

<h1>Edit Content   IAgreeWithPrice</h1>

<form method="post" name="EditContent" action="/admin/contents/edit">
    <input type="hidden" name="data[Content][token]"  value=" IAgreeWithPrice" id="ContentToken" />    <input type="hidden" name="data[Content][reURL]"  value="http://reservationmaestro/admin/contents" id="ContentReURL" />    <table align="center" width="80%">	
	
	  <tr>
	  <td>
        Title:
	  </td>	
	  <td>

        <input name="data[Content][title]"  size="70" class="cont17" value=" IAgreeWithPrice" type="text" id="ContentTitle" />        	  </td>	 
    </tr>
    <tr>
	  <td>
        Body:
	  </td>	
	  <td>
	    <BR>
		<textarea name="data[Content][content]"  rows="20" cols="66" id="ContentContent">I agree to the price per Reservation as described above.</textarea>		<BR>

	  </td>	 
    </tr>
    <tr>   
	  <td>
        Language:
	  </td>	
	  <td>
		<select name="data[Content][id_language]"  id="ContentIdLanguage">
<option value="1"  selected="selected">English</option>
<option value="2" >français</option>
<option value="3" >español</option>

</select>	  </td>	 
    </tr>
    <tr>
	<td>&nbsp;</td>
	<td align="right">
		<a href="javascript:document.EditContent.submit()" class="link-button"   style="float:right;"><span>Update</span></a>
				<input type="submit" style="display:none;"/>							
	</td>
    </tr>

</table>	
</form>
 
	            <div class="clear">&nbsp;</div>
	  </div> <!-- inner-central -->
      <br clear="all" />
    </div> <!-- central -->
  </div>
  
  <ul id="footer">
      <li><a href="/">Home</a></li>

    <li><a href="/pages/contact">Contact Us</a></li>
    <li><a href="/faq">FAQ</a></li>
    <li><a href="/news">News</a></li>
    <li><a href="/pages/about">About Us</a></li>
    	    <li><a href="/admin/settings">Administrative</a></li>
		<li><a href="/news/rss/1"><img src="/img/rss.png" /></a></li>	
	
  </ul>

  <div id="copyright">
    <div>Copyright &copy; 2008 Maestro Results</div>
    <a href="/ajaxes/privacypolcy?height=630&width=600&modal=true&TB_iframe=true" class="thickbox" id="PrivacyPolcy"><span id='content60' class=" thickbox editable"  onclick="tb_show('name','/contents/ajaxeditcontent?height=550&width=600&id=60&modal=true&TB_iframe=true', '500');$('#TB_load').remove();return false;">Privacy Policy</span></a> | <a href="/ajaxes/terms?height=630&width=600&modal=true&TB_iframe=true" class="thickbox" id="PrivacyPolcy"><span id='content0' class=" thickbox editable"  onclick="tb_show('name','/contents/ajaxeditcontent?height=550&width=600&id=0&modal=true&TB_iframe=true', '500');$('#TB_load').remove();return false;">Terms &amp; Conditions</span></a>
  </div>

</div>
</body>
</html>
