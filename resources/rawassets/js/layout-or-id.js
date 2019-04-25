$(document).ready(function() {
    const layoutId = $('#layout_id'), layoutDiv = $('#layout-div');

    if (layoutId.val() !== '') {
        layoutDiv.hide();
    }

    layoutId.change(function() {
        layoutDiv.toggle(layoutId.val() === '');
    });
});
