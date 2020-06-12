<?php

namespace Events\CPT;

class CustomPostTypesAndTax
{


    public function __construct()
    {

        // Empty to ensure UTO is only intialize once
    }

    public function initialize()
    {

        $this->register_post_types();
        $this->register_taxonomies();
    }


    public function register_post_types()
    {

        $cpts = [
            new PostTypes\Event(),
        ];

        foreach ($cpts as $cpt) {
            $cpt->init();
        }
    }

    public function register_taxonomies()
    {

        $taxos = [
            new Taxonomies\Place()
        ];

        foreach ($taxos as $taxo) {
            $taxo->init();
        }
    }
}
