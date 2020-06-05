<?php


namespace Events\Classes;

use WP_Date_Query;
use WP_Post;
use WP_Query;

class WPQueryEventsFilters
{


    public function init()
    {

        add_filter('posts_fields', [ $this, 'dateFields'], 10, 2);
        add_filter('posts_where', [$this, 'whereDate'], 10, 2);
        add_filter('posts_join', [$this, 'jointDateTable'], 10, 2);
        add_filter('posts_groupby', [ $this, 'groupByDates'], 10, 2);
        add_filter('posts_orderby', [ $this, 'orderByDates'], 10, 2);

        add_filter('posts_results', [ $this, 'eventsQueryResults'], 10, 2);
    }


    /**
     * We want only the title of the posts
     *
     * @param string $fields String containing fields.
     * @param WP_Query $wp_query Object.
     * @return string
     */
    public function dateFields($fields, $wp_query)
    {
        if ($this->isQueryEvents($wp_query)) {
            if ($this->areDatesFieldsConcatened($wp_query)) {
                $fields .= ',CONCAT("[",GROUP_CONCAT( CONCAT("{\"date_start\":\"", wpe_dates.wpe_date_start, "\",\"date_end\":\"", wpe_dates.wpe_date_end,"\"}")),"]") as dates';
            } else {
                $fields .= ',wpe_dates.*';
            }
        }

        return $fields;
    }

    /**
     * Filters the WHERE clause of the query.
     *
     * @param string $where String containing fields.
     * @param WP_Query $wp_query Object.
     * @return string
     */
    public function whereDate($where, $wp_query)
    {
        global $wpdb;

        if (isset($wp_query->query['wpe_date_query'])) {
            $date_query = new WP_Date_Query($wp_query->query['wpe_date_query'], "wpe_dates.wpe_date_end");
            $where .= $date_query->get_sql();
        }

        return $where;
    }



    /**
     * Joining another table and relating the column post_id with the Post's ID
     *
     * @param string $join String containing joins.
     * @param WP_Query $wp_query Object.
     * @return string
     */
    public function jointDateTable($join, $wp_query)
    {
        global $wpdb;

        if ($this->isQueryEvents($wp_query)) {
            $join .= " LEFT JOIN {$wpdb->wpe_dates} as wpe_dates on wpe_dates.wpe_event_id = {$wpdb->posts}.ID ";
        }
        return $join;
    }


    /**
     * We will first order by the share count.
     *
     * @param string $groupby String containing groupby fields.
     * @param WP_Query $wp_query Object.
     * @return string
     */
    public function groupByDates($groupby, $wp_query)
    {
        global $wpdb;
        if ($this->isQueryEvents($wp_query)) {
            $groupeByArray = explode(',', $groupby);

            if ($this->areDatesFieldsConcatened($wp_query) && ! in_array("{$wpdb->posts}.ID", $groupeByArray)) {
                if (count($groupeByArray) === 1 && $groupeByArray[0] === '') {
                    $groupeByArray = ["{$wpdb->posts}.ID"];
                } else {
                    $groupeByArray[] = "{$wpdb->posts}.ID";
                }
            } else {
                $groupeByArray[] = 'wpe_dates.wpe_date_id';
            }


            $groupby = implode(',', $groupeByArray);
        }
        return $groupby;
    }


    /**
     * We will first order by the share count.
     *
     * @param string $orderBy String containing groupby fields.
     * @param WP_Query $wp_query Object.
     * @return string
     */
    public function orderByDates($orderBy, $wp_query)
    {
        global $wpdb;
        if (isset($wp_query->query['orderby'])) {
            if ((is_array($wp_query->query['orderby']) && in_array('wpe_date', $wp_query->query['orderby']) )
            ||
            $wp_query->query['orderby'] === 'wpe_date'
            ) {
                $orderBy = str_replace("{$wpdb->posts}.post_date", 'wpe_dates.wpe_date_end', $orderBy);
            }
        }
        return $orderBy;
    }

    /**
     * We will first order by the share count.
     *
     * @param WP_Post[] $posts Object.
     * @param WP_Query $wp_query Object.
     * @return WP_Post[]
     */
    public function eventsQueryResults($posts, $wp_query) : array
    {

        $cpt_name = apply_filters('wpe/event_post_type_name', 'event');


        foreach ($posts as $key => $post) {
            if ($post->post_type === $cpt_name) {
                if (property_exists($post, 'dates')) {
                    $dates = json_decode($post->dates);
                    $formatedDates = [];
                    foreach ($dates as $d) {
                        $d->date_start = get_date_from_gmt($d->date_start);
                        $d->date_end = get_date_from_gmt($d->date_end);
                        $formatedDates[] = $d;
                    }
                    $posts[$key]->dates = $formatedDates;
                } elseif (property_exists($post, 'wpe_date_start')) {
                    $posts[$key]->dates = (object) [
                        'date_start' => $post->wpe_date_start,
                        'date_end' => $post->wpe_date_end
                    ];
                }
            }
        }

        return $posts;
    }

    /**
     *
     * @param WP_Query $wp_query Object.
     * @return bool
     */
    private function areDatesFieldsConcatened($wp_query)
    {

        return ! isset($wp_query->query['wpe_date_query']);
    }

    /**
     *
     * @param WP_Query $wp_query Object.
     * @return bool
     */
    private function isQueryEvents($wp_query) : bool
    {
        if (isset($wp_query->query['post_type'])) {
            $post_type = $wp_query->query['post_type'];
            $cpt_name = apply_filters('wpe/event_post_type_name', 'event');

            if (is_array($post_type)) {
                return in_array($cpt_name, $post_type);
            } elseif (is_string($post_type)) {
                return $post_type === $cpt_name;
            }
        }

        return false;
    }
}
