$(document).ready(function(){
    var submitBtn = $('#login-submit'),
        form = $('#login-form'),
        password = $('#login-password'),
        rawPassword = $('#login-raw-password'),
        username = $('#login-username'),
        salt = frontendSalt
        ;

    username.change(function(){
        var val = $(this).val();

        if (val === '') {
            submitBtn.attr('disabled', true);
            return false;
        }

        submitBtn.attr('disabled', rawPassword.val() === '');
    });

    rawPassword.change(function(){
        var val = $(this).val(), hash;

        if (val === '') {
            submitBtn.attr('disabled', true);
            password.val('');
            return false;
        }

        hash = sha3_512(salt + val);

        password.val(hash);
        submitBtn.attr('disabled', username.val() === '');
    });

    form.submit(function(e) {
        if (rawPassword.val() === '' || username.val() === '') {
            e.preventDefault();
        }
    });

    $('.only-js-login-warning').hide();
});