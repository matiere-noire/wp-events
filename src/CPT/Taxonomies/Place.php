<?php
/**
 * Registers the `type de partenaire` taxonomy,
 * for use with 'reference'.
 */

namespace Events\CPT\Taxonomies;

class Place extends Taxonomies
{

    public function __construct()
    {

        $this->name = 'place';

        $this->post_types = [ 'event' ];

        $this->labels = array(
            'name'                       => __( 'Lieux/Salles', 'wpe' ),
            'singular_name'              => _x( 'Lieu', 'taxonomy general name', 'wpe' ),
            'search_items'               => __( 'Chercher lieux', 'wpe' ),
            'popular_items'              => __( 'Lieux populaires', 'wpe' ),
            'all_items'                  => __( 'Tous les lieux', 'wpe' ),
            'parent_item'                => __( 'Lieu parent', 'wpe' ),
            'parent_item_colon'          => __( 'Lieu parent:', 'wpe' ),
            'edit_item'                  => __( 'Editer le lieu', 'wpe' ),
            'update_item'                => __( 'Mettre à jour le lieu', 'wpe' ),
            'view_item'                  => __( 'Voir le lieu', 'wpe' ),
            'add_new_item'               => __( 'Nouveau lieu', 'wpe' ),
            'new_item_name'              => __( 'Nouveau lieu', 'wpe' ),
            'separate_items_with_commas' => __( 'Séparer les lieux avec des virgules', 'wpe' ),
            'add_or_remove_items'        => __( 'Ajouter ou supprimer des lieux', 'wpe' ),
            'choose_from_most_used'      => __( 'Choisir parmi les lieux les plus utilisés', 'wpe' ),
            'not_found'                  => __( 'Pas de lieu trouvé.', 'wpe' ),
            'no_terms'                   => __( 'Pas de lieu', 'wpe' ),
            'menu_name'                  => __( 'Lieux/Salles', 'wpe' ),
            'items_list_navigation'      => __( 'Lieux liste navigation', 'wpe' ),
            'items_list'                 => __( 'Lieux liste', 'wpe' ),
            'most_used'                  => _x( 'Les plus utilisés', 'place', 'wpe' ),
            'back_to_items'              => __( '&larr; Retour aux Lieux', 'wpe' ),
        );

        $this->args = array(
            'hierarchical'      => true,
            'public'            => true,
            'show_in_nav_menus' => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => true,
            'show_in_rest'      => true,
            'meta_box_cb'       => false
        );
    }

    public function updated_messages($message)
    {
        $messages['place'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __( 'Lieu ajouté.', 'wpe' ),
            2 => __( 'Lieu supprimé.', 'wpe' ),
            3 => __( 'Lieu mis à jour.', 'wpe' ),
            4 => __( 'Lieu non ajouté.', 'wpe' ),
            5 => __( 'Lieu non mis à jour.', 'wpe' ),
            6 => __( 'Lieux supprimés.', 'wpe' ),
        );

        return $messages;
    }
}
