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

        //If apy_key is not set, initialize it on false
        if(is_array($mh_settings) && !isset($mh_settings['txt_after_links'])) {
            $mh_settings['txt_after_links'] = false;
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

        //get option to open in new window
        if(isset($tmdb_options['target_blank']) && ((bool)$tmdb_options['target_blank']) === true) {
            $tmdb_options['target_blank'] = true;
        } else {
            $tmdb_options['target_blank'] = false;
        }

        //get option for adult content
        if(isset($tmdb_options['include_adult']) && ((bool)$tmdb_options['include_adult']) === true) {
            $tmdb_options['include_adult'] = true;
        } else {
            $tmdb_options['include_adult'] = false;
        }

        //If apy_key is not set, initialize it on false
        if(!isset($tmdb_options['api_key'])) {
            $tmdb_options['api_key'] = false;
        }

        return $tmdb_options;

    }
}