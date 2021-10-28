<?php

/*

Copyright 2021 Dario Curvino (email : d.curvino@tiscali.it)

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

/**
 * @author Dario Curvino <@dudo>
 * @since 1.0.0
 * Class movieHelperSettings
 */
class movieHelperSettings {
    public function init() {
        //hook to admin menu to add the link to the setting page
        add_action('admin_menu', [$this, 'addOptionsPageLink']);

        add_action('admin_init', [$this, 'tmdbSection']); //This is for general options

        define('MOVIEHELPER_SAVE_All_SETTINGS_TEXT', __('Save all settings', 'movie-helper'));
    }

    /**
     * Add page's setting link
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function addOptionsPageLink () {
        $option_page = add_options_page(
            'Movie Helper: Settings', //Page Title
            __( 'Movie Helper: Settings', 'movie-helper' ), //Menu Title
            'manage_options', //capability
            'moviehelper_settings_page', //menu slug
            [$this, 'optionsPageCallback'] //The function to be called to output the content for this page.
        );

        if(!defined('MOVIEHELPER_SETTINGS_PAGE')) {
            define('MOVIEHELPER_SETTINGS_PAGE', $option_page);
        }
    }

    /**
     * Callback for setting page
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function optionsPageCallback () {
        if (!current_user_can('manage_options')) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'movie-helper' ) );
        }

        $this->settingsPage();
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @return void
     */
    private function settingsPage() {
        ?>
        <div class="wrap">
            <h2>
                <?php esc_html_e('Movie Helper: Settings', 'movie-helper') ?>
            </h2>
            <div class="moviehelper-settingsdiv">
                <form action="options.php" method="post" id="moviehelper-settings-form">
                    <?php
                        settings_fields('moviehelper_settings_group');
                        do_settings_sections('moviehelper_tmdb_settings');
                        submit_button(MOVIEHELPER_SAVE_All_SETTINGS_TEXT);
                    ?>
                </form>
            </div>
            <div class="moviehelper-settings-clear"></div>
            <?php $this->rightPanel(); ?>
        </div>



        <?php
    }

    /**
     * Register setttings section and field for TMDB
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function tmdbSection() {
        register_setting(
            'moviehelper_settings_group', // A settings group name. Must exist prior to the register_setting call.
            // This must match the group name in settings_fields()
            'moviehelper_tmdb_settings', //The name of an option to sanitize and save.
            [$this, 'sanitizeTMDBSettings']
        );

        $tmdb_settings    = get_option('moviehelper_tmdb_settings');
        $tmdb_description = __('The Movie Database (TMDB) is a community built movie and TV database.', 'movie-helper');

        add_settings_section(
            'moviehelper_tmdb_section',
            __('General settings', 'movie-helper'),
            '',
            'moviehelper_tmdb_settings'
        );

        add_settings_field(
            'moviehelper_tmdb_api_key',
            $tmdb_description,
            [$this, 'tmdbSettingsInputApiKey'],
            'moviehelper_tmdb_settings',
            'moviehelper_tmdb_section',
            $tmdb_settings
        );

        add_settings_field(
            'moviehelper_tmdb_filter_adult',
            '',
            [$this, 'tmdbSettingsFilterAdult'],
            'moviehelper_tmdb_settings',
            'moviehelper_tmdb_section',
            $tmdb_settings
        );
    }

    /**
     * Print the input id to insert the TMDB api key
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     *
     * @param $tmdb_settings
     */
    public function tmdbSettingsInputApiKey($tmdb_settings) {
        if(!isset($tmdb_settings['api_key'])) {
            $key = '';
        } else {
            $key = $tmdb_settings['api_key'];
        }
        ?>
        <strong><?php esc_html_e('Api Key', 'movie-helper') ?></strong>
        <p></p>
            <input
                type="text"
                id="moviehelper-tmdb-apikey"
                name="moviehelper_tmdb_settings[api_key]"
                value="<?php echo esc_attr($key) ?>"
            > <br />
            <label for="moviehelper-tmdb-apikey" class="moviehelper-element-description">
                <?php echo wp_kses_post(sprintf(
                        __('Click %shere%s to create your API key. A (free) account on TMDB is needed.', 'movie-helper'),
                    '<a href="https://developers.themoviedb.org/3/getting-started/introduction">', '</a>')); ?>
            </label>
        <?php
    }


    public function tmdbSettingsFilterAdult($tmdb_settings) {
        if(!isset($tmdb_settings['include_adult'])) {
            $include_adult = false;
        } else {
            $include_adult = true;
        }
        ?>
        <strong><?php esc_html_e('Include adult content?', 'movie-helper') ?></strong>
        <p></p>
        <div class="moviehelper-onoffswitch-big">
            <input type="checkbox" name="moviehelper_tmdb_settings[include_adult]"
                <?php if ($include_adult === true){ echo 'checked="checked'; }?>
                   value="true"
                   class="moviehelper-onoffswitch-checkbox"
                   id="moviehelper-include-adult"
            />
            <label class="moviehelper-onoffswitch-label" for="moviehelper-include-adult">
                <span class="moviehelper-onoffswitch-inner"></span>
                <span class="moviehelper-onoffswitch-switch"></span>
            </label>
        </div> <br />
        <?php
    }

    /***
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     * @param $options
     *
     * @return array
     */
    public function sanitizeTMDBSettings($options) {
        // Create our array for storing the validated options
        $output = array();

        // Loop through each of the incoming options
        foreach($options as $key => $value ) {
            // Check to see if the current option has a value. If so, process it.
            if(isset($value)) {
                // Strip all HTML and PHP tags and properly handle quoted strings
                $output[$key] = esc_html(strip_tags(stripslashes($value)));
            } // end it
        } // end foreach

        delete_transient('tmdb_api_key');

        return $output;
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since  1.0.0
     */
    private function rightPanel () {
        $this->buyACoffee();
        $this->alsoLike();
        $this->askRating();
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    private function buyACoffee() {
        $buymecofeetext = __('Coffee is vital to make Movie Helper development going on!', 'movie-helper');
        $buymecofeetext .= '<br />';
        $buymecofeetext .= __('If you are enjoying Movie Helper, please consider to buy me a coffee, thanks!',
            'movie-helper');
        $div = "<div class='moviehelper-donatedivdx' id='moviehelper-buy-cofee'>";
        $text  = '<div class="moviehelper-donate-title">' . __('Buy me a coffee!', 'movie-helper') .'</div>';
        $text .= '<div class="moviehelper-donate-content">';
        $text .= '<a href="https://www.buymeacoffee.com/dariocurvino" target="_blank">
                    <img src="'.MOVIEHELPER_IMG_DIR.'/buymecofyel.png" alt="buymeacofee">
                  </a>';
        $text .= '<p>';
        $text .= $buymecofeetext;
        $text .= '</p>';
        $text .= '</div>';
        $div_and_text = $div . $text . '</div>';

        echo wp_kses_post($div_and_text);
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since
     */
    private function askRating() {
        $div = "<div class='moviehelper-donatedivdx' id='moviehelper-ask-five-stars'>";

        $text = '<div class="moviehelper-donate-title">' . __('Can I ask your help?', 'movie-helper') .'</div>';
        $text .= '<div class="moviehelper-donate-content">';
        $text .= '<div style="font-size: 32px; color: #F1CB32; margin-bottom: 20px; margin-top: -5px;">
                        <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                        <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                        <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                        <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                        <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                    </div>';
        $text .= '<p>';
        $text .= __('Please rate Movie Helper 5 stars on', 'movie-helper');
        $text .= ' <a href="https://wordpress.org/support/view/plugin-reviews/movie-helper">
        WordPress.org.</a><br />';
        $text .= __(' It will require just 1 min but it\'s a HUGE help for me. Thank you.', 'movie-helper');
        $text .= '<br /><br />';
        $text .= '<em>> Dario Curvino</em>';
        $text .= '<p>';
        $text .= '</div>';

        $div_and_text = $div . $text . '</div>';

        echo wp_kses_post($div_and_text);
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    private function alsoLike() {
        $text  = "<div class='moviehelper-donatedivdx' id='alsolike'>";
        $text .= '<div class="moviehelper-donate-title">' . __('You may also like...', 'movie-helper') .'</div>';
        $text .= '<div class="moviehelper-donate-content">';
        $text .= $this->yasr();
        $text .= '</p><hr />';
        $text .= $this->cnrt();
        $text .= '</div>'; //second div
        $text .= '</div>'; //first div

        echo wp_kses_post($text);
    }

    /**
     * Yasr Box
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.2
     * @return string
     */
    private function yasr() {
        $text = '<a href="https://wordpress.org/plugins/yet-another-stars-rating/">';
        $text .= '<img src="'.MOVIEHELPER_IMG_DIR.'/yasr.png" alt="yasr" width="110">';
        $text .= '<div>YASR - Yet Another Stars Rating</div>';
        $text .= '</a>';
        $text .= '<p>';
        $text .= __('Boost the way people interact with your site with an easy WordPress stars rating system! 
        With Schema.org rich snippets YASR will improve your SEO!', 'movie-helper');

        return $text;
    }

    /**
     * CNRT box
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.2
     * @return string
     */
    private function cnrt() {
        $text  = '<div class="moviehelper-donate-content">';
        $text .= '<a href="https://wordpress.org/plugins/comments-not-replied-to/">';
        $text .= '<img src="'.MOVIEHELPER_IMG_DIR.'/cnrt.png" alt="cnrt" width="110">';
        $text .= '<div>Comments Not Replied To</div>';
        $text .= '</a>';
        $text .= '<p>';
        $text .= __('"Comments Not Replied To" introduces a new area in the administrative dashboard that allows you to 
        see what comments to which you - as the site author - have not yet replied.', 'movie-helper');
        $text .= '</p>';
        $text .= '</div>';

        return $text;
    }
    
}