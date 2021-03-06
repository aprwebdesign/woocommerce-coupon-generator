<?php
/*
Plugin Name: Create coupon
Plugin URI:  https://aprwebdesign.com
Description: Automatic generate coupon woocommerce
Version:     1.0
Author:      APR Webdesign
Author URI:  https://aprwebdesign.com
License:     GNU v3.0
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/* generate coupons */

function generate_coupons ($clientemail) {
$coupon_code = substr( "abcdefghijklmnopqrstuvwxyz123456789", mt_rand(0, 50) , 1) .substr( md5( time() ), 1); 
$coupon_code2 = substr( "abcdefghijklmnopqrstuvwxyz1234567890", mt_rand(0, 80) , 1) .substr( md5( time() ), 1);
$coupon_code = "bkleder-".substr( $coupon_code, 0,3)."-".substr( $coupon_code2, 0,4); // create the coupon
$amount = '20'; // Amount
$discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

$coupon = array(
    'post_title' => $coupon_code,
    'post_content' => '20 Euro coupon',
    'post_excerpt' => '20 Euro coupon',
    'post_status' => 'publish',
    'post_author' => 1,
    'post_type'     => 'shop_coupon'
);

$new_coupon_id = wp_insert_post( $coupon );

// Add meta
update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
update_post_meta( $new_coupon_id, 'product_ids', '' );
update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
update_post_meta( $new_coupon_id, 'usage_limit', '1' );
update_post_meta( $new_coupon_id, 'expiry_date', '' );
update_post_meta( $new_coupon_id, 'apply_before_tax', 'no' );
update_post_meta( $new_coupon_id, 'free_shipping', 'no' );      
update_post_meta( $new_coupon_id, 'exclude_sale_items', 'no' );     
update_post_meta( $new_coupon_id, 'product_categories', '' );       
update_post_meta( $new_coupon_id, 'exclude_product_categories', '' );       
update_post_meta( $new_coupon_id, 'minimum_amount', '100' );       
update_post_meta( $new_coupon_id, 'customer_email', $clientemail );       

return $coupon_code;

}

add_action('admin_menu', 'coupon_generator_setup_menu');

// Generate coupon from admin order overview

add_filter( 'woocommerce_admin_order_actions', 'add_generate_coupon_order_action', PHP_INT_MAX, 2 );

function add_generate_coupon_order_action( $actions, $the_order ) {
    
        $actions[] = array(
            'url'       => admin_url( 'admin.php?page=coupon-generator&cpgn=y&orderid=' . $the_order->id ),
            'name'      => __( 'Genereer kortingscode', 'woocommerce' ),
            'action'    => "view coupon", // setting "view" for proper button CSS
      );
   
    return $actions;
}

add_action( 'admin_head', 'add_generate_coupon_action_css' );
function add_generate_coupon_action_css() {
    echo '<style>.view.coupon::after { content: "\f524" !important; }</style>';
}

// Add coupon generator menu button 
function coupon_generator_setup_menu(){
        add_menu_page( 'Generate coupon', 'Coupon generator', 'manage_options', 'coupon-generator', 'generate_init' );
}
 
// submit to create coupon
function generate_init(){
        
		if (isset($_GET['cpgn'])) {
			 $order = new WC_Order($_GET['orderid']);
$clientemail = $order->billing_email;
generate_coupons($clientemail);
echo '<h1>Coupon generated!</h1>';
?><a class="btn" href="<?php echo admin_url( 'edit.php?post_type=shop_order');?>">Return to orders</a><?php
		}else{
		
		 if (isset($_POST['generetecouponbtn'])) {
			 echo '<h1>Coupon generated!</h1>';
			   
				  $clientemail =''; 
				  generate_coupons($clientemail);
			 
  }
  
  ?>
  <h2>Coupon generation</h2>
<form action="" method="post">
<input type="submit" value="generate coupon" name="generetecouponbtn"/>
</form>
	
<?php
		}
		}
