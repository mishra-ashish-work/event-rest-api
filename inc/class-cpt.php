<?php
/**
 * Package: Event Rest API
 * Class: CPT
 * Description: Register CPT and Taxonomy
 */

class ERA_CPT{
    private $cpt;
    private $tax;

    public function __construct($cpt, $tax){
        $this->post_type = $cpt;
        $this->taxonomy = $tax;
        
        add_action('init', array($this, 'register_event_cpt_tax'));
    }

    public function register_event_cpt_tax(){
        $cpt_args = array(
            'labels' => array(
                'name' => __('Events', ERA_TEXT_DOMAIN),
                'singular_name' => __('Event', ERA_TEXT_DOMAIN),
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'events'),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'taxonomies' => array($this->taxonomy),
        );

        $tax_args = array(
            'label' => 'Category',
            'public' => true,
            'hierarchical' => true,
            'rewrite' => array('slug' => 'event_cat'),
        );
        register_post_type($this->post_type, $cpt_args);
        register_taxonomy($this->taxonomy, $this->post_type, $tax_args);
    }
}
?>