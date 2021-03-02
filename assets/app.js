/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import "nes.css/css/nes.min.css";

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import 'bootstrap';

(function () {
    const button = document.querySelector(".start-button");
    const menu = document.querySelector(".start-menu");

    button.addEventListener('click', function (event) {
        menu.classList.toggle('isHidden')
        button.classList.toggle('isClicked')
    })
})();

const $ = require('jquery');
global.jQuery = $;
global.$ = $;

function addTagFormDeleteLink($tagFormLi) {
    const $removeFormButton = $('<div class="delete-button">\n'
        + '                             <a href="#" class="js-remove-methodLink">\n'
        + '                                <button type="button" class="btn btn-secondary btn-sm">Supprimer</button>\n'
        + '                          </a>\n'
        + '                   </div>');
    $tagFormLi.append($removeFormButton);

    $removeFormButton.on('click', (e) => {
        // remove the li for the tag form
        $tagFormLi.fadeOut().remove();
    });
}

$(document).ready(() => {
// JS threeDots action sheet.
    $('.menu').hide();
    $('.threeDots').show().click(() => {
        $('.menu').toggle();
    });


    // method forms
    const $wrapper = $('.js-methodLink-wrapper');

    $wrapper.find('.js-methodLink-item').each(function () {
        addTagFormDeleteLink($(this));
    });


    $('.js-add-methodLink').on('click', (e) => {
        e.preventDefault();
        // Get the data-prototype explained earlier
        const prototype = $wrapper.data('prototype');

        // get the new index
        const index = $wrapper.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        let newForm = prototype.replace(/__name__/g, index);

        // increase the index with one for the next item
        $wrapper.data('index', index + 1);

        newForm = $(newForm);
        addTagFormDeleteLink(newForm);
        // Display the form in the page before the "new" link
        newForm.appendTo($wrapper);
    });
});