<?php

/**
 * Collection of static methods to get MH Settings
 *
 * @author Dario Curvino <@dudo>
 * @since 1.1.2
 * Class movieHelperGetSettings
 */
class movieHelperGetSettings {

    /***
     * Return a cleaned array of MH options
     *
     * @author Dario Curvino <@dudo>
     * @since 1.1.2
     * @return array|mixed
     */
    public static function mh () {
        $mh_settings    = get_option('moviehelper_settings');

        if(!is_array($mh_settings) || (is_array($mh_settings) && !isset($mh_settings['txt_after_links']))) {
            if(!is_array($mh_settings)) {
                $mh_settings = array();
            }

            if(!isset($mh_settings['txt_after_links'])) {
                $mh_settings['txt_after_links'] = false;
            }
        }

        return $mh_settings;
    }

    /**
     * Return a cleaned array of tmdb options
     *
     * @author Dario Curvino <@dudo>
     * @since  1.1.2
     * @return array|mixed
     */
    public static function tmdb () {
        $tmdb_options = get_option('moviehelper_tmdb_settings');

        // set default value for target_blank to false
        $target_blank  = false;
        $include_adult = false;

        // check if target_blank is set to true in $tmdb_options
        if(isset($tmdb_options['target_blank']) && (bool) $tmdb_options['target_blank'] === true) {
            $target_blank = true;
        }

        //get option for adult content
        if(isset($tmdb_options['include_adult']) && (bool)$tmdb_options['include_adult'] === true) {
            $include_adult = true;
        }

        $tmdb_options['target_blank']  = $target_blank;
        $tmdb_options['include_adult'] = $include_adult;

        //If apy_key is not set, initialize it on false
        if(!isset($tmdb_options['api_key'])) {
            $tmdb_options['api_key'] = false;
        }

        return $tmdb_options;
    }
}