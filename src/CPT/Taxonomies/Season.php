<?php
/**
 * Registers the `type de partenaire` taxonomy,
 * for use with 'reference'.
 */

namespace Events\CPT\Taxonomies;

class Season extends Taxonomies
{

    public function __construct()
    {

        $this->name = 'season';

        $this->post_types = [ 'event' ];

        $this->labels = array(
            'name'                       => __( 'Saisons', 'wpe' ),
            'singular_name'              => _x( 'Saison', 'taxonomy general name', 'wpe' ),
            'search_items'               => __( 'Chercher saisons', 'wpe' ),
            'popular_items'              => __( 'Saisons populaires', 'wpe' ),
            'all_items'                  => __( 'Toutes les saisons', 'wpe' ),
            'parent_item'                => __( 'Saison parente', 'wpe' ),
            'parent_item_colon'          => __( 'Saison parente:', 'wpe' ),
            'edit_item'                  => __( 'Editer la saison', 'wpe' ),
            'update_item'                => __( 'Mettre à jour la saison', 'wpe' ),
            'view_item'                  => __( 'Voir la saison', 'wpe' ),
            'add_new_item'               => __( 'Nouvel saison', 'wpe' ),
            'new_item_name'              => __( 'Nouvel saison', 'wpe' ),
            'separate_items_with_commas' => __( 'Séparer les saisons avec des virgules', 'wpe' ),
            'add_or_remove_items'        => __( 'Ajouter ou supprimer des saisons', 'wpe' ),
            'choose_from_most_used'      => __( 'Choisir parmi les saisons les plus utilisées', 'wpe' ),
            'not_found'                  => __( 'Pas de saison trouvée.', 'wpe' ),
            'no_terms'                   => __( 'Pas de saison', 'wpe' ),
            'menu_name'                  => __( 'Saisons', 'wpe' ),
            'items_list_navigation'      => __( 'Saisons liste navigation', 'wpe' ),
            'items_list'                 => __( 'Saisons liste', 'wpe' ),
            'most_used'                  => _x( 'Les plus utilisées', 'place', 'wpe' ),
            'back_to_items'              => __( '&larr; Retour aux saisons', 'wpe' ),
        );

        $this->args = array(
            'hierarchical'      => false,
            'public'            => true,
            'show_in_nav_menus' => true,
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
        $messages['season'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __( 'Saison ajoutée.', 'wpe' ),
            2 => __( 'Saison supprimée.', 'wpe' ),
            3 => __( 'Saison mise à jour.', 'wpe' ),
            4 => __( 'Saison non ajoutée.', 'wpe' ),
            5 => __( 'Saison non mise à jour.', 'wpe' ),
            6 => __( 'Saisons supprimées.', 'wpe' ),
        );

        return $messages;
    }
}
