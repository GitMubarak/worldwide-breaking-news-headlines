(function($) {

    // USE STRICT
    "use strict";

    /*
    $(document).on('ready widget-added widget-updated', function(event, widget) {
        var params = {
            change: function(e, ui) {
                $(e.target).val(ui.color.toString());
                $(e.target).trigger('change'); // enable widget "Save" button
            },
        }

        $('.hmnfw-color-picker').not('[id*="__i__"]').wpColorPicker(params);
    });
    
    var hmnfwIdsOfColorPicker = ['#widget-hmnewsfeedwidgetactivater-3-news_title_color', '#aabbnews_title_color'];
    $.each(hmnfwIdsOfColorPicker, function(index, value) {
        $(value).wpColorPicker();
    });
    */
    function initColorPicker(widget) {
        widget.find('.hmnfw-color-picker').not('[id*="__i__"]').wpColorPicker({
            change: _.throttle(function() {
                $(this).trigger('change');
            }, 3000)
        });
    }

    function onFormUpdate(event, widget) {
        initColorPicker(widget);
    }

    $(document).on('widget-added widget-updated', onFormUpdate);

    $(document).ready(function() {
        $('.widget-inside:has(.hmnfw-color-picker)').each(function() {
            initColorPicker($(this));
        });
    });

    /*
     $('.hmnfw-color-picker').on('focus', function() {
         var parent = $(this).parent();
         $(this).wpColorPicker()
         parent.find('.wp-color-result').click();
     });
     */
})(jQuery);