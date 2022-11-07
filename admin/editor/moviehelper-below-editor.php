<?php
/*

Copyright 2020 Dario Curvino (email : d.curvino@gmail.com)

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

$valid_api = false;
$tmdb_transient = movieHelper::getTMDBApiKeyTransient();

if($tmdb_transient === false) {
    $valid_api = movieHelper::validateTMDBApiKey(MOVIEHELPER_TMDB_CUSTOM_APIKEY);
} else {
    if(isset($tmdb_transient['error']) && $tmdb_transient['error'] === false) {
        $valid_api = true;
    }
}

?>

<div>
<?php if ($valid_api === true) { ?>
    <div class="moviehelper-metabox-row">
        <div id="moviehelper-search" class="moviehelper-search">
            <div class="moviehelper-metabox-title">
                <label for="moviehelper-search-form">
                    <?php esc_html_e('Search for a movie or TV show','movie-helper') ?>
                </label>
            </div>
            <input type="text"
                   id="moviehelper-search-form"
                   placeholder="<?php esc_attr_e('Search with TMDB', 'movie-helper')?>
            ">
            <br/>
            <span>
                <small>
                    <?php esc_html_e('Click on an image to insert the link', 'movie-helper') ?>
                </small>
            </span>
            <div class="moviehelper-link-settings-container">
                <?php esc_html_e('After a link, insert a:','movie-helper') ?>
                <label>
                    <input type="radio" id="moviehelper-insert-space" value="space" name="moviehelper-after-link">
                    <?php esc_html_e('White space','movie-helper') ?>
                </label>
                <label>
                    <input type="radio" id="moviehelper-insert-newline" value="newline" name="moviehelper-after-link" checked>
                    <?php esc_html_e('Newline','movie-helper') ?>
                </label>
            </div>
            <?php if(movieHelper::isGutenPage()) { ?>
                <div id="moviehelper-block-movie-list"></div>
                <div id="moviehelper-block-links-container">
                    <button href="#" id="moviehelper-insert-block-link" class="button-primary">
                        <?php esc_html_e('Insert', 'movie-helper') ?>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="moviehelper-metabox-row">
        <div id="moviehelper-query-results" class="moviehelper-query-results">
        </div>
    </div>
    <?php } else { ?>
    <div class="moviehelper-metabox-row">
        <span class="moviehelper-error">
            <?php
                if(isset($tmdb_transient['message'])) {
                    $error = $tmdb_transient['message'];
                } else {
                    $error = $valid_api;
                }
                echo esc_html($error);
            ?>
        </span>
    </div>
    <?php } ?>
</div>


