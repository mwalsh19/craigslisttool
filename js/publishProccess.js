var app = app || {};
app.interval = '';
app.linecount = 0;
app.intervalNumber = 5000;
app.isPublished;

app.getParameterByName = function (url, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(url);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};

app.readLogs = function () {
    app.interval = setInterval(function () {
        $.get('logs', {linecount: app.linecount}, function (result) {
            if (result.data.length > 0) {
                for (var i = 0; i < result.data.length; i++) {
                    $('.publish-logs').append('<small>' + result.data[i] + '</small><br>');
                }
                app.linecount = result.total + app.linecount;
            } else {
                console.log('not found more logs');
                console.log('Start new request');
            }
        });
    }, app.intervalNumber);
};

//When event is fired, stop interval and linecount is equal to 0
$('#publishProccess-modal').on('hidden.bs.modal', function (e) {
    console.log('stop interval');
    clearInterval(app.interval);
    app.linecount = 0;
    $('.publish-logs').html('');
});

//When event is fired, open model and start new ajax request
$('#publishProccess-btn').on('click', function () {
    $('#publishProccess-modal').modal('show');
    app.readLogs();
});

//Verify is job list is published
app.isPublished = function () {
    var isPublish = app.getParameterByName(window.location.href, 'publish');
//    console.log(isPublish);
    if (isPublish && isPublish !== "") {
        $('#publishProccess-modal').modal('show');
        app.readLogs();
    }
};

app.isPublished();
