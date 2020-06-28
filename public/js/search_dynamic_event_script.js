"use strict";

$(function() {
    $('#select-subgenre').selectize();

    $('.popover-dismiss').popover({
        trigger: 'focus',
    });

    $('.pop-over').click(function () {
        var entity_id = $(this).data('id');
        var $element = $(this)
        $.ajax({
            url : 'http://larp.local/events/'+entity_id,
            type: "get",
            contentType: "application/json",
            success: function(data) {
                $element.data("content", JSON.parse(data))
                $element.popover('show');
            }
        });
    });

    $('.delete-entity').click(function () {
        $.ajax({
            url: 'http://larp.local/events/' + $(this).data('id'),
            type: "delete",
            contentType: "application/json",
            success: function() {$('#flash-here').empty().append('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>removed successfully</div>')},
            error: function() {$('#flash-here').empty().append('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button>removal failed</div>')}
        });
    });
});
