
This is a very simple PHP library for interacting with the v3 bit.ly api (supports OAuth endpoints). It should support all basic endpoints that are found/available via http://dev.bitly.com/api.html.

==============
REQUIREMENTS:
==============

PHP, Curl, JSON

======
USAGE:
======

1. Simply download the bitly.php file and include it in your project directory

2. Make sure to include the file whenever you want to access the bitly api functionality... include_once('bitly.php');

3. Set up an associative array with the required parameters for the endpoint you want to access.

4. Make the bitly_get or bitly_post call, passing in the endpoint and the parameters as defined via the bit.ly API documentation ( http://dev.bitly.com/api.html ).

=============
EXAMPLES:
=============

```php
include_once('bitly.php');
$params = array();
$params['access_token'] = 'THE_TOKEN_SET_VIA_OAUTH';
$params['longUrl'] = 'http://knowabout.it';
$params['domain'] = 'j.mp';
$results = bitly_get('shorten', $params);
var_dump($results);
```

```php
$params = array();
$params['access_token'] = 'THE_TOKEN_SET_VIA_OAUTH';
$params['url'] = 'http://knowabout.it';
$results = bitly_get('link/lookup', $params);
var_dump($results);
```

a slightly more complex example with complex params (simply pass a third param of true when dealing with complex params):

```php
$params = array();
$params['access_token'] = 'THE_TOKEN_SET_VIA_OAUTH';
$params['hash'] = array('dYhyia','dYhyia','abc123');
$results = bitly_get('expand', $params, true);
var_dump($results);
```


You can find more detailed examples in the test.php file within this repo.

=============
SPECIAL NOTE:
=============

To use the new OAuth endpoints, you must first obtain an access token for a user. You do this by passing the user off to bit.ly to approve your apps access to their account...and then you use the return code along with the bitly_oauth_access_token method to obtain the actual bitly access token:

1. Present the user with a link as such:

https://bit.ly/oauth/authorize?client_id=YOUR_BITLY_CLIENT_ID&redirect_uri=THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER

2. a code ($_REQUEST['code']) will be supplied as a param to the url Bit.ly redirects to. So you can then execute:

```php
$results = bitly_oauth_access_token($_REQUEST['code'],
  'THE_URL_YOU_WANT_BITLY_TO_REDIRECT_TO_WHEN_APP_IS_APPROVED_BY_USER',
  'YOUR_BITLY_APP_CLIENT_ID',
  'YOUR_BITLY_APP_CLIENT_SECRET');
```

3. If everything goes correctly, you should now have a $results['access_token'] value that you can use with the oauth requests for that user.

=======
CONTACT:
=======

As always, if you've got any questions, comments, or concerns about
anything you find here, please feel free to drop me an email at info@falicon.com or find me on Twitter @falicon

=======
License:
=======

Copyright 2015 Kevin Marshall.

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

