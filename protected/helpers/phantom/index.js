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
            content = fs.read(args.xml),
            jQueryXML = jQuery(jQuery.parseXML(content)),
            host = 'http://joinswift.com';

    webpage.settings.userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36';

    function getNewPostJobData(form, cryptedStepCheck) {
        //console.log('1.1');
        var area_find = new RegExp('%%area%%', 'g'),
                url_find = new RegExp('%%url%%', 'g'),
                title = jQueryXML.find('jobs > title').text(),
                description = jQueryXML.find('jobs > description').text(),
                requirements = jQueryXML.find('jobs > requirements').text(),
                compensation = jQueryXML.find('jobs > compensation').text(),
                type = jQueryXML.find('jobs > drivertype').text().toLowerCase(),
                short_url = jQueryXML.find('area').eq(args.indx).find('title').text().substr(-2),
                state = toTitleCase(getUnitedStates()[short_url].toLowerCase()),
                url_only = host + '/landing-pages/l/craigslist/' + type + '/' + short_url.toLowerCase(),
                url_apply = '<a href="' + host + '/landing-pages/l/craigslist/' + type + '/' + short_url.toLowerCase() + '" target="_blank">Click To Apply Online</a>',
                body;

        title = title.replace(area_find, state);
        description = description.replace(area_find, state);
        description = description.replace(url_find, url_only);
        body = description + '<br>' + requirements;
        return {
            title: title,
            body: body,
            compensation: compensation
        };
    }

    //PREVIEW AND PUBLISH
    function doPlubish(options) {
        console.log('publishing draft');

        var postData = serialize({
            'continue': options.cont,
            cryptedStepCheck: options.cryptedStepCheck,
            go: options.go
        });

        //console.log('using this params', postData);
        //phantom.exit();

        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var search, s;

            search = webpage.evaluate(function () {
                return window.location.search;
            });

            s = getParameterByName(search, 's');

            if (s === 'mailoop') {
                console.log('check email');
            }

            phantom.exit();
        });
    }

    //EDIT IMAGE
    function editimage(options) {
        var postData = serialize({
            cryptedStepCheck: options.cryptedStepCheck,
            a: options.a,
            go: options.go
        });

        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options, search, s, url;

            form = webpage.evaluate(function () {
                return document.querySelector('.draft_warning form');
            });

            search = webpage.evaluate(function () {
                return window.location.search;
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            s = getParameterByName(search, 's');

            if (s === 'preview') {
                options = {
                    url: form.action,
                    cryptedStepCheck: cryptedStepCheck,
                    cont: 'y',
                    go: 'Continue'
                };
                doPlubish(options);
            } else {
                phantom.exit();
            }
        });
    }

    //CREATE POST
    function createPost(options) {
        console.log('creating post now!');

        var postData = serialize({
            PostingTitle: options.title,
            PostingBody: options.body,
            remuneration: options.compensation,
            Privacy: 'A',
            employment_type: '1',
            FromEMail: 'noreply@swifttrans.com',
            ConfirmEMail: 'noreply@swifttrans.com',
            cryptedStepCheck: options.cryptedStepCheck,
            go: 'Continue'
        });

        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options, search, s, url;

            search = webpage.evaluate(function () {
                return window.location.search;
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            s = getParameterByName(search, 's');

            if (s === 'preview') {
                form = webpage.evaluate(function () {
                    return document.querySelector('.draft_warning form');
                });

                options = {
                    url: form.action,
                    cryptedStepCheck: cryptedStepCheck,
                    cont: 'y',
                    go: 'Continue'
                };
                doPlubish(options);
            } else if (s === 'editimage') {
                form = webpage.evaluate(function () {
                    return document.querySelectorAll('form')[1];
                });

                options = {
                    url: form.action,
                    cryptedStepCheck: cryptedStepCheck,
                    a: 'fin',
                    go: 'Done with Images'
                };
                editimage(options);
            } else {
                phantom.exit();
            }
        });
    }

    //CHOOSE SUBAREA
    function chooseSubArea(options) {
        console.log('choosing subarea');

        var postData = serialize({
            n: jQueryXML.find('area').eq(args.indx).find('code').text().split('-')[1],
            cryptedStepCheck: options.cryptedStepCheck,
            go: 'Continue'
        });

        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options;

            form = webpage.evaluate(function () {
                return document.querySelector('#postingForm');
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });
            //console.log('1');

            options = getNewPostJobData(form);
            //console.log('2');
            options.url = form.action;
            options.cryptedStepCheck = cryptedStepCheck;
            options.go = 'Continue';

            createPost(options);

            //console.log(JSON.stringify(options));
        });
    }

    //POST STEP4
    function goToCreatePostOrChooseSubArea(options) {
        var postData = serialize({
            id: options.id,
            cryptedStepCheck: options.cryptedStepCheck,
            go: options.go
        });
        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options, search, s, url;

            search = webpage.evaluate(function () {
                return window.location.search;
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            s = getParameterByName(search, 's');

            if (s === 'edit') {
                form = webpage.evaluate(function () {
                    return document.querySelector('#postingForm');
                });
                options = getNewPostJobData(form);
                options.cryptedStepCheck = cryptedStepCheck;
                options.url = form.action;
                options.go = 'Continue';

                createPost(options);
            } else if (s === 'subarea') {
                form = webpage.evaluate(function () {
                    return document.querySelector('form.subareapick.picker');
                });
                chooseSubArea({
                    url: form.action,
                    cryptedStepCheck: cryptedStepCheck
                });
            } else {
                phantom.exit();
            }
        });
    }

    //POST STEP3
    function chooseCat(options) {
        console.log('choosing category');

        var postData = serialize({
            id: options.id,
            cryptedStepCheck: options.cryptedStepCheck,
            go: options.go
        });
//        console.log(options.url);
//        console.log(postData);
        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options;

            form = webpage.evaluate(function () {
                return document.querySelector('form.catpick.picker');
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            options = {
                url: form.action,
                id: 125, //transportation cat
                cryptedStepCheck: cryptedStepCheck,
                go: 'Continue'
            };

            goToCreatePostOrChooseSubArea(options);

            //console.log(JSON.stringify(options));
        });
    }

    //POST STEP2
    function chooseType(options) {
        console.log('choosing type');

        var postData = serialize({
            n: options.n,
            cryptedStepCheck: options.cryptedStepCheck,
            go: options.go
        });
        webpage.open(options.url, 'POST', postData, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, options;

            form = webpage.evaluate(function () {
                return document.querySelector('form.catpick.picker');
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            options = {
                url: form.action,
                id: 'jo',
                cryptedStepCheck: cryptedStepCheck,
                go: 'Continue'
            };

            chooseCat(options);

            //console.log(JSON.stringify(options));
        });
    }

    //POST STEP1
    function chooseArea() {
        console.log("STARTING POST NUMBER", (args.indx + 1));
        console.log('choosing area');

        //phantom.exit();

        webpage.open(urls.postNew, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            var form, cryptedStepCheck, area, options;

            form = webpage.evaluate(function () {
                return document.querySelector('form.areapick.picker');
            });

            cryptedStepCheck = webpage.evaluate(function () {
                return document.querySelector('input[name=cryptedStepCheck]').value;
            });

            area = webpage.evaluate(function () {
                return document.querySelector('select[name=n]').value;
            });

            options = {
                url: form.action,
                n: jQueryXML.find('area').eq(args.indx).find('code').text().split('-')[0],
                cryptedStepCheck: cryptedStepCheck,
                go: 'Continue'
            };

            chooseType(options);

            //console.log(JSON.stringify(options));
        });
    }

    //LOGIN PAGE, REDIRECTS TO HOME
    function login() {
        //console.log(args.indx);

        //console.log(JSON.stringify(jsObj[0].jobs[0].title[0]));
        //console.log(JSON.stringify(loadXMLDoc('args.xml')));
        //console.log(JSON.stringify(parseXml(content).jobs.nickname));
        //console.log('read data:', content);

        //console.log(jQueryXML.find('area').eq(args.indx).find('title').text());

        // console.log(jQueryXML.find('area').eq(args.indx).find('code').text().split('-')[1]);
        // console.log(jQueryXML.find('area').eq(args.indx).find('code').text().split('-')[1]);
        // var type = jQueryXML.find('jobs drivertype').text().toLowerCase();.ucfirst()
        //var state_arr = getUnitedStates();
        //var state = getUnitedStates()[jQueryXML.find('area').eq(args.indx).find('title').text().substr(-2)].toLowerCase().ucfirst();
        // console.log(jQueryXML.find('jobs title').text()),
        // console.log(jQueryXML.find('jobs description').text());
        // console.log(jQueryXML.find('jobs requirements').text());
        // console.log(jQueryXML.find('jobs compensation').text());
        // console.log(type);
        // console.log(state);
        // console.log('<a href="http://98.129.135.61/landing-pages/l/craiglist/' + type + '/' + state + '" target="_blank">Click to apply online</a>');
        //console.log(state);
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
                phantom.exit();
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
                chooseArea();
            } else {
                phantom.exit();
            }
        });
    }

    function init() {
        console.log('init');

        //phantom.exit();

        webpage.customHeaders = {
            'Referer': urls.home
        };

        webpage.open(urls.home, function (status) {
            if (status === 'fail') {
                console.log('Failed');
                phantom.exit();
            }

            if (webpage.url.indexOf('login?rt=') >= 0) {
                login();
            } else {
                console.log('skiping login');
                chooseArea();
            }
            //console.log('web page loaded', webpage.url);
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

    if (args.indx % 4 === 0) {
        console.log('waiting 3 mins. ' + args.indx);
        setTimeout(function () {
            init();
        }, 180000);
    } else {
        init();
    }




}());