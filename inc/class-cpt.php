<?php
/**
 * Package: Event Rest API
 * Class: CPT
 * Description: This class is responsible for registering a Custom Post Type (CPT) and a Taxonomy for events.
 */

class ERA_CPT{
    private $post_type;
    private $taxonomy;

    /**
     * Constructor for the ERA_CPT class.
     *
     * @param string $cpt The name of the Custom Post Type (CPT).
     * @param string $tax The name of the Taxonomy associated with the CPT.
     */
    public function __construct($cpt, $tax){
        $this->post_type = $cpt;
        $this->taxonomy = $tax;
        
        // Register CPT and Taxonomy when WordPress initializes.
        add_action('init', array($this, 'register_event_cpt_tax'));
    }

    /**
     * Register the Custom Post Type (CPT) and Taxonomy for events.
     */
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

        // Register the Custom Post Type (CPT) and Taxonomy.
        register_post_type($this->post_type, $cpt_args);
        register_taxonomy($this->taxonomy, $this->post_type, $tax_args);
    }
}
?>
