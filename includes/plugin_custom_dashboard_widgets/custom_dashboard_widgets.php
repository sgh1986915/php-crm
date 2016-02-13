<?php

// upload this file to includes/plugin_custom_dashboard_widgets/custom_dashboard_widgets.php

class module_custom_dashboard_widgets extends module_base{

    public static function can_i($actions,$name=false,$category=false,$module=false){
        if(!$module)$module=__CLASS__;
        return parent::can_i($actions,$name,$category,$module);
    }
	public static function get_class() {
        return __CLASS__;
    }
    public function init(){
		$this->links = array();
		$this->module_name = "custom_dashboard_widgets";
		$this->module_position = 1;
        $this->version = 1;

		hook_add( 'dashboard_widgets', 'module_custom_dashboard_widgets::my_widgets' );
    }

	public static function my_widgets() {
		$widgets = array();

		// start first widget:
		ob_start();
		?>
		<div>
<!DOCTYPE html>
<html>

<body>

<!-- Step 1: Create the containing elements. -->

<section id="auth-button"></section>

<section id="timeline"></section>


<!-- Step 2: Load the library. -->

<script>
(function(w,d,s,g,js,fjs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
  js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
}(window,document,'script'));
</script>

<script>
gapi.analytics.ready(function() {

  // Step 3: Authorize the user.

  var CLIENT_ID = '59170461002-nqkcj3qpd6520i6kqhl93clq3l9mhqgr.apps.googleusercontent.com';

  gapi.analytics.auth.authorize({
    container: 'auth-button',
    clientid: CLIENT_ID,
	userInfoLabel:""
  });

  // Step 5: Create the timeline chart.
  var timeline = new gapi.analytics.googleCharts.DataChart({
    reportType: 'ga',
    query: {
      'dimensions': 'ga:sourceMedium',
      'metrics': 'ga:sessions,ga:goalCompletionsAll, ga:goalConversionRateAll',
      'sort' : '-ga:sessions',
      'start-date': '30daysAgo',
      'end-date': 'yesterday',
     'ids': "ga:111679201"
    },
    chart: {
      type: 'TABLE',
      container: 'timeline'
    }
  });
  
  
  gapi.analytics.auth.on('success', function(response) {
  	//hide the auth-button
  	document.querySelector("#auth-button").style.display='none';
  	
    timeline.execute();

  });
  

});
</script>
</body>
</html>


		</div>
		<?php
		$widgets[] = array(
            'title' => "Campaign Performance Summary - Last 30 Days",
			'columns' => 2, // this can be 1, 2, 3 or 4
			'content' => ob_get_clean(),
		);
		// end first widget.

		// start second widget:
		ob_start();
		?>
		<div>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		        <script>window.jQuery || document.write('<script src="http://marketing.matorero.com/includes/plugin_custom_dashboard_widgets/js/jquery-1.6.2.min.js"><\/script>')</script>
		                <script defer src="http://marketing.matorero.com/includes/plugin_custom_dashboard_widgets/js/script.js"></script>
		<script>
        	function updateOutput()
        	{
        		var domain = $('#domain').val();
        		var source = $('#source').val();
        		var medium = $('#medium').val();
        		var term = $('#term').val();
        		var content = $('#content').val();
        		var name = $('#name').val();

        		var html = domain+'?utm_source='+encodeURIComponent(source)+'&utm_medium='+encodeURIComponent(medium)+'&utm_campaign='+encodeURIComponent(name);
        		if (term) {
        			var html = html + '&utm_term='+encodeURIComponent(term);
        		}
        		if (content) {
        			var html = html + '&utm_content='+encodeURIComponent(content);
        		}


        		if (domain && source && medium && name) {
        			$('#url').html(html.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"));
        			$('#url').change();
        		} else {
        			$('#url').html('');
        			$('#url').change();
        		}
	
        	}
	
           function initpage() {
             	$('.auto').autogrow();
        		$('.auto').click(function() {
        		  $(this).select();
        		});
        		$('input').keyup(window.updateOutput);
        		$('input').click(window.updateOutput);
        		$('select').change(window.updateOutput);

              	updateOutput();
            }

        	$(window.initpage);


        </script>
<form action="">
          	<fieldset>
          		<p><label for="domain"><abbr title="Uniform Resource Locator">URL</abbr></label> <small>Enter the link for the website</small><br/><input type="text" name="domain" id="domain" class="big" value="http://matorero.com"></p><br/>
          		<p><strong>Campaign Source</strong>, <strong>Campaign Medium</strong> and <strong>Campaign Name</strong> should always be used.</p>
          		<p><label for="source">Campaign Source</label> <small>(referrer: google, citysearch, newsletter4)</small><br/><input type="text" name="source" id="source" value=""></p>
          		<p><label for="medium">Campaign Medium</label> <small>(marketing medium: cpc, banner, email)</small><br/><input type="text" name="medium" id="medium" value=""></p>
                <p><label for="name">Campaign Name</label> <small>(product, promo code, or slogan)</small><br/><input type="text" name="name" id="name" value=""></p>
          		<p><label for="term">Campaign Term</label> <small>(identify the paid keywords)</small><br/><input type="text" name="term" id="term" value=""></p>
          		<p><label for="content">Campaign Content</label> <small>(use to differentiate ads)</small><br/><input type="text" name="content" id="content" value=""></p>
          		
          	</fieldset>
          	</form>

            <hr/>
          	<div class="output" id="html">
          		<textarea id="url" readonly="readonly" wrap="off" class="auto"></textarea>
		</div>
		<?php
		$widgets[] = array(
            'title' => "Online Inbound Traffic URL Builder",
			'columns' => 2, // this can be 1, 2, 3 or 4
			'content' => ob_get_clean(),
		);
		// end second widget.
		
		
		
		
		
		return $widgets;
	} // end hook function

}