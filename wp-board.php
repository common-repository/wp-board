<?php
/*
Plugin Name: WP-Board
Plugin URI: http://www.xploreautomation.com
Description: This plugin adds a simple board useful to change messages among users.
Version: 1.1(Beta)
Author: Tr3nT89
Author URI: http://www.xploreautomation.com
License:GPL2
*/

/*
 * TODO
 
*/

function wp_board_install()
{
	global $wpdb;
    $table = $wpdb->prefix . "wpb_table";
    $sql = "CREATE TABLE " . $table . " (
    id int(10) NOT NULL AUTO_INCREMENT,
    author VARCHAR(25) NOT NULL,
    text LONGTEXT NOT NULL,
    tag VARCHAR(30),
    status int(10) NOT NULL,
    date DATETIME NOT NULL,
    PRIMARY KEY  (id)
    );";			
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	//option lunghezza board	
	add_option("board_limit",50);
	//option lunghezza board	
	add_option("board_height",300);
	//option lunghezza board	
	add_option("boarddb_limit",300);
	//option auto-message
	add_option("automsg",0);
	//option auto-mail
	add_option("automail",0);
	add_option("automailimponly",0);
		//carie option condizioni automsg
		add_option("automsg-draft",0);
		add_option("automsg-rev",0);
		add_option("automsg-pub",0);
		add_option("automsg-del",0);
}
function wp_board_deinstall()
{
	global $wpdb;
    $table = $wpdb->prefix . "wpb_table";
    $sql = "DROP TABLE " . $table . ";";			
	$wpdb->query($sql);	
	//option lunghezza board	
	delete_option("board_limit");
	//option lunghezza board	
	delete_option("board_height");
	//option lunghezza board	
	delete_option("boarddb_limit");
	//option auto-message
	delete_option("automsg");
	//option auto-mail
	delete_option("automail");
	delete_option("automailimponly");
		delete_option("automsg-draft");
		delete_option("automsg-rev");
		delete_option("automsg-pub");
		delete_option("automsg-del");
}
register_activation_hook(__FILE__, 'wp_board_install');
register_deactivation_hook(__FILE__, 'wp_board_deinstall');

//Parte riguardante la Sidebar
function wp_board_opt_menu() {
    global $wpdb;
    include 'php/board-admin.php';
} 
function init_wp_board_opt_menu() {
	global $current_user;
	get_currentuserinfo();	
 		if(current_user_can('administrator')) add_options_page("WP-Board", "WP-board", 1,"Wp-Board", "wp_board_opt_menu");
    } 
add_action('admin_menu', 'init_wp_board_opt_menu');

//Parte riguardante la Dashboard
function wp_board_dashboard() {
	global $current_user;
	global $wpdb;
	include 'php/board-dash.php';
}
function init_wp_board_dashboard() {
	wp_add_dashboard_widget( 'my_wp_dashboard_board', __( 'WP-Board' ), 'wp_board_dashboard' );
}
add_action('wp_dashboard_setup', 'init_wp_board_dashboard');
//Parte riguardante la Admin_Bar
function wp_board_adminbar() {			
		global $wpdb;	      	
		global $current_user;
		include 'php/board-bar.php';
}
add_action('admin_bar_menu', 'wp_board_adminbar', 1000);

function new_post_notify($postID,$post) {		
	$post_id = get_post($postID); 
	$status = $post_id->post_status;
	$title=$post_id->post_title;
	global $wpdb;
    $table = $wpdb->prefix . "wpb_table";
	if(strcmp($status,"pending")==0&&get_option())
		$sql="INSERT into ".$table." (author,text,date) values ('0','L\'articolo \" ".$title." \" è in attesa di revisione','".current_time('mysql')."')";
	if(strcmp($status,"auto-draft")==0)
		$sql="INSERT into ".$table." (author,text,date) values ('0','E\' stata creata la bozza \" ".$title." \" ','".current_time('mysql')."')";
	if(strcmp($status,"trash")==0)
		$sql="INSERT into ".$table." (author,text,date) values ('0','L\'articolo \" ".$title." \" è stato cancellato','".current_time('mysql')."')";
	if(strcmp($status,"publish")==0)
		$sql="INSERT into ".$table." (author,text,date) values ('0','L\'articolo \" ".$title." \" è stato pubblicato','".current_time('mysql')."')";
	
}

if(get_option("automsg")==1)
add_action('save_post','new_post_notify',10,2)
?>