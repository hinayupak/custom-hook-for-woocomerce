<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );



// Additional Functions
// =============================================================================
add_filter( 'cs_looper_custom_rel_prod', 'rel_prod');
function rel_prod($result){
	global $product;

  	$prod_id = $product->get_id();	
	$rel_pod_array = wc_get_related_products( $prod_id, 4, $prod_id );
	$prd_arr_final = array();
		
	foreach ($rel_pod_array as $prod_item) {
				$product_det = wc_get_product( $prod_item );
				$prd_arr = $product_det->get_data();
				
				$img_url = wp_get_attachment_url( $prd_arr['image_id'] );
				$prd_title = $prd_arr['name'];
				$prd_price = $prd_arr['price'];
				$prd_slug = $prd_arr['slug'];
				$prd_link = get_permalink( $prd_arr['id'] );
				$pid = $prd_arr['id'];		
				$prd_arr_final[$pid] = array('img_url'=>$img_url, 'prd_title'=>$prd_title,'prd_price'=>$prd_price,'prd_slug'=>$prd_slug,'prd_link'=>$prd_link,'prd_id'=>$pid );
		}
// 	echo "<pre>";
// 	print_r($prd_arr_final);
// 	echo "</pre>";
	return $prd_arr_final;
}

// =============================================================================
add_filter( 'cs_looper_custom_sub_cat_prod', 'sub_cat_prod');

function sub_cat_prod($result) {
	global $wp_query;

	$paged = get_query_var( 'paged', 1 );
	$slug = get_queried_object()->slug;
	$products = wc_get_products(array(
		'status' => array( 'publish' ),
		'stock_status' => 'instock',
		'category'	=> array($slug),
		'orderby' => 'date',  
        'order' => 'DESC',
        'limit' => 8,
        'value' => 'instock',
        'page' => $paged,
	));

	$prd_arr_final = array();

	foreach ($products as $prod_item) {
		$product_det = wc_get_product( $prod_item );
		$prd_arr = $product_det->get_data();

		$img_url = wp_get_attachment_url( $prd_arr['image_id'] );
		$prd_title = $prd_arr['name'];
		$prd_price = $prd_arr['price'];
		$prd_slug = $prd_arr['slug'];
		$prd_link = get_permalink( $prd_arr['id'] );
		$pid = $prd_arr['id'];		
		$prd_arr_final[$pid] = array('img_url'=>$img_url, 'prd_title'=>$prd_title,'prd_price'=>$prd_price,'prd_slug'=>$prd_slug,'prd_link'=>$prd_link,'prd_id'=>$pid );
	}

   return $prd_arr_final;
}

// =============================================================================
include 'function-themeco-end.php';
include 'function-themeco-frontend.php';

// =============================================================================
function ajd_cart_table() {
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();
	$c_total = WC()->cart->get_total();
    $c_stotal = WC()->cart->get_cart_subtotal();
	$cart_html = '';
    
    $cart_html .= '<table class="woo-cart-info">';
    $cart_html .= '<tr>';
    $cart_html .= '<td><h5 class="table-label">Product</h5></td>';
    $cart_html .= '<td></td>';
    $cart_html .= '<td><h5 class="table-label">Subtotal</h5></td>';
    $cart_html .= '</tr>';
   
        foreach($items as $item => $values) { 
            $_product =  wc_get_product( $values['data']->get_id());
            $price = get_post_meta($values['product_id'] , '_price', true);
            $getProductDetail = wc_get_product( $values['product_id'] );
            $cart_html .= "<tr class='cart-item-tr'>";
            $cart_html .= "<td class='image-cell c-left'>" . $getProductDetail->get_image() . "</td>";
            $cart_html .= "<td class='quantity-cell c-middle'><span class='c-product-quantity'>x" . $values['quantity'] . "</span></td>";
            $cart_html .= "<td class='price-cell c-right'><span class='p-price'>$" . $price . "</span></td></tr>";	
        }
	
    $cart_html .= "<tr>";
	$cart_html .= "<td></td>";
	$cart_html .= "<td class='td-right td-stotal'>Subtotal</td>";
	$cart_html .= "<td><span class='c-stotal'>" . $c_stotal . "</span></td>";
	$cart_html .= "</tr>";
	$cart_html .= "<tr>";
	$cart_html .= "<td></td>";
	$cart_html .= "<td class='td-right td-total''>Total</td>";
	$cart_html .= "<td><span class='c-total'>" . $c_total . "</span></td>";
	$cart_html .= "</tr>";
	$cart_html .= "</table>";
	
	echo $cart_html;
}
add_shortcode('ajd_ctable', 'ajd_cart_table');

//==============================================================================
function woo_custom_order_button_text() {
    return __( 'place order now', 'woocommerce' ); 
}

add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); 

//=============================================================================
function webendev_woocommerce_checkout_fields( $fields ) {

	$fields['order']['order_comments']['placeholder'] = '';
	return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'webendev_woocommerce_checkout_fields' );

//=============================================================================
function wc_billing_field_strings( $translated_text, $text, $domain ) {
	switch ( $translated_text ) {
		case 'Billing details' :
		$translated_text = __( 'Billing Information', 'woocommerce' );
		break;
	}
	return $translated_text;
}

add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );

// =============================================================================
function customCartCoupon_function() {
	if ( wc_coupons_enabled() ) {
?>
		<div class="coupon_box">
			<div><h2 class="heading1">Got a coupon? Use it here.</h2></div>
			<div class="coupon">
				<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
					<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
					<button type="submit" class="button" name="apply_coupon" value="Apply Coupon" style="outline: none;">Apply Coupon</button>
				</form>
			</div>
		</div>
<?php 
	}
}

add_shortcode('customCartCoupon', 'customCartCoupon_function');

// =============================================================================
function customCartTotal_function() {
?>
	<div class="cart-collaterals">
		<?php //do_action( 'woocommerce_cart_collaterals' ); ?>
		<?php include 'woocommerce/cart/cart-totals.php'; ?>
	</div>
<?php
}

add_shortcode('customCartTotal', 'customCartTotal_function');

// =============================================================================
function add_rand_orderby_rest_post_collection_params( $query_params ) {
	$query_params['orderby']['enum'][] = 'rand';
	return $query_params;
}

add_filter( 'rest_post_collection_params', 'add_rand_orderby_rest_post_collection_params' );

// =============================================================================
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
add_action('woocommerce_after_cart_table', 'woocommerce_cross_sell_display');