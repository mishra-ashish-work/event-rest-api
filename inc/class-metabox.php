<?php
/**
 * Class: ERA_Event_MetaBox
 * Description: This class is responsible for adding and managing a custom meta box for Event post type.
 */
class ERA_Event_MetaBox {

    /**
     * Constructor for the ERA_Event_MetaBox class.
     * It adds necessary hooks for adding and saving the meta box.
     */
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_event_meta_box'));
        add_action('save_post', array($this, 'save_event_meta'));
    }

    /**
     * Add the custom meta box for Event post type.
     */
    public function add_event_meta_box() {
        add_meta_box(
            'event_meta_box',
            'Event Details',
            array($this, 'render_event_meta_box'),
            'events',
            'normal',
            'high'
        );
    }

    /**
     * Render the content of the custom meta box.
     *
     * @param WP_Post $post The current post object.
     */
    public function render_event_meta_box($post) {
        $event_start_date = get_post_meta($post->ID, '_event_start_date', true);
        $event_end_date = get_post_meta($post->ID, '_event_end_date', true);

        ?>
        <p>
            <label for="event_start_date">Event Start Date:</label>
            <input type="datetime-local" id="event_start_date" name="event_start_date" value="<?php echo esc_attr($event_start_date); ?>" />
        </p>
        <p>
            <label for="event_end_date">Event End Date:</label>
            <input type="datetime-local" id="event_end_date" name="event_end_date" value="<?php echo esc_attr($event_end_date); ?>" />
        </p>
        <?php
    }

    /**
     * Save the meta data for the Event post.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save_event_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['event_start_date'])) {
            update_post_meta($post_id, '_event_start_date', sanitize_text_field($_POST['event_start_date']));
        }

        if (isset($_POST['event_end_date'])) {
            update_post_meta($post_id, '_event_end_date', sanitize_text_field($_POST['event_end_date']));
        }
    }
}

// Create an instance of the ERA_Event_MetaBox class.
new ERA_Event_MetaBox();
