/**
 * Created by Huulktya on 3/4/14.
 */

var cols = 40;
//The following code is under the MIT licensce and was taken from this post:
//http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
$('[placeholder]').focus(function () {
    var input = $(this);
    if (input.val() == input.attr('placeholder')) {
        input.val('');
        input.removeClass('placeholder');
    }
}).blur(function () {
        var input = $(this);
        if (input.val() == '' || input.val() == input.attr('placeholder')) {
            input.addClass('placeholder');
            input.val(input.attr('placeholder'));
        }
    }).blur().parents('form').submit(function () {
        $(this).find('[placeholder]').each(function () {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
            }
        })
    });
//End code from blog post

$(document).on('click', '.dropdown-link', function () {
    var selText = $(this).text();
    var id = $(this).attr('ref');
    $('#dropdown-' + id).html(selText + ' <span class="caret"</span>');
});

$(document).ready(function () {
    var inputs = $('input[placeholder]');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].size > cols) {
            cols = inputs[i].size
        }
        if (inputs[i].placeholder.length > cols) {
            cols = inputs[i].placeholder.length;
        }
    }
    for (var j = 0; j < inputs.length; j++) inputs[j].size = cols;
});

