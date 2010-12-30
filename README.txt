
This is a very simple PHP library for interacting with the v3 bit.ly api (only deals with JSON format, but supports new OAuth endpoints)

==============
REQUIREMENTS:
==============

PHP, Curl, JSON

======
USAGE:
======

1. Simply download the bitly.php file and include it in your project directory

2. Edit the first few lines of the file to use your own Bitly login and keys

3. Make sure to include the file whenever you want to access the bitly api functionality... include_once('bitly.php');

4. Use any of the functions as such:

$results = bitly_v3_shorten('http://knowabout.it', 'j.mp');

$results = bitly_v3_expand('dYhyia');

$results = bitly_v3_expand(array('http://bit.ly/dYhyia','http://j.mp/dYhyia'));

$result = bitly_v3_validate('USERS_LOGIN','USERS_APIKEY');

$results = bitly_v3_clicks('dYhyia');

$results = bitly_v3_clicks(array('dYhyia','http://bit.ly/dYhyia'));

$results = bitly_v3_referrers('grqSlY');

$results = bitly_v3_countries('grqSlY');

$results = bitly_v3_clicks_by_minute(array('grqSlY','dYhyia'));

$results = bitly_v3_clicks_by_day(array('grqSlY','dYhyia'));

$results = bitly_v3_bitly_pro_domain('nyti.ms');

$results = bitly_v3_lookup('http://knowabout.it');

$results = bitly_v3_lookup(array('http://knowabout.it','http://blog.botfu.com'));

$results = bitly_v3_authenticate('USERS_LOGIN','USERS_PASSWORD');

$results = bitly_v3_info(array('grqSlY','dYhyia'));

$results = bitly_oauth_access_token('CODE_ASSIGNED_BY_BITLY', 'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER');

$results = bitly_v3_user_clicks('USERS_ACCESS_TOKEN');

$results = bitly_v3_user_referrers('USERS_ACCESS_TOKEN');

$results = bitly_v3_user_countries('USERS_ACCESS_TOKEN');

$results = bitly_v3_user_realtime_links('USERS_ACCESS_TOKEN');

=============
SPECIAL NOTE:
=============

To use the new OAuth endpoints, you must first obtain an access token for a user. You do this by passing the user off to bit.ly to approve your apps access to their account...and then you use the return code along with the bitly_oauth_access_token method to obtain the actual bitly access token:

1. Present the user with a link as such <a href=" https://bit.ly/oauth/authorize?client_id=<?= bitly_clientid ?>&redirect_uri=THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER">Authorize giftabit</a>

2. a code ($_REQUEST['code']) will be supplied as a param to the url Bit.ly redirects to...so you can then execute $results = bitly_oauth_access_token($_REQUEST['code'], 'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER');

3. If everything goes correctly, you should now have a $results['access_token'] value that you can use with the oauth requests for that user.

==============
SPECIAL THANKS:
==============

Robin Monks (https://github.com/mozillamonks) - for great additional documentation and general suggestions/improvements.

=======
CONTACT:
=======

As always, if you've got any questions, comments, or concerns about
anything you find here, please feel free to drop me an email at info@falicon.com or find me on Twitter @falicon

=======
License:
=======

Copyright 2010 Kevin Marshall.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

