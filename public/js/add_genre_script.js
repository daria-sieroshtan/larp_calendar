"use strict";

var $collectionHolder;

var $addSubgenreButton = $('<button type="button" class="add_subgenre_link btn btn-primary text-light mx-1 mt-3 brand-bg nobull">' +
    '<span class="fa fa-plus text-white" aria-hidden="true"></span>' +
    ' Поджанр' +
    '</button>                                                                                                                                                                                                                                                                                                                                                              ');

var $newLinkLi = $('<li class="form-li"></li>').append($addSubgenreButton);

jQuery(document).ready(function() {
    $collectionHolder = $('ul.subgenres');

    $collectionHolder.find('li').each(function() {
        addSubgenreFormDeleteLink($(this));
    });

    $collectionHolder.append($newLinkLi);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addSubgenreButton.on('click', function(e) {
        addSubgenreForm($collectionHolder, $newLinkLi);
    });
});

function addSubgenreForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype;

    $collectionHolder.data('index', index + 1);

    var $newFormLi = $('<li><div class="row my-2"><div class="col-10 input-field"></div><div class="col-2 delete-button"></div></li>');
    $newFormLi.find('.input-field').append(newForm);

    $newLinkLi.before($newFormLi);

    addSubgenreFormDeleteLink($newFormLi);
}

function addSubgenreFormDeleteLink($subgenreFormLi) {
    var $removeFormButton = $('<button type="button" class="delete_subgenre_link btn btn-primary text-light mx-1 brand-bg nobull">' +
        '<span class="fa fa-trash-o text-white" aria-hidden="true"></span> ' +
        '</button>');
    $subgenreFormLi.find('.delete-button').append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        $subgenreFormLi.remove();
    });
}
