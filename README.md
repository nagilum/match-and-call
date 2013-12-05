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

You can also send in the base URL, request URi, as well as a return array for information about the function call.

```php
if (!match_and_call(
      array(
        'about'           => 'about_us',       // Fixed URL.
        'blog/%id/$title' => 'show_blog_post'  // Dynamic URL.
      ),
      '/~my_user/awesome_sub_dir/',
      '/my/very/own/request/uri',
      $match_info
    )) {
    echo 'NO MATCH FOUND';
  }

  echo '<pre>' . print_r($match_info, TRUE) . '</pre>';
```

Which will show something like this:

```
Array
(
    [route_sections] => Array
        (
            [0] => Array
                (
                    [type] => fixed
                    [identifier] => blog
                )

            [1] => Array
                (
                    [type] => number
                    [identifier] => id
                )

            [2] => Array
                (
                    [type] => string
                    [identifier] => title
                )

        )

    [function_name] => show_blog_post
    [function_parameters] => Array
        (
            [0] => 123
            [1] => boom
        )

    [execution_time_start] => 1386255146.1502
    [execution_time_end] => 1386255146.1502
    [execution_time_length] => 1.0013580322266E-5
)
```
