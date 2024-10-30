=== Ldap WP Login / Active Directory Integration ===
Contributors: robert05
Tags: ldap, authentication, active directory, openldap, security, windows sso, ldap login, AD
Requires at least: 5.0
Tested up to: 6.0.0
Requires PHP: 7.2
Stable tag: 3.0.2
License: GPLv2


== Description ==

Ldap WP Login / Active Directory Integration is a intranet plugin allows WordPress to authenticate, create and update users using Active Directory credentials.

So why to use our plugin?
* **Easy to use**
* **Flexible**
* **We can provide you with the custom made features according to your usecase**
* **Support**: A customer friendly team to help you.

= Features =
* Authenticating/Login WordPress users against Active Directory credentials.
* A great flexibility to authenticate user agnaist WordPress or Active Directory credentials by enabling in the plugin settings.
* Supports Active Directory and OpenLDAP (And other directory systems, such as OpenDS, that compatible with the LDAP standard.)
* Attribute Mapping
* Mapping of Active Directory groups to WordPress roles.
* LDAP user validation by entering the credentials in the plugin.

=ADDITIONAL FEATURES=

If you are looking for aditional features please drop us a mail <a href="mailto:securiseweb@gmail.com">securiseweb@gmail.com</a>. Our team will reach you and get the requirements to complete your task.


= Requirements =

* WordPress since 5.0 or higher
* PHP >= 7.2
* LDAP support


== Installation ==
To install Ldap Wordpress Login Integration with Active Directory you need at least WordPress 5.0 and PHP 7.2

Ldap Wordpress Login Integration with Active Directory requires to enable 'php_ldap.dll' and 'php_openssl.dll' PHP modules.

Steps to enable 'PHP Modules'
1. Go to php.ini file.
2. Search for "extension=php_ldap.dll" and "extension=php_openssl.dll" in php.ini file. Uncomment this line, if it is not there, then add this line to the file and save the file.


== Frequently Asked Questions ==

For support or troubleshooting help please email us at <a href="mailto:securiseweb@gmail.com">securiseweb@gmail.com</a>


== Screenshots ==

1. LDAP Configuration.
2. LDAP login setup
3. Attributes Mapping from AD to WP users.
4. Role Mapping automatically map AD groups to the WP roles.



== Changelog ==

= 3.0.2 =
*Resolved security flaws

= 3.0.1 =
*Security related bug fixes

= 3.0.0 =
*Bug fixes
*PHP 8 compatability
*Uimprovements
*Aditional features 

= 2.0.0 =
* Added get AD attribute for mapping WP roles, get AD groups for mapping WP roles, test role mapping to check assigned role to Wp-user, contact Us & feedback form 

= 1.0.1 =
*bug fixes and readme changes

= 1.0.0 =
* this is the first release.

== Upgrade Notice ==

= 2.0.0 =
* Added get AD attribute for mapping WP roles, get AD groups for mapping WP roles, test role mapping to check assigned role to Wp-user, contact Us & feedback form 

= 1.0.1 =
*bug fixes and readme changes

= 1.0.0 =
First version of plugin.