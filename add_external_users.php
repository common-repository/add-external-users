<?php
/*
Plugin Name: Add External Users
Plugin URI: http://userwww.service.emory.edu/~ekenda2/addexternalusers/
Description: Allows blog admins to add external users to their blogs
Author: Elliot Kendall
Author URI: http://dx4.org/
Version: 0.1

Copyright (C) 2010 Emory University

Uses code and/or ideas from the following plugins:
- fd feedburner by John Watson
- HTTP Authentication by Daniel Westermann-Clark

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
'
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/ 

/*
 * If for some reason your root blog doesn't have ID 1, you can change
 * this. Heck, as long as there IS a blog #1, things will probably still
 * work.
 */
define('ADDEXTERNALUSERS_ROOTBLOGNUMBER', 1);

define('ADDEXTERNALUSERS_TEXTDOMAIN', 'addexternalusers');

/*
 * Generate a random password.
 */
function addexternalusers_get_password($length = 10) {
  return substr(md5(uniqid(microtime())), 0, $length);
}

if (function_exists('load_plugin_textdomain')) {
  load_plugin_textdomain(ADDEXTERNALUSERS_TEXTDOMAIN, false,
   dirname(plugin_basename(__FILE__)).'/languages' );
}

# Display the Add External User option to blog admins
add_action('admin_menu', 'addexternalusers_config_page');

function addexternalusers_config_page() {
  global $wpdb;

  $updated = False;

  $defaults = array(
    'menulabel' => 'Add External User',
    'userlabel' => 'Username',
    'emaildomain' => 'example.com',
  );
  $options = get_blog_option(ADDEXTERNALUSERS_ROOTBLOGNUMBER,
   'addexternalusers');
  foreach (array('menulabel', 'userlabel', 'emaildomain') as $item) {
    if (! isset($options[$item])) {
        $options[$item] = $defaults[$item];
	$updated = True;
    }
  }

  if ($updated)
    update_option('addexternalusers', $options);

  if (function_exists('add_submenu_page'))
    add_submenu_page('users.php',
     __($options['menulabel'], ADDEXTERNALUSERS_TEXTDOMAIN),
     __($options['menulabel'], ADDEXTERNALUSERS_TEXTDOMAIN),
     'create_users', __FILE__, 'addexternalusers_add');
    add_submenu_page('wpmu-admin.php',
     __('Add External Users', ADDEXTERNALUSERS_TEXTDOMAIN),
     __('Add External Users', ADDEXTERNALUSERS_TEXTDOMAIN),
     10, __FILE__, 'addexternalusers_options');
}
                            
function addexternalusers_options() {
  if (! is_site_admin())
    wp_die(__('You do not have permission to access this page.'));

  $options = get_blog_option(ADDEXTERNALUSERS_ROOTBLOGNUMBER,
   'addexternalusers');

  if (isset($_POST['submit'])) {
    check_admin_referer('addexternalusers-options');

    $updated = False;

    foreach (array('menulabel', 'userlabel', 'emaildomain') as $item) {
      if (isset($_POST["addexternalusers_$item"])) {
        $options[$item] = $_POST["addexternalusers_$item"];
        $updated = True;
      }
    }

    if ($updated)
      update_blog_option(ADDEXTERNALUSERS_ROOTBLOGNUMBER, 'addexternalusers',
       $options);
  }

?>

<div class="wrap">
<?php
if ($updated) {
	echo "<div id='message' class='updated fade'><p>";
	_e('Options saved.');
	echo "</p></div>";
}
?>
<h2>
<?php _e('Add External Users Options', ADDEXTERNALUSERS_TEXTDOMAIN); ?>
</h2>
<form action="" method="post" id="addexternalusers">
  <?php wp_nonce_field('addexternalusers-options'); ?>

  <h3>
    <label for="addexternalusers_menulabel">
      <?php _e('Label for menu item', ADDEXTERNALUSERS_TEXTDOMAIN); ?>
    </label>
  </h3>
  <p>
    <input id="addexternalusers_menulabel" name="addexternalusers_menulabel"
     type="text" maxlength="200" size="30"
     value="<?php echo $options['menulabel']; ?>" />
  </p>

  <h3>
    <label for="addexternalusers_userlabel">
      <?php _e('Description of username', ADDEXTERNALUSERS_TEXTDOMAIN); ?>
    </label>
  </h3>
  <p>
    <input id="addexternalusers_userlabel" name="addexternalusers_userlabel"
     type="text" maxlength="200" size="30"
     value="<?php echo $options['userlabel']; ?>" />
  </p>

  <h3>
    <label for="addexternalusers_emaildomain">
      <?php _e('Email address domain', ADDEXTERNALUSERS_TEXTDOMAIN); ?>
    </label>
  </h3>
  <p>
    <input id="addexternalusers_emaildomain" name="addexternalusers_emaildomain"
     type="text" maxlength="200" size="30"
     value="<?php echo $options['emaildomain']; ?>" />
  </p>

  <p class="submit" style="text-align: left">
    <input type="submit" name="submit"
     value="<?php _e('Update Options'); ?> &raquo;" />
  </p>
</form>
</div>
<?php
}

function addexternalusers_add() {
  if (! current_user_can('create_users'))
    wp_die(__('You can&#8217;t create users.'));

  $options = get_blog_option(ADDEXTERNALUSERS_ROOTBLOGNUMBER,
   'addexternalusers');

  if (isset($_POST['submit'])) {
    check_admin_referer('addexternalusers-add');

    $message = Null;

    if (isset($_POST['addexternalusers_uid']) &&
     isset($_POST['addexternalusers_role'])) {
      $username = strtolower($_POST['addexternalusers_uid']);
      $role = $_POST['addexternalusers_role'];

      if (preg_match('/^[a-z0-9_.-]+$/', $username)) {
        # Create the user if necessary
        $user = get_userdatabylogin($username);
        if (! $user or $user->user_login != $username) {
          $password = addexternalusers_get_password();

          require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'registration.php');
          wp_create_user($username, $password, $username . '@'
           . $options['emaildomain']);
        }

        # Add the user
        add_existing_user_to_blog(array('user_id' => $user->ID,
         'role' => $role));
        $message = "New user created.";
      } else {
        $message = "That username is not allowed";
      }
    }
  }


?>
<div class="wrap">
<?php
if ($message) {
	echo "<div id='message' class='updated fade'><p>";
	print __($message);
	echo "</p></div>";
}
?>
<h2><?php _e($options['menulabel'], ADDEXTERNALUSERS_TEXTDOMAIN); ?></h2>
<form action="" method="post" id="addexternalusers">
  <?php wp_nonce_field('addexternalusers-add'); ?>
  <h3>
    <label for="addexternalusers_uid">
      <?php _e($options['userlabel'], ADDEXTERNALUSERS_TEXTDOMAIN); ?>
    </label>
  </h3>
  <p>
    <input id="addexternalusers_uid" name="addexternalusers_uid" type="text"
     maxlength="200" />
  </p>

  <h3>
    <label for="addexternalusers_role">
      <?php _e('Role'); ?>
    </label>
  </h3>
  <p>
    <select name="addexternalusers_role" id="addexternalusers_role">
      <?php wp_dropdown_roles(get_option('default_role')); ?>
    </select>
  </p>

  <p class="submit" style="text-align: left">
    <input type="submit" name="submit"
     value="<?php esc_attr_e('Add User'); ?> &raquo;" />
  </p>
</form>
</div>
<?php
}

?>
