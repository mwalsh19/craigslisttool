/*
 Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

        CKEDITOR.editorConfig = function(config)
        {
            // Define changes to default configuration here. For example:
            config.language = 'es';
            config.toolbar = [
                {
                    name: 'document',
                    groups: ['mode', 'document', 'doctools'],
                    items: ['Source']
                },
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo'],
                    items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                        '-', 'Undo', 'Redo']
                },
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup'],
                    items: ['Bold', 'Italic', 'Strike', '-',
                        'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align'],
                    items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter',
                        'JustifyRight', 'JustifyBlock', 'Outdent', 'Indent']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image',
                        'Table',
                        'HorizontalRule',
                        'SpecialChar']
                },
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'FontSize']
                            //items: ['Format']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ];

            config.filebrowserBrowseUrl = 'elfinder/elfinder.html';
            config.filebrowserWindowHeight = '540';
            config.skin = 'BootstrapCK-Skin';
            //config.stylesSet = [];
            //config.extraPlugins = 'stylesheetparser';
//            config.contentsCss = '/admin/ckeditor/parsestlyes.css';
            config.fontSize_sizes = "30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%";
            //config.toolbar = 'Full';
        };
