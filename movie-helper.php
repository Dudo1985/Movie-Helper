<?php

/**
 * Plugin Name: Movie Helper
 * Plugin URI: https://dariocurvino.it
 * Description: Movie Helper allows you to easily add links to movie and tv shows, just by searching them while you're writing your content. Search, click, done!
 * Version: 1.2.3
 * Requires at least: 5.0
 * Requires PHP: 5.4
 * Author: Dario Curvino
 * Author URI: https://dariocurvino.it/
 * Text Domain: movie-helper
 * Domain Path: /languages
 * License: GPL2
 **
 */
/*

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

if(is_admin()) {
    define('MOVIEHELPER_VERSION_NUM', '1.2.3');

    require 'admin/movieHelper.php';

    $movie_helper = new movieHelper();
    $movie_helper->init();
}

//this adds a link under the plugin name, must be in the main plugin file
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), static function ($links){
    $settings_link='<a href="' . admin_url( 'options-general.php?page=moviehelper_settings_page' ) . '">General Settings</a>';

    //array_unshift adds to the beginning of array
    array_unshift($links, $settings_link);

    return $links;
});
