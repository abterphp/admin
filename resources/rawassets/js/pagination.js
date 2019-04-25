$(document).ready(function () {
    $('.pagination-sizes').change(function(){
        var newLocation = window.location.toString(),
            value = $(this).val(),
            regex;

        if (newLocation.indexOf('page-size=') > -1) {
            regex = new RegExp('(page\-size=)[^\&]+');
            newLocation = newLocation.replace( regex , '$1' + value);
        } else {
            if (newLocation.indexOf('?') === -1) {
                newLocation += '?';
            }
            newLocation = newLocation + 'page-size=' + value + '&';
        }

        if (newLocation.indexOf('&page=') > -1 || newLocation.indexOf('?page=') > -1) {
            regex = new RegExp('(page=)[^\&]+');
            newLocation = newLocation.replace( regex , '$1' + '1');
        }

        window.location = newLocation;
    });
});
