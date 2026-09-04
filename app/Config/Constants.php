<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/*
| --------------------------------------------------------------------------
| Application upload paths
| --------------------------------------------------------------------------
| Carried over from the CodeIgniter 3 build. These are relative to the
| document root (the project root here), because they are used both to write
| files on disk and to build public URLs. ROOTPATH is prepended where an
| absolute filesystem path is required.
*/
defined('FILE_UPLOAD_PATH_TEMP')    || define('FILE_UPLOAD_PATH_TEMP', 'uploads/tmp/');
defined('PDF_PATH_TEMP')            || define('PDF_PATH_TEMP', ROOTPATH . 'uploads/tmp/');
defined('ORDER_CIRCULAR_PATH')      || define('ORDER_CIRCULAR_PATH', 'uploads/order_circular/');
defined('DOWNLOAD_PATH')            || define('DOWNLOAD_PATH', 'uploads/download/');
defined('GALLERY_ORIGINAL')         || define('GALLERY_ORIGINAL', 'uploads/gallery/original');
defined('GALLERY_THUMB')            || define('GALLERY_THUMB', 'uploads/gallery/thumb');
defined('GALLERY_RESIZE')           || define('GALLERY_RESIZE', 'uploads/gallery/resize');
defined('ADAYAPAKA_SABHAM_IMAGE')   || define('ADAYAPAKA_SABHAM_IMAGE', 'uploads/adayapaka_sabham/image');
defined('ADAYAPAKA_SABHAM_FILE')    || define('ADAYAPAKA_SABHAM_FILE', 'uploads/adayapaka_sabham/file');
defined('MEMBERSHIP_PATH')          || define('MEMBERSHIP_PATH', 'uploads/membership/');
defined('OFFICE_BEARER')            || define('OFFICE_BEARER', 'uploads/office_bearer');
defined('SLIDER_IMAGES')            || define('SLIDER_IMAGES', 'uploads/slider');
defined('REACTION_GALLERY_IMAGES')  || define('REACTION_GALLERY_IMAGES', 'uploads/reaction_gallery');

/*
| Aauth checks CI_VERSION to decide whether to load CI 2.x's driver library.
| CI4 exposes its version as a class constant rather than a global, so the
| constant is defined here to keep that check resolving.
*/
// Defined as a literal, not read off CodeIgniter\CodeIgniter: this file is
// loaded before the autoloader, so the class is not available yet.
defined('CI_VERSION') || define('CI_VERSION', '4.7.4');
