jQuery(document).on( 'click', '.bpwcamp-notice .notice-dismiss', function() {

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'bpwcamp_notification'
        }
    })

})