<?php
/*
Plugin Name: WP context for GetSiteControl
Plugin URI:  https://kanvas.fr/getsitecontrol-wordpress-tag
Description: Add some useful tags in the header to work well with GetSiteControl
Version:     1.0.1
Author:      Mathieu Basili
Author URI:  https://kanvas.fr
Text Domain: kvgsctag
Domain Path: /languages
License:     GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'Nope!' );

add_action( 'wp_footer', 'kv_add_content_footer', 30 );

function kv_add_content_footer(){
	
	/*
		Ajouter tous les tags qui existent. 
		Ajouter info si utilisateur abonné, connecté, nom, prenom, email ...
	*/
	$usrInfo = '';
	
	if( is_user_logged_in() ) {
		
		$current_user = get_currentuserinfo();
		
		$usrInfo[] = "_gscq.push(['targeting','typeUser', 'logged_in']);";
		if( $current_user->user_login ) $usrInfo[] = "_gscq.push(['targeting','user_login', '".$current_user->user_login."']);";
		if( $current_user->user_firstname ) $usrInfo[] = "_gscq.push(['targeting','user_firstname', '".$current_user->user_firstname."']);";
		if( $current_user->user_lastname ) $usrInfo[] = "_gscq.push(['targeting','user_lastname', '".$current_user->user_lastname."']);";
		$usrInfo[] = "_gscq.push(['targeting','user_ID', '".$current_user->ID."']);";
			
	}
	
	if( function_exists( 'wcs_user_has_subscription' ) ) {
		
		if( wcs_user_has_subscription( '', '', 'active' ) ) {
			
			$usrInfo[] = "_gscq.push(['targeting','typeUser', 'has_subscription']);";	
			
		}
	}

	$post_types = apply_filters( 'wpgsc_posttypes', array('post', 'product') );

	if( is_singular($post_types) ) {
		
		global $post;
		$tag_ids = wp_get_post_terms( $post->ID, 'post_tag', array( 'fields' => 'id=>slug' ) );
		if( $tag_ids ) {
			foreach ( $tag_ids as $tag ){
				$usrInfo[] = "_gscq.push(['targeting','post_tag', '".$tag."']);";
			}
		}
		
		$cat_ids = wp_get_post_terms( $post->ID, 'category', array( 'fields' => 'id=>slug' ) );
		if( $cat_ids ) {
			foreach ( $cat_ids as $cat ){
				$usrInfo[] = "_gscq.push(['targeting','post_cat', '".$cat."']);";
			}
		}
	}
	
	if( is_singular('product') ) {
		
		global $post;
		$tag_ids = wp_get_post_terms( $post->ID, 'product_tag', array( 'fields' => 'id=>slug' ) );
		if( $tag_ids ) {
			foreach ( $tag_ids as $tag ){
				$usrInfo[] = "_gscq.push(['targeting','product_tag', '".$tag."']);";
			}
		}
		
		$cat_ids = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'id=>slug' ) );
		if( $cat_ids ) {
			foreach ( $cat_ids as $cat ){
				$usrInfo[] = "_gscq.push(['targeting','product_cat', '".$cat."']);";
			}
		}
	}
	
	$usrInfo = apply_filters( 'wpgsc_array', $usrInfo );
	
	
	if( $usrInfo ) {
		echo '<script>';
		foreach ($usrInfo as $u) {
			echo $u;
		}
		echo '</script>';
	}
	
	
	
}

