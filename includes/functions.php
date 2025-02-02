<?php

/* Restrict menus */

function smi_show_menu_items_based_on_role($items, $args)
{
    // Get the menu ID from the $args parameter
    $menu_id = $args->menu->term_id;

    // Get the current user's roles
    $user_roles = wp_get_current_user()->roles;

    // Get the selected menu items for this menu ID
    $selected_items = array();
    $menu_items = wp_get_nav_menu_items($menu_id);
    foreach ($menu_items as $menu_item) {
        $menu_item_id = $menu_item->ID;
        $menu_item_user_roles = get_post_meta($menu_item_id, '_menu_item_user_roles', true);
        if (!empty($menu_item_user_roles)) {
            $selected_items[$menu_item->title] = $menu_item_user_roles;
        }
    }

    // If the user has selected user roles for this menu, hide the menu items for those roles
    if (!empty($selected_items)) {
        foreach ($items as $key => $item) {
            if (isset($selected_items[$item->title])) {
                $item_user_roles = $selected_items[$item->title];
                if (!empty(!array_intersect($user_roles, $item_user_roles))) {
                    unset($items[$key]);
                }
            }
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'smi_show_menu_items_based_on_role', 10, 2);

/* Restrict content */

function smi_hide_restricted_pages_dynamic($query) {
    if (!is_admin() && $query->is_main_query()) {
        $restricted_pages = array();
        
        $args = array(
            'post_type'      => array('post', 'page'),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);
        
        foreach ($posts as $post) {
            $allowed_roles = get_post_meta($post->ID, '_members_access_role', false);
            if (!empty($allowed_roles)) {
                $user = wp_get_current_user();
                if (!array_intersect($allowed_roles, $user->roles)) {
                    $restricted_pages[] = $post->ID;
                }
            }
        }
        
        if (!empty($restricted_pages)) {
            $query->set('post__not_in', $restricted_pages);
        }
    }
}
add_action('pre_get_posts', 'smi_hide_restricted_pages_dynamic');

function smi_restrict_direct_access_dynamic() {
    if (is_single() || is_page()) {
        global $post;
        
        $allowed_roles = get_post_meta($post->ID, '_members_access_role', false);
        if (!empty($allowed_roles)) {
            $user = wp_get_current_user();
            if (!array_intersect($allowed_roles, $user->roles)) {
                wp_die('<h1>Access Denied</h1><p>This page is restricted.</p>', 'Access Restricted', array('response' => 403));
                exit;
            }
        }
    }
}
add_action('template_redirect', 'smi_restrict_direct_access_dynamic');

function smi_filter_restricted_posts($posts, $query) {
    if (is_admin()) {
        return $posts; // Don't modify admin queries
    }

    $user = wp_get_current_user();
    $allowed_roles = $user->roles; // Get current user roles

    foreach ($posts as $key => $post) {
        $restricted_roles = get_post_meta($post->ID, '_members_access_role', false);
        
        if (!empty($restricted_roles) && !array_intersect($allowed_roles, $restricted_roles)) {
            unset($posts[$key]); // Remove the post from results
        }
    }

    return array_values($posts); // Reset array keys
}
add_filter('posts_results', 'smi_filter_restricted_posts', 10, 2);
