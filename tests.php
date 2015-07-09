<?php
require_once('bitly.php');

$client_id = '2f4b1eb5beb9b615ac70b16e34daf4bc6b899112';
$client_secret = '149e58cfb3f5d508386822be40afc8ce75ca1c26';
$user_access_token = 'c041ec00cdfa5cefe8bda46afaa996bb038527a4';
$user_login = 'falicon';
$user_api_key = '';  <-- you can obtain this for testing your app
via the bit.ly API dashboard ( https://bitly.com/a/oauth_apps );
confirm your password and click 'generate token'...then copy/paste here 

$params = array();
$params['access_token'] = $user_access_token;
$params['longUrl'] = 'http://knowabout.it';
$params['domain'] = 'j.mp';
$results = bitly_get('shorten', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['hash'] = 'dYhyia';
$results = bitly_get('expand', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['hash'] = array('dYhyia','dYhyia','abc123');
$results = bitly_get('expand', $params, true);
var_dump($results);

$params = array();
$params['hash'] = 'dYhyia';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('clicks', $params);
var_dump($results);

$params = array();
$params['hash'] = 'dYhyia';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('referrers', $params);
var_dump($results);

$params = array();
$params['hash'] = 'dYhyia';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('countries', $params);
var_dump($results);

$params = array();
$params['hash'] = 'dYhyia';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('clicks_by_minute', $params);
var_dump($results);

$params = array();
$params['domain'] = 'nyti.ms';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('bitly_pro_domain', $params);
var_dump($results);

$params = array();
$params['hash'] = 'dYhyia';
$params['login'] = 'falicon';
$params['apiKey'] = 'R_685583af62c827761fd45d693fb3d617';
$results = bitly_get('info', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$results = bitly_get('user/clicks', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$results = bitly_get('user/referrers', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/info', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['url'] = 'http://knowabout.it';
$results = bitly_get('link/lookup', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/clicks', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/countries', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/encoders', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/encoders_by_count', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/encoders_count', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/referrers', $params);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/referrers_by_domain', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['link'] = 'http://bit.ly/dYhyia';
$results = bitly_get('link/referring_domains', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['edit'] = 'title';
$params['title'] = 'tech news to know about';
$params['link'] = 'http://bit.ly/1HNOqk6';
$results = bitly_get('user/link_edit', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['url'] = 'http://knowabout.it';
$results = bitly_get('user/link_lookup', $params);
var_dump($results);

$params = array();
$params['access_token'] = $user_access_token;
$params['longUrl'] = 'http://knowabout.it';
$results = bitly_get('user/link_save', $params);
var_dump($results);

// TODO: finish testing for the following endpoints
//$results = bitly_get('user/save_custom_domain_keyword', $params);
//$results = bitly_get('user/info', $params);
//$results = bitly_get('user/link_history', $params);
//$results = bitly_get('user/network_history', $params);
//$results = bitly_get('user/tracking_domain_list', $params);
//$results = bitly_get('user/clicks', $params);
//$results = bitly_get('user/countries', $params);
//$results = bitly_get('user/popular_earned_by_clicks', $params);
//$results = bitly_get('user/popular_earned_by_shortens', $params);
//$results = bitly_get('user/popular_links', $params);
//$results = bitly_get('user/popular_owned_by_clicks', $params);
//$results = bitly_get('user/popular_owned_by_shortens', $params);
//$results = bitly_get('user/referrers', $params);
//$results = bitly_get('user/referring_domains', $params);
//$results = bitly_get('user/shorten_counts', $params);
//$results = bitly_get('user/tracking_domain_clicks', $params);
//$results = bitly_get('user/tracking_domain_shorten_counts', $params);

//$results = bitly_get('oauth/app', $params);

//$results = bitly_get('organization/brand_messages', $params);
//$results = bitly_get('organization/clicks', $params);
//$results = bitly_get('organization/intersecting_links', $params);
//$results = bitly_get('organization/leaderboard', $params);
//$results = bitly_get('organization/missed_opportunities', $params);
//$results = bitly_get('organization/popular_links', $params);
//$results = bitly_get('organization/shorten_counts', $params);

?>