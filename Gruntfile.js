/*global module:false*/
module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        watch: {
            gruntfile: {
                files: '<%= jshint.gruntfile.src %>',
                tasks: ['jshint:gruntfile']
            },
            lib_test: {
                files: '<%= jshint.lib_test.src %>',
                tasks: ['jshint:lib_test', 'qunit']
            }
        },
        //new stuff
        copy: {
            production: {
                files: [
                    {expand: true, src: [
                            'manager/**',
                            'protected/commands/**',
                            'protected/components/**',
                            'protected/config/**',
                            'protected/controllers/**',
                            'protected/extensions/**',
                            'protected/helpers/**',
                            'protected/models/**',
                            'protected/runtime/**',
                            'protected/views/**',
                            'protected/vendor/**',
                            'css/**',
                            'images/**',
                            'js/**',
                            'fonts/**'
                        ], dest: 'production/'},
                    {src: ['.htaccess'], dest: 'production/', filter: 'isFile'},
                    {src: ['index.php'], dest: 'production/', filter: 'isFile'},
                    {src: ['protected/yiic'], dest: 'production/', filter: 'isFile'},
                    {src: ['protected/yiic.php'], dest: 'production/', filter: 'isFile'}
                ]
            }
        },
        uglify: {
            production: {
                files: [{
                        expand: true,
                        src: 'production/js/**/*.js',
                        dest: ''
                    }, {
                        expand: true,
                        src: ['production/vendor/**/*.js', '!production/vendor/**/*.min.js'],
                        dest: ''
                    }]
            }
        },
        //
        'string-replace': {
            production: {
                files: {
                    'production/': [
                        'index.php',
                        'protected/config/main.php',
                        'protected/yiic.php'
                    ]
                },
                options: {
                    replacements: [
                        {
                            pattern: 'mysql:host=localhost;dbname=craigslist_tool',
                            replacement: 'mysql:host=localhost;dbname=craigslist_tool'
                        },
                        {
                            pattern: '\'username\' => \'dev\',',
                            replacement: '\'username\' => \'root\','
                        },
                        {
                            pattern: '\'password\' => \'XDRseATFrrC8EVuB\',',
                            replacement: '\'password\' => \'3lcDF2BZV7\','
                        },
                        {
                            pattern: '/../../../yii-1.1.14.f0fee9',
                            replacement: '/../yii-1.1.14-rc'
                        },
                        {
                            pattern: '\'enableParamLogging\' => true',
                            replacement: '\'enableParamLogging\' => false'
                        },
                        {
                            pattern: 'defined(\'YII_MODE\') or define(\'YII_MODE\', \'dev\');',
                            replacement: 'defined(\'YII_MODE\') or define(\'YII_MODE\', \'staging\');'
                        },
                        {
                            pattern: '\'phantomjs_path\' => \'/Applications/phantomjs-2.0.0-macosx/bin/phantomjs\'',
                            replacement: '\'phantomjs_path\' => \'/usr/bin/phantomjs\''
                        }]
                }
            }
        },
        //
        clean: {
            production: 'production/',
            logs: ['staging/logs/**', 'production/logs/**']
        },
        //
        cssmin: {
            production: {
                minify: {
                    expand: true,
                    cwd: 'production/css/',
                    src: ['*.css', '!*.min.css'],
                    dest: 'production/css/',
                    ext: '.css'
                }
            }
        },
        //
        chmod: {
            options: {
                mode: '+x'
            },
            production: {
                // Target-specific file/dir lists and/or options go here.
                src: ['production/protected/yiic']
            }
        },
        rsync: {
            options: {
                args: ["--verbose -p"],
                exclude: [".git*", "*.scss", "node_modules"],
                recursive: true
            },
            production: {
                options: {
                    src: "./production/",
                    dest: "root@192.241.223.22:/var/www/html",
                    ssh: true,
                    deleteAll: false // Careful this option could cause data loss, read the docs!
                }
            }
        },
        secret: grunt.file.readJSON('secret.json'),
        sshexec: {
            runsetup: {
                command: ['sh -c "cd /var/www/html/protected; chmod +x yiic; ./yiic setup run;"'],
                options: {
                    host: '<%= secret.host %>',
                    username: '<%= secret.username %>',
                    password: '<%= secret.password %>'
                }
            }
        }
    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks('grunt-contrib-watch');
    //
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks("grunt-rsync");
    grunt.loadNpmTasks('grunt-chmod');
    grunt.loadNpmTasks('grunt-ssh');

    grunt.registerTask('production', ['clean:production', 'copy:production',
        'string-replace:production', 'chmod:production']); //
    grunt.registerTask('sync-production', ['production', 'rsync:production']); //
    grunt.registerTask('runsetup', ['sshexec:runsetup']); //

};
