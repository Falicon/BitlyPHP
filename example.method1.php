<?PHP
/**
 * @file
 * example.method1.php
 * PHP library for interacting with the v3 bit.ly api (only deals with
 * JSON format, but supports new OAuth endpoints).
 * REQUIREMENTS: PHP, Curl, JSON
 * 
 * @link https://github.com/Falicon/BitlyPHP (Original)
 * @link https://github.com/xZero707/BitlyPHP (This)
 * @author Kevin Marshall <info@falicon.com>
 * @author Robin Monks <devlinks@gmail.com>
 * @author xZero <xzero@elite7hackers.net> (MOD)
 *  ^ Modifications, optimizations and object oriented adaptation (class)
 */

define("bitlyPHP_init", "bitly"); // Second parameter defines instance holder.  This will start new instance. Observe that it is set before inclusion!
require_once "bitly.class.php";


// Set keys and tokens
$bitly->setAPI("API", "YOUR_API_KEY");
$bitly->setAPI("LOGIN", "YOUR_BITLY_USERNAME");
$bitly->setAPI("CLIENT->ID", "YOUR_CLIENT_ID");
$bitly->setAPI("CLIENT->SECRET", "YOUR_CLIENT_SECRET");

// MAIN calls
$results[] = $bitly->shorten('http://knowabout.it', 'USERS_ACCESS_TOKEN', 'j.mp');
$results[] = $bitly->expand('dYhyia');
$results[] = $bitly->expand(array('http://bit.ly/dYhyia','http://j.mp/dYhyia'));
$results[] = $bitly->clicks('dYhyia');
$results[] = $bitly->clicks(array('dYhyia','http://bit.ly/dYhyia'));
$results[] = $bitly->referrers('grqSlY');
$results[] = $bitly->countries('grqSlY');
$results[] = $bitly->clicks_by_minute(array('grqSlY','dYhyia'));
$results[] = $bitly->clicks_by_day(array('grqSlY','dYhyia'));
$results[] = $bitly->bitly_pro_domain('nyti.ms');
$results[] = $bitly->lookup('http://knowabout.it');
$results[] = $bitly->lookup(array('http://knowabout.it','http://blog.botfu.com'));
$results[] = $bitly->authenticate('USERS_LOGIN','USERS_PASSWORD');
$results[] = $bitly->info(array('grqSlY','dYhyia'));
$results[] = $bitly->oauth_access_token('CODE_ASSIGNED_BY_BITLY', 'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER');
$results[] = $bitly->user_clicks('USERS_ACCESS_TOKEN');
$results[] = $bitly->user_referrers('USERS_ACCESS_TOKEN');
$results[] = $bitly->user_countries('USERS_ACCESS_TOKEN');
$results[] = $bitly->user_realtime_links('USERS_ACCESS_TOKEN');
$results[] = $bitly->highvalue('USERS_ACCESS_TOKEN');
$results[] = $bitly->search('USERS_ACCESS_TOKEN', 'awesome');
$results[] = $bitly->realtime_bursting_phrases('USERS_ACCESS_TOKEN');
$results[] = $bitly->realtime_hot_phrases('USERS_ACCESS_TOKEN');
$results[] = $bitly->realtime_clickrate('USERS_ACCESS_TOKEN', 'awesome');
$results[] = $bitly->link_info('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$results[] = $bitly->link_content('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$results[] = $bitly->link_category('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$results[] = $bitly->link_social('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$results[] = $bitly->link_location('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$results[] = $bitly->link_language('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');
$result = $bitly->validate('USERS_LOGIN','USERS_APIKEY');

// NEXT PART IS FOR EXAMPLE PURPOSE ONLY
header("Content-Type:text/plain");
ECHO " ============= RESULTS =============\n";
print_r($results);
ECHO " ============= VALIDATE =============\n{$result}";




