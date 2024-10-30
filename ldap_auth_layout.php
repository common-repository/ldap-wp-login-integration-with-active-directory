<?php
function lwliad_ladp_auth_layout()
{
    wp_enqueue_script("scripts", plugins_url('/Assest/js/script.js', __FILE__));

        isset($_GET['tab']) ? $active_tab = sanitize_text_field($_GET['tab']) : $active_tab = 'ldapConfig';
?>



    <div class="wrap">

        <h2>LDAP AD Intergration</h2>

        <h2 class="nav-tab-wrapper sw_ldap_tab_css" style="border:none;">
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'ldapConfig'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'ldapConfig' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'LDAP Configuration' );?></a>
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'attributeMapping'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'attributeMapping' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'Attribute Mapping' );?></a>
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'loginSetup'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'loginSetup' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'LDAP Login Set up' );?></a>
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'roleMapping'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'roleMapping' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'Role Mapping' );?></a>
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'password'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'password' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'Password' );?></a>
            <a href="<?php echo esc_url_raw(add_query_arg(array('tab' => 'help'), $_SERVER['REQUEST_URI'])); ?>" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>" style="border-bottom:1px solid #ccc;"><?php echo esc_html( 'Help/Feature Request' );?></a>
        </h2>

    </div>


    <!--better CSS purpose like container -->

    <div class="sw_ldap_css">
        <?php
        if ($active_tab === 'ldapConfig') ldapconfig();
        else  if ($active_tab === 'loginSetup') loginsetup();
        else if ($active_tab === 'password') password();
        else  if ($active_tab === 'attributeMapping') attributemapping();
        else  if ($active_tab === 'roleMapping') rolemapping();
        else if ($active_tab === 'help') ldap_help();



        ?></div>

    <div>
        <?php
        // if ($active_tab !== 'help')
        help_us(); ?>
    </div>


<?php }
function ldapconfig()
{
?>
    <div class="card_row">
        <div class="card" style="width: 60%;">

            <div>
                <h3> Configuration to integrate AD </h3>
                <div>
                    <p><b>NOTE: </b> The below information will help to establish connection with the LDAP server. The default port number set to 389 for LDAP, you can customize it as per your requirement.</p>
                </div>
                <form id="ldapConfig" method="post" action="">
                    <input type="hidden" name="action" value="ldapConfig" />
                    <?php wp_nonce_field('ldapConfig_nonce', 'ldapConfig_nonce') ?>
                    <table class="table_css">
                        <tr>
                            <td class="labelStyle"><strong>LDAP URI / LDAP Server</strong></td>
                            <td class="td_css"><input type="text" id="ldapURI" style="width:87%;" name="ldapURI" class="box_border_css" placeholder="Ex:  www.example.com or 15.25.221.25" value="<?php echo get_option('lwliad_AD_Config_ldapURl'); ?>" required />
                                <input type="text" id="ldapport" name="ldapport" style="width:11%;" class="box_border_css" value="<?php if (esc_attr(get_option('lwliad_AD_Config_ldapport'))) echo esc_attr(get_option('lwliad_AD_Config_ldapport'));
                                                                                                                                    else echo esc_attr('389'); ?>" required />
                                <p style="margin:0px;font-style:italic;font-size:10px;">
                                    Users were authenticated and authorised using domain controllers.<br />
                                    The Active Directory listens on this port. <br />Port 389 is used for unencrypted LDAP or STARTTLS. Port 636 is used by LDAPS.<br />

                                    eg: www.example.com or 15.25.221.25</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><strong>Select Encryption</strong></td>
                            <td class="td_css">
                                <select id="ldapEncrpytMethod" class="box_border_css" name="ldapEncrpytMethod" value="<?php echo esc_attr(get_option('lwliad_ldap_encrpyt_method')); ?>">
                                    <option value="ldap" <?php if (esc_attr(get_option('lwliad_ldap_encrpyt_method') === 'ldap')) echo " selected"; ?>>LDAP (None)</option>
                                    <option value="ldaps" <?php if (esc_attr(get_option('lwliad_ldap_encrpyt_method') === 'ldaps')) echo " selected"; ?>>LDAPS (SSL)</option>
                                    <option value="tls" <?php if (esc_attr(get_option('lwliad_ldap_encrpyt_method') === 'tls')) echo " selected"; ?>>STARTTLS </option>
                                </select>
                                <p style="margin:0px;font-style:italic;font-size:10px;">This option controls the LDAP connection's encryption type.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><strong>LDAP DN / Username</strong></td>
                            <td class="td_css"> <input type="text" id="ldapDN" name="ldapDN" class="box_border_css" placeholder="user1@example.com" value="<?php if (esc_attr(get_option('lwliad_AD_Config_ldapDn'))) echo esc_attr(get_option('lwliad_AD_Config_ldapDn')); ?>" required/>
                                <p style="margin:0px;font-style:italic;font-size:10px;">Username used to bind your WordPress site to a domain through Active Directory authentication <br />(e.g. user1@example.com or CN=user1,CN=Users,DC=example,DC=com</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><strong>Password</strong></td>
                            <td class="td_css"><input type="password" id="ldappassword" name="ldappassword" placeholder="password" class="box_border_css" value="<?php echo esc_attr(get_option('lwliad_AD_Config_ldappassword')); ?>" required/></td>

                        </tr>

                        <tr>
                            <td class="labelStyle"><strong>Filter</strong></td>
                            <td class="td_css"><select id="ldapFilter" name="ldapFilter" class="box_border_css">
                                    <option value="sAMAccountName" <?php if (esc_attr(get_option('lwliad_AD_Config_filter')) == 'sAMAccountName') echo esc_attr('selected'); ?>>sAMAccountName</option>
                                    <option value="userPrincipalName" <?php if (esc_attr(get_option('lwliad_AD_Config_filter')) == 'userPrincipalName') echo esc_attr('selected'); ?>>userPrincipalName</option>

                                </select>
                                <p>A filter specifies the conditions that must be satisfied for a record to be included in the recordset (or collection) that returns from a query.</p>
                            </td>
                        </tr>

                        <tr>

                            <td colspan="2" class="button_frame"><input type="submit" id="ADconfig" class="buttons_style" value="Save & Test Connection" /></td>
                        </tr>
                    </table>
                </form>
            </div>

            <hr />

            <div style="margin-top:3rem;">
                <h3> Test User Authentication </h3>
                <p>Enter the user credentials to verify the configuration is successfully done. Please enter the username according to the selected filter.</p>
                <form id="testLDAPConfig" method="post" action="">
                    <input type="hidden" name="action" value="testLdapConfig" />
                    <?php wp_nonce_field('testldapConfig_nonce', 'testldapConfig_nonce') ?>
                    <table class="table_css">
                        <tr>
                            <td class="labelStyle"><strong>Username</strong></td>
                            <td class="td_css"> <input type="text" id="username" name="username" require class="box_border_css" placeholder="Username" /></td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><strong>Password</strong></td>
                            <td class="td_css"> <input type="password" id="password" name="password" require class="box_border_css" placeholder="Password" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="button_frame"> <input type="submit" class="buttons_style" id="ADconfig_test" value="Test Configuration" /></td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>
        <?php help(); ?>
    </div>

<?php

}
function loginsetup()
{
?>
    <div class="card_row">
        <div class="card" style="width:60%">

            <h3> Configuration for WordPress Login</h3>
            <form id="loginsettings" method="POST" action="">
                <input type="hidden" name="action" value="ldapsettings" />
                <?php wp_nonce_field('ldapsettings_nonce', 'ldapsettings_nonce') ?>
                <div style="margin:1rem;">
                    <input type="checkbox" id="ldapLogin" name="ldapLogin" class="box_border_css" <?php if (esc_attr(get_option('lwliad_AD_ldapLogin')) == '1') echo esc_attr('checked'); ?>>By enabling this option user will login using the AD(Active Direcrory) credentials.

                </div>
                <div style="margin:1rem;">
                    <input type="checkbox" id="disableWPLogin" name="disableWPLogin" class="box_border_css" <?php if (esc_attr(get_option('lwliad_AD_ldapLogin')) == '0') echo esc_attr('disabled'); ?> <?php if (esc_attr(get_option('lwliad_disableWPLogin')) == 1) echo 'checked'; ?>>By enabling this user can't login with the WordPress credentials.
                </div>
                <div class="button_frame"><input class="buttons_style" type="submit" value="Save" /></div>

            </form>

        </div>
        <?php help(); ?>
    </div>

    <script>
        jQuery('#ldapLogin').click(function() {
            if (jQuery('#ldapLogin').prop("checked") == true) {
                jQuery('#disableWPLogin').attr("disabled", false);
            } else {
                jQuery('#disableWPLogin').attr("disabled", true);
            }
        });
    </script>

<?php

}
function attributemapping()
{
?>
    <div class="card_row">
        <div id="attributeTab" class="card" style="width: 60%;">

            <div>
                <h3> Get Attributes</h3>
                <form id="attributemapping" method="POST" action="">
                    <input type="hidden" name="action" value="testattributeMappingSettings" />
                    <?php wp_nonce_field('testattributeMappingSettings_nonce', 'testattributeMappingSettings_nonce') ?>
                    <table class="table_css">
                        <tr>
                            <td colspan="2" style="width:100%; padding:0px;">
                                <p> You can obtain the attributes available to the user to configure attribute mapping by entering the username(depending on the filter) of the user.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><label>username</td>
                            <td class="td_css"><input type="text" class="box_border_css" id="ldapUsername" name="ldapUsername" /></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="button_frame"> <input type="submit" class="buttons_style" id="getattributes" value="Check Attributes" /></td>
                        </tr>
                    </table>
                </form>
            </div>
            <hr />
            <div style="margin-top:3rem;">
                <h3> Map WP attributes to AD attributes</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="attributeMappingSettings" />
                    <?php wp_nonce_field('attributeMappingSettings_nonce', 'attributeMappingSettings_nonce') ?>
                    <table class="table_css">
                        <tr>
                            <td class="labelStyle">Email</td>
                            <td class="td_css">
                                <select id="ldapemail" name="ldapemail" class="box_border_css" value="<?php echo esc_attr(get_option('lwliad_AD_attr_map_email')); ?>">
                                    <option value="none">Select AD Atttribute</option>
                                    <option value="userprincipalname" <?php if (esc_attr(get_option('lwliad_AD_attr_map_email') === 'userprincipalname')) echo " selected"; ?>>userprincipalname</option>
                                    <option value="sAMAccountName" <?php if (esc_attr(get_option('lwliad_AD_attr_map_email') === 'sAMAccountName')) echo " selected"; ?>>sAMAccountName</option>
                                    <option value="cn" <?php if (esc_attr(get_option('lwliad_AD_attr_map_email') === 'cn')) echo " selected"; ?>>cn</option>
                                    <option value="ui" <?php if (esc_attr(get_option('lwliad_AD_attr_map_email') === 'uid')) echo " selected"; ?>>uid</option>
                                </select>

                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle">FirstName</td>
                            <td class="td_css">
                                <select id="ldapFirstName" name="ldapFirstName" class="box_border_css" value="<?php echo esc_attr(get_option('lwliad_AD_attr_map_firstName')); ?>">
                                    <option value="userprincipalname" <?php if (esc_attr(get_option('lwliad_AD_attr_map_firstName') === 'userprincipalname')) echo " selected"; ?>>userprincipalname</option>
                                    <option value="sAMAccountName" <?php if (esc_attr(get_option('lwliad_AD_attr_map_firstName') === 'sAMAccountName')) echo " selected"; ?>>sAMAccountName</option>
                                    <option value="cn" <?php if (esc_attr(get_option('lwliad_AD_attr_map_firstName') === 'cn')) echo " selected"; ?>>cn</option>
                                    <option value="ui" <?php if (esc_attr(get_option('lwliad_AD_attr_map_firstName') === 'uid')) echo " selected"; ?>>uid</option>
                                </select>

                        </tr>
                        <tr>
                            <td class="labelStyle">Lastname</td>
                            <td class="td_css">
                                <select id="ldapLastName" name="ldapLastName" class="box_border_css" value="<?php echo esc_attr(get_option('lwliad_AD_attr_map_lastName')); ?>">
                                    <option value="userprincipalname" <?php if (esc_attr(get_option('lwliad_AD_attr_map_lastName') === 'userprincipalname')) echo " selected"; ?>>userprincipalname</option>
                                    <option value="sAMAccountName" <?php if (esc_attr(get_option('lwliad_AD_attr_map_lastName') === 'sAMAccountName')) echo " selected"; ?>>sAMAccountName</option>
                                    <option value="cn" <?php if (esc_attr(get_option('lwliad_AD_attr_map_lastName') === 'cn')) echo " selected"; ?>>cn</option>
                                    <option value="ui" <?php if (esc_attr(get_option('lwliad_AD_attr_map_lastName') === 'uid')) echo " selected"; ?>>uid</option>
                                </select>

                                <!-- <input type="text" id="ldapLastName" name="ldapLastName" value="<?php echo esc_attr(get_option('lwliad_AD_attr_map_lastName')); ?>" /></td> -->

                        </tr>


                        <tr>
                            <td colspan="2" class="button_frame "> <input type="submit" id="ADconfig" class="buttons_style" value="Save" /></td>
                        </tr>
                    </table>
                </form>
            </div>
            <hr />
            <div>
                <div style="display: flex;">
                    <h3> Customize Attribute Mapping</h3>
                    <p style="color: red;">(It is being worked on and will be ready shortly.)</p>
                </div>

                <div style="display: flex; flex-direction:row-reverse;">
                    <input type="button" id="attribute_mapping" value="Add Additional Attributes" onclick="addCustomFeilds('attribute_mapping')" style=" width:fit-content;cursor:pointer;" class="box_border_css buttons_style" />
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="lwliad_testLDAPCustomizeMapping" />
                    <?php wp_nonce_field('lwliad_testLDAPCutomizeMapping_nonce', 'lwliad_testLDAPCutomizeMapping_nonce') ?>
                    <div id="attribute_mapping_setup">
                        <p>Add extra attributes in user WP profile.</p>
                        <div style="display: flex; margin:2% 0%;" id="attribute_mapping_1" class="custom_attribute_item">
                            <input type="text" class="disabled_mappingfields box_border_css" placeholder="Enter Attribute Name in WP User Profile" disabled />
                            <input type="text" class="disabled_mappingfields box_border_css" placeholder="Enter AD attribute name" disabled />
                            <input type="button" value="remove" class="remove_attributemapping_button" id="remove_attr_1" />
                        </div>

                    </div>

                    <div class="button_frame">
                        <input type="button" value="Save Advance Attribute Mapping" class="buttons_style" disabled>
                    </div>
                </form>

            </div>

        </div>
        <?php help(); ?>

    </div>

    <script>


    </script>





<?php
}
function rolemapping()
{
?>
    <div class="card_row">
        <div class="card" style="width:60%">

            <div>
                <!-- <h3> Get Roles</h3> -->

                <!-- <form id="testgroups" method="POST" action="">
                    <table>
                        <tr>
                            <td colspan="2" style="width:100%; padding:0px;">
                                <p> By entering the memberof you can receive the groups available for the user to configure Role Mapping. </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:23%;"><label>Group ID</label></td>
                            <td><input type="input" id="ldapgroup" disabled value="memberof" /></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="button_css"> <input type="submit" id="getgroups" disabled value="Test" /></td>
                        </tr>
                    </table>
                </form> -->

            </div>


            <div>
                <form id="groups" method="POST" action="">
                    <input type="hidden" name="action" value="groupMappingSettings" />
                    <?php wp_nonce_field('groupMappingSettings_nonce', 'groupMappingSettings_nonce') ?>
                    <h3> Role Mapping </h3>
                    <div style="margin:1rem;">
                        <input type="checkbox" id="enabledefaultRole" name="enabledefaultRole" class="box_border_css" <?php if (esc_attr(get_option('lwliad_enabledefaultRole')) == 1) echo esc_attr('checked'); ?>> Enabling Role Mapping
                        <p style="font-style:italic;font-size:13px;">Role Mapping automatically maps users from LDAP groups to the default WordPress role selected below on user login with AD credentials.</p>
                    </div>
                    <div style="margin:1rem;">
                        <input type="checkbox" id="enabledefaultRole" name="enabledefaultRole" disabled class="disabled_mappingfields box_border_css" />
                        While updating the user, remove any existing roles from WordPress.
                    </div>

                    <div>
                        <table class="table_css">
                            <tr>
                                <td class="labelStyle ">Default Role:</td>
                                <td class="td_css">
                                    <select id="deaultRole" name="deaultRole" class="box_border_css">
                                        <option value="none">Select Default user role</option>
                                        <option value="subscriber" <?php if (esc_attr(get_option('lwliad_deaultRole')) == 'subscriber') echo esc_attr('selected'); ?>>Subscriber</option>
                                        <option value="editor" <?php if (esc_attr(get_option('lwliad_deaultRole')) == 'editor') echo esc_attr('selected'); ?>>Editor</option>
                                        <option value="contributor" <?php if (esc_attr(get_option('lwliad_deaultRole')) == 'contributor') echo esc_attr('selected'); ?>>Contributor</option>
                                        <option value="administrator" <?php if (esc_attr(get_option('lwliad_deaultRole')) == 'administrator') echo esc_attr('selected'); ?>>Administrator</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td class="labelStyle ">Administrator:</td>
                                <td class="td_css">
                                    <select id="roleAgainstAdmin" name="roleAgainstAdmin" placeholder="CN=prefix,DC=domain,DC=com" class="box_border_css">
                                        <?php

                                        $ADoptions = json_decode(json_encode(esc_attr(get_option('ADgroups'))));

                                        foreach ($ADoptions as $option) {
                                        ?>

                                            <option value="<?php echo $option; ?>" <?php if ($option == esc_attr(get_option('lwliad_AD_roleAgainstAdmin'))) {
                                                                                        echo "selected";
                                                                                    } ?>><?php if ($option) {
                                                                                                        echo esc_attr($option);
                                                                                                    } ?></option><?php
                                                                                                            }




                                                                                                                ?>
                                    </select>






                                    <!-- *-<input type="text" id="roleAgainstAdmin" name="roleAgainstAdmin" placeholder="CN=prefix,DC=domain,DC=com" style="width:20rem;" value="<?php echo esc_attr(get_option('lwliad_AD_roleAgainstAdmin')); ?>"> -->
                                    <p style="margin:0px;font-style:italic;font-size:9px;">eg: CN=GroupA,CN=Users,DC=example,DC=com</p>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" class="button_frame td_css"><input type="submit" class="buttons_style" value="Save"></td>
                            </tr>



                        </table>
                    </div>
                </form>


            </div>

            <hr />

            <div style="width: 100%; max-width:100%; margin-right:0%">
                <div style="display: flex;">
                    <h3> AD Groups to allow login:</h3>
                    <p style="color: red;">(It is being worked on and will be ready shortly.)</p>
                </div>
                <p>Only members of a specified group are allowed to log in your site. By default, every member of an AD group is allowed to log in.</p>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="lwliad_defaultRoleMapping" />
                    <?php wp_nonce_field('lwliad_defaultRoleMapping_nonce', 'lwliad_defaultRoleMapping_nonce') ?>
                    <div>
                        <div style="display: flex;">
                            <input type="text" class="disabled_mappingfields" placeholder="Enter AD  Group" disabled style="width:84.2%" />
                            <input type="button" value="remove" class="remove_attributemapping_button" disabled />
                        </div>

                        <div class="button_frame">
                            <input type="button" value="Save Group" class="buttons_style" disabled>
                        </div>
                    </div>
                </form>

            </div>

            <hr />
            <div style="width: 100%; max-width:100%; margin-right:0%">
                <div style="display: flex;">
                    <h3> Customize Role Mapping</h3>
                    <p style="color: red;">(It is being worked on and will be ready shortly.)</p>
                </div>

                <div style="display: flex; flex-direction:row-reverse;">
                    <input type="button" value="Add Roles" onclick="addCustomFeilds('role_mapping')" style="width:fit-content;cursor:pointer;" class="buttons_style" />
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="lwliad_testCustomizeMapping" />
                    <?php wp_nonce_field('lwliad_testCutomizeMapping_nonce', 'lwliad_testCutomizeMapping_nonce') ?>
                    <div id="role_mapping_setup">
                        <p> Map AD Groups to Wordpress Roles.</p>
                        <div style="display: flex;" id="custom_role_1" class="custom_role_item">
                            <select style="width:40%;margin-right: 24px;" class="box_border_css" placeholder="Enter Attribute Name in WP User Profile">
                                <option>Select WP Role.</option>
                                <option value="subscriber">Subscriber</option>
                                <option value="editor">Editor</option>
                                <option value="contributor">Contributor</option>
                                <option value="administrator">Administrator</option>
                            </select>
                            <input type="text" class="disabled_mappingfields box_border_css" placeholder="Enter AD  Group" disabled style="width:41%" />
                            <input type="button" value="remove" class="remove_roleemapping_button" id="remove_role_1" />
                        </div>
                    </div>

                    <div class="button_frame">
                        <input type="button" value="Save Role Mapping" class="buttons_style" disabled>
                    </div>
                </form>

            </div>

            <hr />

            <script>
                let temp = <?php echo (json_encode(esc_attr(get_option('ADgroups')))); ?>;
            </script>
            <div>
                <h3> Test Role Mapping</h3>
                <form id="testRoleMapping" method="POST" action="">
                    <input type="hidden" name="action" value="testRoleMapping" />
                    <?php wp_nonce_field('testRoleMapping_nonce', 'testRoleMapping_nonce') ?>
                    <table class="table_css">
                        <tr>
                            <td colspan="2" style="width:100%; padding:0px;" class="td_css">
                                <p> Test role mapping for the WordPress users</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="labelStyle"><label>username</td>
                            <td class="td_css"><input type="text" id="adUsername" name="adUsername" class="box_border_css" /></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="button_frame td_css"> <input type="submit" id="testroles" class="buttons_style" value=" Test Role Mapping" /></td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>
        <?php help(); ?>
    </div>

    <script>
        jQuery('#ldapLogin').click(function() {
            if (jQuery('#ldapLogin').prop("checked") == true) {
                jQuery('#disableWPLogin').attr("disabled", false);
            } else {
                jQuery('#disableWPLogin').attr("disabled", true);
            }
        });
    </script>
<?php
}
function help()
{
?>
    <div class="card" style="width: 40%; min-height: 24%; height:fit-content;margin-left: 2rem;">
        <div>

            <!-- <p>For support or troubleshooting help please email us at <a href="mailto:ldapsso@protonmail.com">ldapsso@protonmail.com</a>.</p> -->
            <!-- <p style="text-align: center;"><b>OR</b></p> -->
            <form action="" method="POST" id="contactUs">
                <input type="hidden" name="action" value="contactUsForm" />
                <h3> Contact Us</h3>
                <?php wp_nonce_field('contactUs_nonce', 'contactUs_nonce') ?>
                <div class="elem-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="customer_name" class="box_border_css" name="customer_name" placeholder="username" required>
                </div>
                <div class="elem-group">
                    <label for="email">Your E-mail:</label>
                    <input type="email" id="customer_email" class="box_border_css" name="customer_email" placeholder="username@example.com" required>
                </div>
                <div class="elem-group">
                    <label for="title">Subject:</label>
                    <input type="text" id="email_title" class="box_border_css" name="email_title" required>
                </div>
                <div class="elem-group">
                    <label for="message">Write your Query:</label>
                    <textarea id="customer_message" name="customer_message" class="box_border_css textarea_css" placeholder="" required></textarea>
                </div>
                <div class="button_frame" style="padding-left: 1rem;"><input type="submit" class="buttons_style" value="Send Query" /></div>

            </form>

        </div>
    </div>

<?php

}
function contact()
{
?>
    <div class="card" style="width:35%; margin-left:42rem; height:30rem; position:absolute;">
        <div>


            <form action="" method="POST" id="contactUs">
                <input type="hidden" name="action" value="contactUsForm" />
                <h3> Contact Us</h3>
                <?php wp_nonce_field('contactUs_nonce', 'contactUs_nonce') ?>
                <div class="elem-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="username" required>
                </div>
                <div class="elem-group">
                    <label for="email">Your E-mail:</label>
                    <input type="email" id="customer_email" name="customer_email" placeholder="username@example.com" required>
                </div>
                <div class="elem-group">
                    <label for="title">Subject:</label>
                    <input type="text" id="email_title" name="email_title" required>
                </div>
                <div class="elem-group">
                    <label for="message">Write your Query:</label>
                    <textarea id="customer_message" name="customer_message" placeholder="" required></textarea>
                </div>
                <button type="submit" class="buttons_style">Send Query</button>
            </form>

        </div>
    </div>



<?php
}
function help_us()
{ ?>
    <section class="container">
        <div class="outer-col">
            <div class="heading">Need Any help?</div>
            <form action="" method="POST" id="contactUs" class="help-form-col">
                <input type="hidden" name="action" value="contactUsForm" />
                <h3> Contact Us</h3>
                <?php wp_nonce_field('contactUs_nonce', 'contactUs_nonce') ?>
                <div class="elem-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="username" required>
                </div>
                <div class="elem-group">
                    <label for="email">Your E-mail:</label>
                    <input type="email" id="customer_email" name="customer_email" placeholder="username@example.com" required>
                </div>
                <div class="elem-group">
                    <label for="title">Subject:</label>
                    <input type="text" id="email_title" name="email_title" required>
                </div>
                <div class="elem-group">
                    <label for="message">Write your Query:</label>
                    <textarea id="customer_message" name="customer_message" placeholder="" required style="height:5rem;"></textarea>
                </div>
                <button type="submit" class="buttons_style">Send Query</button>
            </form>

        </div>
    </section>


    <script>
        jQuery(function() {
            var hidden = true;
            jQuery(".heading").click(function() {
                if (hidden) {
                    jQuery(this).parent('.outer-col').animate({
                        bottom: "0"
                    }, 1200);
                } else {
                    jQuery(this).parent('.outer-col').animate({
                        bottom: "-399px"
                    }, 1200);
                }
                hidden = !hidden;
            });
        });
    </script>


<?php }
function ldap_help()
{
    $mailTo = "mailto:securiseweb@gmail.com";
?>
    <div class="card_row">
        <div class="card" style="width: 60%;">
            <div>
                <p class="notethick">For support or troubleshooting help please email us at <a href=esc_url($mailTo)>securiseweb@gmail.com</a>.</p>

                <p> If you're looking for a new or specific functionality, please contact us. We would be happy to help and create for you.</p>
            </div>
        </div>


        <?php help() ?>

    </div>


<?php
}
function
password()
{
?>
    <div class="card_row">
        <div class="card" style="width:60%">
            <div style="display: flex;">
                <h3>Password Setting</h3>
                <p style="color: red;">(It is being worked on and will be ready shortly.)</p>
            </div>
            <table class="table_css">
                <tbody>
                    <tr>
                        <td class="td_css">
                            <input type="checkbox" id="enabledefaultRole" name="enabledefaultRole" class="disabled_mappingfields box_border_css" disabled> Set local password on first successfully login.
                            <!-- <p style="font-style:italic;font-size:13px;">Role Mapping automatically maps users from LDAP groups to the default WordPress role selected below on user login with AD credentials.</p> -->

                        </td>
                    </tr>
                    <tr>
                        <td class="td_css">
                            <input type="checkbox" id="enabledefaultRole" class="disabled_mappingfields box_border_css" disabled name="enabledefaultRole"> Allow local password changes.
                            <!-- <p style="font-style:italic;font-size:13px;">Role Mapping automatically maps users from LDAP groups to the default WordPress role selected below on user login with AD credentials.</p> -->

                        </td>
                    </tr>
                    <tr>
                        <td class="td_css">
                            <input type="checkbox" id="enabledefaultRole" name="enabledefaultRole" class="disabled_mappingfields box_border_css" disabled> Fallback to local password.
                            <!-- <p style="font-style:italic;font-size:13px;">Role Mapping automatically maps users from LDAP groups to the default WordPress role selected below on user login with AD credentials.</p> -->

                        </td>
                    </tr>
                    <tr>
                        <td class="td_css">
                            <input type="checkbox" id="enabledefaultRole" name="enabledefaultRole" class="disabled_mappingfields box_border_css" disabled> Automatic password update.
                            <!-- <p style="font-style:italic;font-size:13px;">Role Mapping automatically maps users from LDAP groups to the default WordPress role selected below on user login with AD credentials.</p> -->

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php help(); ?>
    </div>
<?php
}
