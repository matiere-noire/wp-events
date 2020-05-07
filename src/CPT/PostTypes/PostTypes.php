<?php

namespace Events\CPT\PostTypes;

abstract class PostTypes
{
    public $cpt_name;
    public $labels;
    public $args;

    /**
     * On lance toutes les actions pour ajouter le poste type
     */
    public function init()
    {

        add_action('init', array( $this, 'register_post_type' ));
        add_filter('post_updated_messages', array( $this, 'updated_messages' ));

        add_filter("manage_{$this->cpt_name}_posts_columns", array( $this, 'manage_admin_columns' ));
        add_action("manage_{$this->cpt_name}_posts_custom_column", array( $this, 'manage_admin_custom_column' ), 10, 2);
    }

    /**
     * Register post type
     */
    public function register_post_type()
    {

        $this->args['labels'] = $this->labels;
        register_post_type($this->cpt_name, $this->args);
    }


    /**
     * @param array $messages
     * @return array
     */
    public function updated_messages($messages)
    {

        return $messages;
    }


    /**
     * @param array $columns
     * @return array
     */
    public function manage_admin_columns($columns)
    {

        return $columns;
    }


    /**
     * @param string $column
     * @param int $post_id
     */
    public function manage_admin_custom_column($column, $post_id)
    {
    }
}