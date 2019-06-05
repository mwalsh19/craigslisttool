/*global require, console, el, phantom, parseArguments, jQuery, getParameterByName, serialize*/
(function app() {
    'use strict';

    var system = require('system'),
        webpage = require('webpage').create(),
        urls = {
            login: 'https://accounts.craigslist.org/login',
            home: 'https://accounts.craigslist.org/login/home',
            postNew: 'https://post.craigslist.org/c/us?lang=en'
        },
        isUtilsInjected = phantom.injectJs('utils.js');

    webpage.settings.userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';

    console.log(isUtilsInjected);

    function init() {
        var postData = serialize({
            'step': 'confirmation',
            'rt': '',
            'rp': '',
            'p': '0',
            'inputEmailHandle': 'mwalsh@lacedagency.com',
            'inputPassword': 'w00dm0use'
        });

        console.log(postData);

        webpage.customHeaders = {
            'Referer': urls.login
        };

        webpage.open(urls.login, 'POST', 'step=confirmation&rt=&rp=&p=0&inputEmailHandle=mwalsh%40lacedagency.com&inputPassword=w00dm0use', function (status) {
            if (status === 'fail') {
                console.log('Failed');
                //phantom.exit();
            }

            //            if (args.showCookies) {
            //                phantom.cookies.forEach(function (cookie, i) {
            //                    var key;
            //                    for (key in cookie) {
            //                        if (cookie.hasOwnProperty(key)) {
            //                            console.log('[cookie:' + i + '] ' + key + ' = ' +
            //                                cookie[key]);
            //                        }
            //                    }
            //                });
            //            }

            var title = webpage.evaluate(function () {
                return document.title;
            });

            console.log('web page loaded', webpage.url);
            phantom.exit();
        });
    }

    //BOOTSTRAP APP
    //    webpage.onLoadStarted = function () {
    //        var currentUrl = webpage.evaluate(function () {
    //            return window.location.href;
    //        });
    //        console.log('Current page ' + currentUrl + ' will gone...');
    //        console.log('Now loading a new page...');
    //    };
    //
    //    webpage.onResourceReceived = function (response) {
    //        console.log('Response (#' + response.id + ', stage "' + response.stage + '"): ' + JSON.stringify(response));
    //        var i;
    //        for (i = 0; i < response.headers.length; i += 1) {
    //            if (response.headers[i].name === 'Location') {
    //                console.log('redirecting to', response.headers[i].value);
    //            }
    //        }
    //    };
    //
    //    webpage.onResourceError = function (resourceError) {
    //        console.log('Unable to load resource (#' + resourceError.id + 'URL:' + resourceError.url + ')');
    //        console.log('Error code: ' + resourceError.errorCode + '. Description: ' + resourceError.errorString);
    //    };
    //
    //    webpage.onUrlChanged = function (targetUrl) {
    //        console.log('New URL: ' + targetUrl);
    //    };
    webpage.onResourceRequested = function (request) {
        system.stderr.writeLine('= onResourceRequested()');
        system.stderr.writeLine('  request: ' + JSON.stringify(request, undefined, 4));
    };

    webpage.onResourceReceived = function (response) {
        system.stderr.writeLine('= onResourceReceived()');
        system.stderr.writeLine('  id: ' + response.id + ', stage: "' + response.stage + '", response: ' + JSON.stringify(response));
    };

    webpage.onLoadStarted = function () {
        system.stderr.writeLine('= onLoadStarted()');
        var currentUrl = webpage.evaluate(function () {
            return window.location.href;
        });
        system.stderr.writeLine('  leaving url: ' + currentUrl);
    };

    webpage.onLoadFinished = function (status) {
        system.stderr.writeLine('= onLoadFinished()');
        system.stderr.writeLine('  status: ' + status);
    };

    webpage.onNavigationRequested = function (url, type, willNavigate, main) {
        system.stderr.writeLine('= onNavigationRequested');
        system.stderr.writeLine('  destination_url: ' + url);
        system.stderr.writeLine('  type (cause): ' + type);
        system.stderr.writeLine('  will navigate: ' + willNavigate);
        system.stderr.writeLine('  from page\'s main frame: ' + main);
    };

    webpage.onResourceError = function (resourceError) {
        system.stderr.writeLine('= onResourceError()');
        system.stderr.writeLine('  - unable to load url: "' + resourceError.url + '"');
        system.stderr.writeLine('  - error code: ' + resourceError.errorCode + ', description: ' + resourceError.errorString);
    };

    webpage.onError = function (msg, trace) {
        system.stderr.writeLine('= onError()');
        var msgStack = ['  ERROR: ' + msg];
        if (trace) {
            msgStack.push('  TRACE:');
            trace.forEach(function (t) {
                msgStack.push('    -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function + '")' : ''));
            });
        }
        system.stderr.writeLine(msgStack.join('\n'));
    };

    init();
}());