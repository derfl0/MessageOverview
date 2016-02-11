$(document).ready(function() {

    $('div.message_list a').click(function(e) {
        e.preventDefault();
        $('div.message_list a').removeClass('active');
        $(this).addClass('active');
        $('div.message_display').load($(this).attr('href'));
    });

    $('form.sidebar-search').submit(function(e){
        return false;
    });

    $('.sidebar-search input[name="search"]').on('input', function() {
        var search = $('.sidebar-search input[name="search"]').val()
        if (search.length > 0) {
            $('div.message_list a:not(:contains(' + search + '))').hide();
            $('div.message_list a:contains(' + search + ')').show();
        } else {
            $('div.message_list a').show();
        }

        if ($('div.message_list a:visible').length > 0) {
            $('div.message_list p.no_messages').hide();
        } else {
            $('div.message_list p.no_messages').show();
        }
    });

});