
// function that is claled when short code is sued
function function_donors( $atts ){

    	// setting the defautl values for paramters. Cid = 0 won't return any results
       $donors_atts = shortcode_atts( array(
	  'cid' => 0,
          'sort' => 'none',
          'sortref' => 'first_name',
          'showamount' => false
        ), $atts );
        
        // handling errro when cid is not provided
        if( $donors_atts['cid'==0]){
        	return "Error: Can't show donor list: cid must be provided in shortcode!!";
        }

	global $wpdb;
	// creating query
	$query = "";
	if(strcmp($donors_atts['sort'],'none')==1){
		// if no sort, then randomize
		$query = "SELECT first_name, last_name, sum(amount) as amount FROM wp_charitable_campaign_donations as cd, wp_charitable_donors as d WHERE cd.donor_id = d.donor_id AND campaign_id = ".$donors_atts['cid']." GROUP by d.donor_id order by rand();";
	}else{
		// checking values for sort and sort ref to be valid
		if(!strcmp($donors_atts['sortref'],'amount') && !strcmp($donors_atts['sortref'],'first_name') && !strcmp($donors_atts['sortref'],'last_name')){
			return "Error: Can't show donors list: wrong sortref value";
		}
		if(!strcmp($donors_atts['sort'],'asc') && !strcmp($donors_atts['sort'],'desc')){
			return "Error: Can't show donors list: wrong sort value";
		}
		// creating query that sorts
		$query = "SELECT first_name, last_name, sum(amount) as amount FROM wp_charitable_campaign_donations as cd, wp_charitable_donors as d WHERE cd.donor_id = d.donor_id AND campaign_id = ".$donors_atts['cid']." GROUP by d.donor_id ORDER BY ".$donors_atts['sortref']." ".$donors_atts['sort'].";";
	}	
	// submiting query
	$mylink = $wpdb->get_results($query);
	
	// generating results as ul tag
	$tmp = "<ul class='donors'>";
	foreach($mylink as $donor){

		$fname = $donor->first_name;
		$lname = $donor->last_name;
		$sumamount =$donor->amount;	
		
		//checking if amount of donations should be displayed
		if($donors_atts['showamount']==0){
			$tmp = $tmp . "<li> ".$fname." ".$lname." </li>";		
		}else{
			$tmp = $tmp . "<li> ".$fname." ".$lname.": ".$sumamount." </li>";
		}

	}
	$tmp = $tmp . "</ul>";
	return $tmp;
}
//addin shortcode
add_shortcode( 'donors', 'function_donors' );
