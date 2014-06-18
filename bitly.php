<?php
/**
 * @file
 * Simple PHP library for interacting with the v3 bit.ly api (only deals with
 * JSON format, but supports new OAuth endpoints).
 * REQUIREMENTS: PHP, Curl, JSON
 * 
 * @link https://github.com/Falicon/BitlyPHP
 * @author Kevin Marshall <info@falicon.com>
 * @author Robin Monks <devlinks@gmail.com>
 */

/**
 * The bitlyKey assigned to your bit.ly account. (http://bit.ly/a/account)
 */
define('bitlyKey', 'R_685583af62c827761fd45d693fb3d617');

/**
 * The bitlyLogin assigned to your bit.ly account. (http://bit.ly/a/account)
 */
define('bitlyLogin' , 'falicon');

/**
 * The client_id assigned to your OAuth app. (http://bit.ly/a/account)
 */
define('bitly_clientid' , '2f4b1eb5beb9b615ac70b16e34daf4bc6b899112');

/**
 * The client_secret assigned to your OAuth app. (http://bit.ly/a/account)
 */
define('bitly_secret' , '149e58cfb3f5d508386822be40afc8ce75ca1c26');

/**
 * The URI of the standard bitly v3 API.
 */
define('bitly_api', 'http://api.bit.ly/v3/');

/**
 * The URI of the bitly OAuth endpoints.
 */
define('bitly_oauth_api', 'https://api-ssl.bit.ly/v3/');

/**
 * The URI for OAuth access token requests.
 */
define('bitly_oauth_access_token', 'https://api-ssl.bit.ly/oauth/');

/**
 * Given a longUrl, get the bit.ly shortened version.
 *
 * Example usage:
 * @code
 *   $results = bitly_v3_shorten('http://knowabout.it', 'j.mp');
 * @endcode
 *
 * @param $longUrl
 *   Long URL to be shortened.
 * @param $access_token
 *   The OAuth access token for the user.
 * @param $domain
 *   Uses bit.ly (default), j.mp, or a bit.ly pro domain.
 * @param $x_login
 *   User's login name.
 * @param $x_api_key
 *   User's API key.
 *
 * @return
 *   An associative array containing:
 *   - url: The unique shortened link that should be used, this is a unique
 *     value for the given bit.ly account.
 *   - hash: A bit.ly identifier for long_url which is unique to the given
 *     account.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - long_url: An echo back of the longUrl request parameter.
 *   - new_hash: Will be set to 1 if this is the first time this long_url was
 *     shortened by this user. It will also then be added to the user history.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/shorten
 */
function bitly_v3_shorten($longUrl, $access_token, $domain = '', $x_login = '', $x_apiKey = '') {
  $result = array();
  $url = bitly_oauth_api . "shorten?access_token=" . $access_token . "&longUrl=" . urlencode($longUrl);
  if ($domain != '') {
    $url .= "&domain=" . $domain;
  }
  if ($x_login != '' && $x_apiKey != '') {
    $url .= "&x_login=" . $x_login . "&x_apiKey=" . $x_apiKey;
  }
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'hash'})) {
    $result['url'] = $output->{'data'}->{'url'};
    $result['hash'] = $output->{'data'}->{'hash'};
    $result['global_hash'] = $output->{'data'}->{'global_hash'};
    $result['long_url'] = $output->{'data'}->{'long_url'};
    $result['new_hash'] = $output->{'data'}->{'new_hash'};
  }
  $result['status_code'] = $output->status_code;
  return $result;
}

/**
 * Expand a bit.ly url or hash.
 *
 * @param $data
 *   Either a full bit.ly short url or a bit.ly hash to be expanded.
 *
 * @return
 *   An associative array containing:
 *   - hash: A bit.ly identifier for long_url which is unique to the given
 *     account.
 *   - long_url: The URL that the requested short_url or hash points to.
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/expand
 */
function bitly_v3_expand($data) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      $tmp = explode('/', $rec);
      $tmp = array_reverse($tmp);
      array_push($recs, $tmp[0]);
    }
    $data = implode('&hash=', $recs);
  } else {
    $tmp = explode('/', $data);
    $tmp = array_reverse($tmp);
    $data = $tmp[0];
  }
  // make the call to expand
  $url = bitly_api . "expand?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'expand'})) {
    foreach ($output->{'data'}->{'expand'} as $tmp) {
      $rec = array();
      $rec['hash'] = $tmp->{'hash'};
      $rec['long_url'] = $tmp->{'long_url'};
      $rec['user_hash'] = $tmp->{'user_hash'};
      $rec['global_hash'] = $tmp->{'global_hash'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * Validate that a bit.ly login/apiKey combination is valid.
 *
 * @param $x_login
 *   The end users user's bit.ly login (for validation).
 * @param $x_apiKey
 *   The end users bit.ly apiKey (for validation).
 *
 * @return
 *   TRUE if the combination is valid.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/validate
 */
function bitly_v3_validate($x_login, $x_apiKey) {
  $result = 0;
  $url = bitly_api . "validate?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&x_login=" . $x_login . "&x_apiKey=" . $x_apiKey;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'valid'})) {
    $result = $output->{'data'}->{'valid'};
  }
  return (bool) $result;
}

/**
 * For one or more bit.ly URL's or hashes, returns statistics about the clicks
 * on that link.
 *
 * @param $data
 *   Can be a bit.ly shortened URL, a bit.ly hash, or an array of bit.ly URLs
 *   and/or hashes.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - short_url: The unique bit.ly hash.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - user_clicks: The total count of clicks to this user's bit.ly link.
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - global_clicks: The total count of clicks to all bit.ly links that point
 *     to the same same long url.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/clicks
 */
function bitly_v3_clicks($data) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      $tmp = explode('/', $rec);
      $tmp = array_reverse($tmp);
      array_push($recs, $tmp[0]);
    }
    $data = implode('&hash=', $recs);
  } else {
    $tmp = explode('/', $data);
    $tmp = array_reverse($tmp);
    $data = $tmp[0];
  }
  $url = bitly_api . "clicks?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'clicks'})) {
    foreach ($output->{'data'}->{'clicks'} as $tmp) {
      $rec = array();
      $rec['short_url'] = !empty($tmp->{'short_url'}) ? $tmp->{'short_url'} : '';
      $rec['global_hash'] = !empty($tmp->{'global_hash'}) ? $tmp->{'global_hash'} : '';
      $rec['user_clicks'] = !empty($tmp->{'user_clicks'}) ? $tmp->{'user_clicks'} : 0;
      $rec['user_hash'] = !empty($tmp->{'user_hash'}) ? $tmp->{'user_hash'} : '';
      $rec['global_clicks'] = !empty($tmp->{'global_clicks'}) ? $tmp->{'global_clicks'} : 0;
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * Provides a list of referring sites for a specified bit.ly short link or hash,
 * and the number of clicks per referrer.
 *
 * @param $data
 *   A bit.ly shortened URL or bit.ly hash.
 *
 * @return
 *   An associative array containing:
 *   - created_by: The service that created the link.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - short_url: The unique bit.ly hash.
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - referrers: A multidimensional numbered associative array containing:
 *     - clicks: Number of clicks from this referrer.
 *     - referrer: (optional) Referring site.
 *     - referrer_app: (optional) Referring application (e.g.: Tweetdeck).
 *     - url: (optional) URL of referring application
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/referrers
 */
function bitly_v3_referrers($data) {
  $results = array();
  $tmp = explode('/', $data);
  $tmp = array_reverse($tmp);
  $data = $tmp[0];
  $url = bitly_api . "referrers?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'referrers'})) {
    $results['created_by'] = $output->{'data'}->{'created_by'};
    $results['global_hash'] = $output->{'data'}->{'global_hash'};
    $results['short_url'] = $output->{'data'}->{'short_url'};
    $results['user_hash'] = $output->{'data'}->{'user_hash'};
    $results['referrers'] = array();
    foreach ($output->{'data'}->{'referrers'} as $tmp) {
      $rec = array();
      $rec['clicks'] = $tmp->{'clicks'};
      $rec['referrer'] = $tmp->{'referrer'};
      $rec['referrer_app'] = $tmp->{'referrer_app'};
      $rec['url'] = $tmp->{'url'};
      array_push($results['referrers'], $rec);
    }
  }
  return $results;
}

/**
 * Provides a list of countries from which clicks on a specified bit.ly short
 * link or hash have originated, and the number of clicks per country.
 *
 * @param $data
 *   A bit.ly shortened URL or bit.ly hash.
 *
 * @return
 *   An associative array containing:
 *   - created_by: The service that created the link.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - short_url: The unique bit.ly hash.
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - countries: A multidimensional numbered associative array containing:
 *     - clicks: Number of clicks from this country.
 *     - country: The country code these clicks originated from or null when
 *       displaying clicks that could not be mapped to a specific country.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/countries
 */
function bitly_v3_countries($data) {
  $results = array();
  $tmp = explode('/', $data);
  $tmp = array_reverse($tmp);
  $data = $tmp[0];
  $url = bitly_api . "countries?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'countries'})) {
    $results['created_by'] = $output->{'data'}->{'created_by'};
    $results['global_hash'] = $output->{'data'}->{'global_hash'};
    $results['short_url'] = $output->{'data'}->{'short_url'};
    $results['user_hash'] = $output->{'data'}->{'user_hash'};
    $results['countries'] = array();
    foreach ($output->{'data'}->{'countries'} as $tmp) {
      $rec = array();
      $rec['clicks'] = $tmp->{'clicks'};
      $rec['country'] = $tmp->{'country'};
      array_push($results['countries'], $rec);
    }
  }
  return $results;
}

/**
 * For one or more bit.ly short urls or hashes, provides time series clicks per
 * minute for the last hour in reverse chronological order (most recent to least
 * recent).
 *
 * @param $data
 *   Can be a bit.ly shortened URL, a bit.ly hash, or an array of bit.ly URLs
 *   and/or hashes.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - clicks: An array with sixty entires, each for the number of clicks
 *     received for the given link that minute.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - short_url: The unique bit.ly hash.
 *   - user_hash: The corresponding bit.ly user identifier.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/clicks_by_minute
 */
function bitly_v3_clicks_by_minute($data) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      $tmp = explode('/', $rec);
      $tmp = array_reverse($tmp);
      array_push($recs, $tmp[0]);
    }
    $data = implode('&hash=', $recs);
  } else {
    $tmp = explode('/', $data);
    $tmp = array_reverse($tmp);
    $data = $tmp[0];
  }
  $url = bitly_api . "clicks_by_minute?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'clicks_by_minute'})) {
    foreach ($output->{'data'}->{'clicks_by_minute'} as $tmp) {
      $rec = array();
      $rec['clicks'] = $tmp->{'clicks'};
      $rec['global_hash'] = $tmp->{'global_hash'};
      $rec['short_url'] = $tmp->{'short_url'};
      $rec['user_hash'] = $tmp->{'user_hash'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * For one or more bit.ly short urls or hashes, provides time series clicks per
 * day for the last 30 days in reverse chronological order (most recent to least
 * recent).
 *
 * @param $data
 *   Can be a bit.ly shortened URL, a bit.ly hash, or an array of bit.ly URLs
 *   and/or hashes.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - short_url: The unique bit.ly hash.
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - clicks: A multidimensional numbered associative array containing:
 *     - clicks: The number of clicks received for a given link that day.
 *     - day_start: A time code representing the start of the day for which
 *       click data is provided.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/clicks_by_day
 */
function bitly_v3_clicks_by_day($data, $days = 7) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      $tmp = explode('/', $rec);
      $tmp = array_reverse($tmp);
      array_push($recs, $tmp[0]);
    }
    $data = implode('&hash=', $recs);
  } else {
    $tmp = explode('/', $data);
    $tmp = array_reverse($tmp);
    $data = $tmp[0];
  }
  $url = bitly_api . "clicks_by_day?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&days=" . $days . "&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'clicks_by_day'})) {
    foreach ($output->{'data'}->{'clicks_by_day'} as $tmp) {
      $rec = array();
      $rec['global_hash'] = $tmp->{'global_hash'};
      $rec['short_url'] = $tmp->{'short_url'};
      $rec['user_hash'] = $tmp->{'user_hash'};
      $rec['clicks'] = array();
      $clicks = $tmp->{'clicks'};
      foreach ($clicks as $click) {
        $clickrec = array();
        $clickrec['clicks'] = $click->{'clicks'};
        $clickrec['day_start'] = $click->{'day_start'};
        array_push($rec['clicks'], $clickrec);
      }
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * This is used to query whether a given short domain is assigned for bitly.Pro,
 * and is consequently a valid shortUrl parameter for other API calls.
 *
 * @param $domain
 *   The short domain to check.
 *
 * @return
 *   An associative array containing:
 *   - domain: An echo back of the request parameter.
 *   - bitly_pro_domain: 0 or 1 designating whether this is a current bitly.Pro
 *     domain.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/bitly_pro_domain
 */
function bitly_v3_bitly_pro_domain($domain) {
  $result = array();
  $url = bitly_api . "bitly_pro_domain?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&domain=" . $domain;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'bitly_pro_domain'})) {
    $result['domain'] = $output->{'data'}->{'domain'};
    $result['bitly_pro_domain'] = $output->{'data'}->{'bitly_pro_domain'};
  }
  return $result;
}

/**
 * This is used to query for a bit.ly link based on a long URL.
 *
 * @param $data
 *   One or more long URLs to lookup.
 *
 * @return
 *   An associative array containing:
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - short_url: The unique shortened link that should be used, this is a
 *     unique value for the given bit.ly account.
 *   - url: An echo back of the url parameter.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/lookup
 */
function bitly_v3_lookup($data) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      array_push($recs, urlencode($rec));
    }
    $data = implode('&url=', $recs);
  } else {
    $data = urlencode($data);
  }
  $url = bitly_oauth_api . "lookup?url=" . $data . "&access_token=" . bitlyKey;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'lookup'})) {
    foreach ($output->{'data'}->{'lookup'} as $tmp) {
      $rec = array();
      $rec['global_hash'] = $tmp->{'global_hash'};
      $rec['short_url'] = $tmp->{'short_url'};
      $rec['url'] = $tmp->{'url'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * This is used by applications to lookup a bit.ly API key for a user given a
 * bit.ly username and password.
 *
 * @param $x_login
 *   Bit.ly username.
 * @param $x_password
 *   Bit.ly password.
 *
 * @return
 *   An associative array containing:
 *   - successful: An indicator of weather or not the login and password
 *     combination is valid.
 *   - username: The corresponding bit.ly users username.
 *   - api_key: The corresponding bit.ly users API key.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/authenticate
 */
function bitly_v3_authenticate($x_login, $x_password) {
  $result = array();
  $url = bitly_api . "authenticate";
  $params = array();
  $params['login'] = bitlyLogin;
  $params['apiKey'] = bitlyKey;
  $params['format'] = "json";
  $params['x_login'] = $x_login;
  $params['x_password'] = $x_password;
  $output = json_decode(bitly_post_curl($url, $params));
  if (isset($output->{'data'}->{'authenticate'})) {
    $result['successful'] = $output->{'data'}->{'authenticate'}->{'successful'};
    $result['username'] = $output->{'data'}->{'authenticate'}->{'username'};
    $result['api_key'] = $output->{'data'}->{'authenticate'}->{'api_key'};
  }
  return $result;
}

/**
 * This is used to return the page title for a given bit.ly link.
 *
 * @param $data
 *   Can be a bit.ly shortened URL, a bit.ly hash, or an array of bit.ly URLs
 *   and/or hashes.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - created_by: The service that created the link.
 *   - global_hash: A bit.ly identifier for long_url which can be used to track
 *     aggregate stats across all matching bit.ly links.
 *   - hash: The unique bit.ly hash.
 *   - title: The HTML page title for the destination page (when available).
 *   - user_hash: The corresponding bit.ly user identifier.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/info
 */
function bitly_v3_info($data) {
  $results = array();
  if (is_array($data)) {
    // we need to flatten this into one proper command
    $recs = array();
    foreach ($data as $rec) {
      $tmp = explode('/', $rec);
      $tmp = array_reverse($tmp);
      array_push($recs, $tmp[0]);
    }
    $data = implode('&hash=', $recs);
  } else {
    $tmp = explode('/', $data);
    $tmp = array_reverse($tmp);
    $data = $tmp[0];
  }
  // make the call to expand
  $url = bitly_api . "info?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&hash=" . $data;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'info'})) {
    foreach ($output->{'data'}->{'info'} as $tmp) {
      $rec = array();
      $rec['created_by'] = $tmp->{'created_by'};
      $rec['global_hash'] = $tmp->{'global_hash'};
      $rec['hash'] = $tmp->{'hash'};
      $rec['title'] = $tmp->{'title'};
      $rec['user_hash'] = $tmp->{'user_hash'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * Returns an OAuth access token as well as API users for a given code.
 *
 * @param $code
 *   The OAuth verification code acquired via OAuth’s web authentication
 *   protocol.
 * @param $redirect
 *   The page to which a user was redirected upon successfully authenticating.
 *
 * @return
 *   An associative array containing:
 *   - login: The corresponding bit.ly users username.
 *   - api_key: The corresponding bit.ly users API key.
 *   - access_token: The OAuth access token for specified user.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/oauth/access_token
 */
function bitly_oauth_access_token($code, $redirect) {
  $results = array();
  $url = bitly_oauth_access_token . "access_token";
  $params = array();
  $params['client_id'] = bitly_clientid;
  $params['client_secret'] = bitly_secret;
  $params['code'] = $code;
  $params['redirect_uri'] = $redirect;
  $output = bitly_post_curl($url, $params);
  $parts = explode('&', $output);
  foreach ($parts as $part) {
    $bits = explode('=', $part);
    $results[$bits[0]] = $bits[1];
  }
  return $results;
}

/**
 * Provides the total clicks per day on a user’s bit.ly links.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 * @param $days
 *   An integer value for the number of days (counting backwards from the
 *   current day) from which to retrieve data (min:1, max:30, default:7).
 *
 * @return
 *   An associative array containing:
 *   - days: An echo of the dupplied days parameter.
 *   - total_clicks: The total number of clicks over the supplied period.
 *   - clicks: A multidimensional numbered associative array containing:
 *     - clicks: The number of clicks received for a given link that day.
 *     - day_start: A time code representing the start of the day for which
 *       click data is provided.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/user/clicks
 */
function bitly_v3_user_clicks($access_token, $days = 7) {
  // $results = bitly_v3_user_clicks('BITLY_SUPPLIED_ACCESS_TOKEN');
  $results = array();
  $url = bitly_oauth_api . "user/clicks?access_token=" . $access_token . "&days=" . $days;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'clicks'})) {
    $results['days'] = $output->{'data'}->{'days'};
    $results['total_clicks'] = $output->{'data'}->{'total_clicks'};
    $results['clicks'] = array();
    foreach ($output->{'data'}->{'clicks'} as $clicks) {
      $rec = array();
      $rec['clicks'] = $clicks->{'clicks'};
      $rec['day_start'] = $clicks->{'day_start'};
      array_push($results['clicks'], $rec);
    }
  }
  return $results;
}

/**
 * Provides a list of referring sites for a specified bit.ly user, and the
 * number of clicks per referrer.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 * @param $days
 *   An integer value for the number of days (counting backwards from the
 *   current day) from which to retrieve data (min:1, max:30, default:7).
 *
 * @return
 *   An associative array containing:
 *   - days: An echo of the dupplied days parameter.
 *   - referrers: A multidimensional numbered associative array containing:
 *     - clicks: Number of clicks from this referrer.
 *     - referrer: (optional) Referring site.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/user/referrers
 */
function bitly_v3_user_referrers($access_token, $days = 7) {
  // $results = bitly_v3_user_referrers('BITLY_SUPPLIED_ACCESS_TOKEN');
  $results = array();
  $url = bitly_oauth_api . "user/referrers?access_token=" . $access_token . "&days=" . $days;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'referrers'})) {
    $results['days'] = $output->{'data'}->{'days'};
    $results['referrers'] = array();
    foreach ($output->{'data'}->{'referrers'} as $referrers) {
      $recs = array();
      foreach ($referrers as $ref) {
        $rec = array();
        $rec['referrer'] = $ref->{'referrer'};
        $rec['clicks'] = $ref->{'clicks'};
        array_push($recs, $rec);
      }
      array_push($results['referrers'], $recs);
    }
  }
  return $results;
}

/**
 * Provides a list of referring sites for a specified bit.ly short link or hash,
 * and the number of clicks per referrer.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 * @param $days
 *   An integer value for the number of days (counting backwards from the
 *   current day) from which to retrieve data (min:1, max:30, default:7).
 *
 * @return
 *   An associative array containing:
 *   - days: An echo of the dupplied days parameter.
 *   - referrers: A multidimensional numbered associative array containing:
 *     - clicks: Number of clicks from this referrer.
 *     - countries: (optional) Country code for where the clicks originated.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/user/countries
 */
function bitly_v3_user_countries($access_token, $days = 7) {
  // $results = bitly_v3_user_countries('BITLY_SUPPLIED_ACCESS_TOKEN');
  $results = array();
  $url = bitly_oauth_api . "user/countries?access_token=" . $access_token . "&days=" . $days;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'countries'})) {
    $results['days'] = $output->{'data'}->{'days'};
    $results['countries'] = array();
    foreach ($output->{'data'}->{'countries'} as $countries) {
      $recs = array();
      foreach ($countries as $country) {
        $rec = array();
        $rec['country'] = $country->{'country'};
        $rec['clicks'] = $country->{'clicks'};
        array_push($recs, $rec);
      }
      array_push($results['countries'], $recs);
    }
  }
  return $results;
}

/**
 * Provides a given user’s 100 most popular links based on click traffic in the
 * past hour, and the number of clicks per link.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - user_hash: The corresponding bit.ly user identifier.
 *   - clicks: Number of clicks on this link.
 *
 * @see http://code.google.com/p/bitly-api/wiki/ApiDocumentation#/v3/user/realtime_links
 */
function bitly_v3_user_realtime_links($access_token) {
  // $results = bitly_v3_user_realtime_links('BITLY_SUPPLIED_ACCESS_TOKEN');
  $results = array();
  $url = bitly_oauth_api . "user/realtime_links?format=json&access_token=" . $access_token;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'realtime_links'})) {
    foreach ($output->{'data'}->{'realtime_links'} as $realtime_links) {
      $rec = array();
      $rec['clicks'] = $realtime_links->{'clicks'};
      $rec['user_hash'] = $realtime_links->{'user_hash'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * Returns entries from a user's link history in reverse chronological order.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *   A multidimensional numbered associative array containing:
 *   - link - the bitly link specific to this user and this long_url.
 *   - aggregate_link - the global bitly identifier for this long_url.
 *   - long_url - the original long URL.
 *   - archived - a true/false value indicating whether the user has archived this link.
 *   - private - a true/false value indicating whether the user has made this link private.
 *   - created_at - an integer unix epoch indicating when this link was shortened/encoded.
 *   - user_ts - a user-provided timestamp for when this link was shortened/encoded, used for backfilling data.
 *   - modified_at - an integer unix epoch indicating when this link's metadata was last edited.
 *   - title - the title for this link.
 *
 * @see http://dev.bitly.com/user_info.html#v3_user_link_history
 */
function bitly_v3_user_link_history($access_token) {
  // $results = bitly_v3_user_link_history('BITLY_SUPPLIED_ACCESS_TOKEN');
  $results = array();
  $url = bitly_oauth_api . "user/link_history?format=json&access_token=" . $access_token;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'link_history'})) {
    foreach ($output->{'data'}->{'link_history'} as $link_history) {
      $rec = array();
      $rec['aggregate_link'] = $link_history->{'aggregate_link'};
      $rec['archived'] = $link_history->{'archived'};
      $rec['client_id'] = $link_history->{'client_id'};
      $rec['created_at'] = $link_history->{'created_at'};
      $rec['link'] = $link_history->{'link'};
      $rec['long_url'] = $link_history->{'long_url'};
      $rec['modified_at'] = $link_history->{'modified_at'};
      $rec['private'] = $link_history->{'private'};
      $rec['title'] = $link_history->{'title'};
      $rec['user_ts'] = $link_history->{'user_ts'};
      array_push($results, $rec);
    }
  }
  return $results;
}

/**
 * Returns a specified number of "high-value" bitly links that are popular
 * across bitly at this particular moment.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @param $limit
 *   the maxiumum number of high-value links to return.
 *
 * @return
 *   An array of bit.ly links
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_highvalue($access_token, $limit = 5) {
  $results = array();
  $url = bitly_oauth_api . "highvalue?access_token=" . $access_token . "&limit=" . $limit;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'values'})) {
    foreach ($output->{'data'}->{'values'} as $link) {
      array_push($results, $link);
    }
  }
  return $results;
}

/**
 * Search links receiving clicks across bitly by content, language, location, and more.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @param $query
 *   string to query for
 *
 * @param $limit
 *   the maximum number of links to return.
 *
 * @param $offset
 *   which result to start with (defaults to 0)
 *
 * @param $domain
 *   restrict results to this web domain
 *
 * @param $lang
 *   favor results in this language (two letter ISO code)
 *
 * @param $cities
 *   show links active in this city (ordered like country-state-city, e.g. us-il-chicago)
 *
 * @param $fields
 *   which fields to return in the response (comma-separated).
 *   May be any of: domain, initial_epoch, h2, h3, site, lastindexed, keywords,
 *   last_indexed_epoch, title, initial, summaryText, content, score, summaryTitle,
 *    type, description, cities, lang, url, referrer, aggregate_link, lastseen, page,
 *    ogtitle aggregate_link. By default, all will be returned.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_search($access_token, $query, $limit = 10, $offset = 0, $domain = '', $lang = '', $cities = '', $fields = array()) {
  $results = array();
  $url = bitly_oauth_api . "search?access_token=" . $access_token . "&query=" . urlencode($query) . "&limit=" . $limit . "&offset=" . $offset;
  if ($domain != '') {
    $url .= '&domain=' . urlencode($domain);
  }
  if ($lang != '') {
    $url .= '&lang=' . urlencode($lang);
  }
  if ($cities != '') {
    $url .= '&cities=' . urlencode($cities);
  }
  if (!empty($fields)) {
    # only return certain fields
    $url .= '&fields=' . implode(',', $fields);
  }
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'results'})) {
    foreach ($output->{'data'}->{'results'} as $result) {
      $res = array();
      foreach ($result as $key => $val) {
        $res[$key] = $val;
      }
      array_push($results, $res);
    }
  }
  return $results;
}

/**
 * Returns phrases that are receiving an uncharacteristically high volume of
 * click traffic, and the individual links (hashes) driving traffic to pages
 * containing these phrases.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_realtime_bursting_phrases($access_token) {
  $results = array();
  $url = bitly_oauth_api . "realtime/bursting_phrases?access_token=" . $access_token;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'phrases'})) {
    foreach ($output->{'data'}->{'phrases'} as $phrase) {
      $res = array();
      $res['std'] = $phrase->{'std'};
      $res['ghashes'] = array();
      foreach($phrase->{'ghashes'} as $ghashes) {
        $temp = array();
        $temp['visitors'] = $ghashes->{'visitors'};
        $temp['ghash'] = $ghashes->{'ghash'};
        array_push($res['ghashes'], $temp);
      }
      $res['N'] = $phrase->{'N'};
      $res['rate'] = $phrase->{'rate'};
      $res['urls'] = array();
      foreach($phrase->{'urls'} as $urls) {
        $temp = array();
        $temp['visitors'] = $urls->{'visitors'};
        $temp['aggregate_url'] = $urls->{'aggregate_url'};
        array_push($res['urls'], $temp);
      }
      $res['phrase'] = $phrase->{'phrase'};
      $res['mean'] = $phrase->{'mean'};
      array_push($results, $res);
    }
  }
  return $results;
}

/**
 * Returns phrases that are receiving a consistently high volume of click
 * traffic, and the individual links (hashes) driving traffic to pages
 * containing these phrases.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_realtime_hot_phrases($access_token) {
  $results = array();
  $url = bitly_oauth_api . "realtime/bursting_phrases?access_token=" . $access_token;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'phrases'})) {
    $results['lag'] = $output->{'data'}->{'lag'};
    $results['time'] = $output->{'data'}->{'time'};
    $results['phrases'] = array();
    foreach ($output->{'data'}->{'phrases'} as $phrase) {
      $res = array();
      $res['phrase'] = $phrase->{'phrase'};
      $res['rate'] = $phrase->{'rate'};
      $res['ghashes'] = array();
      foreach($phrase->{'ghashes'} as $ghashes) {
        $temp = array();
        $temp['visitors'] = $ghashes->{'visitors'};
        $temp['ghash'] = $ghashes->{'ghash'};
        array_push($res['ghashes'], $temp);
      }
      array_push($results['phrases'], $res);
    }
  }
  return $results;
}

/**
 * Returns the click rate for content containing a specified phrase.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_realtime_clickrate($access_token, $phrase) {
  $results = array();
  $url = bitly_oauth_api . "realtime/clickrate?access_token=" . $access_token . "&phrase=" . urlencode($phrase);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'phrase'})) {
    $results['phrase'] = $output->{'data'}->{'phrase'};
    $results['rate'] = $output->{'data'}->{'rate'};
    $results['lag'] = $output->{'data'}->{'lag'};
    $results['time'] = $output->{'data'}->{'time'};
  }
  return $results;
}

/**
 * Returns metadata about a single bitly link.
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_info($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/info?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'original_url'})) {
    foreach ($output->{'data'} as $key=>$val) {
      if (!is_array($val)) {
        $results[$key] = $val;
      }
    }
  }
  return $results;
}

/**
 * Returns the “main article” from the linked page, as determined by the
 * content extractor, in either HTML or plain text format
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_content($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/content?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'content'})) {
    $results['content'] = $output->{'data'}->{'content'};
    $results['content_type'] = $output->{'data'}->{'content_type'};
  }
  return $results;
}

/**
 * Returns the detected categories for a document, in descending order of
 * confidence
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_category($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/category?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'categories'})) {
    foreach ($output->{'data'}->{'categories'} as $category) {
      array_push($results, $category);
    }
  }
  return $results;
}

/**
 * Returns the "social score" for a specified bitly link
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *   An associative array
 *     - The key is the link
 *     - The value is the score
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_social($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/social?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'social_scores'})) {
    foreach ($output->{'data'}->{'social_scores'} as $key=>$val) {
      $results[$key] = $val;
    }
  }
  return $results;
}

/**
 * Returns the significant locations for the bitly link or None if locations do not exist
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *   An array of locations
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_location($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/location?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'locations'})) {
    foreach ($output->{'data'}->{'locations'} as $location) {
      array_push($results, $location);
    }
  }
  return $results;
}

/**
 * Returns the significant languages for the bitly link
 *
 * @param $access_token
 *   The OAuth access token for the user.
 *
 * @return
 *   An associative array containing
 *   - the link as the key
 *   - the language as the value
 *
 * @see http://dev.bitly.com/data_apis.html
 */
function bitly_v3_link_language($access_token, $link) {
  $results = array();
  $url = bitly_oauth_api . "link/language?access_token=" . $access_token . "&link=" . urlencode($link);
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'languages'})) {
    foreach ($output->{'data'}->{'languages'} as $key=>$val) {
      $results[$key] = $val;
    }
  }
  return $results;
}

/**
 * Make a GET call to the bit.ly API.
 *
 * @param $uri
 *   URI to call.
 */
function bitly_get_curl($uri) {
  $output = "";
  try {
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = curl_exec($ch);
  } catch (Exception $e) {
  }
  return $output;
}

/**
 * Make a POST call to the bit.ly API.
 *
 * @param $uri
 *   URI to call.
 * @param $fields
 *   Array of fields to send.
 */
function bitly_post_curl($uri, $fields) {
  $output = "";
  $fields_string = "";
  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  rtrim($fields_string,'&');
  try {
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_POST,count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = curl_exec($ch);
  } catch (Exception $e) {
  }
  return $output;
}

?>
