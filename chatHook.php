<?php
/* Define Hook to get user information */
/* admin_notices to print notice(message) on admin section */
add_action('admin_notices', 'wtbChatRoom_warning');
/* Call this hook to initialized chat room on WordPress site. */
add_action('wp_head', 'wtb_chatroom_init');
/* Call this hook to add Chat Room option into WordPress admin menu. */
add_action('admin_menu', 'wtbChatRoom_add_admin_menu');
add_action('admin_init', 'wtbchatroom_admin_init');
/* Call this hook to sent HTTP request on the Chat Room if create a account on WordPress site. */
add_action('user_register', 'wtbChatRoom_register_user');
/* Call this hook to sent HTTP request on the Chat Room if a user logged-in on WordPress site. */
add_action('wp_login','wtbChatRoom_login_user');
/* print IMG tags to the footer if needed */
add_action('wp_footer','ssoChatRoomLoginLogout');
add_action('admin_footer','ssoChatRoomLoginLogout');
/* print IMG tags to the admin login page if user redirected to login page after logged-out. */
add_action('login_footer', 'ssoChatRoomLoginLogout');
/* Call this hook if single or multiple user delete from the WordPress site. */
add_action('delete_user', 'delete_chatroom_user');
/* Call this hook if profile updated from WordPress Site. */
add_action( 'profile_update', 'wtbUpdateChatRoomInfo', 10, 2);
/* Call this hook to check Chat Room API key as well as Chat Room is enable/disable. */
add_action( 'wp_dashboard_setup', 'checkChatRoomEnableOrInvalidAPI' );
?>