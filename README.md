A PHP function that applies dynamic matching to the request URL and calls a function accordingly.

Example:

```php
if (!match_and_call(
      array(
        'about'           => 'about_us',       // Fixed URL.
        'blog/%id/$title' => 'show_blog_post'  // Dynamic URL.
      )
    )) {
  echo 'NO MATCH FOUND';
}

function show_blog_post($id, $title) {}
```
