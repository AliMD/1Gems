<?php

add_theme_support( 'post-thumbnails' ); 

// Create the custom post type
add_action('init', 'catalog_init');
function catalog_init(){
	$labels = array(
		'name' => _x('Products', 'post type general name'),
		'singular_name' => _x('Product', 'post type singular name'),
		'add_new' => _x('Add New', 'product'),
		'add_new_item' => __('Add New Product'),
		'edit_item' => __('Edit Product'),
		'new_item' => __('New Product'),
		'view_item' => __('View Product'),
		'search_items' => __('Search Products'),
		'not_found' =>  __('No products found'),
		'not_found_in_trash' => __('No products found in Trash'),
		'parent_item_colon' => '',
		'menu_name' => 'Products'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon' => get_bloginfo('template_url') . '/images/producticon.png',
		'supports' => array('title','editor','thumbnail','excerpt')
	);
	register_post_type('products',$args);
}

// Create meta boxes for product information
add_action('add_meta_boxes', 'catalog_add_custom_box');
function catalog_add_custom_box() {
	add_meta_box('catalog_priceid', 'Price', 'catalog_price_box', 'products','side');
	add_meta_box('catalog_paypalid', 'PayCode', 'catalog_pay_box', 'products','side');
}

function catalog_price_box() {
	$price = 0;
	if ( isset($_REQUEST['post']) ) {
		$price = get_post_meta((int)$_REQUEST['post'],'catalog_product_price',true);
		$price = (float) $price;
	}
	?>
	<label for="catalog_product_price">Product Price</label>
	<input id="catalog_product_price" class="widefat" name="catalog_product_price" size="20" type="text" value="<?php echo $price; ?>">
	<?php
}

function catalog_pay_box() {
	$code = '';
	if ( isset($_REQUEST['post']) ) {
		$code = get_post_meta((int)$_REQUEST['post'],'catalog_product_paypal',true);
		$code = stripslashes($code);
	}
	?>
	<label for="catalog_product_paypal">Product PayPal Code</label>
	<textarea id="catalog_product_paypal" cols="30" rows="5" name="catalog_product_paypal"><?php echo $code; ?></textarea>
	<?php
}

// Save the product info
add_action('save_post','catalog_save_meta');
function catalog_save_meta($postID) {
	if ( is_admin() ) {
		if ( isset($_POST['catalog_product_paypal']) ) {
			update_post_meta($postID,'catalog_product_paypal', $_POST['catalog_product_paypal']);
		}
		if ( isset($_POST['catalog_product_price']) ) {
			update_post_meta($postID,'catalog_product_price', $_POST['catalog_product_price']);
		}
	}
}

// Products Category
add_action('init', 'reg_cat');
function reg_cat() {
         register_taxonomy_for_object_type('category','products');
}

/* display

<?php get_header(); ?>
<?php query_posts(array('post_type' => 'product')); ?>
<?php while(have_posts()): the_post(); ?>
<h1 class="product-title"><?php the_title(); ?></h1>
<div class="product-thumb">
			<?php the_post_thumbnail(); ?></div>
<div class="product-price">
			<?php echo get_post_meta(get_the_ID(),'catalog_product_price',true); ?></div>
<div class="product-description">
			<?php the_content() ?></div>
<div class="product-buy">
			<?php echo get_post_meta(get_the_ID(),'catalog_product_paypal',true); ?></div>
<?php endwhile; ?>
<?php get_footer(); ?>
*/