<?php
/*
Plugin Name: Custom Portfolio Plugin
Description: Adds a Portfolio custom post type with ACF fields and a shortcode to display them.
Version: 1.0
Author: Michael Damilare
*/

if (!defined('ABSPATH')) {
    exit;
}

class CustomPortfolioPlugin
{

    public function __construct()
    {
        add_action('init', [$this, 'register_portfolio_cpt']);
        add_action('acf/init', [$this, 'register_acf_fields']);
        add_shortcode('portfolio_items', [$this, 'render_portfolio_shortcode']);

        // Admin columns
        add_filter('manage_portfolio_posts_columns', [$this, 'add_admin_columns']);
        add_action('manage_portfolio_posts_custom_column', [$this, 'populate_admin_columns'], 10, 2);
    }

    public function register_portfolio_cpt(): void
    {
        $labels = [
            'name' => 'Portfolios',
            'singular_name' => 'Portfolio',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Portfolio',
            'edit_item' => 'Edit Portfolio',
            'new_item' => 'New Portfolio',
            'view_item' => 'View Portfolio',
            'all_items' => 'All Portfolios',
            'search_items' => 'Search Portfolios',
            'menu_name' => 'Portfolio',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => ['slug' => 'portfolio'],
            'menu_position' => 5,
            'menu_icon' => 'dashicons-portfolio',
            'show_in_rest' => true,
        ];

        register_post_type('portfolio', $args);
    }

    public function register_acf_fields(): void
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_portfolio_fields',
                'title' => 'Portfolio Details',
                'fields' => [
                    [
                        'key' => 'field_client_name',
                        'label' => 'Client Name',
                        'name' => 'client_name',
                        'type' => 'text',
                    ],
                    [
                        'key' => 'field_project_url',
                        'label' => 'Project URL',
                        'name' => 'project_url',
                        'type' => 'url',
                    ],
                    [
                        'key' => 'field_completed_date',
                        'label' => 'Completed Date',
                        'name' => 'completed_date',
                        'type' => 'date_picker',
                    ],
                    [
                        'key' => 'field_gallery',
                        'label' => 'Gallery',
                        'name' => 'gallery',
                        'type' => 'gallery',
                        'preview_size' => 'thumbnail',
                    ],
                    [
                        'key' => 'field_technologies_used',
                        'label' => 'Technologies Used',
                        'name' => 'technologies_used',
                        'type' => 'checkbox',
                        'choices' => [
                            'HTML' => 'HTML',
                            'CSS' => 'CSS',
                            'JavaScript' => 'JavaScript',
                            'PHP' => 'PHP',
                            'WordPress' => 'WordPress',
                            'React' => 'React',
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'portfolio',
                        ],
                    ]
                ],
            ]);
        }
    }

    public function render_portfolio_shortcode($atts)
    {
        $query = new WP_Query([
            'post_type' => 'portfolio',
            'posts_per_page' => -1,
        ]);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="portfolio-items">';
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_portfolio_item(get_the_ID());
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>No portfolio items found.</p>';
        }

        return ob_get_clean();
    }

    private function render_portfolio_item($post_id): void
    {
        $client_name = get_field('client_name', $post_id);
        $project_url = get_field('project_url', $post_id);
        $completed_date = get_field('completed_date', $post_id);
        $gallery = get_field('gallery', $post_id);
        $technologies = get_field('technologies_used', $post_id);

        echo '<div class="portfolio-item">';
        echo get_the_post_thumbnail($post_id, 'medium');
        echo '<h3>' . get_the_title($post_id) . '</h3>';
        echo '<p><strong>Client:</strong> ' . esc_html($client_name) . '</p>';
        echo '<p><strong>Completed:</strong> ' . esc_html($completed_date) . '</p>';
        echo '<p><strong>Project:</strong> <a href="' . esc_url($project_url) . '" target="_blank">' . esc_url($project_url) . '</a></p>';

        if ($technologies) {
            echo '<p><strong>Technologies:</strong> ' . implode(', ', $technologies) . '</p>';
        }

        if ($gallery) {
            echo '<div class="portfolio-gallery">';
            foreach ($gallery as $image) {
                echo '<img src="' . esc_url($image['sizes']['thumbnail']) . '" alt="' . esc_attr($image['alt']) . '" />';
            }
            echo '</div>';
        }

        echo '<div class="portfolio-content">' . apply_filters('the_content', get_the_content(null, false, $post_id)) . '</div>';
        echo '</div>';
    }

    public function add_admin_columns($columns)
    {
        $columns['client_name'] = 'Client Name';
        $columns['completed_date'] = 'Completed Date';
        $columns['technologies_used'] = 'Technologies Used';
        return $columns;
    }

    public function populate_admin_columns($column, $post_id): void
    {
        switch ($column) {
            case 'client_name':
                echo esc_html(get_field('client_name', $post_id));
                break;
            case 'completed_date':
                echo esc_html(get_field('completed_date', $post_id));
                break;
            case 'technologies_used':
                $tech = get_field('technologies_used', $post_id);
                if (is_array($tech)) {
                    echo implode(', ', $tech);
                }
                break;
        }
    }
}

new CustomPortfolioPlugin();