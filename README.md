# MimeList
Guesses file mime type from it's extension basing on [apache.org](http://apache.org) mime type list. 

### Usage
```php
use \MimeList;

// To use MimeList create it's instance defining the nesessity to cache data
$list = new MimeList(MimeList::USE_CACHE, 'mimelist.php');

// And now you can guess file mime type
echo $list->guess('jpg');
```

