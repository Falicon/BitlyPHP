<?
/*
Simple PHP library for interacting with the v3 bit.ly api (only deals with JSON format, but supports new OAuth endpoints)

REQUIREMENTS: PHP, Curl, JSON
*/

// the bitlyKey assigned to your bit.ly account (http://bit.ly/a/account)
define('bitlyKey', 'YOUR_BITLY_ASSIGNED_KEY');
// the bitlyLogin assigned to your bit.ly account (http://bit.ly/a/account)
define('bitlyLogin' , 'YOUR_BITLY_LOGIN');
// the client_id assigned to your OAuth app (http://bit.ly/a/account)
define('bitly_clientid' , 'YOUR_BITLY_ASSIGNED_CLIENT_ID_FOR_OAUTH');
// the client_secret assigned to your OAuth app (http://bit.ly/a/account)
define('bitly_secret' , 'YOUR_BITLY_ASSIGNED_CLIENT_SECRET_FOR_OAUTH');

/*****************************************************************************
YOU SHOULDN'T NEED TO EDIT THINGS BELOW HERE.

However, feel free to do as you please (and if you happen to find/fix an issue
please do send me a pull request on github ( http://github.com/falicon )

Oh and as always, if you've got any questions, comments, or concerns about
anything you find here, please feel free to drop me an email at info@falicon.com
or find me on Twitter @falicon

*****************************************************************************/

// the uri of the standard bitly v3 api
define('bitly_api', 'http://api.bit.ly/v3/');
// the uri of the bitly OAuth endpoints
define('bitly_oauth_api', 'https://api-ssl.bit.ly/v3/');
// the uri for OAuth access token requests
define('bitly_oauth_access_token', 'https://api-ssl.bit.ly/oauth/');

function bitly_v3_shorten($longUrl, $domain = '', $x_login = '', $x_apiKey = '') {
  // given a longUrl, get the bit.ly shortened version
  // $results = bitly_v3_shorten('http://knowabout.it', 'j.mp');
  $result = array();
  $url = bitly_api . "shorten?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&longUrl=" . urlencode($longUrl);
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
  return $result;
}

function bitly_v3_expand($data) {
  // expand a bit.ly url or hash (to get the long url)
  // $results = bitly_v3_expand('dYhyia');
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

function bitly_v3_validate($x_login, $x_apiKey) {
  // validate that a bit.ly login/apiKey combination is valid
  // $result = bitly_v3_validate('USER_SUPPLIED_USERNAME','USER_SUPPLIED_BITLY_API_KEY');
  $result = 0;
  $url = bitly_api . "validate?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&x_login=" . $x_login . "&x_apiKey=" . $x_apiKey;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'valid'})) {
    $result = $output->{'data'}->{'valid'};
  }
  return $result;
}

function bitly_v3_clicks($data) {
  // $results = bitly_v3_clicks('dYhyia');
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
      $rec['short_url'] = $tmp->{'short_url'};
      $rec['global_hash'] = $tmp->{'global_hash'};
      $rec['user_clicks'] = $tmp->{'user_clicks'};
      $rec['user_hash'] = $tmp->{'user_hash'};
      $rec['global_clicks'] = $tmp->{'global_clicks'};
      array_push($results, $rec);
    }
  }
  return $results;
}

function bitly_v3_referrers($data) {
  // $results = bitly_v3_referrers('grqSlY');
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
      array_push($results['referrers'], $rec);
    }
  }
  return $results;
}

function bitly_v3_countries($data) {
  // $results = bitly_v3_countries('grqSlY');
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

function bitly_v3_clicks_by_minute($data) {
  // $results = bitly_v3_clicks_by_minute('grqSlY');
  // $results = bitly_v3_clicks_by_minute(array('grqSlY','dYhyia'));
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

function bitly_v3_clicks_by_day($data, $days = 7) {
  // $results = bitly_v3_clicks_by_day('grqSlY');
  // $results = bitly_v3_clicks_by_day(array('grqSlY','dYhyia'));
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

function bitly_v3_bitly_pro_domain($domain) {
  // $results = bitly_v3_bitly_pro_domain('nyti.ms');
  $result = array();
  $url = bitly_api . "bitly_pro_domain?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&domain=" . $domain;
  $output = json_decode(bitly_get_curl($url));
  if (isset($output->{'data'}->{'bitly_pro_domain'})) {
    $result['domain'] = $output->{'data'}->{'domain'};
    $result['bitly_pro_domain'] = $output->{'data'}->{'bitly_pro_domain'};
  }
  return $result;
}

function bitly_v3_lookup($data) {
  // $results = bitly_v3_lookup('http://knowabout.it');
  // $results = bitly_v3_lookup(array('http://knowabout.it','http://blog.botfu.com'));
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
  $url = bitly_api . "lookup?login=" . bitlyLogin . "&apiKey=" . bitlyKey . "&format=json&url=" . $data;
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

function bitly_v3_authenticate($x_login, $x_password) {
  // $results = bitly_v3_authenticate('USER_SUPPLIED_USERNAME','USER_SUPPLIED_PASSWORD');
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

function bitly_v3_info($data) {
  // $results = bitly_v3_info(array('grqSlY','dYhyia'));
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

function bitly_oauth_access_token($code, $redirect) {
  // $results = bitly_oauth_access_token('BITLY_SUPPLIED_CODE', 'URL_BITLY_SHOULD_RETURN_TO');
  $results = array();
  $url = bitly_oauth_access_token . "access_token";
  $params = array();
  $params['client_id'] = bitly_clientid;
  $params['client_secret'] = bitly_secret;
  $params['code'] = $code;
  $params['redirect_uri'] = $redirect;
  $output = json_decode(bitly_post_curl($url, $params));
  $parts = explode('&', $output);
  foreach ($parts as $part) {
    $bits = explode('=', $part);
    $results[$bits[0]] = $bits[1];
  }
  return $results;
}

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

function bitly_get_curl($uri) {
  // make a get call to the bit.ly api
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

function bitly_post_curl($uri, $fields) {
  // make a post call to the bit.ly api
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