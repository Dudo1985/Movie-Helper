<?php

/**
 * Collection of static methods to get MH Settings
 *
 * @author Dario Curvino <@dudo>
 * @since 1.1.2
 * Class movieHelperGetSettings
 */
class movieHelperGetSettings {

    /**
     * Return a cleanded array of tmdb options
     *
     * @author Dario Curvino <@dudo>
     * @since  1.1.2
     * @return array|mixed
     */
    public static function tmdb () {
        $tmdb_options = get_option('moviehelper_tmdb_settings');

        //If apy_key is not set, initialize it on false
        if(!isset($tmdb_options['api_key'])) {
            $tmdb_options['api_key'] = false;
        }

        if(isset($tmdb_options['include_adult']) && ((bool)$tmdb_options['include_adult']) === true) {
            $tmdb_options['include_adult'] = true;
        } else {
            $tmdb_options['include_adult'] = false;
        }

        return $tmdb_options;

    }
}