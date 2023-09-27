<?php

/**
 * @author Dario Curvino <@dudo>
 * @since
 * @return
 */
class movieHelperScripts {
    public function init() {
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    //$hook contain the current page in the admin side
    public function enqueueScripts($hook) {
        wp_register_script('moviehelper-global-data', '', [], '', true);
        wp_enqueue_script('moviehelper-global-data');

        //enqueue css
        $this->enqueueCSS($hook);

        //enqueue js
        $this->enqueueJS($hook);

        //enqueue inline scripts
        $this->enqueueInlineScripts($hook);
    }

    /**
     * Enqueue css
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $hook
     */
    public function enqueueCSS($hook) {
        if ($hook === 'post.php'
            || $hook === 'post-new.php'
            || $hook === MOVIEHELPER_SETTINGS_PAGE) {

            wp_enqueue_style(
                'moviehelpercss', MOVIEHELPER_CSS_DIR . 'admin.css', false, MOVIEHELPER_VERSION_NUM
            );
        }
    }

    /**
     * Enqueue js
     *
     * @author Dario Curvino <@dudo>
     * @since 2.0.0
     * @param $hook
     */
    public function enqueueJS($hook) {
        if ($hook === 'post.php' || $hook === 'post-new.php') {

            wp_enqueue_script(
                'themoviedb',
                MOVIEHELPER_JS_DIR . 'wrappers/themoviedb.js',
                '',
                MOVIEHELPER_VERSION_NUM,
                true
            );

            wp_enqueue_script(
                'moviehelper-editor',
                MOVIEHELPER_JS_DIR . 'editor.js',
                ['themoviedb', 'wp-i18n'],
                MOVIEHELPER_VERSION_NUM,
                true
            );

        }

        if($hook === 'settings_page_moviehelper_settings_page') {
            wp_enqueue_script(
                'themoviedb_settings',
                MOVIEHELPER_JS_DIR . 'settings.js',
                '',
                MOVIEHELPER_VERSION_NUM,
                true
            );
        }

    }

    /**
     * Enqueue inline scripts
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $hook
     */
    public function enqueueInlineScripts($hook) {
        if ($hook === 'post.php' || $hook === 'post-new.php') {

            $mh_common_data = json_encode(
                [
                    'guten_page'         => movieHelper::isGutenPage(),
                    'img_dir'            => MOVIEHELPER_IMG_DIR,
                    'lang'               => str_replace('_', '-', get_locale()),
                    'custom_text_link'   => json_encode(wp_kses_post(MOVIEHELPER_TEXT_AFTER_LINKS)),
                    'target_blank'  => json_encode(MOVIEHELPER_TARGET_BLANK),
                    'tmdb' => [
                        'include_adult' => MOVIEHELPER_TMDB_ADULT, //leave this as a string
                        'api_key'       => MOVIEHELPER_TMDB_CUSTOM_APIKEY
                    ]
                ]
            );

            //check if wp_add_inline_script has already run before
            if (!defined('MOVIEHELPER_GLOBAL_DATA_EXISTS')) {
                wp_add_inline_script(
                    'moviehelper-global-data', 'var movieHelperCommonData = ' . $mh_common_data, 'before'
                );

                //use a constant to be sure that moviehelper-global-data is not loaded twice
                define('MOVIEHELPER_GLOBAL_DATA_EXISTS', true);
            }
        }
    }
}