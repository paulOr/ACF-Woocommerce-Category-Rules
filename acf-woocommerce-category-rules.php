<?php
/*
	* Plugin Name: ACF WooCommerce Category Rules
	* Description: Adds location rules to Advanced Custom Fields to show fields based on WooCommerce product categories.
	* Version: 1.0
	* Requires at least: 5.2
	* Requires PHP: 7.2
	* Author: P4UL
	* Author URI: https://www.p4ul.dev
	* License: DBAD
	* License URI: https://dbad-license.org/
	* Text Domain: acf-woocommerce-category-rules
*/
	
	if (!defined('ABSPATH')) exit;
	
	add_filter('acf/location/rule_types', function($choices) {
		$choices['WooCommerce']['product_category'] = 'Product Category';
		return $choices;
	});
	
	add_filter('acf/location/rule_values/product_category', function($choices) {
		if (!class_exists('WooCommerce')) return $choices;
		$terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
		if(!empty($terms) && !is_wp_error($terms)):
			foreach($terms as $term):
				$choices[$term->term_id] = $term->name;
			endforeach;
		endif;
		return $choices;
	});
	
	add_filter('acf/location/rule_match/product_category', function($match, $rule, $screen) {
		if (!isset($screen['post_id']) || get_post_type($screen['post_id']) !== 'product') return false;
		$product_categories = wp_get_post_terms($screen['post_id'], 'product_cat', ['fields' => 'ids']);
		if($rule['operator'] === '=='):
			$match = in_array($rule['value'], $product_categories);
		elseif($rule['operator'] === '!='):
			$match = !in_array($rule['value'], $product_categories);
		endif;
		return $match;
	}, 10, 3);
