<?php

/* Update MapSVG data from Custom Post type */
add_action('save_post', 'update_mapsvg_data', 10, 2);
add_action('edit_post', 'update_mapsvg_data', 10, 2);

//Triggered anytime a post is edited or saved
function update_mapsvg_data($post_id, $post)
{
    if($post->post_type == 'attorneys')//make sure its an attorney post type
    {
        $key = 'attorneys';

        $args = array(
            'order'             => 'DESC',
            'post_status'       => 'publish',
            'suppress_filters' => true,
            'post_type'         => $key,
            'posts_per_page'    => -1,
        );

        $posts_arr = new WP_Query( $args ); //Queries all attorneys

        $post = $posts_arr->posts;


        mapsvg_data_clear_custom();
        foreach ($post as $p) //Loops through all attorneys
        {

            $arr = array();
            $fields = get_fields($p->ID); //Gets all the data fields for the post

            $map_id = $fields['map']; //Initialized map id from custom map field


            $data = array( //This is the data that mapsvg functions take to add new items to the database
                'title' => '',
                'first_name' => $fields['first_name'],
                'middle_name' => $fields['middle_name'],
                'last_name' => $fields['last_name'],
                'description' => '',
                'regions' =>  array (array (
                    'id' => $fields['region'],
                )),
                'images' => array (array (
                    'sizes' => array(
                        'full' => array( 'width' => 150, 'height' => 150, ),
                        'thumbnail' => array( 'width' => 150, 'height' => 150, ),
                        'medium' => array( 'width' => 150, 'height' => 150, ),
                    ),
                    'full' => get_the_post_thumbnail_url( $p->ID, 'thumbnail' ) ,
                    'thumbnail' => get_the_post_thumbnail_url( $p->ID, 'thumbnail' ),
                    'medium' => get_the_post_thumbnail_url( $p->ID, 'thumbnail' ),
                )),
                'link' => get_permalink( $p->ID),
                'location' => '',
                'id' => $p->ID,
            );


            //mapsvg_data_create_custom($map_id, 'database', $data ); //Add the $data to the map $map_id using mapSVG function

            if(isset($fields['country']))
            {
                $country_map_id = $fields['country'];//Initialized country map id from custom country field
                mapsvg_data_create_custom($country_map_id, 'database', $data );  //Add the $data to the map $country_map_id using mapSVG function
            }

        }

    }

}

//Used before adding data to a map. I copied the function from mapSVG plugin and appended _custom
function mapsvg_data_clear_custom()
{
    $mapNavigator = new MapNavigator(); //Holds tree to navigate our maps and cities /inc/MapNavigator.php. included through functions.php
    global $wpdb;
    foreach($mapNavigator->mapsNames as $id => $name)
    {
        $databaseObjectsToDelete = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."mapsvg_database_".$id . " WHERE location_x IS NULL");


        foreach ($databaseObjectsToDelete as $deleteId)
        {
            $wpdb->delete($wpdb->prefix."mapsvg_database_".$id, array('id'=>$deleteId->id));

        }

    }
}

//Used to add data to a map. I copied the function from mapSVG plugin and appended _custom
function mapsvg_data_create_custom($map_id, $table, $data){
    global $wpdb;


    $_data = mapsvg_encode_data_custom($map_id, $table, $data);



    $wpdb->insert($wpdb->prefix.MAPSVG_TABLE_NAME.'_'.$table.'_'.$map_id, $_data);

    // Add regions-to-dbObject relations
    $object_id = $data['id'];

    if($object_id && isset($data['regions']) && is_array($data['regions'])){
        $regions = $data['regions'];

        $wpdb->delete($wpdb->prefix.'mapsvg_r2d', array('map_id' => $map_id, 'object_id'=>$object_id));
        foreach($regions as $region){
            $wpdb->insert($wpdb->prefix.'mapsvg_r2d', array('map_id'    => $map_id,
                'region_id' => $region['id'],
                'object_id' => $object_id));
        }
    }

    $data['id'] = $object_id;

    if($wpdb->last_error){
        echo $wpdb->last_error;

    }


}

//Used to by function mapsvg_data_create_custom. I copied the function from mapSVG plugin and appended _custom
function mapsvg_encode_data_custom($map_id, $table, $data){

    global $db_schema, $db_types, $db_options, $db_multi, $wpdb;

    if(!$db_schema){
        $db_schema = $wpdb->get_var("SELECT fields FROM ".$wpdb->prefix."mapsvg_schema WHERE table_name LIKE '%mapsvg_".$table."_".$map_id."'");
        $db_schema = json_decode($db_schema, true);
    }

    if(!$db_types){
        $db_options = array();
        $db_multi = array();
        $db_types = array('id'=>'id');
        foreach($db_schema as $s){
            $db_types[$s['name']] = $s['type'];
            if(isset($s['options']))
                $db_options[$s['name']] = $s['optionsDict'];
            if(isset($s['multiselect']) && $s['multiselect'] === true)
                $db_multi[$s['name']] = true;
        }
    }

    $_data = array();

    foreach($data as $key=>$value){
        if(isset($db_types[$key])) switch ($db_types[$key]){
            case 'region':
                $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                break;
            case 'status':
                $key_text = $key.'_text';
                if(isset($db_options[$key][$value])){
                    $_data[$key] = $value;
                    $_data[$key_text] = $db_options[$key][$value]['label'];
                }else{
                    $_data[$key] = '';
                    $_data[$key_text] = '';
                }
                break;
            case 'select':
            case 'radio':
                $key_text = $key.'_text';

                if(isset($db_multi[$key]) && $db_multi[$key]) {
                    $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                }else{
                    if(isset($db_options[$key][$value])){
                        $_data[$key] = $value;
                        $_data[$key_text] = $db_options[$key][$value];
                    }else {
                        $options = array_flip($db_options[$key]);
                        if(isset($options[$value])){
                            $_data[$key] = $options[$value];
                            $_data[$key_text] = $value;
                        }else {
                            $_data[$key] = '';
                            $_data[$key_text] = '';
                        }
                    }
                }
                break;
            case 'checkbox':
                $_data[$key] = (int)($data[$key] === true || $data[$key] === 'true' || $data[$key] === '1' || $data[$key] === 1);
                break;
            case 'image':
            case 'marker':
                if(is_array($data[$key])){
                    $_data[$key] = json_encode($data[$key], JSON_UNESCAPED_UNICODE);
                }else{
                    $_data[$key] = $data[$key];
                }
                break;
            case 'location':
                if(!empty($data[$key])){
                    $location = array();

                    if(is_array($data[$key])){
                        $location = $data[$key];
                    } else {
                        $location = json_decode($data[$key]);
                    }

                    if((isset($location['lat']) && isset($location['lng'])) && (!empty($location['lat']) && !empty($location['lng']))) {
                        $_data['location_lat'] = $location['lat'];
                        $_data['location_lng'] = $location['lng'];
                    } else if((isset($location['x']) && isset($location['y'])) && (!empty($location['x']) && !empty($location['y']))){
                        $_data['location_x'] = $location['x'];
                        $_data['location_y'] = $location['y'];
                    }

                    if(isset($location['address'])){
                        $_data['location_address'] = isset($location['address']) ? json_encode($location['address'], JSON_UNESCAPED_UNICODE) : '';
                    }

                    $_data['location_img'] = isset($location['img']) ? $location['img'] : '';

                }

                break;
            default:
                $_data[$key] = $value;
                break;
        }
    }

    return $_data;
}
