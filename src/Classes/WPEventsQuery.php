<?php

/**
 * Inspired by https://developer.wordpress.org/reference/classes/wp_query/
 * File: wp-includes/class-wp-query.php
 */

namespace Events\Classes;

use WP_Post;

class WPEventsQuery
{

    /**
     * @var array Query vars set by the user
     */
    private $queryArgs;

    /**
     * @var array default query vars
     */
    private $defaultQueryArgs;

    /**
     * Get post database query.
     */
    private $request;

    /**
     * @var WP_Post[]
     */
    private $posts;

    /**
     * The amount of dates for the current query.
     */
    public $dates_count = 0;
    

    /**
     * Constructor.
     *
     * Sets up the WordPress query, if parameter is not empty.
     *
     * @param array $queryArgs array of vars.
     */
    public function __construct($queryArgs = [])
    {
        $this->defaultQueryArgs = [
            'post_status' => ['publish', 'draft']
        ];

        $this->setQueryArgs($queryArgs);
        $this->query();
    }

    /**
     * @return WP_Post[]
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @param array $queryArgs
     */
    public function setQueryArgs($queryArgs): void
    {
        $this->queryArgs = wp_parse_args($queryArgs, $this->defaultQueryArgs);
    }


    /**
     * Sets up the WordPress query by parsing query string.
     *
     * @return WP_Post[] Array WP_Post
     */
    public function query(): array
    {
        global $wpdb;

        $where = '';

        $this->request = "
            SELECT d.wpe_date_id, d.wpe_date_start, d.wpe_date_end, d.wpe_place_id, posts.*
            FROM {$wpdb->wpe_dates} d
            LEFT OUTER JOIN {$wpdb->posts} AS posts ON d.wpe_event_id = posts.ID
            WHERE 1 = 1
        ";

        // We will update request in regards of args passed to the query
        if (isset($this->queryArgs['event_id'])) {
            $where .= " AND d.wpe_event_id = {$this->queryArgs['event_id']} ";
        }


        if (isset($this->queryArgs['post_status'])) {
            if (! is_array($this->queryArgs['post_status'])) {
                $this->queryArgs['post_status'] = [$this->queryArgs['post_status']];
            }
            $statusString = implode('\',\'', $this->queryArgs['post_status']);
            $where .= " AND posts.post_status in ('{$statusString}') ";
        }

        $this->request .= $where;
        $dates = $wpdb->get_results($this->request);
        $this->dates_count = count($dates);
        $this->posts = [];

        // Convert to WP_Post objects.
        if ($dates) {
            foreach ($dates as $date) {
                $date->wpe_date_start = get_date_from_gmt($date->wpe_date_start);
                $date->wpe_date_end = get_date_from_gmt($date->wpe_date_end);
                $this->posts[] = get_post($date);
            }
        }

        return $this->posts;
    }
}
