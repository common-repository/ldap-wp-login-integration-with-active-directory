<?php

class contact
{
    public static function  contactUs($username, $email, $subject,$message)
    {
        $headers = array(
            'Accept'  => 'application/json',
            'charset'       => 'UTF - 8',
            'Content-Type' => 'application/x-www-form-urlencoded',

        );

        $args = array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => '5',
            'httpversion' => '1.0',
            'location'    => true,
            'blocking'    => true,
            'headers'     => $headers,
            'cookies'     => array(),
            'sslverify'   => false
        );
        $response = wp_remote_post( 'https://2texq35kaj.execute-api.us-east-1.amazonaws.com/default/ContactUs?email='.$email.'&name='.$username.'&query='.urlencode($message.' <br> HostName: '. home_url() ).'&subject='.urlencode($subject).'&pluginName=wp_ldap', $args );

        return $response;
    }

    public static function  feedback($username, $email,$message)
    {

        $args = array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
            'sslverify'   => false
        );

        $response = wp_remote_post( 'https://2texq35kaj.execute-api.us-east-1.amazonaws.com/default/ContactUs'.$email.'&name='.$username.'&query='.urlencode($message .' <br> HostName: '. home_url() ).'&subject=feedback&pluginName=wp_ldap_feedback', $args );
    }
}