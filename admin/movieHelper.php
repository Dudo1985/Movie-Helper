<?php

/*

Copyright 2021 Dario Curvino (email : d.curvino@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

class movieHelper {

    /**
     * Object instanceof class movieHelperGetSettings
     * @var \movieHelperGetSettings
     */
    public $settings;

    /**
     * @var \movieHelperScripts
     */
    public $scripts;

    public function init() {
        $this->defineConstants();

        //Run this only on plugin activation (doesn't work on update)
        register_activation_hook(MOVIEHELPER_ABSOLUTE_PATH.'/movie-helper.php', [$this, 'onActivation']);

        //load all classes
        $this->autoloadMHClasses();

        //initialize the classes

        //class used to get settings
        $this->settings = new movieHelperGetSettings();

        //class used to hooks js and css scripts
        $this->scripts = new movieHelperScripts();

        //do defines for MH Options
        $this->defineMHOptions();

        //do defines for TMDB Options
        $this->defineTMDBOptions();

        //load settingsPage Class
        $this->settingsPage();

        //load movieHelperEditor class
        $this->initEditor();

        //Init translations
        add_action('init', [$this, 'translate']);

        //enqueue scripts in 'admin_enqueue_scripts' hook
        $this->scripts->init();

        //once plugins are loaded, update version
        add_action('plugins_loaded', [$this, 'updateVersion']);
    }

    /**
     * Define Constants
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function defineConstants() {
        //Plugin absolute path
        //e.g. /var/www/html/plugin_development/wp-content/plugins/movie-helper
        //by default, dirname get the parent
        define('MOVIEHELPER_ABSOLUTE_PATH', dirname(__DIR__));

        //Plugin RELATIVE PATH without slashes (just the directory's name)
        //Do not use just 'movie-helper' here, because the directory name
        //can be different, e.g. movie-helper-premium or
        //MOVIEHELPER-2.3.1 (branch name)
        define('MOVIEHELPER_RELATIVE_PATH', dirname(plugin_basename(__DIR__)));

        //admin absolute path
        define('MOVIEHELPER_ABSOLUTE_PATH_ADMIN', MOVIEHELPER_ABSOLUTE_PATH . '/admin');

        //admin relative path
        define('MOVIEHELPER_RELATIVE_PATH_ADMIN', MOVIEHELPER_RELATIVE_PATH . '/admin');

        //IMG directory absolute URL
        define('MOVIEHELPER_IMG_DIR', plugins_url() . '/' . MOVIEHELPER_RELATIVE_PATH_ADMIN . '/img/');

        define('MOVIEHELPER_JS_DIR', plugins_url() . '/' . MOVIEHELPER_RELATIVE_PATH_ADMIN . '/js/');

        define('MOVIEHELPER_CSS_DIR', plugins_url() . '/' . MOVIEHELPER_RELATIVE_PATH_ADMIN . '/css/');

        //Plugin language directory: here I've to use relative path
        //because load_plugin_textdomain wants relative and not absolute path
        define('MOVIEHELPER_LANG_DIR', MOVIEHELPER_RELATIVE_PATH . '/languages/');

        //version installed
        define('MOVIEHELPER_VERSION_INSTALLED', $this->versionInstalled());

        //default api key
        define('MOVIEHELPER_TMDB_DEFAULT_APIKEY', 'd4c4f18bb357c68018b409f7f00ab072');

    }

    /**
     * Autoload all classes inside admin/ that name contains movieHelper
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function autoloadMHClasses() {
        //AutoLoad MH Classes, only when an object is created
        spl_autoload_register(static function ($class) {
            /**
             * If the class being requested does not start with 'movie' prefix,
             * it's not in movie-helper Project
             */
            if (0 !== strpos($class, 'movieHelper')) {
                return;
            }
            $file_name =  MOVIEHELPER_ABSOLUTE_PATH_ADMIN .'/'. $class . '.php';

            // check if file exists, just to be sure
            if (file_exists($file_name)) {
                require($file_name);
            }

            //load class in /editor dir
            $file_name_editor =  MOVIEHELPER_ABSOLUTE_PATH_ADMIN .'/editor/'. $class . '.php';

            // check if file exists, just to be sure
            if (file_exists($file_name_editor)) {
                require($file_name_editor);
            }
        });
    }

    /**
     * Return version installed, if not found, 0 is returned
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @return false|mixed|void
     */
    public function versionInstalled() {
        return get_option('moviehelper-version', 0);
    }

    /**
     * Actions to do on plugin activation
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $network_wide  //indicate if the plugin is network activated
     */
    public function onActivation($network_wide) {
        //do action when plugin is installed for first time
        if(MOVIEHELPER_VERSION_INSTALLED === 0) {
            $this->install($network_wide);
        }
    }

    /**
     * Action to do when plugin is installed for the first time
     * not yet used
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $network_wide //indicate if the plugin is network activated
     */
    private function install($network_wide) {
        //default settings
        $default_data = [
            'api_key'       => MOVIEHELPER_TMDB_DEFAULT_APIKEY,
            'include_adult' => false
        ];
        delete_transient('tmdb_api_key');
        update_option('moviehelper_tmdb_settings', $default_data);
    }

    /**
     * Update plugin version
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function updateVersion(){
        if (MOVIEHELPER_VERSION_NUM !== MOVIEHELPER_VERSION_INSTALLED) {
            update_option('moviehelper-version', MOVIEHELPER_VERSION_NUM);
        }
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function translate() {
        load_plugin_textdomain('movie-helper', false, MOVIEHELPER_LANG_DIR);
    }

    /**
     * Define MH settings
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function defineMHOptions () {
        $mh_options = $this->settings->mh();

        //define mh settings
        define('MOVIEHELPER_TEXT_AFTER_LINKS',  $mh_options['txt_after_links']);
        define('MOVIEHELPER_TARGET_BLANK', $mh_options['target_blank']);

    }

    /**
     * Define settings for TMDB
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function defineTMDBOptions () {
        $tmdb_options =  $this->settings->tmdb();

        //define tmdb settings
        define('MOVIEHELPER_TMDB_ADULT',         $tmdb_options['include_adult']);
        define('MOVIEHELPER_TMDB_CUSTOM_APIKEY', $tmdb_options['api_key']);
    }

    /**
     * Return true if key is valid, string with status message if error
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $api_key
     *
     * @return bool|string
     */
    public static function validateTMDBApiKey ($api_key) {
        $api_url       = 'https://api.themoviedb.org/3/movie/550?api_key='.$api_key;
        $valid_api     = self::checkUrl($api_url);

        if($valid_api !== true) {
            if(is_object($valid_api)) {
                $error = $valid_api->status_message;
            } else {
                $error = $valid_api;
            }
            $transient_value = [
                'error'   => true,
                'message' => $error
            ];
            set_transient('tmdb_api_key', $transient_value, DAY_IN_SECONDS);
            return $error;
        }

        $transient_value = [
            'error'   => false,
            'api_key' => $api_key
        ];

        set_transient('tmdb_api_key', $transient_value, DAY_IN_SECONDS);
        return true;

    }

    /**
     * Returns transient if exists
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @return false|mixed
     */
    public static function getTMDBApiKeyTransient() {
        $tmdb_transient = get_transient('tmdb_api_key');

        if($tmdb_transient !== false) {
            return $tmdb_transient;
        }
        return false;
    }


    /**
     * Init settings page
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function settingsPage() {
        $mhSettings = new movieHelperSettingsPage();
        $mhSettings->init();
    }

    /**
     * Load editor class
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function initEditor() {
        $mhEditor = new movieHelperEditor();
        $mhEditor->init();
    }

    /**
     * Return all user registered post types.
     * Must be used on init or after
     *
     * @return bool|string[]|WP_Post_Type[]
     */
    public static function getCustomPostTypes() {
        $args = [
            'public'   => true,
            '_builtin' => false
        ];

        $output   = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        //if not found, returns an empty array
        $post_types = get_post_types( $args, $output, $operator );

        if ($post_types) {
            return ($post_types);
        }
        return false;
    }

    /**
     * Check if a given url exists and validate api key
     * return true if url is reachable and api keys works
     * return false if url is not valid
     * return error code if is_wp_error
     * return the body is https status is not 200
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $url
     *
     * @return bool|int|object
     */
    public static function checkUrl ($url) {
        //Check if url is valid
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            $api_response = wp_remote_get($url);

            if(is_wp_error($api_response)) {
                return $api_response->get_error_message();
            }

            if(wp_remote_retrieve_response_code($api_response) !== 200) {
                return json_decode(wp_remote_retrieve_body($api_response));
            }
            return true;
        }
        return false;
    }

     /**
     * Check if the current page is the Gutenberg block editor.
     *
     * @since  1.0.0
     *
     * @return bool
     */
    public static function isGutenPage() {
        if (function_exists('is_gutenberg_page') && is_gutenberg_page() ) {
            // The Gutenberg plugin is on.
            return true;
        }
        $current_screen = get_current_screen();
        if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ) {
            // Gutenberg page on 5+.
            return true;
        }
        return false;
    }

}