<?php
/**
 * @package Website Toolbox Chat Room
 * @author Website Toolbox
 */
/*
Plugin Name: Website Toolbox Chat Room
Description: Integrates single sign on and embeds your chat room into your WordPress website.
Version: 1.1.5
Author: <a href="https://www.websitetoolbox.com/chat_room/index.html?wordpress" target="_blank">Website Toolbox</a>
Purpose: Integrates your chat room with your WordPress website
*/
// start new session if not started from another files.
try {
    // Set script execution time if execution time is less than default script execution time.
    // Default script execution time is 30 seconds.
    $scriptExecutionTimeLimit = ini_get('max_execution_time');
    if($scriptExecutionTimeLimit && $scriptExecutionTimeLimit < 30) {
        set_time_limit(0);
    }
    ob_start();
} catch (Exception $e) {
}

// start new session if not started from another files.
if(!session_id()) {
	session_start();
}

/* Purpose: Function is used to insert Chat Room title, default page content, plug-in status, in the option table.
Parameter: None
Return: None */
function create_wtbChatRoom_page() {
    $my_post = array();
    $page_named_forum = get_page_by_title('Chat Room');
    $title = "Chat Room";
    if($page_named_forum) $title = "Chat Room";
    $my_post['post_title'] = $title;
    $my_post['post_content'] = "Please go to the admin section and change your Website Toolbox Chat Room settings.";
    $my_post['post_status'] = 'publish';
    $my_post['post_author'] = 1;
    $my_post['post_category'] = array(1);
    $my_post['post_type'] = 'page';
    $my_post['comment_status'] = 'closed';
    $my_post['ping_status'] = 'closed';
    $pid = wp_insert_post( $my_post );
    update_option('wtbChatRoomPageid', $pid);
}

/* Purpose: Set page content on the front end according to the basic theme
Parameter: None
Return: None */
function wtbChatRoom_lol($content) {
	$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
	$page_content = get_page($wtbChatRoomPageid);
	$page_content = $page_content->post_content;
	$theme_data = wp_get_theme();
	$wrap_pre = "<style>.nocomments { display: block; }</style>";
	$wrap_post = "";
	if($theme_data['Name']=="WordPress Default" && strpos($theme_data['Description'], '>Kubrick<')==90) {
		$wrap_pre .= "<div style='background-color: white;'>";
		$wrap_post .= "</div>";
	}
	if($theme_data['Template'] == "twentyeleven") {
	$wrap_pre .= "
		<style type=\"text/css\">
		.singular .entry-header, .singular .entry-content, .singular footer.entry-meta, .singular #comments-title {
		width: 100%;
		}
		.singular #content, .left-sidebar.singular #content {
		margin: 0 1.5%;
		}
		.page-id-$wtbChatRoomPageid  .entry-title {display: none;}

		#main { padding: 0; }
		.singular.page .hentry { padding: 0; }
		</style>";
	}
	return "$wrap_pre
	$page_content
	$wrap_post";
}

/* Purpose: Create a Chat Room page for front end into WordPress site.
Parameter: None
Return: None */
function wtb_chatroom_init() {
	$wtb_chatroom_page_id = get_option('wtbChatRoomPageid');
	if(is_page($wtb_chatroom_page_id)) {
		$page = get_page($wtb_chatroom_page_id);
		if($page && $page->post_status!='publish') {
			$page->post_status = 'publish';
			wp_update_post($page);
		}
		add_filter("the_content", "wtbChatRoom_lol");
	}
}

/* Purpose: Create a Chat Room settings menu under settings tab into WordPress admin menu.
Parameter: None
Return: None */
function wtbChatRoom_add_admin_menu() {
    add_options_page( 'Website Toolbox Chat Room', 'Website Toolbox Chat Room', 'manage_options', 'wtbchatroomoptions', 'wtbchatroom_options_page' );
}

/* Purpose: Create a Chat Room settings options page to set chat room settings into WordPress admin section.
Parameter: None
Return: None */
function wtbchatroom_admin_init() {

	// To show settings description on Chat Room settings page on WordPress admin panel.
    add_settings_section(
        'wtb_chatroom_settings_section',
        '<h2>Website Toolbox Chat Room</h2>',
        'wtb_chatroom_settings_desc',
        'wtbchatroomoptions'
    );

    // To show Chat Room username option on Chat Room settings page on WordPress admin panel.
    add_settings_field(
        'wtb_chatroom_username',
        'Chat Room Username:',
        'wtb_chatroom_username_option',
        'wtbchatroomoptions',
        'wtb_chatroom_settings_section'
    );

	// To show Chat Room API option on Chat Room settings page on WordPress admin panel.
	add_settings_field(
        'wtb_chatroom_api',
        'Chat Room API Key:',
        'wtb_chatroom_api_option',
        'wtbchatroomoptions',
        'wtb_chatroom_settings_section'
    );

	// To show Chat Room embed option on Chat Room settings page on WordPress admin panel.
	add_settings_field(
        'wtb_chatroom_redirect',
        'Embed the Chat Room:',
        'wtb_chatroom_embed_option',
        'wtbchatroomoptions',
        'wtb_chatroom_settings_section'
    );


	register_setting( 'wtbchatroomoptions', 'wtb_chatroom_username' );
	register_setting( 'wtbchatroomoptions', 'wtb_chatroom_api' );
	register_setting( 'wtbchatroomoptions', 'wtb_chatroom_redirect' );
}

/* Purpose: Add Chat Room settings description on Chat Room settings options page into WordPress admin section.
Parameter: None
Return: None */
function wtb_chatroom_settings_desc() {
	echo '<OL>
	<LI><a href="https://www.websitetoolbox.com/chat_room/index.html?wordpress" target="_blank">Create a Chat Room on Website Toolbox</a> or <a href="https://www.websitetoolbox.com/tool/members/login" target="_blank">login to your existing Website Toolbox Chat Room</a>.</LI>
	<LI>Click the <i>Settings</i> link in the navigation menu at the top.</LI>
	<LI>Copy the <i>API Key</i> from the page and paste it into the <i>Chat Room API Key</i> text box on this WordPress plugin setup page.</LI>
	<LI>Provide your <i>Website Toolbox Username</i> in the text box below and click the <i>Update</i> button.</LI>
	</OL>
	<p>Please <a href="https://www.websitetoolbox.com/contact?subject=WordPress+Chat+Room+Plugin+Setup+Help" target="_blank">Contact Customer Support</a> if you need help getting setup.</p>
	';
}

/* Purpose: Add username option on Chat Room settings page into WordPress Chat Room settings page.
Parameter: None
Return: None */
function wtb_chatroom_username_option($args) {
	$chatroom_username = isset($_POST['wtb_chatroom_username']) ? $_POST['wtb_chatroom_username'] : get_option('wtb_chatroom_username');

	$html = '<input type="text" name="wtb_chatroom_username" id="wtb_chatroom_username" value="'.$chatroom_username.'" size="50"/>';

	$html .= '<label for="wtb_chatroom_username"> <a href="https://www.websitetoolbox.com/chat_room/index.html?wordpress" target="_blank">Create a Chat Room at Website Toolbox</a> to get your username.</label>';

	echo $html;
}

/* Purpose: Add API option on Chat Room settings page into WordPress Chat Room settings page.
Parameter: None
Return: None */
function wtb_chatroom_api_option($args) {
	$chatroom_api = isset($_POST['wtb_chatroom_api']) ? $_POST['wtb_chatroom_api'] : get_option('wtb_chatroom_api');

	$html = '<input type="text" name="wtb_chatroom_api" id="wtb_chatroom_api" value="'.$chatroom_api.'" size="50"/>';

	$html .= '<label for="wtb_chatroom_api"> Get your <a href="https://www.websitetoolbox.com/support/getting-your-api-key-634" target="_blank">API key</a>.</label>';

	echo $html;
}

/* Purpose: Add embed option on Chat Room settings page into WordPress Chat Room settings page.
Parameter: None
Return: None */
function wtb_chatroom_embed_option($args) {
	// enable Embed option bydefault when user install the plugin.
	if(!get_option("wtb_chatroom_username")) {
		$checked = 1;
	} else {
		$checked = get_option('wtb_chatroom_redirect');
	}
	$html = '<input type="checkbox" name="wtb_chatroom_redirect" id="wtb_chatroom_redirect" value="1" ' . checked(1, $checked, false) . '/>';

	$html .= '<label for="wtb_chatroom_redirect">Yes</label>
  <p style="margin-top: 20px;">Enable this option to have your Website Toolbox Chat Room load within an iframe on your website.</p>
  <p>Disable this option to have your Website Toolbox Chat Room load in a full-sized window.</p>';

	echo $html;
}

/* Purpose: Create a Chat Room options (Settings) page into WordPress admin panel.
Parameter: None
Return: None */
function wtbchatroom_options_page() {
	if($_POST) {
		global $wpdb;
		if($_POST['wtb_chatroom_username'] && $_POST['wtb_chatroom_api']) {

			//URL to check authentication token.
			$URL = "https://www.websitetoolbox.com/tool/members/chat/settings";

			// WordPress site login, logout and registration URL.
			$chatRoom_login_url = '';
			$chatRoom_register_url = '';
			// If registration option is enable from WordPress site then sent login, logout, registration URL into HTTP request.
			if(get_option('users_can_register')) {
				$chatRoom_login_url = wp_login_url();
				$chatRoom_register_url = wp_registration_url();
			}

			// Get embed page URL
			if($_POST['wtb_chatroom_redirect']) {
				$chatRoom_pageid = get_option('wtbChatRoomPageid');
				$chatroom_embed_page = get_page($chatRoom_pageid);
				$chatroom_embed_page->post_title = "Chat Room";
				$chatroom_embed_page_url = $chatroom_embed_page->guid;
			}

			// create an array to check api key on the forum and save SSO URL"s on the related forum
			$fields = array('action' => 'updateChatRoomSSOURLs');
			if(isset($_POST['wtb_chatroom_username']) && $_POST['wtb_chatroom_username'] != "") {
				$fields['chatRoomUsername'] = $_POST['wtb_chatroom_username'];
			}
			if(isset($_POST['wtb_chatroom_api']) && $_POST['wtb_chatroom_api'] != "") {
				$fields['chatRoomApikey'] = $_POST['wtb_chatroom_api'];
			}
			if($chatRoom_login_url != "") {
				$fields['loginPageUrl'] = $chatRoom_login_url;
			}
			if($chatRoom_register_url != "") {
				$fields['registrationPageUrl'] = $chatRoom_register_url;
			}
			if($chatroom_embed_page_url != "") {
				$fields['chatroom_embed_page_url'] = $chatroom_embed_page_url;
			}
			$chatRoom_address = '';
			// Send http or https request to get authentication token.
			$response_array = wp_remote_post($URL, array('method' => 'POST', 'body' => $fields));
			//Check if http request could not return any error then filter JSON from response
			if(!is_wp_error( $response_array )) {
				$response = trim(wp_remote_retrieve_body($response_array));
				$response = json_decode($response);
				// Get authentication token from JSON response.
				if($response && $response->{'error'} != "") {
					#create a session varibe to show warning if return an error message
					if($response->{'error'} == 'Error: Chat Room is disabled.') {
						$_SESSION['wtb_chatroom_account_settings'] = 1;
					} else {
						$_SESSION['wtb_chatroom_account_settings'] = 2;
					}
					echo "<div id='setting-error-settings_updated' class='updated error'><p>".$response->{'error'}."</p></div>";
					wtbChatRoomSettingsPage();
					return false;
				} else if($response && $response->{'chatroomaddress'} != "") {
					$chatRoom_address = $response->{'chatroomaddress'};
				}
			}

			# remove the backslash at the end for consistency
			$chatRoom_address = preg_replace('#/$#', '', $chatRoom_address);
			if(get_option("wtb_chatroom_username")) {
				#update Website Toolbox Chat Room user name in option table if exist
				update_option('wtb_chatroom_username', $_POST['wtb_chatroom_username']);
			} else {
				#insert Website Toolbox Chat Room user name in option table
				add_option('wtb_chatroom_username', $_POST['wtb_chatroom_username']);
			}
			if(get_option("wtb_chatroom_api")) {
				#update Website Toolbox Chat Room API in option table if exist
				update_option('wtb_chatroom_api', $_POST['wtb_chatroom_api']);
			} else {
				#insert Website Toolbox Chat Room API name in option table
				add_option('wtb_chatroom_api', $_POST['wtb_chatroom_api']);
			}
			if(get_option("wtb_chatroom_url")=="") {
				#insert Website Toolbox Chat Room URL name in option table
				add_option('wtb_chatroom_url', $chatRoom_address);
				update_option('wtb_chatroom_url', $chatRoom_address);
			} else {
				#update Website Toolbox Chat Room URL in option table if exist
				add_option('wtb_chatroom_url', $chatRoom_address);
				update_option('wtb_chatroom_url', $chatRoom_address);
			}
			if(get_option("wtb_chatroom_redirect")=="") {
				#insert Website Toolbox Chat Room redirect type (New window or in iframe) in option table
				add_option('wtb_chatroom_redirect', $_POST['wtb_chatroom_redirect']);
			}
			update_option('wtb_chatroom_redirect', $_POST['wtb_chatroom_redirect']);

			$wtb_chatroom_url		 = get_option("wtb_chatroom_url");
			#Get Website Toolbox Chat Room page id
			$post_ID = $wpdb->get_results( "SELECT ID
				FROM " ."$wpdb->posts
				WHERE post_title='Chat Room'" );
			foreach ($post_ID as $result) {
				$post_ID = $result->ID;
			}

			#check on post meta
			$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
			$page = get_page($wtbChatRoomPageid);

			if(get_post_meta( $wtbChatRoomPageid, '_links_chat_to', true )){
				update_post_meta( $post_ID, '_links_chat_to', $wtb_chatroom_url );
			} else {
				add_post_meta( $post_ID, '_links_chat_to', $wtb_chatroom_url );
			}
			if(get_post_meta( $wtbChatRoomPageid, '_links_chat_to_target', true )) {
				update_post_meta( $post_ID, '_links_chat_to_target', 'websitetoolboxchatroom' );
			} else {
				add_post_meta( $post_ID, '_links_chat_to_target', 'websitetoolboxchatroom' );
			}
			if(get_post_meta( $wtbChatRoomPageid, '_links_chat_to_type', true )) {
				update_post_meta( $post_ID, '_links_chat_to_type', 'custom_post_type' );
			} else {
				add_post_meta( $post_ID, '_links_chat_to_type', 'custom_post_type' );
				add_post_meta( $post_ID, '_wtbchatroom_redirect_active', '1' );
			}
			#end of check post meta

			if(preg_match('#^https?://#', get_option("wtb_chatroom_url"))) {
				$wtb_chat_url = get_option("wtb_chatroom_url");
			} else {
				$wtb_chat_url = "http://".get_option("wtb_chatroom_url");
			}
			if(get_option("wtb_chatroom_redirect") == 1) {
				// remove (http:|https:) from the URL
				$wtb_chat_embed_url = preg_replace('#^https?:#', '', $wtb_chat_url);
				#open Chat Room in iframe
				$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
				$page = get_page($wtbChatRoomPageid);
				$page->post_title = "Chat Room";
				wp_update_post($page);
				$page->post_content = '<div id="wtChatRoomEmbedCode"><script type="text/javascript" id="embedded_chatroom" src="'.$wtb_chat_embed_url.'/js/embed.js"></script><noscript><a href="'.$wtb_chat_url.'">Chat Room</a></noscript></div>';
				wp_update_post($page);
				update_post_meta( $post_ID, '_wtbchatroom_redirect_active', '' );
			} else {
				#open Chat Room in new window
				$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
				if($wtbChatRoomPageid) {
					$page = get_page($wtbChatRoomPageid);
					$page->post_content = "";
					wp_update_post($page);
					update_post_meta( $post_ID, '_wtbchatroom_redirect_active', '1' );
				}
			}
			if($post_ID) {
				$_SESSION['wtb_chatroom_account_settings'] = 0;
				echo "<div id='setting-error-settings_updated' class='updated notice'><p>Your settings have been saved.</p></div>";
			}


		} else {
			if(!$_POST['wtb_chatroom_username']) {
				$err_message = "Enter your chat room username";
			} elseif(!$_POST['wtb_chatroom_api']) {
				$err_message = "Enter your chat room API key.";
			}
			if($err_message) {
				echo "<div id='setting-error-settings_updated' class='updated error'><p>".$err_message."</p></div>";
			}
		}
	}
	wtbChatRoomSettingsPage();
}

/* Purpose: This function is used to show forum settings page.
Param: none
Return: Nothing */
function wtbChatRoomSettingsPage() {
	?>
	<!-- create a form in the wordpress admin panel -->
	<div class="wrap">
        <form action="options-general.php?page=wtbchatroomoptions" method="POST">
            <?php settings_fields( 'wtb_chatroom_settings_section' ); ?>
            <?php do_settings_sections( 'wtbchatroomoptions' ); ?>
            <?php submit_button('Update'); ?>
        </form>
    </div>
    <?php
}

/* Purpose: This function is used to activate Chat Room plugin.
Parameter: None
Return: None */
function wtbchatroom_activate() {
	/* Created a page for Website Toolbox Chat Room when Plugin installed and activated and publish it as a page. */
	$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
	if($wtbChatRoomPageid) {
		$page = get_page($wtbChatRoomPageid);
		if($page) {
			$page->post_status = 'publish';
			wp_update_post($page);
		} else {
			// someone might have deleted the page, recreate it
			create_wtbChatRoom_page();
		}
	} else {
		create_wtbChatRoom_page();
	}
}

/* Purpose: This function is used to deactivate Chat Room plugin.
Parameter: None
Return: None */
function wtbchatroom_deactivate() {
	// hide the Website Toolbox Chat Room page if it exists
	$wtbChatRoomPageid = get_option('wtbChatRoomPageid');
	if($wtbChatRoomPageid) {
		$page = get_page($wtbChatRoomPageid);
		if($page) {
			$page->post_status = 'draft';
			wp_update_post($page);
		}
	}
}


/* Purpose: Show to WordPress admin after activate the Chat Room SSO plugin while SSO will not be configured.
Parameter: None
Return: None */
function wtbChatRoom_warning() {
  if(!get_option("wtb_chatroom_username") || !get_option("wtb_chatroom_api") || !get_option("wtb_chatroom_url")) {
    echo "
    <div id='wtb-warning' class='error'>
    <p>You will need to complete the Website Toolbox Chat Room <a href='options-general.php?page=wtbchatroomoptions'>Settings</a> in order for the plugin to work.</p>
    </div>
    ";
  } else if (isset($_SESSION['wtb_chatroom_account_settings']) && $_SESSION['wtb_chatroom_account_settings'] == 1) {
    echo '<div id="wtb-warning" class="error"><p>Your Chat Room account is currently disabled. Please login to your <a href="https://www.websitetoolbox.com/tool/members/login/'.get_option("wtb_chatroom_username").'" target="_blank">Chat Room</a> account for more information. Thank you.</a></p></div>';
  } else if (isset($_SESSION['wtb_chatroom_account_settings']) && $_SESSION['wtb_chatroom_account_settings'] == 2) {
    echo '<div id="wtb-warning" class="error"><p>The API Key entered in the Chat Room plugin is invalid. Please <a href="options-general.php?page=wtbchatroomoptions">update it</a></p></div>';
  }
}

/* Purpose: Prepare URL.
Parameter: None
Return: None */
if (!function_exists('esc_attr')) {
	function esc_attr($attr){return attribute_escape( $attr );}
	function esc_url($url){return clean_url( $url );}
}

/* Purpose: get all link of the menu and append / with Website Toolbox Chat Room.
Parameter: None
Return: None */
if(get_option("wtb_chatroom_redirect") == '') {
	function filter_page_links_wtbchatroom ($link, $post) {
		if(isset($post->ID)) {
			$id = $post->ID;
		} else {
			$id = $post;
		}
		#get array
		$newCheck = get_url_array();
		if(!is_array($newCheck)) { $newCheck = array(); }
		#check array according to the key if Chat Room used external url
		if(array_key_exists($id, $newCheck)) {
			$matchedID = $newCheck[$id];
			$newURL = $matchedID['_links_chat_to'];
			if(strpos($newURL,get_option('home'))>=0 || strpos($newURL,'www.')>=0 || strpos($newURL,'http://')>=0 || strpos($newURL,'https://')>=0) {
				if($matchedID['_links_chat_to_target'] == 'websitetoolboxchatroom') {
					$newURL = trim($matchedID['_links_chat_to']);
					// Added / at the end of Chat Room url if open into parent window.
					if(!preg_match("/\/$/", $newURL)) {
						$link = $newURL."/";
					}
				} else {
					$link = esc_url( $newURL );
				}
			} else {
				if($matchedID['_links_chat_to_target'] == 'websitetoolboxchatroom') {
					// Added / at the end of Chat Room url if open into parent window.
					if(!preg_match("/\/$/", $newURL)) {
						$link = $newURL."/";
					}
				} else {
					$link = esc_url( get_option( 'home').'/'. $newURL );
				}
			}
		}
		return $link;
	}
	add_filter('page_link', 'filter_page_links_wtbchatroom', 20, 2);
}

/* Purpose: Get main array from the post meta and post table according to redirest url
Parameter: None
Return: None */
function get_url_array(){
	global $wpdb;
	$theArray = array();

	$theqsl = "SELECT *
		FROM $wpdb->postmeta pmeta, $wpdb->posts post
		WHERE pmeta.`post_id`=post.`ID`
		AND post.`post_status`!='trash'
		AND (pmeta.`meta_key` = '_wtbchatroom_redirect_active' || pmeta.`meta_key` = '_links_chat_to' || pmeta.`meta_key` = '_links_chat_to_target' || pmeta.`meta_key` = '_links_chat_to_type')
		ORDER BY pmeta.`post_id` ASC;";
	$thetemp = $wpdb->get_results($theqsl);
	if(count($thetemp)>0){
		foreach($thetemp as $key){
			$theArray[$key->post_id][$key->meta_key] = $key->meta_value;
		}
		foreach($thetemp as $key){
			// defaults
			if(!isset($theArray[$key->post_id]['_links_chat_to'])){$theArray[$key->post_id]['_links_chat_to']	= 0;}
			if(!isset($theArray[$key->post_id]['_links_chat_to_type'] )){$theArray[$key->post_id]['_links_chat_to_type']				= 302;}
			if(!isset($theArray[$key->post_id]['_links_chat_to_target'])){$theArray[$key->post_id]['_links_chat_to_target']	= 0;}
		}

	}
	return $theArray;
}

/* Purpose: Delete account From WordPress table if any error occurred
Parameter: userid
Return: None */
function wp_delete_chat_user( $id, $reassign = 'novalue' ) {
	global $wpdb;
	$id = (int) $id;
	// allow for transaction statement
	do_action('delete_user', $id);
	if ( 'novalue' === $reassign || null === $reassign ) {
		$post_ids = $wpdb->get_col( $wpdb->prepare("SELECT ID
			FROM $wpdb->posts
			WHERE post_author = %d", $id) );
		if ( $post_ids ) {
			foreach ( $post_ids as $post_id )
			wp_delete_post($post_id);
		}
		// Clean links
		$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id
			FROM $wpdb->links
			WHERE link_owner = %d", $id) );
		if ( $link_ids ) {
			foreach ( $link_ids as $link_id )
			wp_delete_link($link_id);
		}
	} else {
		$reassign = (int) $reassign;
		$wpdb->update( $wpdb->posts, array('post_author' => $reassign), array('post_author' => $id) );
		$wpdb->update( $wpdb->links, array('link_owner' => $reassign), array('link_owner' => $id) );
	}
	clean_user_cache($id);
	// FINALLY, delete user
	if ( !is_multisite() ) {
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta
			WHERE user_id = %d", $id) );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->users
			WHERE ID = %d", $id) );
	} else {
		$level_key = $wpdb->get_blog_prefix() . 'capabilities'; // wpmu site admins don't have user_levels
		$wpdb->query("DELETE FROM $wpdb->usermeta
			WHERE user_id = $id
			AND meta_key = '{$level_key}'");
	}
	// allow for commit transaction
	do_action('deleted_user', $id);
	return true;
}

/* Purpose: create an account on the related Website Toolbox Chat Room
Parameter: userid
Return: None */
function wtbChatRoom_register_user($userid) {
	// Create object of WP_User class to get information of registered user.
	$user_obj = new WP_User($userid);
	$chatroom_api		= get_option("wtb_chatroom_api");
	$chatroom_url		= get_option("wtb_chatroom_url");

	$login_id = $user_obj->ID;
	$username 	  = $user_obj->user_login;
	$email 	  = $user_obj->user_email;
      $first_name = $user_obj->first_name; # ie: John
      $last_name = $user_obj->last_name; # ie: Doe
      $fullname = trim($first_name." ".$last_name);
	// pass1 used for password filed (Registration form) in WordPress older version (below 4.3).
	// pass1-text used for password filed in WordPress version (4.3 and above 4.3).
	// Pass this password into SSO regisration request if not peresent then sent blank password.
	if (isset($_POST['pass1']) && $_POST['pass1'] != "") {
		$password = $_POST['pass1'];
	} elseif (isset($_POST['pass1-text']) && $_POST['pass1-text'] != "") {
		$password = $_POST['pass1-text'];
	} else {
		$password = '';
	}
	// URL to create a new account on Website Toolbox Chat room.
	$create_account_url = $chatroom_url."/sso/user/register";

	// Fields array.
	$fields = array('apikey' => $chatroom_api, 'username' => $username, 'email' => $email, 'password' => $password, 'name' => $fullname);

	// Sent https/https request on related Chat Room to create an account on the Chat Room.
	$response_array = wp_remote_post($create_account_url, array('method' => 'POST', 'body' => $fields));
	return true;
}

/* Purpose: Logged-in on Website Toolbox Chat Room
Parameter: userid
Return: None */
function wtbChatRoom_login_user($user_login) {
	$user_obj = new WP_User(0,$user_login);
	$username = $user_obj->user_login;
	$email = $user_obj->user_email;

	$chatroom_api		= get_option("wtb_chatroom_api");
	$chatroom_url		= get_option("wtb_chatroom_url");

	// create URL to get authentication token.
	$URL = $chatroom_url."/sso/token/generate";

	$fields = array('apikey' => $chatroom_api, 'username' => $username, 'email' => $email);
	// Get plain password from WordPress login form value and append it in HTTP request.
	if(isset($_POST['pwd']) && $_POST['pwd'] != "") {
		$fields['password'] = $_POST['pwd'];
	}

	// Send http or https request to get authentication token.
	$response_array = wp_remote_post($URL, array('method' => 'POST', 'body' => $fields));

	//Check if http/https request could not return any error then filter json from response
	if(!is_wp_error( $response_array )) {
		$response = trim(wp_remote_retrieve_body($response_array));
		// Decode json string
		$response = json_decode($response);

		// get response in the keys $response->{'success'}, $response->{'message'} and $response->{'access_token'}
		if($response->{'success'}) {
			if(htmlentities($response->{'access_token'})) {
				$resultdata = htmlentities($response->{'access_token'});
			} else {
				$resultdata = '';
			}
      		$persistent = 0;
			#set cookie for 10 days if user logged-in with "remember me" option, to remain logged-in after closing browser. Otherwise set cookie 0 to logged-out after clossing browser.
			if(!empty($_POST['rememberme'])) {
				wtbChatRoomSetCookies('wtbchat_login_remember', 1, $persistent);
        		$persistent = 1;
			}
			// Save authentication token into cookie for one day to use into SSO logout.
			wtbChatRoomSetCookies('wtbchat_logout_token', $resultdata, $persistent);
			#Save authentication token into session variable.
			save_chatauthtoken($resultdata);
			return true;
		}
	}
}

/* Purpose: This function is used to set authentication token into session variable if user logged-in.
Param: authentication token
Return: Nothing */
function save_chatauthtoken($authtoken) {
	$_SESSION['wtbchat_login_auth_token'] = $authtoken;
}

/* Purpose: This function is used to unset session variable if user logged-in/logged-out.
Param1: type (login/logout)
Return: Nothing */
function clean_chatauthtoken($loginLogoutFlag) {
	if($loginLogoutFlag=='login') {
		unset($_SESSION['wtbchat_login_auth_token']);
	} else if($loginLogoutFlag=='logout')	{
		setcookie("wtbchat_logout_token", '', 0);
	}
}

/* Purpose: If a user Logged-in/logged-out on WordPress site from front end/admin section write an image tag after page load to loggout from Chat Room.
Parameter: None
Return: None */
function ssoChatRoomLoginLogout() {
	// If user logged-out from the Forum then call wp_logout function to logged-out from WordPress site as well as forum.
	if(isset($_GET['action']) && $_GET['action']=='ssoLogout' && is_user_logged_in()) {
		wp_logout();
		exit;
	}

	if (isset($_SESSION['wtbchat_login_auth_token'])) {
		//remove (http:https:) from the Chat room URL
		$chatlogin_auth_url = preg_replace('#^https?:#', '', get_option('wtb_chatroom_url'));
		$chatlogin_auth_url = $chatlogin_auth_url."/sso/token/login?access_token=".$_SESSION['wtbchat_login_auth_token'];
		if(isset($_COOKIE['wtbchat_login_remember'])) {
			$chatlogin_auth_url = $chatlogin_auth_url."&rememberMe=".$_COOKIE['wtbchat_login_remember'];
		}
		/* Print image tag on the login landing success page to sent login request on the related chat Room */
		echo '<img src="'.$chatlogin_auth_url.'" border="0" width="0" height="0" alt="">';
		/* remove authentication token from session variable so that above image tag not write again and again */
		clean_chatauthtoken('login');
		return false;
	}
	if(!is_user_logged_in() && isset($_COOKIE['wtbchat_logout_token'])) {
		//remove (http:https:) from the Chat room URL
		$wtbchat_logout_token = preg_replace('#^https?:#', '', get_option('wtb_chatroom_url'));
		$chatlogout_auth_url = $wtbchat_logout_token."/sso/token/logout?access_token=".$_COOKIE['wtbchat_logout_token'];
		/* Print image tag in header section of WordPress site to sent logout request on the related Chat Room */
		echo '<img src="'.$chatlogout_auth_url.'" border="0" width="0" height="0" alt="">';
		clean_chatauthtoken('logout');
		return false;
	}

	// User logged-out from wordpress site and session varible exist then unset the session varible.
	if(!is_user_logged_in() && isset($_SESSION['wtb_chatroom_account_settings'])) {
		unset($_SESSION['wtb_chatroom_account_settings']);
	}
}

/* Purpose: If a user deleted from WordPress site then delete from Chat Room.
Parameter: None
Return: None */
function delete_chatroom_user($args) {
	global $wpdb;

	#get All the user id's deleted from wordpress site.
	$userids = implode(",", $_POST['users']);
	#Get user emails from users table on the basis of userids.
	$user_emails = $wpdb->get_results( "SELECT user_email
		FROM $wpdb->users
		WHERE ID IN ($userids)" );
	$emails = array();
	foreach ( $user_emails as $useremails ) {
		$emails[] = $useremails->user_email;
	}
	#create a comma(,) separated string to sent user delete request on the Chat Room.
	if ( $emails ) {
		$useremails = implode(",", $emails);
	}

	$chatroom_api		= get_option("wtb_chatroom_api");
	$chatroom_url		= get_option("wtb_chatroom_url");

	// create URL to to delete users from related Chat Room.
	$URL = $chatroom_url."/sso/user/delete";

	$fields = array('apikey' => $chatroom_api, 'emails' => $useremails);

	// Send http or https request to get authentication token.
	$response_array = wp_remote_post($URL, array('method' => 'POST', 'body' => $fields));
	if(!is_wp_error( $response_array )) {
		$response = trim(wp_remote_retrieve_body($response_array));
		// Decode json string
		$response = json_decode($response);

		// get response in the keys $response->{'success'}, $response->{'message'} and $response->{'access_token'}
		if($response->{'success'}) {
			return true;
		}
	}
}

/* Purpose: Update email address and password on the Chat Room using SSO.
Parameter: None
Return: None */
function wtbUpdateChatRoomInfo($userid, $old_user_data) {
	// Get old email address (Before update profile information).
	$old_email = $old_user_data->user_email;
	// Get user information.
	$user_obj = new WP_User($userid);
	$username 	  = $user_obj->user_login;
	$email 	  = $user_obj->user_email;
      $first_name = $user_obj->first_name; # ie: John
      $last_name = $user_obj->last_name; # ie: Doe
      $fullname = trim($first_name." ".$last_name);

	$chatroom_api		= get_option("wtb_chatroom_api");
	$chatroom_url		= get_option("wtb_chatroom_url");

	// pass1 used for password filed (Update Profile form) in WordPress older version (below 4.3).
	// pass1-text used for password filed in WordPress version (4.3 and above 4.3).
	// Pass this password into SSO update profile request.
	if (isset($_POST['pass1']) && $_POST['pass1'] != "") {
		$password = $_POST['pass1'];
	} elseif (isset($_POST['pass1-text']) && $_POST['pass1-text'] != "") {
		$password = $_POST['pass1-text'];
	} else {
		$password = '';
	}

	// create URL to get authentication token.
	$URL = $chatroom_url."/sso/user/edit";

      $avatarUrl = get_avatar_url($userid);
	$fields = array('apikey'=>$chatroom_api,'user'=>$old_email,'email'=>$email,'password'=>$password, 'avatarUrl' => $avatarUrl, 'name' => $fullname);

	// Sent https/https request on related Chat Room to update email address and password.
	$response_array = wp_remote_post($URL, array('method' => 'POST', 'body' => $fields));

	if(!is_wp_error( $response_array )) {
		$response = trim(wp_remote_retrieve_body($response_array));
		// Decode json string
		$response = json_decode($response);
		if($response->{'success'}) {
			return true;
		}
	}
}

/* Purpose: Function is used to append access_token at the end of Chat Room URL if user logged-in on wordpress site and then clicks on the Chat Room link.
Parameter: Manual link and post id.
Return: Manual links */
function changeChatRoolLink($items, $post){
    // Append access_token in the forum link if user logged-in on WordPress site and Chat Room open in window independently.
    if(is_user_logged_in()) {
        if($post == get_option('wtbChatRoomPageid') && get_option("wtb_chatroom_redirect") == '') {
            $access_token = $_COOKIE['wtbchat_logout_token'];
            $accessTokenArray = array();
            if(isset($_COOKIE['wtbchat_logout_token'])) {
                $accessTokenArray['access_token'] = $_COOKIE['wtbchat_logout_token'];
            }
            if(isset($_COOKIE['wtbchat_login_remember'])) {
                $accessTokenArray['rememberMe'] = 1;
            }
            $items = add_query_arg( $accessTokenArray, $items );
        }
    }
    return $items;
}
add_filter('page_link', 'changeChatRoolLink', 20, 2);

/* Purpose: Function is used to append access token token into the Chat Room URL.
Parameter: page content.
Return: replace page content */
function updateChatRoomPageContent($content) {
    // Append access token in the embed code if user logged-in on WordPress site and Chat Room open in iframe.
    if($GLOBALS['post']->ID == get_option('wtbChatRoomPageid') && get_option("wtb_chatroom_redirect") != '' && is_user_logged_in()) {
        if(preg_match('#^https?://#', get_option('wtb_chatroom_url'))) {
            $wtb_chat_url = get_option('wtb_chatroom_url');
        } else {
            $wtb_chat_url = "http://".get_option('wtb_chatroom_url');
        }
        $wtb_chat_embed_url = preg_replace('#^https?:#', '', $wtb_chat_url);
        $access_token = '';
        if(isset($_COOKIE['wtbchat_logout_token'])) {
            $access_token = "?access_token=".$_COOKIE['wtbchat_logout_token'];
        }

        if(isset($_COOKIE['wtbchat_login_remember'])) {
            $access_token .= "&rememberMe=1";
        }
        $wtbChatEmbedSrcCount = preg_match('/id="embedded_chatroom"\ssrc=(["\'])(.*?)\1/', $content, $match);
        if($wtbChatEmbedSrcCount == 1) {
            $wtbChatembedURL = $match[2];
            $wtbChatNewURL =  $match[2].$access_token;
        }
        $content = str_replace($wtbChatembedURL,$wtbChatNewURL,$content);
    }
    return $content;
}
add_filter( 'the_content', 'updateChatRoomPageContent', 20, 2 );

/* Purpose: Sent HTTP request on the related Chat Room to check Chat Room is diable and API.
Parameter: None
Return: None */
function checkChatRoomEnableOrInvalidAPI() {
	// Check logged-in user is admin
	$user_ID = get_current_user_id();
	$is_admin = is_super_admin($user_ID);
	$chatRoomUsername = get_option("wtb_chatroom_username");
	$chatRoomAPI = get_option("wtb_chatroom_api");
	$chatRoomUrl = get_option("wtb_chatroom_url");


	if($chatRoomUsername && $chatRoomAPI && $chatRoomUrl && $is_admin == 1) {
		// Sent HTTP request to check API key.
		$URL = "https://www.websitetoolbox.com/tool/members/chat/settings";

		// Create an arry of parameters
		$fields = array('action' => 'checkChatRoomAPIKey','chatRoomUsername' => $chatRoomUsername, 'chatRoomApikey' => $chatRoomAPI);

		// Send http or https request to check Chat Room API key as well as Chat Room is disable.
		$response_array = wp_remote_post($URL, array('method' => 'POST', 'body' => $fields));

		// Print warning API key is invalid OR chat room is disable.
		if(!is_wp_error( $response_array )) {
			$response = trim(wp_remote_retrieve_body($response_array));
			$response = json_decode($response);
			// Get error message from JSON response.
			if($response->{'error'}) {
				if($response->{'error'} == 'Error: Chat Room is disabled.') {
					$_SESSION['wtb_chatroom_account_settings'] = 1;
				} else if($response->{'error'} == 'Error: Chat Room API Key is invalid. Please enter a valid API key from your Chat Room account.') {
					$_SESSION['wtb_chatroom_account_settings'] = 2;
				}
			} else {
				$_SESSION['wtb_chatroom_account_settings'] = 0;
			}
		}
	}
}


function wtbChatRoomSetCookies($cname, $cvalue, $persistent) {
  if($persistent == 1) {
    $expiration = (int) time() + (86400 * 365);
  } else {
    $expiration = 0;
  }
  // Pass param in setcookie function as a array if php version is 7.3 or above, to overcome the waring return on wordpress website.
  if(PHP_VERSION_ID < 70300) {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
      setcookie($cname, $cvalue, $expiration, COOKIEPATH." ; SameSite=None; Secure;", COOKIE_DOMAIN);
    } else {
      setcookie($cname, $cvalue, $expiration, COOKIEPATH, COOKIE_DOMAIN);
    }
  } else {
    $wtbChatCookieOptions = [
      'expires' => $expiration,
      'path' => COOKIEPATH,
      'domain' => COOKIE_DOMAIN,
      'httponly' => true,
    ];
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
      $wtbChatCookieOptions['secure'] = true;
      $wtbChatCookieOptions['samesite'] = 'None';
    }
    setcookie($cname, $cvalue, $wtbChatCookieOptions);
  }
}

function addChatSettingsLink( $links ) {
    $settings_link = '<a href="options-general.php?page=wtbchatroomoptions">Settings</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}
add_filter( "plugin_action_links_websitetoolboxChatRoom", 'addChatSettingsLink' );

register_activation_hook( __FILE__, 'wtbchatroom_activate' );
register_deactivation_hook( __FILE__, 'wtbchatroom_deactivate' );
