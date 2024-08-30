<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/** Create a Custom Post Type: "Cities" */
function create_cities_cpt() {
    $labels = array(
        'name' => __('Cities', 'textdomain'),
        'singular_name' => __('City', 'textdomain'),
        'menu_name' => __('Cities', 'textdomain'),
        'name_admin_bar' => __('City', 'textdomain'),
        'add_new' => __('Add New', 'textdomain'),
        'add_new_item' => __('Add New City', 'textdomain'),
        'new_item' => __('New City', 'textdomain'),
        'edit_item' => __('Edit City', 'textdomain'),
        'view_item' => __('View City', 'textdomain'),
        'all_items' => __('All Cities', 'textdomain'),
        'search_items' => __('Search Cities', 'textdomain'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'show_in_rest' => true,
    );

    register_post_type('cities', $args);
}
add_action('init', 'create_cities_cpt');

/** Add Meta Box with Custom Fields for Latitude and Longitude */

function cities_add_meta_box() {
    add_meta_box(
        'cities_location_meta_box', 
        __('City Location', 'textdomain'), 
        'cities_location_meta_box_callback', 
        'cities', 
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'cities_add_meta_box');

function cities_location_meta_box_callback($post) {
    wp_nonce_field('save_city_location', 'city_location_nonce');
    $latitude = get_post_meta($post->ID, '_city_latitude', true);
    $longitude = get_post_meta($post->ID, '_city_longitude', true);

    echo '<label for="city_latitude">' . __('Latitude', 'textdomain') . '</label>';
    echo '<input type="text" id="city_latitude" name="city_latitude" value="' . esc_attr($latitude) . '" size="25" />';
    
    echo '<label for="city_longitude">' . __('Longitude', 'textdomain') . '</label>';
    echo '<input type="text" id="city_longitude" name="city_longitude" value="' . esc_attr($longitude) . '" size="25" />';
}

function save_city_location($post_id) {
    if (!isset($_POST['city_location_nonce']) || !wp_verify_nonce($_POST['city_location_nonce'], 'save_city_location')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['city_latitude'])) {
        update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
    }
    if (isset($_POST['city_longitude'])) {
        update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}
add_action('save_post', 'save_city_location');

/** Create a Custom Taxonomy: "Countries" */

function create_countries_taxonomy() {
    $labels = array(
        'name' => _x('Countries', 'taxonomy general name', 'textdomain'),
        'singular_name' => _x('Country', 'taxonomy singular name', 'textdomain'),
        'search_items' => __('Search Countries', 'textdomain'),
        'all_items' => __('All Countries', 'textdomain'),
        'edit_item' => __('Edit Country', 'textdomain'),
        'update_item' => __('Update Country', 'textdomain'),
        'add_new_item' => __('Add New Country', 'textdomain'),
        'new_item_name' => __('New Country Name', 'textdomain'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'show_in_rest' => true,
    );

    register_taxonomy('countries', array('cities'), $args);
}
add_action('init', 'create_countries_taxonomy');

/** Create a Widget to Display City Name and Current Temperature */

class City_Temperature_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'city_temperature_widget',
            __('City Temperature Widget', 'text_domain'),
            array('description' => __('Displays the temperature of a city', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : 0;
        if ($city_id) {
            $city_name = get_the_title($city_id);
            $latitude = get_post_meta($city_id, '_city_latitude', true);
            $longitude = get_post_meta($city_id, '_city_longitude', true);

            // API call to OpenWeatherMap
            $api_key = '37876874c186113917051cfa7c678f4c';
            $weather_data = wp_remote_get("https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&units=metric&appid=$api_key");

            if (is_wp_error($weather_data)) {
                echo 'Unable to retrieve weather data.';
            } else {
                $body = wp_remote_retrieve_body($weather_data);
                $weather = json_decode($body);

                if ($weather && $weather->main->temp) {
                    echo "<p>$city_name: " . $weather->main->temp . "°C</p>";
                } else {
                    echo 'Weather data not available.';
                }
            }
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>">City:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>">
                <?php
                $cities = get_posts(array('post_type' => 'cities', 'numberposts' => -1));
                foreach ($cities as $city) {
                    echo '<option value="' . $city->ID . '" ' . selected($city_id, $city->ID, false) . '>' . $city->post_title . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['city_id'] = (!empty($new_instance['city_id'])) ? strip_tags($new_instance['city_id']) : '';
        return $instance;
    }
}

function register_city_temperature_widget() {
    register_widget('City_Temperature_Widget');
}
add_action('widgets_init', 'register_city_temperature_widget');

/** Enqueue the Script */
function enqueue_search_cities_script() {
    wp_enqueue_script('search-cities', get_template_directory_uri() . '/assets/js/search-cities.js', array('jquery'), null, true);

    // Localize the script with the AJAX URL
    wp_localize_script('search-cities', 'ajax_data', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_search_cities_script');


/** Handle the AJAX Request */

function search_cities() {
    global $wpdb;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Debugging
    error_log('Search Query: ' . $search);

    $results = $wpdb->get_results($wpdb->prepare("
        SELECT posts.ID, posts.post_title, meta_lat.meta_value as latitude, meta_lon.meta_value as longitude, term_tax.term_id as country_id, terms.name as country_name
        FROM $wpdb->posts as posts
        JOIN $wpdb->postmeta as meta_lat ON posts.ID = meta_lat.post_id AND meta_lat.meta_key = '_city_latitude'
        JOIN $wpdb->postmeta as meta_lon ON posts.ID = meta_lon.post_id AND meta_lon.meta_key = '_city_longitude'
        JOIN $wpdb->term_relationships as term_rel ON posts.ID = term_rel.object_id
        JOIN $wpdb->term_taxonomy as term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id AND term_tax.taxonomy = 'countries'
        JOIN $wpdb->terms as terms ON term_tax.term_id = terms.term_id
        WHERE posts.post_type = 'cities' AND posts.post_status = 'publish' AND posts.post_title LIKE %s
    ", '%' . $wpdb->esc_like($search) . '%'));

    if ($results) {
        foreach ($results as $city) {
            // Debugging
            error_log('Found City: ' . $city->post_title);

            $api_key = '37876874c186113917051cfa7c678f4c';
            $weather_url = "http://api.openweathermap.org/data/2.5/weather?lat={$city->latitude}&lon={$city->longitude}&appid={$api_key}&units=metric";
            $weather_data = wp_remote_get($weather_url);
            $temperature = 'N/A';

            if (!is_wp_error($weather_data)) {
                $body = wp_remote_retrieve_body($weather_data);
                $data = json_decode($body);
                $temperature = isset($data->main->temp) ? $data->main->temp : 'N/A';
            }

            echo "<tr data-city-id='{$city->ID}'>";
            echo "<td>{$city->country_name}</td>";
            echo "<td>{$city->post_title}</td>";
            echo "<td>{$temperature}</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No results found</td></tr>";
    }

    wp_die();
}

add_action('wp_ajax_search_cities', 'search_cities');
add_action('wp_ajax_nopriv_search_cities', 'search_cities');

// Function to display content before the table
function before_cities_table_content() {
    echo '<div class="before-table-message">Here is some information before the table.</div>';
}
add_action('before_cities_table', 'before_cities_table_content');

// Function to display content after the table
function after_cities_table_content() {
    echo '<div class="after-table-message">Here is some information after the table.</div>';
}
add_action('after_cities_table', 'after_cities_table_content');


/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
