/**
 * Gruntfile for compiling theme_bootstrap .less files.
 *
 * This file configures tasks to be run by Grunt
 * http://gruntjs.com/ for the current theme.
 *
 *
 * Requirements:
 * -------------
 * nodejs, npm, grunt-cli.
 *
 * Installation:
 * -------------
 * node and npm: instructions at http://nodejs.org/
 *
 * grunt-cli: `[sudo] npm install -g grunt-cli`
 *
 * node dependencies: run `npm install` in the root directory.
 *
 *
 * Usage:
 * ------
 * Call tasks from the theme root directory. Default behaviour
 * (calling only `grunt`) is to run the watch task detailed below.
 *
 *
 * Porcelain tasks:
 * ----------------
 * The nice user interface intended for everyday use. Provide a
 * high level of automation and convenience for specific use-cases.
 *
 * grunt watch   Watch the less directory (and all subdirectories)
 *               for changes to *.less files then on detection
 *               run 'grunt compile'
 *
 *               Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 * grunt compile Run the .less files through the compiler, create the
 *               RTL version of the output, then run decache so that
 *               the results can be seen on the next page load.
 *
 *               Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 *
 *
 * Plumbing tasks & targets:
 * -------------------------
 * Lower level tasks encapsulating a specific piece of functionality
 * but usually only useful when called in combination with another.
 *
 * grunt less         Compile all less files.
 *
 * grunt less:moodle  Compile Moodle less files only.
 *
 * grunt decache      Clears the Moodle theme cache.
 *
 *                    Options:
 *
 *                    --dirroot=<path>  Optional. Explicitly define
 *                                      the path to your Moodle root
 *                                      directory when your theme is
 *                                      not in the standard location.
 *
 *
 * grunt replace             Run all text replace tasks.
 *
 *
 * @package theme
 * @subpackage Awesome
 * @author Willian Mano www.willianmano.net
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = function(grunt) { // jshint ignore:line

    // Import modules.
    var path = require('path');

    // Theme Bootstrap constants.
    var LESSDIR         = 'less',
        BOOTSWATCHDIR   = path.join(LESSDIR, 'bootswatch'), // jshint ignore:line
        THEMEDIR        = path.basename(path.resolve('.'));

    // PHP strings for exec task.
    var moodleroot = path.dirname(path.dirname(__dirname)), // jshint ignore:line
        configfile = '',
        decachephp = '',
        dirrootopt = grunt.option('dirroot') || process.env.MOODLE_DIR || ''; // jshint ignore:line

    // Allow user to explicitly define Moodle root dir.
    if ('' !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    var PWD = process.cwd(); // jshint ignore:line
    configfile = path.join(moodleroot, 'config.php');

    decachephp += 'define(\'CLI_SCRIPT\', true);';
    decachephp += 'require(\'' + configfile + '\');';
    decachephp += 'theme_reset_all_caches();';

    grunt.initConfig({
        less: {
            // Compile moodle styles.
            moodle: {
                options: {
                    compress: false,
                    strictMath: true,
                    outputSourceFiles: true,
                    sourceMap: true,
                    sourceMapRootpath: '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/moodle.css'
                },
                src: 'less/moodle.less',
                dest: 'style/moodle.css'
            }
        },
        autoprefixer: {
          options: {
            browsers: [
              'Android 2.3',
              'Android >= 4',
              'Chrome >= 20',
              'Firefox >= 24', // Firefox 24 is the latest ESR
              'Explorer >= 8',
              'iOS >= 6',
              'Opera >= 12',
              'Safari >= 6'
            ]
          },
          core: {
            options: {
              map: true
            },
            src: ['style/moodle.css'],
          },
        },
        cssmin: {
            options: {
                compatibility: 'ie8',
                keepSpecialComments: '*',
                noAdvanced: true
            },
            core: {
                files: {
                    'style/moodle_min.css': 'style/moodle.css'
                }
            }
        },
        csscomb: {
            options: {
                config: 'less/.csscomb.json'
            },
            dist: {
                expand: true,
                cwd: 'style/',
                src: ['moodle.css'],
                dest: 'style/'
            }
        },
        exec: {
            decache: {
                cmd: 'php -r "' + decachephp + '"',
                callback: function(error) {
                    // exec will output error messages
                    // just add one to confirm success.
                    if (!error) {
                        grunt.log.writeln("Moodle theme cache reset.");
                    }
                }
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["less/**/*.less"],
            tasks: ["compile"],
            options: {
                spawn: false,
                livereload: true
            }
        },
        replace: {
            sourcemap: {
                src: ['style/moodle.css'],
                    overwrite: true,
                    replacements: [{
                        from: 'sourceMappingURL=',
                        to: 'sourceMappingURL=/theme/' + THEMEDIR + '/style/'
                    }]
            }
        }
    });

    // Load contrib tasks.
    grunt.loadNpmTasks("grunt-autoprefixer");
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-exec");
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks("grunt-css-flip");
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-csscomb');

    // Load core tasks.
    grunt.loadNpmTasks('grunt-contrib-jshint');

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("decache", ["exec:decache"]);

    grunt.registerTask("compile", [
        "less",
        "autoprefixer",
        'csscomb',
        'cssmin',
        "replace:sourcemap",
        "decache"]);
    grunt.registerTask(["jshint", "decache"]);
};
