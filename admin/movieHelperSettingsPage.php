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

/**
 * @author Dario Curvino <@dudo>
 * @since 1.0.0
 * Class movieHelperSettings
 */
class movieHelperSettingsPage {
    public function init() {
        //hook to admin menu to add the link to the setting page
        add_action('admin_menu', [$this, 'addOptionsPageLink']);

        require MOVIEHELPER_ABSOLUTE_PATH_ADMIN . '/movie-helper-settings-misc.php';

        add_action('admin_init', [$this, 'mhSection']); //This is for general options

        add_action('admin_init', [$this, 'tmdbSection']); //This is for TMDB settings

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
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'movie-helper' ) );
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
                <br />
                <form action="options.php" method="post" id="moviehelper-settings-form">
                    <?php
                        settings_fields('moviehelper_settings_group');
                        do_settings_sections('moviehelper_settings');
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
    public function mhSection() {
        register_setting(
            'moviehelper_settings_group', // A settings group name. Must exist prior to the register_setting call.
            // This must match the group name in settings_fields()
            'moviehelper_settings', //The name of an option to sanitize and save.
            [$this, 'sanitizeTMDBSettings']
        );

        add_settings_section(
            'moviehelper_section',
            '',
            '',
            'moviehelper_settings'
        );

        add_settings_field(
            'moviehelper_customize_links',
            movie_helper_customize_links_description(),
            [$this, 'customizeLinks'],
            'moviehelper_settings',
            'moviehelper_section'
        );


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

        add_settings_section(
            'moviehelper_tmdb_section',
            '',
            '',
            'moviehelper_settings'
        );

        add_settings_field(
            'moviehelper_tmdb_filter_adult',
            esc_html__('The Movie Database (TMDB) Settings', 'movie-helper'),
            [$this, 'tmdbSettingsFilterAdult'],
            'moviehelper_settings',
            'moviehelper_tmdb_section'
        );

        add_settings_field(
            'moviehelper_tmdb_api_key',
            esc_html__('', 'movie-helper'),
            [$this, 'tmdbSettingsInputApiKey'],
            'moviehelper_settings',
            'moviehelper_tmdb_section'
        );


    }

    /**
     * Print the input fields to customize links
     *
     * @author Dario Curvino <@dudo>
     * @since  1.1.2
     */
    public function customizeLinks () {
        ?>
        <div class="moviehelper-general-settings">
            <label for="moviehelper-custom-links">
                <strong>
                    <?php echo esc_html__('Customize links', 'movie-helper') ?>
                </strong>
            </label>
            <p></p>
            <input
                type="text"
                id="moviehelper-custom-links"
                name="moviehelper_settings[txt_after_links]"
                value="<?php echo esc_attr(MOVIEHELPER_TEXT_AFTER_LINKS) ?>"
                placeholder="<?php echo esc_html__('(%vote_average%), %year% ')?>"
            >
            <p></p>
            <div class="moviehelper-element-description" style="margin-top: 10px;">
                <span>Custom text to show after a link, allowed variables:</span>
                <p style="margin-left: 10px;">
                    <strong>%year%</strong> - Display the year <br />
                    <strong>%vote_average%</strong> - Display the vote average <br />
                    <strong>%vote_count%</strong> - Display the vote count
                </p>
            </div>

            <div>
                <strong><?php esc_html_e('Open link in new tab?', 'movie-helper') ?></strong>
                <p></p>
                <div class="moviehelper-onoffswitch-big">
                    <input type="checkbox" name="moviehelper_settings[target_blank]"
                        <?php if (MOVIEHELPER_TARGET_BLANK === true){ echo 'checked="checked"'; }?>
                           value="true"
                           class="moviehelper-onoffswitch-checkbox"
                           id="moviehelper-target-blank"
                    />
                    <label class="moviehelper-onoffswitch-label" for="moviehelper-target-blank">
                        <span class="moviehelper-onoffswitch-inner"></span>
                        <span class="moviehelper-onoffswitch-switch"></span>
                    </label>
                </div>
            </div>

        </div>

        <?php
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since  1.1.0
     */
    public function tmdbSettingsFilterAdult() {
        ?>
        <div>
            <strong><?php esc_html_e('Include adult content?', 'movie-helper') ?></strong>
            <p></p>
            <div class="moviehelper-onoffswitch-big">
                <input type="checkbox" name="moviehelper_tmdb_settings[include_adult]"
                    <?php if (MOVIEHELPER_TMDB_ADULT === true){ echo 'checked="checked"'; }?>
                       value="true"
                       class="moviehelper-onoffswitch-checkbox"
                       id="moviehelper-include-adult"
                />
                <label class="moviehelper-onoffswitch-label" for="moviehelper-include-adult">
                    <span class="moviehelper-onoffswitch-inner"></span>
                    <span class="moviehelper-onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * Print the input id to insert the TMDB api key
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     *
     */
    public function tmdbSettingsInputApiKey() {
        if(MOVIEHELPER_TMDB_CUSTOM_APIKEY === false || MOVIEHELPER_TMDB_CUSTOM_APIKEY === MOVIEHELPER_TMDB_DEFAULT_APIKEY) {
            $restore_button = '';
            $key = MOVIEHELPER_TMDB_DEFAULT_APIKEY;
        } else {
            $restore_button = '<input type="button"
                     id="moviehelper-default-apikey"
                     class="button"
                     value="'.esc_attr__('Restore default Api Key', 'movie-helper').'" />' ;
            $key = MOVIEHELPER_TMDB_CUSTOM_APIKEY;
        }

        ?>
        <strong><?php esc_html_e('Api Key', 'movie-helper') ?></strong>
        <p></p>
            <input
                type="text"
                id="moviehelper-tmdb-apikey"
                name="moviehelper_tmdb_settings[api_key]"
                value="<?php echo esc_attr($key) ?>"
            >
            <?php
                //allow html input tag or button will not be printed
                $allowed_html = [
                    'input' => [
                        'type'  => [],
                        'id'    => [],
                        'class' => [],
                        'value' => []
                    ]
                ];
                echo wp_kses($restore_button, $allowed_html)
            ?>
            <br />
            <label for="moviehelper-tmdb-apikey" class="moviehelper-element-description">
                <?php esc_html_e('Change this value only if you want to use your own TMDB API Key.', 'movie-helper') ?>
            </label>
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
        $output = [];

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
        add_thickbox();
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
        $text .= '<a href="https://ko-fi.com/L4L6HBQQ4" target="_blank">
                    <img src="'.MOVIEHELPER_IMG_DIR.'/kofi.png" alt="paypal" width="150">
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
        $text .= ' <a href="https://wordpress.org/support/plugin/yet-another-movie/reviews/">
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
        $text .= $this->cnrt();
        $text .= '</div>'; //second div
        $text .= '</div>'; //first div

        echo wp_kses_post($text);
    }

    /**
     * CNRT box
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.2
     * @return string
     */
    private function cnrt() {
        $url = add_query_arg(
            [
                'tab'       => 'plugin-information',
                'plugin'    => 'comments-not-replied-to',
                'TB_iframe' => 'true',
                'width'     => '772',
                'height'    => '670'
            ],
            network_admin_url( 'plugin-install.php' )
        );

        $text  = '<h4>Comments Not Replied To</h4>';
        $text .= '<div style="margin-top: 15px;">';
        $text .= esc_html__('"Comments Not Replied To" introduces a new area in the administrative dashboard that allows you to
        see what comments to which you - as the site author - have not yet replied.', 'movie-helper');
        $text .= '</div>';
        $text .= '<div style="margin-top: 15px;"> 
                  <a href="'. esc_url( $url ).'" class="install-now button thickbox open-plugin-details-modal"
                        target="_blank">'. __( 'Install', 'yet-another-stars-rating' ).'</a>';
        $text .= '</div>';

        return $text;
    }

}
