$(document).ready(function () {
    var countables = $('.countable');

    countables.each(function() {
        var count = $('.count', this), max = count.data('count');

        count.text(' (' + max + ')');

        $('textarea', this).keyup(function () {
            var newCount = max - $(this).val().length;
            count.text(' (' + newCount + ')');
        }).keyup();
    });
});
