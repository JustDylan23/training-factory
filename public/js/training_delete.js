$(document).ready(function (e) {
    $('.button-remove-training').click(function (e) {
        e.preventDefault();
        $.ajax({
            method: 'POST',
            url: $(e.currentTarget).attr('href')
        }).done((data) => {
            if (data.success === true) {
                $(this).closest('.training').remove();
            }
        });
    });
});