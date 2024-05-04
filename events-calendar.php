<?php
/**
 * Plugin name: Events -Calendar
 *
 *
 */
//  Scripts
 add_action('wp_enqueue_scripts', 'assets');

 function assets() {
    
    wp_enqueue_style('common-css', plugins_url( '/css/common.css', __FILE__ ));
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('fullcalendar', plugins_url( '/src/libs/fullcalendar-6.1.11/dist/index.global.min.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('common-js', plugins_url( '/js/common.js', __FILE__ ));
  }
  function localize_ajax_url() {
    wp_localize_script('common-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'localize_ajax_url');

//   Register post types
function create_custom_post_types() {
    // Реєстрація типу запису "Події"
    register_post_type( 'events',
        array(
            'labels' => array(
                'name' => __( 'Події' ),
                'singular_name' => __( 'Подія' ),
                'menu_name'          => __( 'Подія', 'Подія' ),
                'name'               => __( 'Подія', 'Подія' ),
                'singular_name'      => __( 'Подія', 'Подія' ),
                'name_admin_bar'     => __( 'Подія', 'Подія' ),
                'add_new'            => __( 'Подія', 'Створити' ),
                'add_new_item'       => __( 'Подія', 'Додати нову подію' ),
                'new_item'           => __( 'Подія', 'Нова подія' ),
                'edit_item'          => __( 'Подія', 'Редагувати' ),
                'view_item'          => __( 'Подія', 'Дивитись' ),
                'all_items'          => __( 'Подія', 'Усі подіі' ),
                'search_items'       => __( 'Подія', 'Шукати подію' )
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array( 'title', 'thumbnail', 'exerpt', 'editor' ) // Дозволені поля
        )
    );

    // Реєстрація типу запису "Ліди"
    register_post_type( 'leads',
        array(
            'labels' => array(
                'name' => __( 'Ліди' ),
                'singular_name' => __( 'Лід' ),
                'menu_name'          => __( 'Лід', 'Лід' ),
                'name'               => __( 'Лід', 'Лід' ),
                'singular_name'      => __( 'Лід', 'Лід' ),
                'name_admin_bar'     => __( 'Лід', 'Лід' ),
                'add_new'            => __( 'Лід', 'Створити' ),
                'add_new_item'       => __( 'Лід', 'Додати новмй лід' ),
                'new_item'           => __( 'Лід', 'Новий Лід' ),
                'edit_item'          => __( 'Лід', 'Редагувати' ),
                'view_item'          => __( 'Лід', 'Дивитись' ),
                'all_items'          => __( 'Лід', 'Усі ліди' ),
                'search_items'       => __( 'Лід', 'Шукати лід' )
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array( 'title' ) // Дозволені поля
        )
    );
}
add_action( 'init', 'create_custom_post_types' );

// Events


// Events post type fields
// Додавання метабоксу для дати події
function add_event_date_meta_box() {
    add_meta_box(
        'event_date',
        'Дата події',
        'render_event_date_meta_box',
        'events',
        'normal', // Позиція метабоксу (зліва)
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_event_date_meta_box' );

// Відображення метабоксу для дати події
function render_event_date_meta_box( $post ) {
    // Отримання дати початку та закінчення події, якщо вони вже збережені
    $start_date = get_post_meta( $post->ID, 'start_date', true );
    $end_date = get_post_meta( $post->ID, 'end_date', true );
    ?>
    <div class="meta-dates_wrap">
        
        <label for="event_start_date">Дата початку:</label>
        <input type="date" id="event_start_date" name="event_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
    
        <label for="event_end_date">Дата закінчення:</label>
        <input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
    </div>
    <?php
}
// Add custom meta box to display leads
function event_leads_meta_box() {
    add_meta_box(
        'event_leads_meta_box',
        'Associated Leads',
        'render_event_leads_meta_box',
        'events',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'event_leads_meta_box');

// Update event post meta with associated leads
$events_leads = get_post_meta($event_id, 'event_leads', true);
$eventsleads[] = array(
    'name' => $lead_name,
    'email' => $lead_email,
    // Add more lead data as needed
);
update_post_meta($event_id, 'event_leads', $event_leads);

// Render custom meta box content
function render_event_leads_meta_box($post) {
    $event_leads = get_post_meta($post->ID, 'event_leads', true);
    if (!empty($event_leads)) {
        echo '<ul>';
        foreach ($event_leads as $lead) {
            echo '<li>' . esc_html($lead['name']) . ' - ' . esc_html($lead['email']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo 'No leads associated with this event.';
    }
}

// Збереження значень дати події при оновленні або створенні запису
function save_event_date_meta( $post_id ) {
    if ( isset( $_POST['event_start_date'] ) ) {
        update_post_meta( $post_id, 'start_date', sanitize_text_field( $_POST['event_start_date'] ) );
    }
    if ( isset( $_POST['event_end_date'] ) ) {
        update_post_meta( $post_id, 'end_date', sanitize_text_field( $_POST['event_end_date'] ) );
    }
}
add_action( 'save_post', 'save_event_date_meta' );

// Register AJAX handler for fetching events
add_action( 'wp_ajax_get_events', 'get_events_callback' );
add_action( 'wp_ajax_nopriv_get_events', 'get_events_callback' );

function get_events_callback() {
    $events = array();

    // Query events
    $events_query = new WP_Query( array(
        'post_type' => 'events',
        'posts_per_page' => -1,
    ) );

    // Fetch event data
    if ( $events_query->have_posts() ) {
        while ( $events_query->have_posts() ) {
            $events_query->the_post();
            $event = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'start' => get_post_meta( get_the_ID(), 'start_date', true ),
                'end' => get_post_meta( get_the_ID(), 'end_date', true ),
                // Add more event data as needed
            );
            $events[] = $event;
        }
    }

    wp_reset_postdata();

    // Output JSON response
    header( 'Content-Type: application/json' );
    echo json_encode( $events );

    // Don't forget to exit
    wp_die();
}




// Leads
// Add metabox for Leads post type
function add_leads_metabox() {
    add_meta_box(
        'leads_details',
        'Lead Details',
        'render_lead_details_metabox',
        'leads',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_leads_metabox');

// Render metabox for Lead details
function render_lead_details_metabox($post) {
    // Retrieve lead details if they are already saved
    $lead_event = get_post_meta($post->ID, 'lead_event', true);
    $lead_phone = get_post_meta($post->ID, 'lead_phone', true);
    $lead_email = get_post_meta($post->ID, 'lead_email', true);
    ?>
    <label for="lead_event">Select Event:</label><br>
    <select id="lead_event" name="lead_event">
        <option value="">Select Event</option>
        <?php
        $events = new WP_Query(array(
            'post_type' => 'events',
            'posts_per_page' => -1
        ));
        if ($events->have_posts()) :
            while ($events->have_posts()) : $events->the_post(); ?>
                <option value="<?php the_ID(); ?>" <?php selected($lead_event, get_the_ID()); ?>><?php the_title(); ?></option>
        <?php endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </select>
    <br><br>
    <label for="lead_phone">Phone:</label><br>
    <input type="text" id="lead_phone" name="lead_phone" value="<?php echo esc_attr($lead_phone); ?>"><br>
    <label for="lead_email">Email:</label><br>
    <input type="email" id="lead_email" name="lead_email" value="<?php echo esc_attr($lead_email); ?>">
    <?php
}

// Save lead details
function save_lead_meta( $post_id ) {
    if ( isset( $_POST['lead_phone'] ) ) {
        update_post_meta( $post_id, 'lead_phone', sanitize_text_field( $_POST['lead_phone'] ) );
    }
    if ( isset( $_POST['lead_event'] ) ) {
        update_post_meta( $post_id, 'lead_event', sanitize_text_field( $_POST['lead_event'] ) );
    }
    if ( isset( $_POST['lead_email'] ) ) {
        update_post_meta( $post_id, 'lead_email', sanitize_text_field( $_POST['lead_email'] ) );
    }
}
add_action( 'save_post', 'save_lead_meta' );



add_action('wp_ajax_process_lead_form', 'process_lead_form');
add_action('wp_ajax_nopriv_process_lead_form', 'process_lead_form');
// Process lead form AJAX request
add_action('wp_ajax_process_lead_form', 'process_lead_form');
add_action('wp_ajax_nopriv_process_lead_form', 'process_lead_form');

function process_lead_form() {
    // Check if the request came from AJAX
    if (!isset($_POST['action']) || $_POST['action'] !== 'process_lead_form') {
        wp_send_json_error('Invalid AJAX request');
    }

    // Sanitize and validate form data
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $evemt = sanitize_text_field($_POST['id']);
    
    // Validate form data
    if (empty($name) || empty($phone) || empty($email) || empty($id)) {
        wp_send_json_error('All fields are required');
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        wp_send_json_error('Invalid phone number');
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address');
    }

    // Create a new Leads Post Type post
    $post_data = array(
        'post_title' => $name,
        'post_type' => 'leads',
        'post_status' => 'publish'
        // You can add more post data here if needed
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        // Save custom fields (phone, email, event name, etc.)
        update_post_meta($post_id, 'lead_phone', $phone);
        update_post_meta($post_id, 'lead_email', $email);
        update_post_meta($post_id, 'lead_event', $event);
        // Update other custom fields as needed

        wp_send_json_success('Lead created successfully');
    } else {
        wp_send_json_error('Error creating lead');
    }

    // Always exit to avoid extra output
    wp_die();
}

