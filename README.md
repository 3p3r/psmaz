# psmaz

## PHP port of SMAZ text shortening algorithm
Smaz is an algorithm, suitable for compressing short texts or strings. From [the original library](https://github.com/antirez/smaz):
> Smaz is a simple compression library suitable for compressing very short
strings. General purpose compression libraries will build the state needed
for compressing data dynamically, in order to be able to compress every kind
of data. This is a very good idea, but not for a specific problem: compressing
small strings will not work.

### Usage:

Class style:
```php
require_once('psmaz.php');
$smaz_instance = new SMAZ();
//for compressing:
echo $smaz_instance->compress("This is a test string.");
//for decompressing:
echo $smaz_instance->decompress("...");
```
Original function style (compatible with the original library):
```php
require_once('psmaz.php');

smaz_compress("This is a test string");
smaz_decompress("...");
```

### Quick example:
Smaz can be very handy for URL shortening or even obfuscating! Check this out:
```php
require_once('psmaz.php');
$url = "http://google.com";

echo urlencode($url);
//outputs: http%3A%2F%2Fgoogle.com

echo urlencode(smaz_compress($url));
//outputs: C%3B%06%06%3BW%FD

//compressed by 6 characters (26%)
```

#### Credits and other ports:
* Original C library: https://github.com/antirez/smaz
* Javascript (Node.js) port: https://github.com/personalcomputer/smaz.js
* Java port: https://github.com/icedrake/jsmaz
