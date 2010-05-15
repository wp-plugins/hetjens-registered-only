<?php
/*
Plugin Name: Hetjens Registered Only
Plugin URI: http://hetjens.com/wordpress/hetjens_registered_only/
Version: 0.4
Description: Forwards all not loggedin visitors to the login page and offers private Feeds.
Author: Philip Hetjens
Author URI: http://hetjens.com
Text Domain: Hetjens_Registered_Only
License: GPL
*/

/*
  Copyright 2010 Philip Hetjens (email : Philip at Hetjens dot eu);

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/ 

class Hetjens_Registered_Only {
  function register() {
    add_action('wp', array(&$this,'wp'));
    add_filter('feed_link',array(&$this,'feed_link'));
    add_action('admin_init',array(&$this,'register_admin'));
  }

  function register_admin() {
    load_plugin_textdomain('hetjens_registered_only', false, '/'.dirname(plugin_basename(__FILE__)));

    add_settings_section('hetjens_registered_only', __('Security settings','hetjens_registered_only'), array(&$this,'admin_section_description'), 'reading');

    add_settings_field('hetjens_registered_only_active', __('Require login','hetjens_registered_only'), array(&$this,'admin_require_login'), 'reading', 'hetjens_registered_only', array());
    register_setting('reading','hetjens_registered_only_active');
    add_settings_field('hetjens_registered_only_feed', '<label for="hetjens_registered_only_feed">'.__('Private feed','hetjens_registered_only').'</label>', array(&$this,'admin_private_feed'), 'reading', 'hetjens_registered_only', array());
    register_setting('reading','hetjens_registered_only_feed');
  }

  /*
   * Functions used by the settings api
   */
  function admin_section_description() {}
  function admin_require_login() {
    echo '<fieldset>';
    echo '<legend class="screen-reader-text"><span>'.__('Require login','hetjens_registered_only').'</span></legend>';
    echo '<label id="hetjens_registered_only_disabled"><input type="radio" id="hetjens_registered_only_disabled" name="hetjens_registered_only_active" value="" '.checked('',get_option('hetjens_registered_only_active'),false).' > '.__('Public aceess','hetjens_registered_only').'</label><br />';
    echo '<label id="hetjens_registered_only_active"><input type="radio" id="hetjens_registered_only_active" name="hetjens_registered_only_active" value="1" '.checked('1',get_option('hetjens_registered_only_active'),false).' > '.__('Restricted aceess','hetjens_registered_only').'</label><br />';
    echo '</fieldset>';
  }

  function admin_private_feed() {
    echo '<label for="hetjens_registered_only_feed"><input type="checkbox" id="hetjens_registered_only_feed" name="hetjens_registered_only_feed" value="1" '.checked('1',get_option('hetjens_registered_only_feed'),false).'> '.__('Enable private Feed','hetjens_registered_only').'</label>';
  }
  
  function feed_link($output,$feed='') {
    $current_user = wp_get_current_user();

    //If private feed is disabled the original url is returned
    if (get_option('hetjens_registered_only_feed') != '1')
      return $output;

    //Depending of the permalink structure it has to add an & or a ?
    if (get_option('permalink_structure') == '')
      $output .= '&amp;';
    else
      $output .= '?';
    return $output.'user='.$current_user->user_login.'&amp;key='.md5($current_user->user_pass);
  }
  
  function wp() {
    global $wpdb;
    $current_user = wp_get_current_user();

    if (get_option('hetjens_registered_only_active') == '1') {
      if (($current_user->ID == 0) && (substr($_SERVER['SCRIPT_NAME'], -12) != "wp-login.php") && (is_feed() == false))
        auth_redirect();

      if (is_feed()) {
        if (get_option('hetjens_registered_only_feed') == '1') {
          if (($_REQUEST['user'] != '') && ($_REQUEST['key'] != '')) {
            $row = $wpdb->get_row($wpdb->prepare('SELECT user_login, user_pass FROM '.$wpdb->users.' WHERE user_login = %s',$_REQUEST['user']));
            if (md5($row->user_pass) != $_REQUEST['key'])
              wp_die('You do not have sufficient permissions to read the feed.');
          } else
            wp_die('You do not have sufficient permissions to read the feed.');
        }
        else
          wp_die('The feed is deactivated.');
      }
    }
  }
}

/* Initialise outselves */
add_action('plugins_loaded', create_function('','$Hetjens_Registered_Only = new Hetjens_Registered_Only(); $Hetjens_Registered_Only->register();'));

?>