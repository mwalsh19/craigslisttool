var app = app || {};
app.filter_page = 1;
app.filter_date = "";
app.dates = "";
app.getNextPagination = '';
app.requestSource = 'request';

var select = $('#dates'),
        today = new Date(),
        dd = today.getDate(),
        mm = "0" + (today.getMonth() + 1),
        yyyy = today.getFullYear();

app.filter_date = yyyy + "-" + mm;


app.table = $("#postings-table").DataTable({
    "pageLength": 50,
    "aaSorting": [],
    "sAjaxSource": app.requestSource + "?filter_page=" + app.filter_page + "&filter_date=" + app.filter_date,
    "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
        $.get(sSource, function (data) {
            fnCallback(data);
            if (app.dates !== null && app.filter_page === 1) {
                app.dates = data.dates;

                for (var i = 0; i < app.dates.length; i++) {
                    select.append($('<option></option>').val(app.dates[i].value).html(app.dates[i].text));
                }
                app.startPagination();
            }
        });
    },
    "columns": [
        null,
        {"width": "20%"},
        null,
        {"width": "15%"},
        null,
        null
    ]
});

app.startPagination = function () {
    $('.currentDate').text(app.filter_date);
    app.filter_page = app.filter_page + 1;
    $.get(app.requestSource, {filter_page: app.filter_page, filter_date: app.filter_date}, function (result) {
        if (!result.data.length) {
            $('#loadingPostings').hide();
        } else {
            app.table.rows.add(result.data).draw();
            app.startPagination();
        }

    });
};

select.on('change', function () {
    if (app.dates !== null) {
        app.filter_date = $(this).find(':selected').val();
        app.filter_page = 0;
        app.table.clear().draw();
        $('#loadingPostings').show();
        app.startPagination();
    }
});
