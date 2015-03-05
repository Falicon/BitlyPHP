Bitly API v3 PHP Class
This is a PHP class library for interacting with the v3 bit.ly api (only deals with JSON format, but supports new OAuth endpoints)

==============
REQUIREMENTS:
==============

PHP, Curl, JSON


======
TODO:
======
- More formats (I don't like JSON)<br/>
- More features per users recommendations<br/>

Send your wishes and recommendations to my Email.<br/>


========
EXAMPLES
========
There are two example files which should get you know how it works.<br/>
Also it should teach you how to construct your code to use this class.<br/>


======
USAGE:
======

1. Simply download the bitly.class.php file and include it in your project directory<br/>

2. Now you have to define class, there are two ways:<br/>
   - Method 1: Just before inclusion of bitly.class.php put this line: define("bitlyPHP_init", "bitly"); <br/>
   - Method 2: $bitly = new PHPBitlyAPIv3;<br/>
    !WARNING! Only ONE method should be used!<br/>

3. To set own login and API keys - In your project make following calls (Before use):<br/>
   $bitly->setAPI("API", "YOUR_API_KEY");<br/>
   $bitly->setAPI("LOGIN", "YOUR_BITLY_USERNAME");<br/>
   $bitly->setAPI("CLIENT->ID", "YOUR_CLIENT_ID");<br/>
   $bitly->setAPI("CLIENT->SECRET", "YOUR_CLIENT_SECRET");<br/>
   <br/>
<br/>
4. Make sure to include the file whenever you want to access the bitly api functionality... include_once('bitly.class.php');<br/>
   - Remember: If you want to use Method 1 of initialization you must define it just before inclusion<br/>

5. Use any of the functions as such:<br/>

$results = $bitly->shorten('http://knowabout.it', 'USERS_ACCESS_TOKEN', 'j.mp');<br/>

$results = $bitly->expand('dYhyia');<br/>

$results = $bitly->expand(array('http://bit.ly/dYhyia','http://j.mp/dYhyia'));<br/>

$result = $bitly->validate('USERS_LOGIN','USERS_APIKEY');<br/>

$results = $bitly->clicks('dYhyia');<br/>

$results = $bitly->clicks(array('dYhyia','http://bit.ly/dYhyia'));<br/>

$results = $bitly->referrers('grqSlY');<br/>

$results = $bitly->countries('grqSlY');<br/>

$results = $bitly->clicks_by_minute(array('grqSlY','dYhyia'));<br/>

$results = $bitly->clicks_by_day(array('grqSlY','dYhyia'));<br/>

$results = $bitly->bitly_pro_domain('nyti.ms');<br/>

$results = $bitly->lookup('http://knowabout.it');<br/>

$results = $bitly->lookup(array('http://knowabout.it','http://blog.botfu.com'));<br/>

$results = $bitly->authenticate('USERS_LOGIN','USERS_PASSWORD');<br/>

$results = $bitly->info(array('grqSlY','dYhyia'));<br/>

$results = $bitly->oauth_access_token('CODE_ASSIGNED_BY_BITLY',<br/> 'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER');<br/>

$results = $bitly->user_clicks('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->user_referrers('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->user_countries('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->user_realtime_links('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->highvalue('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->search('USERS_ACCESS_TOKEN', 'awesome');<br/>

$results = $bitly->realtime_bursting_phrases('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->realtime_hot_phrases('USERS_ACCESS_TOKEN');<br/>

$results = $bitly->realtime_clickrate('USERS_ACCESS_TOKEN', 'awesome');<br/>

$results = $bitly->link_info('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

$results = $bitly->link_content('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

$results = $bitly->link_category('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

$results = $bitly->link_social('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

$results = $bitly->link_location('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

$results = $bitly->link_language('USERS_ACCESS_TOKEN', 'http://bit.ly/S4qgbT');<br/>

=============
SPECIAL NOTE:
==============

To use the new OAuth endpoints, you must first obtain an access token for a user. You do this by passing the user off to bit.ly to approve your apps access to their account...and then you use the return code along with the<br/> bitly_oauth_access_token method to obtain the actual bitly access token:<br/>

1. Present the user with a link as such <a href=" https://bit.ly/oauth/authorize?client_id=<?= bitly_clientid ?>&redirect_uri=THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER">Authorize giftabit</a>

2. a code ($_REQUEST['code']) will be supplied as a param to the url Bit.ly redirects to...so you can then execute $results = bitly_oauth_access_token($_REQUEST['code'], 'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER');

3. If everything goes correctly, you should now have a $results['access_token'] value that you can use with the oauth requests for that user.

===============
SPECIAL THANKS:
===============

Kevin Marshall (https://github.com/Falicon) - Original author, for great job he did.<br/>
Robin Monks (https://github.com/mozillamonks) - for great additional documentation and general suggestions/improvements.<br/>

========
CONTACT:
========

As always, if you've got any questions, comments, or concerns about<br/>
anything you find here, please feel free contact me at <br/>
email: xzero@elite7hackers.net or xzero707@gmail.com<br/>
Twitter: @xZeroOfficial -> https://twitter.com/xZeroOfficial<br/>
Facebook: https://www.facebook.com/Aleks.xZero<br/>

Original author:<br/>
As always, if you've got any questions, comments, or concerns about<br/>
anything you find here, please feel free to drop me an email at info@falicon.com or find me on Twitter @falicon<br/>

========
License:
========

Object oriented version - Copyright 2015 xZero.<br/>
Original work - Copyright 2010 Kevin Marshall<br/>

This program is free software: you can redistribute it and/or modify<br/>
it under the terms of the GNU General Public License as published by<br/>
the Free Software Foundation, either version 3 of the License, or<br/>
(at your option) any later version.<br/>

This program is distributed in the hope that it will be useful,<br/>
but WITHOUT ANY WARRANTY; without even the implied warranty of<br/>
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the<br/>
GNU General Public License for more details.<br/>

You should have received a copy of the GNU General Public License<br/>
along with this program.  If not, see <http://www.gnu.org/licenses/>.<br/>

