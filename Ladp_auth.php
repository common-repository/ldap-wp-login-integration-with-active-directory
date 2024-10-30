<?php
/*
Plugin Name: LDAP WP Login Integration With Active Directory.
Plugin URI: https://www.ldapsso.com
Description: Login and authenticate WordPress users using AD credentials
Version: 3.0.2
Author: robert05 
Author URI: https://www.ldapsso.com
*/

require_once 'ldap_auth_layout.php';
require_once 'ldap_AD_config.php';
require_once 'ldap_feedback_form.php';
require_once 'services/customerUtility.php';
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

class lwliad_Ladp_auth
{

  protected static $instance = NULL;

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  function __construct()
  {

    add_action('admin_menu', array($this, 'addMenuPage'));
    add_action('init', array($this, 'save_ldap_config'));
    // add_action('admin_notices', 'messages');
    if (get_option('lwliad_AD_ldapLogin') == 1) {
      add_filter('authenticate', array($this, 'authenticateUser'), 7, 3);
    }
    add_action('admin_enqueue_scripts', 'myscript');

    register_uninstall_hook(__FILE__, 'deletePluginDB');
    // register_deactivation_hook( __FILE__, array( $this , 'feedback_form' ) );
    add_action('admin_footer', array($this, 'feedback_form'), 20);
    register_activation_hook(__FILE__, array($this, 'lwliad_activate_ldap_plug'));

    function myscript()
    {
      wp_enqueue_style("css_styles", plugins_url('/Assest/css/sw_ldap_plug.css', __FILE__));
    }
  }



  function feedback_form($data)
  {
    ldap_feedbackForm();
  }

  function addMenuPage()
  {

    add_menu_page('LDAP_auth_layout', 'LDAP', 'manage_options', 'LDAP authentication intergrating with AD', 'lwliad_ladp_auth_layout');
  }

  function lwliad_activate_ldap_plug()
  {
    $user = wp_get_current_user()->user_login;
    $userEmail = wp_get_current_user()->user_email;
    contact::contactUs($user, $userEmail, 'Plugin_activated', 'User has activated the plugin.');
    wp_redirect('plugins.php');
  }




  /* Function runs on user login */


  function authenticateUser($user, $username, $password)
  {
    if (empty($username) || empty($password)) {

      $error = new WP_Error();

      if (empty($username)) {
        $error->add('empty_username', __('<strong>ERROR</strong>: Email field is empty.'));
      }
      if (empty($password)) {
        $error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
      }

      return $error;
    } elseif (!empty($username)) {

      $result = lwliad_AD_Config::lwliad_authenticate($username, $password);

      if ( $result && !array_key_exists("Error", is_array($result) ? $result : []) ) {
        $attributesOfAD = lwliad_AD_Config::fetch_logged_userDetails($username);

        $user_id = Self::check_WP_user($attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_email'))][0]);

        if (!empty(get_option('lwliad_AD_attr_map_email'))) {

          $user_info = array();
          $user_info['first_name'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_firstName'))][0];
          $user_info['last_name'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_lastName'))][0];
          $user_info['user_email'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_email'))][0];
          $user_info['user_pass'] = $password;
          // $user_info['user_url'] = $attribs[0][strtolower($authLDAPWebAttr)][0];

          $user_info['display_name'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_email'))][0];
          $user_info['nickname'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_attr_map_email'))][0];

          $user_info['user_login'] = $attributesOfAD[0][strtolower(get_option('lwliad_AD_Config_filter'))][0];
        } else {

          if ($attributesOfAD[0]['userprincipalname'][0]) {

            $ad_email_set = $attributesOfAD[0]['userprincipalname'][0];
          } else {

            $ad_email_set = $attributesOfAD[0]['mail'][0];
          }


          $user_info = array();
          $user_info['first_name'] = $ad_email_set;
          $user_info['user_email'] = $ad_email_set;
          $user_info['user_pass'] = $password;
          $user_info['user_login'] = $username;
        }


        if ($user_id) {

          $user = wp_update_user($user_info);
        } else {

          $user = wp_insert_user($user_info);
        }

        if (get_option('lwliad_enabledefaultRole')) {

          lwliad_AD_Config::set_users_role($user, $username);
        }

        return $user;
      } else if (get_option('lwliad_AD_ldapLogin') == 1 && get_option('lwliad_disableWPLogin') > 0) {
        remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
        remove_filter('authenticate', 'wp_authenticate_email_password', 20, 3);
      }
    }
  }

  /** Check for user exist in WP */

  function check_WP_user($username)
  {

    global $wpdb;

    $user_id = $wpdb->get_var($wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_email= %s", $username));


    if ($user_id) {
      return $user_id;
    } else {
      return null;
    }
  }

  /**  */


  function save_ldap_config()
  {

    if (self::oc_is_site_admin() && isset($_POST['action'])) {
      if ($_POST['action'] == 'ldapConfig') {
        if (isset($_POST['ldapConfig_nonce']) && !empty($_POST['ldapConfig_nonce']) && wp_verify_nonce( sanitize_key( $_POST['ldapConfig_nonce']), 'ldapConfig_nonce')) {

          update_option('lwliad_AD_Config_ldapURl', isset($_POST['ldapURI']) ? sanitize_text_field($_POST['ldapURI']) : '');

          update_option('lwliad_ldap_encrpyt_method', isset($_POST['ldapEncrpytMethod']) ? sanitize_text_field($_POST['ldapEncrpytMethod']) : '');

          update_option('lwliad_AD_Config_ldapport', isset($_POST['ldapport']) ? sanitize_text_field($_POST['ldapport']) : '');

          update_option('lwliad_AD_Config_ldapDn', isset($_POST['ldapDN']) ? sanitize_text_field($_POST['ldapDN']) : '');


          update_option('lwliad_AD_Config_ldappassword', isset($_POST['ldappassword']) ? sanitize_text_field($_POST['ldappassword']) : '');

          if (isset($_POST['ldapFilter']) && !empty($_POST['ldapFilter'])) {
            $filter = "(&(objectCategory=*)(" . sanitize_text_field($_POST['ldapFilter']) . "=?))";

            update_option('lwliad_AD_Config_ldapFilter', $filter);
            update_option('lwliad_AD_Config_filter', sanitize_text_field($_POST['ldapFilter']));
          }

          $result = lwliad_AD_Config::lwliad_AD_setup();
        
          if ($result) {
            lwliad_AD_Config::fetchGroups();

            if (isset($result["Error"])) {
              self::error($result["Error"]);
            } else {
              update_option('lwliad_AD_ldapLogin', 1);
              self::success('AD bind Successfully and Active Directory Login is enabled for your wordpress');
            }
          }
        }
      } elseif ($_POST['action'] == 'testLdapConfig') {
        if (isset($_POST['testldapConfig_nonce']) && !empty($_POST['testldapConfig_nonce']) && wp_verify_nonce( sanitize_key( $_POST['testldapConfig_nonce']), 'testldapConfig_nonce')) {


          if (empty($_POST['username']) || empty($_POST['password'])) {

            if (empty($_POST['username'])) {
              self::error('Please enter username.');
            }
            if (empty($_POST['password'])) {
              self::error('Please enter password.');
            }
          } else {


            $result = lwliad_AD_Config::testConfig(sanitize_text_field($_POST['username']), $_POST['password']);

            if (isset($result["Error"])) {
              self::error($result["Error"]);
            } else {
              self::success($result["Success"]);
            }
          }
        }
      } elseif ($_POST['action'] == 'ldapsettings') {
        if (isset($_POST['ldapsettings_nonce']) && !empty($_POST['ldapsettings_nonce']) && wp_verify_nonce( sanitize_key( $_POST['ldapsettings_nonce']), 'ldapsettings_nonce')) {


          update_option('lwliad_AD_ldapLogin', isset($_POST['ldapLogin']) ? 1 : 0);

          update_option('lwliad_disableWPLogin', isset($_POST['disableWPLogin']) ? 1 : 0);
        }
      } elseif ($_POST['action'] == 'testattributeMappingSettings') {
        if (isset($_POST['testattributeMappingSettings_nonce']) && !empty($_POST['testattributeMappingSettings_nonce']) && wp_verify_nonce( sanitize_key( $_POST['testattributeMappingSettings_nonce']), 'testattributeMappingSettings_nonce')) {
          if (isset($_POST['ldapUsername']) && !!$_POST['ldapUsername']) {

            $result = lwliad_AD_Config::fetch_attributes_for_username(sanitize_text_field($_POST['ldapUsername']));
          } else {
            self::error('Please enter username.');
          }
        }
      } elseif ($_POST['action'] == 'attributeMappingSettings') {
        if (isset($_POST['attributeMappingSettings_nonce']) && !empty($_POST['attributeMappingSettings_nonce']) && wp_verify_nonce( sanitize_key( $_POST['attributeMappingSettings_nonce']), 'attributeMappingSettings_nonce')) {
          if ( isset($_POST['ldapemail']) && $_POST['ldapemail'] != "none" ){
              update_option('lwliad_AD_attr_map_email', isset($_POST['ldapemail']) ? sanitize_text_field($_POST['ldapemail']) : '');
          }
          else{
            self::error('Mapping for email attribute required');
          }

          update_option('lwliad_AD_attr_map_firstName', !empty($_POST['ldapFirstName']) ? sanitize_text_field($_POST['ldapFirstName']) : '');

          update_option('lwliad_AD_attr_map_lastName', !empty($_POST['ldapLastName']) ? sanitize_text_field($_POST['ldapLastName']) : '');
        }
      } elseif ($_POST['action'] == 'groupMappingSettings') {
        if (isset($_POST['groupMappingSettings_nonce']) && !empty($_POST['groupMappingSettings_nonce']) && wp_verify_nonce( sanitize_key( $_POST['groupMappingSettings_nonce']), 'groupMappingSettings_nonce')) {


          update_option('lwliad_deaultRole', !empty($_POST['deaultRole']) ? sanitize_text_field($_POST['deaultRole']) : '');


          update_option('lwliad_enabledefaultRole', isset($_POST['enabledefaultRole']) ? 1 : 0);


          update_option('lwliad_AD_roleAgainstAdmin', !empty($_POST['roleAgainstAdmin']) ? sanitize_text_field($_POST['roleAgainstAdmin']) : '');


          update_option('lwliad_AD_roleAgainstEditor', !empty($_POST['roleAgainstEditor']) ? sanitize_text_field($_POST['roleAgainstEditor']) : '');



          update_option('lwliad_AD_roleAgainstSubscriber', !empty($_POST['roleAgainstSubscriber']) ? sanitize_text_field($_POST['roleAgainstSubscriber']) : '');

          update_option('lwliad_AD_roleAgainstContributer', !empty($_POST['roleAgainstContributer']) ? sanitize_text_field($_POST['roleAgainstContributer']) : '');
        }
      } else if ($_POST['action'] == 'testRoleMapping') {
        if (isset($_POST['testRoleMapping_nonce']) && !empty($_POST['testRoleMapping_nonce']) && wp_verify_nonce( sanitize_key( $_POST['testRoleMapping_nonce']), 'testRoleMapping_nonce')) {
          $username = isset($_POST['adUsername']) ? sanitize_text_field($_POST['adUsername']) : '';

          if (!!$username) {
            $result = lwliad_AD_Config::testRoleMapping($username);
          } else {
            self::error('Please enter username.');
          }
        }
      } elseif ($_POST['action'] == 'contactUsForm') {

        if (isset($_POST['contactUs_nonce']) && !empty($_POST['contactUs_nonce']) && wp_verify_nonce( sanitize_key( $_POST['contactUs_nonce']), 'contactUs_nonce')) {

          $customer_name = "";
          $customer_email = "";
          $email_title = "";
          $customer_message = "";


          if (isset($_POST['customer_name'])) {
            $customer_name = str_replace(array("\r", "\n", "%0a", "%0d"), '', sanitize_text_field($_POST['customer_name']));
          }
          if (isset($_POST['customer_email'])) {
            $customer_email = str_replace(array("\r", "\n", "%0a", "%0d"), '', sanitize_text_field($_POST['customer_email']));
            $customer_email = filter_var($customer_email, FILTER_VALIDATE_EMAIL);
          }
          if (isset($_POST['email_title'])) {
            $email_title = filter_var(sanitize_text_field($_POST['email_title']), FILTER_SANITIZE_STRING);
          }
          if (isset($_POST['customer_message'])) {
            $customer_message = htmlspecialchars(sanitize_text_field($_POST['customer_message']));
          }
          $result = contact::contactUs($customer_name, $customer_email, $email_title, $customer_message);
          if (isset($result["body"]) && $result["body"] === "Email sent!!") {
            self::success("successfully email sent...");
          } else {
            self::error("Sending failed. Please try again or drop mail directly to the mail-id securiseweb@gmail.com");
          }
        }
      } elseif ($_POST['action'] == 'feedbackform') {

        if (isset($_POST['feedback_nonce']) && !empty($_POST['feedback_nonce']) && wp_verify_nonce( sanitize_key( $_POST['feedback_nonce']), 'feedback_nonce')) {

          $customer_name = "";
          $customer_email = "";
          $customer_message = "";



          if (isset($_POST['hasskiped'])  && !empty($_POST['hasskiped'])) {
            $user = wp_get_current_user()->user_login;
            $userEmail = wp_get_current_user()->user_email;
          }


          if (isset($_POST['customer_name']) && !empty($_POST['customer_name'])) {
            $customer_name = str_replace(array("\r", "\n", "%0a", "%0d"), '', sanitize_text_field($_POST['customer_name']));
          } else {
            if (isset($_POST['hasskiped'])) {
              $customer_name = $user;
            }
          }
          if (isset($_POST['customer_email'])  && !empty($_POST['customer_email'])) {
            $customer_email = str_replace(array("\r", "\n", "%0a", "%0d"), '', sanitize_text_field($_POST['customer_email']));
            $customer_email = filter_var($customer_email, FILTER_VALIDATE_EMAIL);
          } else {
            if (isset($_POST['hasskiped'])) {
              $customer_email = $userEmail;
            }
          }

          if (isset($_POST['customer_message'])  && !empty($_POST['customer_message'])) {
            $customer_message = json_encode(htmlspecialchars(sanitize_text_field($_POST['customer_message'])));
          } else {
            if (isset($_POST['hasskiped'])) {
              $customer_message = 'skipped';
            }
          }

          contact::feedback($customer_name, $customer_email, $customer_message);
          deactivate_plugins(__FILE__);
          wp_redirect('plugins.php');
        }
      }
    }
  }

  function deletePluginDB()
  {
    delete_option('lwliad_AD_Config_ldapURl');
    delete_option('lwliad_AD_Config_ldapport');
    delete_option('lwliad_AD_Config_ldapDn');
    delete_option('lwliad_AD_Config_ldappassword');
    delete_option('lwliad_AD_Config_ldapFilter');
    delete_option('lwliad_AD_Config_filter');
    delete_option('lwliad_AD_ldapLogin');
    delete_option('lwliad_disableWPLogin');
    delete_option('lwliad_AD_attr_map_email');
    delete_option('lwliad_AD_attr_map_firstName');
    delete_option('lwliad_AD_attr_map_lastName');
    delete_option('lwliad_deaultRole');
    delete_option('lwliad_enabledefaultRole');
    delete_option('lwliad_AD_roleAgainstAdmin');
    delete_option('lwliad_AD_roleAgainstEditor');
    delete_option('lwliad_AD_roleAgainstSubscriber');
    delete_option('lwliad_AD_roleAgainstContributer');
  }


  /* Notifications on success and error messages */

  public static function success($message)
  {
    $class = 'notice notice-success is-dismissible';
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }
  public static function error($message)
  {
    $class = 'notice notice-error is-dismissible';
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }
  function oc_is_site_admin(){
    return in_array('administrator', wp_get_current_user()->roles);
  }

}
$Ldap_auth = lwliad_Ladp_auth::getInstance();
