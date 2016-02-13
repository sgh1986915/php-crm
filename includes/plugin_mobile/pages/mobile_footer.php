</div>
</div>
</div>
        <?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca
  * Package Date: 2015-11-25 02:55:20 
  * IP Address: 67.79.165.254
  */ /* <div data-role="footer">
		<h4>Footer content</h4>
	</div><!-- /footer --> */ ?>

</div><!-- /page -->
<?php if(module_config::c('mobile_content_scroll',1) && module_security::is_logged_in()){ ?>
<script type="text/javascript">
    var contentscroll = [];
    var content = null;
    window.addEventListener("resize", function() {
        // Get screen size (inner/outerWidth, inner/outerHeight)
//        var headerheight = 20;
//        $('div[data-role="header"]').each(function(){
//            headerheight+= $(this).height();
//        });
//        if(content != null)content.width(window.innerWidth-10).height(window.innerHeight-headerheight);
//        if(contentscroll != null)contentscroll.refresh();
        for (var i in contentscroll){
            if(typeof contentscroll[i] == 'object'){
                contentscroll[i].refresh();
            }
        }
    }, false);

    $(function(){
        /*if(content == null){
            content = $('#mobile_content');
        }
        contentscroll = new iScroll(content[0] ,{
            onBeforeScrollStart: null,
            onTouchEnd: null,
            vScroll:true,
            hScroll:true,
            bounce: false,
            momentum: true,
            hScrollbar: false,
            vScrollbar: false
        });*/
        $('.iscroll').each(function(){
            contentscroll.push( new iScroll(this ,{
                onBeforeScrollStart: null,
                onTouchEnd: null,
                vScroll:true,
                hScroll:true,
                bounce: false,
                momentum: true,
                hScrollbar: false,
                vScrollbar: false
            }) );
        });
    });
</script>
<?php } ?>
</body>
</html>