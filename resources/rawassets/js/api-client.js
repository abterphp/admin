$(document).ready(function () {
    var secret = $('#secret'),
        generateSecret = $('#generateSecret'),
        secretHelp = $('#secretHelp'),
        id = $('#id'),
        form = $('#apiClientForm'),
        sl = parseInt(secretLength);

    generateSecret.click(function () {
        var val = generatePassword(sl, false);

        secret.val(val);
        secretHelp.removeClass('hidden');
    })

    form.submit(function (e) {
        if (!canSubmit) {
            e.preventDefault();
        }
    });

    generateSecret.attr('data-message', secretHelp.text());
    if (!id.val()) {
        generateSecret.click();
    }

    $('.only-js-form-warning').hide();
});
