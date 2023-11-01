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

function movie_helper_customize_links_description () {
    $name        = esc_html__('Customize content', 'movie-helper');

    $div_desc     = '<div class="moviehelper-settings-description">';
    //$description  = esc_html__('Custom text to show after a link','movie-helper');
    $end_div      = '.</div>';

    return $name . $div_desc . $end_div;
}