$(document).ready(function() {
    $('input.semi-auto').each(function() {
        var $this = $(this).prop('disabled', true),
            $container = $this.parent('.form-group'),
            $hidden = $('<input type="hidden" value="' + $this.attr('name') + '">'),
            $toggle = $('<button type="button" class="btn pmd-ripple-effect btn-default btn-visible"><i class="material-icons pmd-xs">edit</i></button>'),
            $clear = $('<button type="button" class="btn pmd-ripple-effect btn-default btn-visible"><i class="material-icons pmd-xs">clear</i></button>')
            disabled = true;

        $container.append($hidden).append($toggle).append($clear);

        $toggle.click(function() {
            disabled = !disabled;

            $this.prop('disabled', disabled);
            $hidden.prop('disabled', !disabled);
        });
        $clear.click(function() {
            disabled = false;

            $this.prop('disabled', disabled).val('');
            $hidden.prop('disabled', !disabled).val('');
        });
    });
});
