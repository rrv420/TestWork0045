<?php
/*
Template Name: Cities Table
*/

get_header();

// Custom action hook before the table
do_action('before_cities_table');
?>

<div id="cities-search">
    <input type="text" id="city-search-input" placeholder="Search for a city...">
</div>

<table id="cities-table">
    <thead>
        <tr>
            <th>Country</th>
            <th>City</th>
            <th>Temperature (°C)</th>
        </tr>
    </thead>
    <tbody id="cities-table-body">
        <?php
        global $wpdb;
        $results = $wpdb->get_results("
            SELECT posts.ID, posts.post_title, meta_lat.meta_value as latitude, meta_lon.meta_value as longitude, term_tax.term_id as country_id, terms.name as country_name
            FROM $wpdb->posts as posts
            JOIN $wpdb->postmeta as meta_lat ON posts.ID = meta_lat.post_id AND meta_lat.meta_key = '_city_latitude'
            JOIN $wpdb->postmeta as meta_lon ON posts.ID = meta_lon.post_id AND meta_lon.meta_key = '_city_longitude'
            JOIN $wpdb->term_relationships as term_rel ON posts.ID = term_rel.object_id
            JOIN $wpdb->term_taxonomy as term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id AND term_tax.taxonomy = 'countries'
            JOIN $wpdb->terms as terms ON term_tax.term_id = terms.term_id
            WHERE posts.post_type = 'cities' AND posts.post_status = 'publish'
        ");

        foreach ($results as $city) {
            // Fetch temperature from OpenWeatherMap API
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
        ?>
    </tbody>
</table>

<?php
// Custom action hook after the table
do_action('after_cities_table');

get_footer(); 
