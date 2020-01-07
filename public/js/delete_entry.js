$(document).ready(function (e) {
    $('.button-remove-entry').click(function (e) {
        $('#delete-confirm-button')
            .attr('href', $(e.currentTarget).attr('data-href'))
    });
    $('#delete-confirm-button').click(async function (e) {
        e.preventDefault();
        let link = $(e.currentTarget).attr('href');
        let response = await fetch(link, {method: 'POST'});
        let data = await response.json();
        if (data.success === true) {
            location.reload();
        }
    });
});