<?php

namespace Events\CPT\PostTypes;

class Event extends PostTypes{

    public function __construct()
    {

        $this->cpt_name = apply_filters( 'wpe/event_post_type_name', 'event' );

        $this->labels = array(
            'name'                  => __( 'Evénements', 'wpe' ),
            'singular_name'         => __( 'Evénement', 'wpe' ),
            'all_items'             => __( 'Tous les événements', 'wpe' ),
            'archives'              => __( 'Evénements Archives', 'wpe' ),
            'attributes'            => __( 'Evénements Attributs', 'wpe' ),
            'insert_into_item'      => __( 'Inserer dans événement', 'wpe' ),
            'uploaded_to_this_item' => __( 'Mettre à jour pour cet événement', 'wpe' ),
            'featured_image'        => _x( 'Image mise en avant', 'event', 'wpe' ),
            'set_featured_image'    => _x( 'Mettre une image en avant', 'event', 'wpe' ),
            'remove_featured_image' => _x( 'Supprimer l\'image mise en avant', 'event', 'wpe' ),
            'use_featured_image'    => _x( 'Utiliser comme image mise en avant', 'event', 'wpe' ),
            'filter_items_list'     => __( 'Filtrer la liste d\'événements', 'wpe' ),
            'items_list_navigation' => __( 'Navigation de la liste d\'événements', 'wpe' ),
            'items_list'            => __( 'Liste des événements', 'wpe' ),
            'new_item'              => __( 'Nouvel événement', 'wpe' ),
            'add_new'               => __( 'Ajouter un nouveau', 'wpe' ),
            'add_new_item'          => __( 'Ajouter un nouvel événement', 'wpe' ),
            'edit_item'             => __( 'Editer événement', 'wpe' ),
            'view_item'             => __( 'Voir l\'événement', 'wpe' ),
            'view_items'            => __( 'Voir les événements', 'wpe' ),
            'search_items'          => __( 'Chercher événements', 'wpe' ),
            'not_found'             => __( 'Pas d\'événements trouvés', 'wpe' ),
            'not_found_in_trash'    => __( 'Pas d\'événements trouvés dans la corbeille', 'wpe' ),
            'parent_item_colon'     => __( 'Evénement parent:', 'wpe' ),
            'menu_name'             => __( 'Evénements', 'wpe' ),
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

    public function updated_messages($messages)
    {
        global $post;

        $permalink = get_permalink( $post );

        $messages['event'] = array(
            0  => '', // Unused. Messages start at index 1.
            /* translators: %s: post permalink */
            1  => sprintf( __( 'Evénement modifié. <a target="_blank" href="%s">Voir événement</a>', 'wpe' ), esc_url( $permalink ) ),
            2  => __( 'Custom field updated.', 'wpe' ),
            3  => __( 'Custom field deleted.', 'wpe' ),
            4  => __( 'Evénement modifié.', 'wpe' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Evénement restauré à partir de %s', 'wpe' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            /* translators: %s: post permalink */
            6  => sprintf( __( 'Evénement publié. <a href="%s">Voir l\'événement</a>', 'wpe' ), esc_url( $permalink ) ),
            7  => __( 'Evénement sauvegardé.', 'wpe' ),
            /* translators: %s: post permalink */
            8  => sprintf( __( 'Evénement soumis. <a target="_blank" href="%s">Prévisualiser événement</a>', 'wpe' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
            /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
            9  => sprintf( __( 'Evénement prévu pour: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Prévisualiser événement</a>', 'wpe' ),
                date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
            /* translators: %s: post permalink */
            10 => sprintf( __( 'Evénement brouillon modifié. <a target="_blank" href="%s">Prévisualiser événement</a>', 'wpe' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
        );

        return $messages;
    }

}