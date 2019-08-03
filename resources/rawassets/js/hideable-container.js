$(document).ready(function () {
    var hidables = $('.hidable');

    hidables.each(function() {
        var hider = $('.hider', this),
            btn = $('button', hider),
            container = $('.hidee', this),
            allowed = true;

        // prevent closing filters when active
        $('.filter-form input, .filter-form select, .filter-form textarea', container).each(function(){
            if ($(this).val() !== '') {
                allowed = false;

                return false;
            }

            return true;
        });

        if (!allowed) {
            return;
        }

        container.hide();

        btn.click(function(){
            container.toggle();
            btn.toggleClass('btn-info').toggleClass('btn-warning')
        });
    });
});
