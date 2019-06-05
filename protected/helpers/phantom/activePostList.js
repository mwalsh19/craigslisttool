/*global require, console, el, phantom, parseArguments, jQuery, getParameterByName, serialize, getUnitedStates*/
(function app() {
    'use strict';

    var webpage = require('webpage').create(),
        QueryString,
        fs = require('fs'),
        urls = {
            login: 'https://accounts.craigslist.org/login',
            home: 'https://accounts.craigslist.org/login/home',
            postNew: 'https://post.craigslist.org/c/us?lang=en'
        },
        isjQueryInjected = phantom.injectJs('lib/jquery-1.11.2.min.js'),
        isUtilsInjected = phantom.injectJs('lib/utils.js'),
        args = parseArguments(require('system').args.slice(1)),
        host = 'http://joinswift.com';

    webpage.settings.userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';

    //PARSE
    function parseList() {
        var filter_page,
            filter_date,
            search,
            _rows,
            array = [];

        search = webpage.evaluate(function () {
            return window.location.search;
        });
        //        filter_date = getParameterByName(search, 'filter_date');


        //        console.log("Filter Number: " + args.filter_page);
        var data = {
            filter_page: args.filter_page,
            filter_cat: 0,
            filter_date: "2015-05",
            filter_active: 0,
            show_tab: "postings"
        };
        webpage.open(urls.home + '?filter_page=' + args.filter_page + '&filter_cat=0&filter_active=0&show_tab=postings&filter_date=' + args.filter_date, function (status) {
            console.log(status);

            if (status === 'fail') {
                console.log('Failed');
                phantom.exit(1);
            }

            //console.log(window.location.href);

            console.log("will be ok");

            _rows = webpage.evaluate(function () {
                var array = [];
                $.each($('.accthp_postings tr'), function (key, value) {
                    if (!$(value).hasClass('headers')) {

                        $(value).find('.buttons form').css({
                            'display': 'inline-block'
                        });
                        $(value).find('.buttons form').attr("target", "_blank");
                        $(value).find('.buttons input[type="submit"]').addClass('btn btn-default btn-sm');
                        $(value).find('.title a').attr("target", "_blank");

                        array.push({
                            status: $(value).find('.gc').text(),
                            actions: $(value).find('.buttons').html(),
                            title: $(value).find('.title').html(),
                            areacat: $(value).find('.areacat').html(),
                            date: $(value).find('.dates').text(),
                            id: $(value).find('.postingID').text()
                        });
                        //return true;
                    }
                });

                var dates = [];

                $('#datePick option').each(function (key, value) {
                    dates.push($(this).val());
                });

                var resultObject = {
                    'dates': dates,
                    'posts': array
                };

                return JSON.stringify(resultObject);
            });

            console.log(_rows);
            phantom.exit(1);
        });
    }

    //LOGIN PAGE, REDIRECTS TO HOME
    function login() {
        console.log('is not logged in trying now!');

        var postData = serialize({
            'step': 'confirmation',
            'rt': '',
            'rp': '',
            'p': '0',
            'inputEmailHandle': 'mwalsh@lacedagency.com',
            'inputPassword': 'w00dm0use'
        });

        webpage.customHeaders = {
            'Referer': urls.login
        };

        webpage.open(urls.login, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit(1);
            }

            if (args.showCookies) {
                phantom.cookies.forEach(function (cookie, i) {
                    var key;
                    for (key in cookie) {
                        if (cookie.hasOwnProperty(key)) {
                            console.log('[cookie:' + i + '] ' + key + ' = ' +
                                cookie[key]);
                        }
                    }
                });
            }

            var title = webpage.evaluate(function () {
                return document.title;
            });

            //console.log('web page loaded', webpage.url);
            if (webpage.url === urls.home) {
                parseList();
            } else {
                phantom.exit(1);
            }
        });
    }

    function init() {
        console.log('init');

        webpage.open(urls.home, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit(1);
            }

            if (webpage.url.indexOf('login?rt=') >= 0) {
                login();
            } else {
                console.log('skiping login');
                parseList();
            }
        });
    }

    //BOOTSTRAP APP
    webpage.onLoadStarted = function () {
        var currentUrl = webpage.evaluate(function () {
            return window.location.href;
        });
        console.log('Current page ' + currentUrl + ' will gone...');
        //console.log('Now loading a new page...');
    };

    webpage.onResourceReceived = function (response) {
        //console.log('Response (#' + response.id + ', stage "' + response.stage + '"): ' + JSON.stringify(response));
        var i;
        for (i = 0; i < response.headers.length; i += 1) {
            if (response.headers[i].name === 'Location') {
                console.log('redirecting to', response.headers[i].value);
            }
        }
    };

    webpage.onResourceError = function (resourceError) {
        console.log('Unable to load resource (#' + resourceError.id + 'URL:' + resourceError.url + ')');
        console.log('Error code: ' + resourceError.errorCode + '. Description: ' + resourceError.errorString);
    };

    webpage.onUrlChanged = function (targetUrl) {
        console.log('New URL: ' + targetUrl);
    };

    init();

}());