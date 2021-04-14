<?php if ( is_user_logged_in() && current_user_can( 'administrator' ) && isset( $_GET ) && array_key_exists( 'location_data', $_GET ) && $_GET[ 'location_data' ] === "1" ) {
	add_action( 'init', 'get_location', 999 );
	
    function get_location(){
	global $wpdb;
    $export= array();
	$location_query = "SELECT * FROM wb_bl_locations";
    $location_hours_query = "SELECT * FROM wb_bl_location_hours where location_id=";
    $states_map = array(
        'AL'=>'Alabama',
        'AK'=>'Alaska',
        'AZ'=>'Arizona',
        'AR'=>'Arkansas',
        'CA'=>'California',
        'CO'=>'Colorado',
        'CT'=>'Connecticut',
        'DE'=>'Delaware',
        'DC'=>'District of Columbia',
        'FL'=>'Florida',
        'GA'=>'Georgia',
        'HI'=>'Hawaii',
        'ID'=>'Idaho',
        'IL'=>'Illinois',
        'IN'=>'Indiana',
        'IA'=>'Iowa',
        'KS'=>'Kansas',
        'KY'=>'Kentucky',
        'LA'=>'Louisiana',
        'ME'=>'Maine',
        'MD'=>'Maryland',
        'MA'=>'Massachusetts',
        'MI'=>'Michigan',
        'MN'=>'Minnesota',
        'MS'=>'Mississippi',
        'MO'=>'Missouri',
        'MT'=>'Montana',
        'NE'=>'Nebraska',
        'NV'=>'Nevada',
        'NH'=>'New Hampshire',
        'NJ'=>'New Jersey',
        'NM'=>'New Mexico',
        'NY'=>'New York',
        'NC'=>'North Carolina',
        'ND'=>'North Dakota',
        'OH'=>'Ohio',
        'OK'=>'Oklahoma',
        'OR'=>'Oregon',
        'PA'=>'Pennsylvania',
        'RI'=>'Rhode Island',
        'SC'=>'South Carolina',
        'SD'=>'South Dakota',
        'TN'=>'Tennessee',
        'TX'=>'Texas',
        'UT'=>'Utah',
        'VT'=>'Vermont',
        'VA'=>'Virginia',
        'WA'=>'Washington',
        'WV'=>'West Virginia',
        'WI'=>'Wisconsin',
        'WY'=>'Wyoming',
    );


    $location_hours_map = array(
        '0'=>'Sunday',
        '1'=>'Monday',
        '2'=>'Tuesday',
        '3'=>'Wednesday',
        '4'=>'Thursday',
        '5'=>'Friday',
        '6'=>'Saturday',

    );
    $results = $wpdb->get_results( $wpdb->prepare($location_query));

    error_log(count($results));

     foreach ( $results as $index=>$row )
		{
			
            
            $export[$index]["id"] = $row->id;
            $terms = wp_get_object_terms($row->id, 'region');
            $export[$index]["address1"]  = $row->address_line_1;
            $export[$index]["address2"] = $row->address_line_2;
            $export[$index]["city"]  =  $terms[0]->name;
        
            $export[$index]["zip"]  =  $row->postal_code;
            $export[$index]["phone"]  =  $row->phone;
            $export[$index]["email"]  =  $row->email;
            $export[$index]["order_online_id"]  =  $row->order_online_id;
            $export[$index]["third_party_order_url"]  =  $row->third_party_order_url;
            $export[$index]["lat"]  =  $row->geo_latitude;
            $export[$index]["long"]  =  $row->geo_longitude;
				
            $state = get_term( $terms[0]->parent )->name;
            if(array_key_exists($state,$states_map)){
             $export[$index]["address"] =  $row->address_line_1 . " " .  $row->address_line_2 .  ", ".  $terms[0]->name . ", ". $states_map[$state]. ", ".$row->postal_code; 
             $export[$index]["state"]   =  $states_map[$state];
             $export[$index]["country"] = "United States";
                
            }else{
            $export[$index]["address"] = $row->address_line_1 . " " .  $row->address_line_2 .  ", ".  $terms[0]->name . ", ".$row->postal_code; 
            $export[$index]["state"] =  $state;
           if(!empty(get_term(get_term( $terms[0]->parent)->parent)->name)){
            $export[$index]["country"] = get_term(get_term( $terms[0]->parent)->parent)->name;
           }else{
            $export[$index]["state"] =  "";
            $export[$index]["country"] = $state;

           }
            
        }



        $location_hours_results = $wpdb->get_results( $wpdb->prepare( $location_hours_query.$row->id));
        $location_hours = "<ul>";
        foreach( $location_hours_results as $h){
            $weekday = $h->weekday;
            $location_hours.= "<li>".  $location_hours_map[$weekday] . " " . $h->open . " " . $h->close . "</li>";
       
         }
        $location_hours.="</ul>";
        $export[$index]["hours"] =   $location_hours;
		}

        // echo "<pre>";
        // print_r($export);
        // echo "</pre>";
        // error_log(count($export));
        // error_log(print_r( $export,true));
        array_csv_download($export);
 
	}
    

}


function array_csv_download( $array, $filename = "export.csv", $delimiter="," )
{
    header( 'Content-Type: application/csv' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '",' );

    // clean output buffer
    ob_end_clean();

    $handle = fopen( 'php://output', 'w' );

    // use keys as column titles
    fputcsv( $handle, array_keys( $array['0'] ) );

    foreach ( $array as $value ) {
        fputcsv( $handle, $value , $delimiter );
    }

    fclose( $handle );

    // flush buffer
    ob_flush();

    // use exit to get rid of unexpected output afterward
    exit();
}

?>