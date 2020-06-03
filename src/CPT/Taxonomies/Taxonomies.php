<?php
/**
 * Created by PhpStorm.
 * User: arnaudbanvillet
 * Date: 2019-01-22
 * Time: 10:44
 */

namespace Events\CPT\Taxonomies;

abstract class Taxonomies
{
    public $name;
    public $post_types;
    public $labels;
    public $args;


    /**
     * On lance toutes les actions pour ajouter la taxo
     */
    public function init()
    {

        $this->register_taxonomy();
        add_filter('term_updated_messages', array( $this, 'updated_messages' ));
    }

    public function register_taxonomy(): void
    {

        $this->args['labels'] = $this->labels;
        register_taxonomy($this->name, $this->post_types, $this->args);
    }


    /**
     * @param $message
     *
     * @return mixed
     */
    public function updated_messages($message)
    {
        return $message;
    }
}
