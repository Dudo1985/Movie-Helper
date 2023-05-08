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

class movieHelperEditor {

    public function init () {
        //hook into add_meta_boxes to add a new metabox
        add_action('add_meta_boxes', [$this, 'addMetaboxes']);
    }

    /**
     * Add a new metabox below the editor, in all CPTS
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function addMetaboxes () {
        //Default post type where display metabox
        $post_types = ['post', 'page'];

        //get the custom post type
        $custom_post_types = movieHelper::getCustomPostTypes();

        if ($custom_post_types) {
            //First merge array then changes keys to int
            $post_types = array_values(array_merge($post_types, $custom_post_types));
        }

        //add meta box
        foreach ($post_types as $post_type) {
            add_meta_box(
                'moviehelper_metabox_below_editor',
                'Movie Helper',
                [$this, 'metaboxBottomEditor'],
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Display the metabox below the editor
     *
     * @author Dario Curvino <@dudo>
     * @since 1.0.0
     */
    public function metaboxBottomEditor () {
        if(current_user_can('edit_posts')) {
            include MOVIEHELPER_ABSOLUTE_PATH_ADMIN . '/editor/moviehelper-below-editor.php';
        } else {
            esc_html_e('You don\'t have enough privileges to use Movie Helper', 'movie-helper');
        }
    }
}