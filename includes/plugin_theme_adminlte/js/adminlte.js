ucm.adminlte = {
    init: function(){

        // do the action button duplication.
        $('.action_bar_duplicate').each(function(){
            if(!$(this).hasClass('action_bar_single')){
                $(this).clone(true).addClass('hidden-xs action_bar_is_duplicated').prependTo($(this).parents('form').first());
            }
        });
        // current selected icon to header area.
        if($('.head .main-bar h3 .fa').length == 0){
            $('#menu li.active i.fa').each(function(){
                $(this).clone(true).addClass('cloned').prependTo('.head .main-bar h3');
            });
        }
        $('.submit_button').each(function(){
            if(!$(this).hasClass('btn')){
                $(this).addClass('btn');
            }
        });

        // this also exists in the metris js file. update both if changing here:
        $('.responsive-toggle-button,.box-responsive .box-title').click(function(e){
            if(typeof e.target.parentElement != 'undefined' && e.target.parentElement.tagName == 'A' && $(e.target.parentElement).attr('href') != '#'){
                e.stopPropagation();
                return true;
            }
            if(typeof(e.target.tagName) != 'undefined' && e.target.tagName == 'A' && $(e.target).attr('href') != '#'){
                e.stopPropagation();
                return true;
            }
            var p = $(this).parents('.box-responsive').first();
            if($(p).hasClass('responsive-toggled')){
                $(p).removeClass('responsive-toggled');
            }else{
                $(p).addClass('responsive-toggled');
            }
            return false;
        });

    }
};

ucm.form = ucm.form || {};
ucm.form.set_required = function(element){

};

display_messages_timeout = false;
ucm.display_messages = function(fadeout){
    var display = false;
    var html = '<div id="header_messages" style="position: absolute; width: 50%; z-index: 9000; margin-top: -56px; margin-left: 28px;">';
    for(var i in ucm.messages){
        html += '<div class="alert alert-success alert-dismissable" style="margin:20px 15px 10px 34px"> <i class="fa fa-check"></i> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        html += ucm.messages[i] + '';
        html += '</div>';
        display = true;
    }
    for(var i in ucm.errors){
        html += '<div class="alert alert-danger alert-dismissable" style="margin:20px 15px 10px 34px"> <i class="fa fa-check"></i> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        html += ucm.errors[i] + '';
        html += '</div>';
        display = true;
    }
    html += '</div>';
    if(display){
        $('#header_messages').remove();
        $('aside.right-side').prepend(html);
    }
    ucm.messages=new Array();
    ucm.errors=new Array();
    if(typeof fadeout != 'undefined' && fadeout){
        clearTimeout(display_messages_timeout);
            display_messages_timeout = setTimeout(function(){
                $('#header_messages').fadeOut();
            },4000);
        }
};