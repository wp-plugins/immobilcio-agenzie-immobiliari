<?php
/*
 * Plugin Name: ImmobilCIO - Agenzia Immobiliare
 * Plugin URI: http://www.CMS-Italia.Org/ImmobilCIO
 * Description: Plugin for real estate management
 * Author: Davide Tommasin
 * Version: 0.8.1
 * Author URI: http://www.tommasin.org
 *    
 * @version 0.8.1
 * @copyright 2013 - 2016
 * @author Davide Tommasin (email: info@CMS-Italia.Org)
 * @link http://www.CMS-Italia.org
 * 
 * @license GNU General Public License v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package ImmobilCIO
 * 
 * @Last Revised 
 */

  /**
   * configure your admin page
   */
  $config = array(    
		'menu'=> 'settings',             //sub page to settings page
		'page_title' => __('ImmobilCIO - Real Estate Management','immobilcio'),       //The name of this page 
		'capability' => 'edit_themes',         // The capability needed to view the page 
		'option_group' => 'demo_options',       //the name of the option to create in the database
		'id' => 'admin_page',            // meta box id, unique per page
		'fields' => array(),            // list of fields (can be added by field arrays)
		'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );  

  // load_muplugin_textdomain( 'immobilcio', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
  // load_textdomain( 'immobilcio', 'lang');
  
function immobillang() {
	if ( is_multisite() ) {
			load_plugin_textdomain( 'immobilcio', basename( dirname( __FILE__ ) ) . 'lang', basename( dirname( __FILE__ ) ) . '/lang' );
			load_plugin_textdomain( 'immobilcio', basename( dirname( __FILE__ ) ) . 'lang', 'immobilcio/lang' );
		} else {
			$lang_path = dirname( plugin_basename( __FILE__ ) ) . '/lang/';
			load_plugin_textdomain( 'immobilcio', false, $lang_path );
		}
}
add_action( 'init', 'immobillang' );
    
  /**
   * add singlehome post type on loop
   */

function custom_post_singlehome_loop( $query ) {
	if ( is_home() && $query->is_main_query() )
	$query->set( 'post_type', array( 'post', 'singlehome') );
	return $query;
	}

add_filter( 'pre_get_posts', 'custom_post_singlehome_loop' );

  /**
   * rename label of posts menu
   */
   
function change_post_menu_label() {
	global $menu;
	global $submenu;
	$menu[5][0] = 'News';
	$submenu['edit.php'][5][0] = 'Blog';
	$submenu['edit.php'][10][0] = 'Add News';
	$submenu['edit.php'][16][0] = 'News Tags';
	echo '';
}
function change_post_object_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['post']->labels;
	$labels->name = 'News';
	$labels->singular_name = 'News';
	$labels->add_new = 'Add News';
	$labels->add_new_item = 'Add News';
	$labels->edit_item = 'Edit News';
	$labels->new_item = 'News';
	$labels->view_item = 'View News';
	$labels->search_items = 'Search News';
	$labels->not_found = 'No News found';
	$labels->not_found_in_trash = 'No News found in Trash';
}
add_action( 'init', 'change_post_object_label' );
add_action( 'admin_menu', 'change_post_menu_label' );

  /**
   * create custom post type singlehome
   */

    function post_type_singlehome() {  
        register_post_type( 'singlehome',  
            array(  
                'labels' => array(  
                    'name' => __( 'Property','immobilcio' ),  
                    'singular_name' => __( 'Property','immobilcio' )  
                ),  
            'public' => true,  
            'menu_position' => 2,
            'menu_icon' => plugins_url( '/img/icon_home.gif' , __FILE__ ),
            'rewrite' => array('slug' => 'property'),
            'supports' => array(
            				'title',
            				'editor',
            				'comments',
            				'thumbnail',
            				'author'),
            )  
        );  
    }  
      
    add_action( 'init', 'post_type_singlehome' );    

  /**
   * rename title field of custom_post_type page entry
   */
    
function change_default_title( $title ){
     $screen = get_current_screen();
 
     if  ( 'singlehome' == $screen->post_type ) {
          $title = 'Add Name of Property';
     }
     return $title;
}
add_filter( 'enter_title_here', 'change_default_title' );

  /**
   * add metabox - details of single property
   */

function wpb_initialize_cmb_meta_boxes() {
	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once(plugin_dir_path( __FILE__ ) . 'init.php');
}

add_action( 'init', 'wpb_initialize_cmb_meta_boxes', 9999 );

//Add Meta Boxes

function wpb_sample_metaboxes( $meta_boxes ) {
	$prefix = '_shd_'; // Prefix for all fields

	$meta_boxes[] = array(
		'id' => 'custom-metabox-details',
		'title' => __( 'Real Estate Management Module','immobilcio' ),
		'pages' => array('singlehome'), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
array(
'name' => __('General Details','immobilcio'),
'desc' => __('Details of home','immobilcio'),
'id' => $prefix . 'gendet',
'type' => 'title',
),
array(
'name' => __('For Sale/Rent?*','immobilcio'),
'desc' => __('Type of adv','immobilcio'),
'id' => $prefix . 'sale_rent',
'type' => 'radio_inline',
'options' => array(
array( 'name' => 'Sale', 'value' => 'sale', ),
array( 'name' => 'Rent', 'value' => 'rent', ),
array( 'name' => 'Sold', 'value' => 'sold', ),
)),
array(
'name' => __('Price','immobilcio'),
'desc' => __('Example: 145.000 or Reserved','immobilcio'),
'id' => $prefix . 'price',
'type' => 'text_small',
),
array(
'name' => __('Bedrooms','immobilcio'),
'desc' => __('numbers of bedrooms (optional)','immobilcio'),
'id' => $prefix . 'bedrooms',
'type' => 'select',
'options' => array(
array( 'name' => '1', 'value' => '1', ),
array( 'name' => '2', 'value' => '2', ),
array( 'name' => '3', 'value' => '3', ),
array( 'name' => '4', 'value' => '4', ),
array( 'name' => '5', 'value' => '5', ),
array( 'name' => '6', 'value' => '6', ),
array( 'name' => '7', 'value' => '7', ),
array( 'name' => '8', 'value' => '8', ),
array( 'name' => '9', 'value' => '9', ),
array( 'name' => '10+', 'value' => '10+', )
),
),
array(
'name' => __('Bathrooms','immobilcio'),
'desc' => __('numbers of bathrooms (optional)','immobilcio'),
'id' => $prefix . 'bathrooms',
'type' => 'select',
'options' => array(
array( 'name' => '1', 'value' => '1', ),
array( 'name' => '2', 'value' => '2', ),
array( 'name' => '3', 'value' => '3', ),
array( 'name' => '4', 'value' => '4', ),
array( 'name' => '5', 'value' => '5', ),
array( 'name' => '6', 'value' => '6', ),
array( 'name' => '7', 'value' => '7', ),
array( 'name' => '8', 'value' => '8', ),
array( 'name' => '9', 'value' => '9', ),
array( 'name' => '10+', 'value' => '10+', )
),
),
array(
'name' => __('Area * ','immobilcio'),
'desc' => __('( Sq.Ft. ) (optional)','immobilcio'),
'id' => $prefix . 'sqft',
'type' => 'text_small',
),
array(
'name' => __('Geolocalization Details','immobilcio'),
'desc' => __('Details of address - All fields are request for search engine features on your web site.','immobilcio'),
'id' => $prefix . 'geolocal',
'type' => 'title',
),
array(
'name' => __('Address','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'address',
'type' => 'text',
),
array(
'name' => __('City','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'city',
'type' => 'text',
),
array(
'name' => __('Country','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'country',
'type' => 'text',
),
array(
'name' => __('State','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'state',
'type' => 'text',
),
array(
'name' => __('Zip code','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'zip',
'type' => 'text',
),
array(
'name' => __('Energy Performance Certificate','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'energy',
'type' => 'title',
),
array(
'name' => __('ACE','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'ace',
'type' => 'select',
'options' => array(
array( 'name' => 'A', 'value' => 'A', ),
array( 'name' => 'B', 'value' => 'B', ),
array( 'name' => 'C', 'value' => 'C', ),
array( 'name' => 'D', 'value' => 'D', ),
array( 'name' => 'E', 'value' => 'E', ),
array( 'name' => 'F', 'value' => 'F', ),
array( 'name' => 'G', 'value' => 'G', )
),
),
array(
'name' => __('IPE','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'ipe',
'type' => 'text_small',
),
array(
'name' => __('IPE Unity','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'ipeunity',
'type' => 'select',
'options' => array(
array( 'name' => __('kWh/m2 year'), 'value' => __('kWh/m2 year'), ),
array( 'name' => __('kWh/m3 year'), 'value' => __('kWh/m3 year'), ),
),
),
array(
'name' => __('About Agent Contacts','immobilcio'),
'desc' => __('You can put custom contact detail on this fields (example: of your clients) otherwise ImmobilCIO gets details from your WordPress profile.','immobilcio'),
'id' => $prefix . 'agent',
'type' => 'title',
),
array(
'name' => __('Telephone','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'telephone',
'type' => 'text',
),
array(
'name' => __('Mail','immobilcio'),
'desc' => __('','immobilcio'),
'id' => $prefix . 'mail',
'type' => 'text',
),

		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'wpb_sample_metaboxes' );

// add taxonomy prices for custom post type singlehome

add_action( 'init', 'price_category_init', 100 ); // 100 so the post type has been registered
function price_category_init()
{
register_taxonomy(
'price',
'singlehome',
array(
'labels' => array(
'name' => 'Prices',
'singular_name' => 'Price Type',
'search_items' => 'Search Price Types',
'popular_items' => 'Popular Price Types',
'all_items' => 'All Price Types',
'edit_item' => __( 'Edit Price Type','immobilcio' ),
'update_item' => __( 'Update Price Type','immobilcio' ),
'add_new_item' => __( 'Add New Price Type','immobilcio' ),
'new_item_name' => __( 'New Price Type','immobilcio' )
),
'hierarchical' => 'false',
'label' => 'Price Type' )
);
}

// populating price taxonomy
/* function price_populating(){
 
    // see if we already have populated any terms
    $price = get_terms( 'price', array( 'hide_empty' => false ) );
 
    // if no terms then lets add our terms
    if( empty( $price ) ){
        $prices = prices_range();
        foreach( $prices as $price ){
            if( !term_exists( $price['pricerange'], 'price' ) ){
                wp_insert_term( $price['pricerange'], 'price', array( 'slug' => $price['short'] ) );
            }
        }
    }
 
}
add_action( 'init', 'price_populating' );

// array price range values

function prices_range(){
    $prices = array(
        '0' => array( 'pricerange' => 'fino a 500', 'short' => '500' ),
        '1' => array( 'pricerange' => 'fino a 1.000', 'short' => '1000' ),
        '2' => array( 'pricerange' => 'fino a 2.000', 'short' => '2000' ),
        '3' => array( 'pricerange' => '50.000', 'short' => '5000' ),
        '4' => array( 'pricerange' => '100.000', 'short' => '100000' ),
        '5' => array( 'pricerange' => '200.000', 'short' => '200000' ),
        '6' => array( 'pricerange' => '300.000', 'short' => '300000' ),
        '7' => array( 'pricerange' => '400.000', 'short' => '400000' ),
        '8' => array( 'pricerange' => '500.000', 'short' => '500000' ),
        '9' => array( 'pricerange' => '600.000', 'short' => '600000' ),
        '10' => array( 'pricerange' => '700.000', 'short' => '700000' ),
        '11' => array( 'pricerange' => '800.000', 'short' => '800000' ),
        '12' => array( 'pricerange' => '900.000', 'short' => '900000' ),
        '13' => array( 'pricerange' => '1.000.000', 'short' => '1000000' ),
        '14' => array( 'pricerange' => 'Oltre 1.000.000', 'short' => 'overmillion' ),
        '15' => array( 'pricerange' => 'Reserved', 'short' => 'reserved' ),
        );
    return $prices;
}
*/

// add taxonomy zone for custom post type singlehome

add_action( 'init', 'zone_category_init', 120 ); // 120 so the post type has been registered
function zone_category_init()
{
register_taxonomy(
'zone',
'singlehome',
array(
'labels' => array(
'name' => 'Zone',
'singular_name' => 'Zone Type',
'search_items' => 'Search Zone Types',
'popular_items' => 'Popular Zone Types',
'all_items' => 'All Zone Types',
'edit_item' => __( 'Edit Zone Type','immobilcio' ),
'update_item' => __( 'Update Zone Type','immobilcio' ),
'add_new_item' => __( 'Add New Zone Type','immobilcio' ),
'new_item_name' => __( 'New Zone Type','immobilcio' )
),
'hierarchical' => 'false',
'label' => 'Zone Type' )
);
}

// add taxonomy type for custom post type singlehome

add_action( 'init', 'type_category_init', 120 ); // 120 so the post type has been registered
function type_category_init()
{
register_taxonomy(
'type',
'singlehome',
array(
'labels' => array(
'name' => 'Type',
'singular_name' => 'Type Real Estate',
'search_items' => 'Search Types',
'popular_items' => 'Popular Types',
'all_items' => 'All Types',
'edit_item' => __( 'Edit Type','immobilcio' ),
'update_item' => __( 'Update Type','immobilcio' ),
'add_new_item' => __( 'Add New Type','immobilcio' ),
'new_item_name' => __( 'New Type','immobilcio' )
),
'hierarchical' => 'false',
'label' => 'Type' )
);
}

/**********************************************
 * Add column
 **********************************************/

// Register the column
function ace_column_register( $columns ) {
	$columns['ACE'] = __( 'Ace', 'immobilcio' );
	return $columns;
}

add_filter( 'manage_edit-singlehome_columns', 'ace_column_register' );

// Display the column content
function ace_column_display( $column_name, $post_id ) {
	if ( '_shd_ace' != $column_name )
		return;
 
	$ace = get_post_meta($post_id, '_shd_ace', true);
	if ( !$ace )
		$ace = '<em>' . __( 'certificazione-energetica','immobilcio' ) . '</em>';
	$ipe = get_post_meta($post_id, '_shd_ipe', true);
	if ( !$ipe )
		$ipe = '<em>' . __( 'certificazione-energetica','immobilcio' ) . '</em>';
 
	echo $ace.' / '.$ipe;
}
add_action( 'manage_posts_custom_column', 'ace_column_display', 10, 2 );

// Register the column as sortable
function ace_column_register_sortable( $columns ) {
	$columns['ace_post'] = '_shd_ace'; 
	return $columns;
}
add_filter( 'manage_edit-singlehome_sortable_columns', 'ace_column_register_sortable' );


// add columns for listing singlehome post type

add_filter( 'manage_edit-singlehome_columns', 'my_edit_singlehome_columns' ) ;

function my_edit_singlehome_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'featimg' => __( 'Photo','immobilcio' ),
		'title' => __( 'Name of proprerty','immobilcio' ),
		'status' => __('Status','immobilcio'),
		'type' => __( 'Type','immobilcio' ),
		'address' => __( 'Adress','immobilcio' ),
		'ace' => __( 'ACE','immobilcio' ),
		'date' => __( 'Date','immobilcio' )
	);

	return $columns;
}

// add value and content on custom columns

add_action( 'manage_singlehome_posts_custom_column', 'my_manage_singlehome_columns', 10, 2 );

function my_manage_singlehome_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'address' column. */
		case 'featimg' :
			
			the_post_thumbnail( array(100,100) );

			break;

		/* If displaying the 'address' column. */
		case 'address' :

			/* Get the post meta. */
			$address0 = get_post_meta($post->ID, '_shd_address', true);
			$address1 = get_post_meta($post->ID, '_shd_city', true);
			$address2 = get_post_meta($post->ID, '_shd_zip', true);
			$address3 = get_post_meta($post->ID, '_shd_country', true);
			$address4 = get_post_meta($post->ID, '_shd_state', true);
		    $realaddress = $address0 .', '. $address1 .', '. $address2 .' '. $address3 .','. $address4;

			if ( empty( $address0 ) && empty( $address1 ) && empty( $address2 ) && empty( $address3 ) && empty( $address4 ) && empty( $address5 ) )
				echo __( ' ','immobilcio' );

			else
				printf( __( '%s','immobilcio' ), $realaddress );

			break;
			
		/* If displaying the 'type' column. */
		case 'status' :

			/* Get the post meta. */
			$status = get_post_meta( $post_id, '_shd_sale_rent', true );

			if ( empty( $status ) )
				echo __( ' ','immobilcio' );

			else
				printf( __( '%s' ), $status );

			break;
			
		/* If displaying the 'ACE' column. */
		case 'ace' :

			/* Get the post meta. */
			$ace = get_post_meta( $post_id, '_shd_ace', true );

			if ( empty( $ace ) )
				echo __( ' ','immobilcio' );

			else
				printf( __( '%s','immobilcio' ), $ace );

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

/**********************************************
 * ADD Google Map on post if exist address
 **********************************************/
 function gmaps_header() {
/*	$options = get_option('immob_options');
	$select = isset($options['chk_maps']);
	$keymaps = $select; */
/*	return $keymaps; */
?>
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<?php
}
add_action('wp_head', 'gmaps_header');

function google_map(){

	if( get_post_type() == 'singlehome' ):
		global $post;

	$options = get_option('immob_options');
	$select = isset($options['act_maps']);
	$mapw =	$options['chk_mapw'];
	$maph =	$options['chk_maph'];
	$act_maps = $select;

	$address0_sc = get_post_meta($post->ID, '_shd_address', true);
	$address1_sc = get_post_meta($post->ID, '_shd_city', true);
	$address2_sc = get_post_meta($post->ID, '_shd_zip', true);
	$address3_sc = get_post_meta($post->ID, '_shd_country', true);
	$address4_sc = get_post_meta($post->ID, '_shd_state', true);
    $address = $address0_sc .', '. $address1_sc .', '. $address2_sc .' '. $address3_sc .','. $address4_sc;
    
	if ( !empty ($address ) && ($act_maps) == "1" ){ ?>
		<div id="google_map" style="width:<?php echo $mapw; ?>px;height:<?php echo $maph; ?>px;"></div><br />
		<form onsubmit="calcRoute();return false;">
			<?php echo __('From: '); ?><input type="text" id="start" value="">
			<?php echo __('To: '); ?><input type="text" value="<?php echo $address; ?>" readonly="readonly">
			<input type="submit" value="<?php echo __('Give me route'); ?>">
		</form><br />
		<div id="directionsPanel" style="width:<?php echo $mapw; ?>px;"></div>
		<script type="text/javascript">
			var directionDisplay;
			var directionsService = new google.maps.DirectionsService();
			directionsDisplay = new google.maps.DirectionsRenderer();
			var latlng = new google.maps.LatLng(0,0);
			var myOptions = {
				zoom: 13,
				center: latlng,
				mapTypeControl: true,
				mapTypeId: google.maps.MapTypeId.HYBRID
			};
			var google_map = new google.maps.Map(document.getElementById("google_map"), myOptions);
			directionsDisplay.setMap(google_map);
			directionsDisplay.setPanel(document.getElementById("directionsPanel"))
			var geocoder_google_map = new google.maps.Geocoder();
			var address = '<?php echo $address ?>';
			geocoder_google_map.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					google_map.setCenter(results[0].geometry.location);
					var marker = new google.maps.Marker({
						map: google_map,
						position: google_map.getCenter()
						});
						<?php $thiscontent = $address;?>
						var contentString = '<b><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></b><br /><?php echo $thiscontent; ?>';
						var infowindow = new google.maps.InfoWindow({
							content: contentString
						});
						infowindow.open(google_map,marker);
						google.maps.event.addListener(marker, 'click', function() {
							infowindow.open(google_map,marker);
						});

					} else {
						alert("Geocode was not successful for the following reason: " + status);
					}
				});

			function calcRoute() {
				var address = '<?php echo $address ?>';
				var start = document.getElementById("start").value;
				var request = {
					origin:start,
					destination:address,
					travelMode: google.maps.DirectionsTravelMode.DRIVING
				};
				directionsService.route(request, function(response, status) {
				  if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(response);
				  }
				});
			}
		</script>
<?php
	}
 endif;
}
	
// add_action('the_content','google_map');

/**********************************************
 * aggregate value of custom field in content of custom post type singlehome
 **********************************************/
 
function custom_field_singlehome($detailshome) {
	global $post; global $wp_query;
	$options = get_option('immob_options');
	if( get_post_type() == 'singlehome' ):
		$price		=	get_post_meta($post->ID, '_shd_price', true);
		$sqft	 	= 	get_post_meta($post->ID, '_shd_sqft', true);

		$bedrooms	= 	get_post_meta($post->ID, '_shd_bedrooms', true);
		$bathrooms	= 	get_post_meta($post->ID, '_shd_bathrooms', true);

		$type	 	= 	get_post_meta($post->ID, '_shd_sale_rent', true);
		
		$ace		=	get_post_meta($post->ID, '_shd_ace', true);
		$ipe		=	get_post_meta($post->ID, '_shd_ipe', true);
		$ipeunity	=	get_post_meta($post->ID, '_shd_ipeunity', true);

		$address0	=	get_post_meta($post->ID, '_shd_address', true);
		$address1 	= 	get_post_meta($post->ID, '_shd_city', true);
		$address2 	= 	get_post_meta($post->ID, '_shd_zip', true);
		$address3 	= 	get_post_meta($post->ID, '_shd_country', true);
		$address4 	= 	get_post_meta($post->ID, '_shd_state', true);
		
		$telephone 	= 	get_post_meta($post->ID, '_shd_telephone', true);
		$mail	 	= 	get_post_meta($post->ID, '_shd_mail', true);
	endif;
	
		$terms = get_terms("type");
		$count = count($terms);

	if( is_single() ) {
	if(!empty($type)): $detailshome .= "<div style=\"border: 1px dotted #000000; margin-bottom: 10px;\"><ul><li>". __('Staus: ') . $type ."</li>"; endif;
	if(!empty($count)): $detailshome .= "<li>". __('Real Estate Type','immobilcio') .": ";
	if ( $count > 0 ) {
		foreach ( $terms as $term ) {
			$detailshome .= '<a href="'. get_bloginfo('url') ."/type/". $term->slug . '">';
			$detailshome .= $term->name;
			$detailshome .= "</a>, ";
		}
	}; endif;
	$detailshome .= "</li>";
	if(!empty($sqft)): $detailshome .= "<li>". __('Square Meter ','immobilcio') .": ". $sqft ."</li>"; endif;
	if(!empty($price)): $detailshome .= "<li>". __('Price: ','immobilcio') .": ". $price ." ". $options['chk_currency'] ."</li>"; endif;
	if(!empty($type)): $detailshome .= "<li>". __('Bedrooms: ','immobilcio') .": ". $bedrooms ."</li>"; endif;
	if(!empty($bedrooms)): $detailshome .= "<li>". __('Bathrooms: ','immobilcio') .": ". $bathrooms ."</li>"; endif;
	if(!empty($ace)): $detailshome .= "<li>". __('ACE: ','immobilcio') . $ace ." ". $ipe ." ". $ipeunity ."</li>"; endif;
	if(!empty($address0)): $detailshome .= "<li>". __('Address: ','immobilcio') . $address0 .', '. $address1 .', '. $address2 .' '. $address3 .','. $address4 ."</li>"; endif;
	if(!empty($type)): $detailshome .= "</ul></div>"; endif;
	
	
	
		if(!empty($telephone) && !empty($mail)) {
			$detailshome .= "<div style=\"border: 1px dotted #000000; margin-bottom: 10px;\"><ul><li>". __('Telephone: ') . $telephone ."</li>";
			$detailshome .= "<li>". __('Mail: ','immobilcio') . $mail ."</li></ul></div>";
			} else {
				// get contacts detail's of blog's author - thelephone, skype, avatar...
				if( get_post_type() == 'singlehome' ):
					$cellophono = get_the_author_meta( 'cellophono', isset($user->ID) );
					$skype 		 = get_the_author_meta( 'skype', isset($user->ID) );
					$local_avatars = get_user_meta( isset($user->ID), 'simple_local_avatar', true );
				endif;
								
				$detailshome .= "<div style=\"border: 1px dotted #000000; margin-bottom: 10px;\">";
				$detailshome .= "<div style=\"float:left\">". get_avatar( $post->post_author, $size = '100' )."</div>";
				$detailshome .= "<ul style=\"margin-left: 130px;\">";
				if(!empty($cellophono)): $detailshome .= "<li>Telephone: ". $cellophono ."</li>"; endif;
				if(!empty($skype)): $detailshome .= "<li>Skype: ". esc_attr($skype) ."</li></ul>"; endif;
				$detailshome .= "<div style=\"clear: both;\"></div></div>";
			}
	}
	
	if(!empty($address0) && !empty($address1) && !empty($address2) && !empty($address3) && !empty($address4) && is_single()) {
		$detailshome .= "<div style=\"border: 1px dotted #000000; margin-bottom: 10px;\">". google_map() ."</div>";
		} else {
		}
			
	return $detailshome;
	
}

add_action('the_content','custom_field_singlehome');


/**********************************************
 * REMOVE MEDIA BUTTON FROM EDIT CUSTOM POST SINGLEHOME
 **********************************************/

/* Feat. on next release */

/**********************************************
 * ADD WIDGET
 **********************************************/
 
class ciowidget extends WP_Widget {
    function ciowidget() {
        parent::__construct( false, 'ImmobilCIO Widget' );
    }
    function widget( $args, $instance ) {
        extract($args);
        echo $before_widget;
        echo $before_title.$instance['title'].$after_title;


$query = new WP_Query();

//Send our widget options to the query
$query->query (
			array(
			'post_type' => 'singlehome',
			/* 'posts_per_page' => 5, */
    		'ignore_sticky_posts' => 1,
			)
		);
global $post; global $wp_query;
if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
 
	<div id="immobsingle" style="border: 1px solid #d3d3d3; width: 99%; box-shadow: 0px 3px 3px #d3d3d3; margin: 0px 0px 7px 0px; height: 74px; font-size: 12px;">
		<div style="float: left;">
			<?php
				if(has_post_thumbnail()) {
					the_post_thumbnail( array(75,75) );
				} else {
				
					$url = network_home_url();
			?>
				<img src="<?php echo $url; ?>wp-content/plugins/immobilcio/images/noimage.png" style="box-shadow: none; width:75px; height:74px;" />
			<?php
				}
			?>
		</div>
		<?php
			$status = get_post_meta($post->ID, '_shd_sale_rent', true);
			if ($status == "sold") {
					echo "<div style=\"float:right; color: #FFFFFF; background: red; font-family: sans-serif; padding: 0px 2px 0px 5px;\">". $status ."</div>";
				} else {
					echo "<div style=\"float:right; color: #FFFFFF; background: green; font-family: sans-serif; padding: 0px 2px 0px 5px;\">". $status ."</div>";
				}
		?>
		<ul style="margin-left: 78px; list-style:none;">
			<li style="line-height: 1.3;">
				<strong>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</strong>
			</li>
		<?php
			$price = get_post_meta($post->ID, '_shd_price', true);
			echo "<li>". __('Price: ','immobilcio') . $price ."</li>";
		?>
		</ul>
				<div style="clear: both;"></div>		
	</div>

<?php endwhile; endif;
// Reset Query
wp_reset_query();


	// add dropdown menu of price taxonomy
	function get_price_dropdown($taxonomies, $args){
    	$myterms = get_terms($taxonomies, $args);
    	$output ="<select name='price'>";
    	$output .="<option value='#'>". __('Select Prices Category','immobilcio') ."</option>";
    	foreach($myterms as $term){
        	$root_url = get_bloginfo('url');
        	$term_taxonomy=$term->taxonomy;
        	$term_slug=$term->slug;
        	$term_name =$term->name;
        	$link = $term_slug;
        	$output .="<option value='".$link."'>".$term_name."</option>";
   		}
    	$output .="</select>";
		return $output;
	}
?>
	<div style="margin: 20px 0px 10px 0px">
	<form action="<?php bloginfo('url'); ?>" method="get">
	<?php
		$taxonomies = array('price');
		$args = array('orderby'=>'name','hide_empty'=>true);
		$select = get_price_dropdown($taxonomies, $args);
 
		$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
		echo $select;
	?>
    <noscript><div><input type="submit" value="Go" /></div></noscript>
    </form>
    </div>

<?php
	// add dropdown menu of zone taxonomy
	function get_zone_dropdown($taxonomies, $args){
    	$myterms = get_terms($taxonomies, $args);
    	$output ="<select name='zone'>";
    	$output .="<option value='#'>". __('Select Zones Category','immobilcio') ."</option>";
    	foreach($myterms as $term){
        	$root_url = get_bloginfo('url');
        	$term_taxonomy=$term->taxonomy;
        	$term_slug=$term->slug;
        	$term_name =$term->name;
        	$link = $term_slug;
        	$output .="<option value='".$link."'>".$term_name."</option>";
   		}
    	$output .="</select>";
		return $output;
	}
?>
		
	<div style="margin: 20px 0px 10px 0px">
	<form action="<?php bloginfo('url'); ?>" method="get">
	<?php
		$taxonomies = array('zone');
		$args = array('orderby'=>'name','hide_empty'=>true);
		$select = get_zone_dropdown($taxonomies, $args);
 
		$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
		echo $select;
	?>
    <noscript><div><input type="submit" value="Go" /></div></noscript>
    </form>
    </div>

<?php
	// add dropdown menu of type taxonomy
	function get_type_dropdown($taxonomies, $args){
    	$myterms = get_terms($taxonomies, $args);
    	$output ="<select name='type'>";
    	$output .="<option value='#'>". __('Select Type Category','immobilcio') ."</option>";
    	foreach($myterms as $term){
        	$root_url = get_bloginfo('url');
        	$term_taxonomy=$term->taxonomy;
        	$term_slug=$term->slug;
        	$term_name =$term->name;
        	$link = $term_slug;
        	$output .="<option value='".$link."'>".$term_name."</option>";
   		}
    	$output .="</select>";
		return $output;
	}
?>
		
	<div style="margin: 20px 0px 10px 0px">
	<form action="<?php bloginfo('url'); ?>" method="get">
	<?php
		$taxonomies = array('type');
		$args = array('orderby'=>'name','hide_empty'=>true);
		$select = get_type_dropdown($taxonomies, $args);
 
		$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
		echo $select;
	?>
    <noscript><div><input type="submit" value="Go" /></div></noscript>
    </form>
    </div>
    
        <?php echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
    function form( $instance ) {
        $title = esc_attr($instance['title']); ?>
        <p><label for="<?php echo $this->get_field_id('title');?>">
        Titolo: <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title; ?>" />
        </label></p>
        <?php
    }
}
 
function my_register_widgets() {
    register_widget( 'ciowidget' );
}
 
add_action( 'widgets_init', 'my_register_widgets' );

/**********************************************
 * add the admin options page
 **********************************************/

include (dirname(__FILE__) . '/admin/panel.php');

/**********************************************
 * ADD SHORTCODE
 **********************************************/

/* for next release */

/**********************************************
 * ADD Modules
 **********************************************/

/* for next release */

/**********************************************
 * add new fields and details for Users WP & Agents
 **********************************************/

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );
 
function extra_user_profile_fields( $user ) { ?>
<h3><?php _e("Details of Agent", "blank"); ?></h3>
 
<table class="form-table">
<tr>
<th><label for="cellophono"><?php _e("Telephone",'immobilcio'); ?></label></th>
<td>
<input type="text" name="cellophono" id="cellophono" value="<?php echo esc_attr( get_the_author_meta( 'cellophono', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Telephone's number of Agent",'immobilcio'); ?></span>
</td>
</tr>
<tr>
<th><label for="skype"><?php _e("Skype",'immobilcio'); ?></label></th>
<td>
<input type="text" name="skype" id="skype" value="<?php echo esc_attr( get_the_author_meta( 'skype', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Skype username of Agent",'immobilcio'); ?></span>
</td>
</tr>
</table>
<?php }
 
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );
 
function save_extra_user_profile_fields( $user_id ) {
 
if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
update_user_meta( $user_id, 'cellophono', $_POST['cellophono'] );
update_user_meta( $user_id, 'skype', $_POST['skype'] );
}

/**
 * add field to user profiles
 */
 
class Simple_Local_Avatars {
	private $user_id_being_edited;
	
	public function __construct() {
		add_filter( 'get_avatar', array( $this, 'get_avatar' ), 10, 5 );
		
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		
		add_action( 'show_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		
		add_action( 'personal_options_update', array( $this, 'edit_user_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );
		
		add_filter( 'avatar_defaults', array( $this, 'avatar_defaults' ) );
	}
	
	public function get_avatar( $avatar = '', $id_or_email, $size = 96, $default = '', $alt = false ) {
		
		if ( is_numeric($id_or_email) )
			$user_id = (int) $id_or_email;
		elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) )
			$user_id = $user->ID;
		elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) )
			$user_id = (int) $id_or_email->user_id;
		
		if ( empty( $user_id ) )
			return $avatar;
		
		$local_avatars = get_user_meta( $user_id, 'simple_local_avatar', true );
		
		if ( empty( $local_avatars ) || empty( $local_avatars['full'] ) )
			return $avatar;
		
		$size = (int) $size;
			
		if ( empty( $alt ) )
			$alt = get_the_author_meta( 'display_name', $user_id );
			
		// generate a new size
		if ( empty( $local_avatars[$size] ) ) {
			$upload_path = wp_upload_dir();
			$avatar_full_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $local_avatars['full'] );
			$image_sized = image_resize( $avatar_full_path, $size, $size, true );
			/* $image_sized = wp_get_image_editor( $avatar_full_path );
			if ( ! is_wp_error( $image_sized ) ) {
			    // $image_sized->rotate( 90 );
			    // $image_sized->resize( 300, 300, true );
			    // $image_sized->save( 'new_image.jpg' );
			} */
			
			// deal with original being >= to original image (or lack of sizing ability)
			$local_avatars[$size] = is_wp_error($image_sized) ? $local_avatars[$size] = $local_avatars['full'] : str_replace( $upload_path['basedir'], $upload_path['baseurl'], $image_sized );	
			// save updated avatar sizes
			update_user_meta( $user_id, 'simple_local_avatar', $local_avatars );
		} elseif ( substr( $local_avatars[$size], 0, 4 ) != 'http' ) {
			$local_avatars[$size] = home_url( $local_avatars[$size] );
		}
		
		$author_class = is_author( $user_id ) ? ' current-author' : '' ;
		$avatar = "<img alt='" . esc_attr( $alt ) . "' src='" . $local_avatars[$size] . "' class='avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";
		
		return apply_filters( 'simple_local_avatar', $avatar );
	}
	
	public function admin_init() {
		load_plugin_textdomain( 'simple-local-avatars', false, dirname( plugin_basename( __FILE__ ) ) . '/localization/' );
		
		register_setting( 'discussion', 'simple_local_avatars_caps', array( $this, 'sanitize_options' ) );
		add_settings_field( 'simple-local-avatars-caps', __('Local Avatar Permissions','immobilcio'), array( $this, 'avatar_settings_field' ), 'discussion', 'avatars' );
	}
	
	public function sanitize_options( $input ) {
		$new_input['simple_local_avatars_caps'] = empty( $input['simple_local_avatars_caps'] ) ? 0 : 1;
		return $new_input;
	}
	
	public function avatar_settings_field( $args ) {		
		$options = get_option('simple_local_avatars_caps');
		
		echo '
			<label for="simple_local_avatars_caps">
				<input type="checkbox" name="simple_local_avatars_caps" id="simple_local_avatars_caps" value="1" ' . @checked( $options['simple_local_avatars_caps'], 1, false ) . ' />
				' . __('Only allow users with file upload capabilities to upload local avatars (Authors and above)','immobilcio') . '
			</label>
		';
	}
	
	public function edit_user_profile( $profileuser ) {
	?>
	<h3><?php _e( 'Avatar','simple-local-avatars' ); ?></h3>
	
	<table class="form-table">
		<tr>
			<th><label for="simple-local-avatar"><?php _e('Upload Logo Real Estate Agency','immobilcio'); ?></label></th>
			<td style="width: 50px;" valign="top">
				<?php echo get_avatar( $profileuser->ID ); ?>
			</td>
			<td>
			<?php
				$options = get_option('simple_local_avatars_caps');
			
				if ( empty($options['simple_local_avatars_caps']) || current_user_can('upload_files') ) {
					do_action( 'simple_local_avatar_notices' ); 
					wp_nonce_field( 'simple_local_avatar_nonce', '_simple_local_avatar_nonce', false ); 
			?>
					<input type="file" name="simple-local-avatar" id="simple-local-avatar" /><br />
			<?php
					if ( empty( $profileuser->simple_local_avatar ) )
						echo '<span class="description">' . __('No local avatar is set. Use the upload field to add a local avatar.','immobilcio') . '</span>';
					else 
						echo '
							<input type="checkbox" name="simple-local-avatar-erase" value="1" /> ' . __('Delete local avatar','immobilcio') . '<br />
							<span class="description">' . __('Replace the local avatar by uploading a new avatar, or erase the local avatar (falling back to a gravatar) by checking the delete option.','immobilcio') . '</span>
						';		
				} else {
					if ( empty( $profileuser->simple_local_avatar ) )
						echo '<span class="description">' . __('No local avatar is set. Set up your avatar at Gravatar.com.','immobilcio') . '</span>';
					else 
						echo '<span class="description">' . __('You do not have media management permissions. To change your local avatar, contact the blog administrator.','immobilcio') . '</span>';
				}
			?>
			</td>
		</tr>
	</table>
	<script type="text/javascript">var form = document.getElementById('your-profile');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>
	<?php		
	}
	
	public function edit_user_profile_update( $user_id ) {
		if ( ! isset( $_POST['_simple_local_avatar_nonce'] ) || ! wp_verify_nonce( $_POST['_simple_local_avatar_nonce'], 'simple_local_avatar_nonce' ) )			//security
			return;
	
		if ( ! empty( $_FILES['simple-local-avatar']['name'] ) ) {
			$mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif' => 'image/gif',
				'png' => 'image/png',
				'bmp' => 'image/bmp',
				'tif|tiff' => 'image/tiff'
			);
		
			// front end (theme my profile etc) support
			if ( ! function_exists( 'wp_handle_upload' ) )
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			
			$this->avatar_delete( $user_id );	// delete old images if successful
			
			// need to be more secure since low privelege users can upload
			if ( strstr( $_FILES['simple-local-avatar']['name'], '.php' ) )
				wp_die('For security reasons, the extension ".php" cannot be in your file name.');
			
			$this->user_id_being_edited = $user_id; // make user_id known to unique_filename_callback function
			$avatar = wp_handle_upload( $_FILES['simple-local-avatar'], array( 'mimes' => $mimes, 'test_form' => false, 'unique_filename_callback' => array( $this, 'unique_filename_callback' ) ) );
			
			if ( empty($avatar['file']) ) {		// handle failures
				switch ( $avatar['error'] ) {
					case 'File type does not meet security guidelines. Try another.' :
						add_action( 'user_profile_update_errors', create_function('$a','$a->add("avatar_error",__("Please upload a valid image file for the avatar.","immobilcio"));') );				
						break;
					default :
						add_action( 'user_profile_update_errors', create_function('$a','$a->add("avatar_error","<strong>".__("There was an error uploading the avatar:","immobilcio")."</strong> ' . esc_attr( $avatar['error'] ) . '");') );
				}
				
				return;
			}
			
			update_user_meta( $user_id, 'simple_local_avatar', array( 'full' => $avatar['url'] ) );		// save user information (overwriting old)
		} elseif ( ! empty( $_POST['simple-local-avatar-erase'] ) ) {
			$this->avatar_delete( $user_id );
		}
	}
	
	/**
	 * remove the custom get_avatar hook for the default avatar list output on options-discussion.php
	 */
	public function avatar_defaults( $avatar_defaults ) {
		remove_action( 'get_avatar', array( $this, 'get_avatar' ) );
		return $avatar_defaults;
	}
	
	/**
	 * delete avatars based on user_id
	 */
	public function avatar_delete( $user_id ) {
		$old_avatars = get_user_meta( $user_id, 'simple_local_avatar', true );
		$upload_path = wp_upload_dir();
			
		if ( is_array($old_avatars) ) {
			foreach ($old_avatars as $old_avatar ) {
				$old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatar );
				@unlink( $old_avatar_path );	
			}
		}
		
		delete_user_meta( $user_id, 'simple_local_avatar' );
	}
	
	public function unique_filename_callback( $dir, $name, $ext ) {
		$user = get_user_by( 'id', (int) $this->user_id_being_edited ); 
		$name = $base_name = sanitize_file_name( $user->display_name . '_avatar' );
		$number = 1;
		
		while ( file_exists( $dir . "/$name$ext" ) ) {
			$name = $base_name . '_' . $number;
			$number++;
		}
				
		return $name . $ext;
	}
}

$simple_local_avatars = new Simple_Local_Avatars;

/**
 * more efficient to call simple local avatar directly in theme and avoid gravatar setup
 * 
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return string <img> tag for the user's avatar
 */
function get_simple_local_avatar( $id_or_email, $size = '96', $default = '', $alt = false ) {
	global $simple_local_avatars;
	$avatar = $simple_local_avatars->get_avatar( '', $id_or_email, $size, $default, $alt );
	
	if ( empty ( $avatar ) )
		$avatar = get_avatar( $id_or_email, $size, $default, $alt );
	
	return $avatar;
}

/**
 * on uninstallation, remove the custom field from the users and delete the local avatars
 */

register_uninstall_hook( __FILE__, 'simple_local_avatars_uninstall' );

function simple_local_avatars_uninstall() {
	$simple_local_avatars = new Simple_Local_Avatars;
	$users = get_users_of_blog();
	
	foreach ( $users as $user )
		$simple_local_avatars->avatar_delete( $user->user_id );
	
	delete_option('simple_local_avatars_caps');
}

?>