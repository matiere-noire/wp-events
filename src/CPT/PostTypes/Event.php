<?php

namespace Events\CPT\PostTypes;

class Event extends PostTypes
{

    public function __construct()
    {

        $this->cpt_name = apply_filters('wpe/event_post_type_name', 'event');

        $this->labels = array(
            'name'                  => __('Events', 'mn-wp-events'),
            'singular_name'         => __('Event', 'mn-wp-events'),
            'all_items'             => __('All events', 'mn-wp-events'),
            'archives'              => __('Archives Events', 'mn-wp-events'),
        );

        $this->args = array(
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_nav_menus'     => true,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'has_archive'           => false,
            'rewrite'               => true,
            'query_var'             => true,
            'menu_icon'             => 'dashicons-megaphone',
            'show_in_rest'          => true,
            'rest_base'             => 'event',
        );
    }

    public function manage_admin_columns($columns)
    {
        $columns['mn-wp-events_date'] = __('Dates');
        return $columns;
    }

    public function manage_admin_custom_column($column, $post_id)
    {
        if ($column === 'mn-wp-events_date') {
            $event = get_post($post_id);

            if (property_exists($event, 'dates') && $event->dates ) {
                echo '<ul>';
                foreach ($event->dates as $d) {
                    echo "<li>{$d->date_start}</li>";
                }
                echo '</ul>';
            }
        }
    }

    public function updated_messages($messages)
    {
        global $post;

        $permalink = get_permalink($post);

        $messages['event'] = array(
            0  => '', // Unused. Messages start at index 1.
            /* translators: %s: post permalink */
            1  => sprintf(__('Event modifié. <a target="_blank" href="%s">Voir event</a>', 'mn-wp-events'), esc_url($permalink)),
            2  => __('Custom field updated.', 'mn-wp-events'),
            3  => __('Custom field deleted.', 'mn-wp-events'),
            4  => __('Event modifié.', 'mn-wp-events'),
            /* translators: %s: date and time of the revision */
            5  => isset($_GET['revision']) ? sprintf(__('Event restauré à partir de %s', 'mn-wp-events'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            /* translators: %s: post permalink */
            6  => sprintf(__('Event publié. <a href="%s">Voir l\'event</a>', 'mn-wp-events'), esc_url($permalink)),
            7  => __('Event sauvegardé.', 'mn-wp-events'),
            /* translators: %s: post permalink */
            8  => sprintf(__('Event soumis. <a target="_blank" href="%s">Prévisualiser event</a>', 'mn-wp-events'), esc_url(add_query_arg('preview', 'true', $permalink))),
            /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
            9  => sprintf(
                __('Event prévu pour: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Prévisualiser event</a>', 'mn-wp-events'),
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
                esc_url($permalink)
            ),
            /* translators: %s: post permalink */
            10 => sprintf(__('Event brouillon modifié. <a target="_blank" href="%s">Prévisualiser event</a>', 'mn-wp-events'), esc_url(add_query_arg('preview', 'true', $permalink))),
        );

        return $messages;
    }
}
