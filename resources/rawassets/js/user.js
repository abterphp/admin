$(document).ready(function () {
    var rawPassword = $('#raw_password'),
        password = $('#password'),
        rawPasswordRepeated = $('#raw_password_confirmed'),
        passwordRepeated = $('#password_confirmed'),
        passwordHelp = $('#password_help'),
        passwordProgress = $('#password_progress'),
        username = $('#username'),
        email = $('#email'),
        form = $('#user-form'),
        salt = frontendSalt,
        acceptedStrength = 3;
        passwordIsStrong = false;

    rawPassword.change(function () {
        var val = $(this).val(), hash;

        isPasswordRepeated();
        isPasswordStrong();

        //password.parent().toggleClass('has-error', !passwordIsStrong);

        if (val === '' || !passwordIsStrong) {
            password.val('');
            return false;
        }

        hash = sha3_512(salt + val);
        password.val(hash);
    });

    rawPasswordRepeated.change(function () {
        var val = $(this).val(), hash;

        hash = sha3_512(salt + val);
        passwordRepeated.val(hash);

        isPasswordRepeated();
    });

    form.submit(function (e) {
        if (!canSubmit) {
            e.preventDefault();
        }
    });

    var isPasswordStrong = function () {
        var val = rawPassword.val();

        passwordIsStrong = false;
        result = zxcvbn(val, [username.val(), email.val()]);

        setPasswordHelp(result);
        setProgress(result);

        passwordIsStrong = (result.score >= acceptedStrength);
    };

    var setProgress = function (result) {
        var percent = (result.score+1) * 20;

        passwordProgress
            .css('width', percent + '%')
            .toggleClass('progress-bar-danger', result.score <= 1)
            .toggleClass('progress-bar-warning', result.score === 2)
            .toggleClass('progress-bar-info', result.score === 3)
            .toggleClass('progress-bar-success', result.score >= acceptedStrength);
    };

    var setPasswordHelp = function (result) {
        var text = result.feedback.warning;

        if (result.score >= 4) {
            passwordHelp.text('');
            return;
        }

        result.feedback.suggestions.forEach(function (item) {
            text += '<br>' + item;
        });

        passwordHelp.html(text);
    };

    var isPasswordRepeated = function () {
        var areSame = (rawPassword.val() === passwordRepeated.val());

        // passwordRepeated.parent().toggleClass('has-error', !areSame);

        return areSame;
    };

    isPasswordRepeated();

    $('.only-js-form-warning').hide();
});