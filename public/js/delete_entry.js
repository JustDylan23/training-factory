$(document).ready(function (e) {
    $('.button-remove-entry').click(function (e) {
        $('#delete-confirm-button').attr('href', $(e.currentTarget).attr('data-href'));
    });
    $('#delete-confirm-button').click(function (e) {
        e.preventDefault();
        let link = $(e.currentTarget).attr('href');
        $.ajax({
            method: 'POST',
            url: link
        }).done((data) => {
            if (data.success === true) {
                $(`[data-link='${link}']`).closest('.entry').remove();
            }
        });
    });
});