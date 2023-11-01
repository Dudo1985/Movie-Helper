<?php

/**
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
    public function mh () {
        $mh_settings = get_option('moviehelper_settings', []);

        if(!isset($mh_settings['txt_after_links'])) {
            $mh_settings['txt_after_links'] = false;
        }

        $mh_settings['target_blank']  = $this->mhGetTarget($mh_settings);

        return $mh_settings;
    }

    /**
     * Return target blank option
     *
     * @author Dario Curvino <@dudo>
     *
     * @since 1.2.2
     *
     * @param $options array  The array with all tmdb options
     *
     * @return bool
     */
    private function mhGetTarget($options) {
        $target_blank  = false;

        // check if target_blank is set to true in $tmdb_options
        if(isset($options['target_blank']) && (bool) $options['target_blank'] === true) {
            $target_blank = true;
        }

        return $target_blank;
    }

    /**
     * Return a cleaned array of tmdb options
     *
     * @author Dario Curvino <@dudo>
     * @since  1.1.2
     * @return array|mixed
     */
    public function tmdb () {
        $tmdb_options = get_option('moviehelper_tmdb_settings', []);

        $tmdb_options['include_adult'] = $this->includeAdult($tmdb_options);

        //If apy_key is not set, initialize it on false
        if(!isset($tmdb_options['api_key'])) {
            $tmdb_options['api_key'] = false;
        }

        return $tmdb_options;
    }

    /**
     * Return include adult option
     *
     * @author Dario Curvino <@dudo>
     *
     * @since 1.2.2
     *
     * @param $options array  The array with all tmdb options
     *
     * @return bool
     */
    private function includeAdult($options) {
        $include_adult = false;

        //get option for adult content
        if(isset($options['include_adult']) && (bool)$options['include_adult'] === true) {
            $include_adult = true;
        }

        return $include_adult;
    }
}