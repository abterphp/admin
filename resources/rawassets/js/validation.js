$(document).ready(function () {
    var $forms = $('form');

    $forms.each(function () {
        var $form = $(this),
            $formGroups = $('.form-group', $form);

        if ($formGroups.length === 0) {
            return;
        }

        $form.submit(function (e) {
            $form.trigger('validate');

            $formGroups.each(function () {
                if ($(this).hasClass('has-error')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    });
});
