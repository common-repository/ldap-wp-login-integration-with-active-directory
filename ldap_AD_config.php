<?php
class lwliad_AD_Config
{
    public static function  lwliad_AD_setup()
    {


        $url = get_option('lwliad_AD_Config_ldapURl');
        $port = get_option('lwliad_AD_Config_ldapport');
        $encrpyt_method = get_option('lwliad_ldap_encrpyt_method');

        if (empty($url))
            return array('Error' => "To connect to AD, please fill in LDAP URI / LDAP Server.");

        if (empty($port))
            return array('Error' => "To connect to AD, please fill port.");


        $urltofeed = $encrpyt_method . '://' . $url . ':' . $port;

        $con = ldap_connect($urltofeed);

        if (empty(get_option('lwliad_AD_Config_ldapDn')))
            return array('Error' => "To connect to AD, please fill the LDAP DN / Username.");

        if (empty(get_option('lwliad_AD_Config_ldappassword')))
            return array('Error' => "To connect to AD, please fill LDAP DN Password.");


        if ($con) {
            $dn = get_option('lwliad_AD_Config_ldapDn');
            $password = get_option('lwliad_AD_Config_ldappassword');

            ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

            if (@ldap_bind($con, $dn, $password)) {
                return $con;
            } else {
                update_option('lwliad_AD_Config_ldappassword', '');
                return array('Error' => "Error trying to bind: " . ldap_error($con) . ". Plese check LDAP DN / Username and Password");
            }
        } else {

            return array('Error' => "That LDAP-URI was not parseable");
        }
    }
    /** fetching search base */
    public static function lwliad_searchBase()
    {
        $con = self::lwliad_AD_setup();

        if ($con) {
            $result = @ldap_read($con, '', '(objectclass=*)', array('namingContexts'));
            $data = @ldap_get_entries($con, $result);
            return $data[0]['namingcontexts'][0];
        }
    }

    public static function lwliad_authenticate($username, $password)
    {

        $con = self::lwliad_AD_setup();

        if ($con) {

            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            $searchBase = Self::lwliad_searchBase();
            $result = @ldap_search($con, $searchBase, $filter);
            $data = @ldap_get_entries($con, $result);

            if ($data['count'] > 0) {

                $user_dn = $data[0]['dn'];

                $bind = @ldap_bind($con, $user_dn, $password);



                if ($bind) {
                    return true;
                } else {

                    return array('Error' => ldap_error($con));
                }
            }
        }
    }

    /* Test LDAP Config  */

    public static function testConfig($username, $password)
    {

        $con = self::lwliad_AD_setup();

        if (isset($con['Error']))
            return array('Error' => $con['Error']);


        if ($con) {

            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            // $bind = ldap_bind( $con, $dn, $password);
            $searchBase = Self::lwliad_searchBase();
            $result = ldap_search($con, $searchBase, $filter);
            $data = ldap_get_entries($con, $result);

            if ($data['count'] > 0) {

                $user_dn = $data[0]['dn'];

                $bind = @ldap_bind($con, $user_dn, $password);

                if ($bind) {
                    return array('Success' => "User Exist.");
                } else {
                    return array('Error' => "Please Enter valid credentials.");
                }
            } else {

                return array('Error' => "Please Enter valid credentials.");
            }
        }
    }


    // function testAttributeMapping($username){
    //     $con = self::lwliad_AD_setup();
    //     if($con){
    //         $filter = get_option('lwliad_AD_Config_ldapFilter');
    //         $filter =str_replace('?',$username, $filter);
    //         $searchBase = Self::lwliad_searchBase();
    //         $result = ldap_search($con, $searchBase, $filter );
    //         $data = ldap_get_entries($con, $result);

    //         echo '<div style="margin-left:20rem; position:relative;">
    //         <div id="testAttributeMappingwin" style="position:fixed; right:3rem; background-color:white; top:32%;width:35%; border:1px solid #80808054; padding:9px;">
    //         <h3>Test Attribute Mapping</h3>
    //         <table style="margin-top:10px;">
    //         <tr>
    //         <th>WordPress Attributes</th>
    //         <th>Mapped AD Attributes</th>
    //         </tr>
    //         <tr>
    //         <td>Email</td>
    //         <td></td>
    //         </tr>
    //         <tr>
    //         <td>FirstName</td>
    //         <td></td>
    //         </tr>
    //         <tr>
    //         <td>LastName</td>
    //         <td></td>
    //         </tr>

    //         </table></div>
    //         </div>

    //         <style>
    //         #testAttributeMappingwin table, #testAttributeMappingwin td, #testAttributeMappingwin th {
    //          border: 1px solid black;
    //        }

    //        #testAttributeMappingwin td {
    //            padding:8px;
    //        }

    //        #testAttributeMappingwin table {
    //          width: 100%;
    //          border-collapse: collapse;

    //        }
    //         </style>


    //         ';


    //     }
    // }


    public static function fetchGroups()
    {
        $con = self::lwliad_AD_setup();
        if ($con) {

            $filter = "(&(objectCategory=group)(distinguishedname=*))";
            $searchBase = Self::lwliad_searchBase();
            $attr = array("CN");
            $dn = get_option('lwliad_AD_Config_ldapDn');
            $password = get_option('lwliad_AD_Config_ldappassword');

            if (@ldap_bind($con, $dn, $password)) {
                $result = ldap_search($con, $searchBase, $filter, $attr);
                $data = ldap_get_entries($con, $result);

                if ($data['count'] > 0) {

                    $groups = array();


                    foreach ($data as $group) {

                        if (is_array($group) && isset($group['dn']))
                            array_push($groups, $group['dn']);
                    }


                    update_option('ADgroups', $groups);
                }
            }
        }
    }


    public static function testRoleMapping($username)
    {
        $con = self::lwliad_AD_setup();
        if ($con) {
            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            $searchBase = Self::lwliad_searchBase();
            $result = ldap_search($con, $searchBase, $filter);
            $data = ldap_get_entries($con, $result);
            $role = array();

            echo " <script type='text/javascript'>";


            echo " function closeButton(){
                 var ele = document.getElementById('displayTestRoleMapping');
                 ele.style.display= 'none';
             }
  
             </script>";

            echo "<div id='displayTestRoleMapping' style='position:relative;z-index:9; display:flex; flex-direction:row-reverse;'>
            <div id='testRoleMappingView' style='position:fixed; right:2.76%;  background-color:#28a0ab; color:#fff; top:31%;width:30.5%; border:5px solid chartreuse; padding:0px 16px 16px;'>
            <h3 style='color:#fff;'>Role mapping Results for " . $username . "  </h3>
            <p><u><i>Given below is/are the list of groups assigned to user <b>" . $username . "</b> in Active Directory</i></u></p>
            <ul style='margin-left:1.5rem;list-style:disc;'>
            ";

            if (get_option('lwliad_deaultRole')) {
                array_push($role, get_option('lwliad_deaultRole'));
            }


            if (isset($data[0]['memberof'])) {
                for ($i = 0; $i < count($data[0]['memberof']) - 1; $i++) {
                    echo "
                <li>" . esc_attr($data[0]['memberof'][$i]) . "</li>
                ";


                    if (strcmp(get_option('lwliad_AD_roleAgainstAdmin'), $data[0]['memberof'][$i]) === 0) {

                        array_push($role, "administrator");
                    }
                    if (strcmp(get_option('lwliad_AD_roleAgainstSubscriber'), $data[0]['memberof'][$i]) === 0) {

                        array_push($role, "subscriber");
                    }
                    if (strcmp(get_option('lwliad_AD_roleAgainstEditor'), $data[0]['memberof'][$i]) === 0) {
                        array_push($role, "editor");
                    }
                    if (strcmp(get_option('lwliad_AD_roleAgainstContributer'), $data[0]['memberof'][$i]) === 0) {
                        array_push($role, "contributor");
                    }
                }
            } else {
                echo "
                <li style='list-style-type:none;'> <h4 style='color:red;'> No groups are assigned to the " . $username . " in AD.</h4></li>
                ";
            }

            $i = 0;
            $count = count($role);
            echo "
            </ul>
            <p> The role assigned to user <b>" . $username . "</b> is/are  <b>";
            foreach ($role as $rol) {
                if ($i == $count - 1)
                    echo esc_attr($rol . '.');
                else
                    echo esc_attr($rol . ', ');
                $i++;
            }

            echo "</b></p>
            <div style='text-align:center;'>";
            echo "<input type='button' id='CloseTestattr' onclick='closeButton()'  value='Close'  class='btn closeButton' />";
            echo "</div>
            </div>
           </div>";


            echo " 
           <style>
           #testRoleMappingView table, #testRoleMappingView td, #testRoleMappingView th {
            border: 1px solid black;
          }

          #testRoleMappingView td {
              padding:8px;
          }
          
          #testRoleMappingView table {
            width: 100%;
            border-collapse: collapse;
            
          }

          .closeButton 
          {
              background-color: #ca4a1f;
              border-radius: 8px;
              width: 30%;
              color: white;
              border: none;
              height: 27px;
              cursor: pointer;
          }
           </style>
           
           ";
        }
    }


    public static function fetch_logged_userDetails($username)
    {
        $con = self::lwliad_AD_setup();
        if ($con) {
            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            $searchBase = Self::lwliad_searchBase();
            $result = ldap_search($con, $searchBase, $filter);
            $data = ldap_get_entries($con, $result);
            return $data;
        }
    }
    /* fetch attributes of users from AD */

    public static function fetch_attributes_for_username($username)
    {
        $con = self::lwliad_AD_setup();
        if ($con) {
            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            $searchBase = Self::lwliad_searchBase();
            $result = ldap_search($con, $searchBase, $filter);
            $data = ldap_get_entries($con, $result);

?>


            <div id='displayFetchedattributes' style='margin-left:20rem; position:relative; z-index:9; display:flex; flex-direction:row-reverse;'>
                <div id='fetchedAttributes' style='position:fixed; right:2.76rem; background-color:#28a0ab; top:29.7%;width:30.5%; border:5px solid chartreuse; padding:0px 16px 16px;'>
                    <h3 style="color: #fff;">Fetched List of Attributes</h3>
                    <table style='margin-top:10px; border:none !important; color:#fff'>
                        <tr>
                            <th style='font-size:14px;'>Attribute Name</th>
                            <th style='font-size:14px;'>Attribute Value</th>
                        </tr>
                        <?php if (isset($data[0]['samaccountname'][0]) && $data[0]['samaccountname'][0]) { ?>
                            <tr>
                                <td>samAccountName</td>
                                <td><?php echo esc_attr($data[0]['samaccountname'][0]); ?></td>
                            </tr>
                        <?php
                        }

                        if (isset($data[0]['userprincipalname'][0]) && $data[0]['userprincipalname'][0]) { ?>
                            <tr>
                                <td>userPrincipalName</td>
                                <td><?php echo esc_attr($data[0]['userprincipalname'][0]); ?></td>
                            </tr>

                        <?php
                        }

                        if (isset($data[0]['mail'][0]) && $data[0]['mail'][0]) { ?>
                            <tr>
                                <td>mail</td>
                                <td><?php echo esc_attr($data[0]['mail'][0]); ?></td>
                            </tr>

                        <?php
                        }
                        if (isset($data[0]['uid'][0]) && $data[0]['uid'][0]) { ?>
                            <tr>
                                <td>uid</td>
                                <td><?php echo esc_attr($data[0]['uid'][0]); ?></td>
                            </tr>

                        <?php }
                        if (isset($data[0]['cn'][0]) && $data[0]['cn'][0]) { ?>

                            <tr>
                                <td>cn</td>
                                <td><?php echo esc_attr($data[0]['cn'][0]); ?></td>
                            </tr>
                        <?php } ?>
                        <tr style="border:none;">
                            <td colspan='2' style='text-align:center; border:none;'><input type='button' class='btn closeButton' onclick='closeWin();' value='Close' /></td>
                        </tr>
                    </table>
                </div>
            </div>

            <script>
                function closeWin() {
                    var ele = document.getElementById('displayFetchedattributes');
                    ele.style.display = 'none';
                }
            </script>

            <style>
                #fetchedAttributes table,
                #fetchedAttributes td,
                #fetchedAttributes th {
                    border: 1px solid #fff;
                }

                #fetchedAttributes td {
                    padding: 8px;
                }

                #fetchedAttributes table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .closeButton {
                    background-color: #ca4a1f;
                    border-radius: 8px;
                    width: 30%;
                    color: white;
                    border: none;
                    height: 27px;
                    cursor: pointer;
                }
            </style>

<?php
            return true;
        }
    }


    /** Fetch user details and mapped roles */

    public static function fetch_roles_for_username($username)
    {


        $con = self::lwliad_AD_setup();
        if ($con) {
            $filter = get_option('lwliad_AD_Config_ldapFilter');
            $filter = str_replace('?', $username, $filter);
            $attrib = array('memberOf');
            $searchBase = Self::lwliad_searchBase();
            $result = ldap_search($con, $searchBase, $filter, $attrib);
            $data = ldap_get_entries($con, $result);
            $role = array();
            if (isset($data[0]['memberof'])) {
                if (in_array(get_option('lwliad_AD_roleAgainstAdmin'), $data[0]['memberof'])) {
                    $role[] = "administrator";
                }
                if (in_array(get_option('lwliad_AD_roleAgainstSubscriber'), $data[0]['memberof'])) {
                    $role[] = "subscriber";
                }
                if (in_array(get_option('lwliad_AD_roleAgainstEditor'), $data[0]['memberof'])) {
                    $role[] = "editor";
                }
                if (in_array(get_option('lwliad_AD_roleAgainstContributer'), $data[0]['memberof'])) {

                    $role[] = "contributor";
                }
            }

            return $role;
        }
    }



    /** Mapping AD roles to WP users */
    public static function set_users_role($user_id, $username)
    {
        $roles = self::fetch_roles_for_username($username);
        $newuser = new WP_User($user_id);
        $newuser->set_role('');
        if (sizeof($roles) > 0) {

            foreach ($roles as $role) {
                $newuser->add_role($role);
            }
        }
        // $newuser->set_role('');
        $newuser->add_role(get_option('lwliad_deaultRole'));
    }
}
