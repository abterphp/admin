$(document).ready(function () {
    var $forms = $('form'),
        isMissing = function ($elem) {
            if ($elem.is('select')) {
                return $("option:selected", $elem).length === 0;
            }

            return $elem.val() === '';
        };

    $forms.each(function () {
        var $form = $(this),
            $inputFields = $('input, textarea, select', $('.form-group.required', $form));

        if ($inputFields.length === 0) {
            return;
        }

        $inputFields.each(function () {
            var $input = $(this),
                id = $input.attr('id'),
                $label;

            if (!id) {
                return;
            }

            $label = $('label[for=' + id + ']', $form);
            $label.html($label.html() + '<sup>*</sup>');

            $input.data('form-group', $input.parent('.form-group'));
        });

        $form.data('required-inputs', $inputFields);
    });

    $forms.on('validate', function (e) {
        var $inputFields = $(this).data('required-inputs');

        $inputFields.each(function () {
            var $this = $(this);

            if (isMissing($this)) {
                $this.data('form-group').addClass('has-error');
            }
        });
    });
});
