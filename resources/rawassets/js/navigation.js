$(document).ready(function () {
    $('.openable').each(function () {
        var $this = $(this), $id = $this.attr('id'), val = window.localStorage.getItem($id);

        if (!$id) {
            return;
        }

        $this.toggleClass('nav-open', val === 'true');

        console.log('each', $this);
    });

    $('.openable > a').click(function (e) {
        var $this = $(this).parent('.openable'), $id = $this.attr('id');

        e.preventDefault();

        if (!$id) {
            return;
        }

        $this.toggleClass('nav-open');

        window.localStorage.setItem($id, $this.hasClass('nav-open'));

        console.log('click', $this);
    });
});
