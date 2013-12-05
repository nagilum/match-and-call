<?php

/**
 * Applies dynamic matching to the request URL and calls a function accordingly.
 *
 * @param array $routes
 *   Array of routes to match against.
 * @param string $base_uri
 *   Base URi of the application if not '/'.
 * @param string $request_uri
 *   Request URi to match against if not the one in $_SERVER.
 * @param array $route_info
 *   The output info, including execution time, of the matched route.
 *
 * @return bool
 *   Whether or not a match has been found.
 *
 * Example usage:
 *   if (!match_and_call(
 *     array(
 *       'about'           => 'about_us',       // Fixed URL.
 *       'blog/%id/$title' => 'show_blog_post'  // Dynamic URL.
 *     )
 *   )) {
 *     echo 'NO MATCH FOUND';
 *   }
 *
 *   function show_blog_post($id, $title) {}
 *
 */
function match_and_call($routes, $base_uri = '/', $request_uri = NULL, &$route_info) {
  if ($request_uri === NULL) {
    $request_uri = $_SERVER['REQUEST_URI'];

    if (strlen($base_uri) > 0 &&
        strlen($request_uri) >= strlen($base_uri) &&
        substr($request_uri, 0, strlen($base_uri)) == $base_uri) {
      $request_uri = substr($request_uri, strlen($base_uri));
    }
  }

  $request_sections    = explode('/', $request_uri);
  $found_match         = FALSE;
  $found_hits          = 0;
  $function_parameters = array();
  $routes_temp         = array();

  foreach ($routes as $route_sections => $function_name) {
    $sections = explode('/', $route_sections);

    $route = array(
      'route_sections'        => array(),
      'function_name'         => $function_name,
      'function_parameters'   => array(),
      'execution_time_start'  => 0,
      'execution_time_end'    => 0,
      'execution_time_length' => 0,
    );

    foreach ($sections as $route_section) {
      if (strlen($route_section) > 0) {
        switch (substr($route_section, 0, 1)) {
          // A dynamic number.
          case '%':
            $route['route_sections'][] = array(
              'type'       => 'number',
              'identifier' => substr($route_section, 1),
            );

            break;

          // A dynamic string.
          case '$':
            $route['route_sections'][] = array(
              'type'       => 'string',
              'identifier' => substr($route_section, 1),
            );

            break;

          // Fixed string.
          default:
            $route['route_sections'][] = array(
              'type'       => 'fixed',
              'identifier' => $route_section,
            );

            break;
        }
      }
    }

    if (count($route['route_sections']) == count($request_sections)) {
      $found_hits = 0;

      for ($i = 0; $i < count($route['route_sections']); $i++) {
        switch ($route['route_sections'][$i]['type']) {
          // Attempt to match as a fixed string.
          case 'fixed':
            if ($route['route_sections'][$i]['identifier'] == $request_sections[$i]) {
              $found_hits++;
            }

            break;

          // Attempt to match as a numeric value.
          case 'number':
            if (is_numeric($request_sections[$i])) {
              $found_hits++;
              $function_parameters[] = $request_sections[$i];
            }

            break;

          // Attempt to match as a string value.
          case 'string':
            if (is_string($request_sections[$i])) {
              $found_hits++;
              $function_parameters[] = $request_sections[$i];
            }

            break;
        }
      }

      if ($found_hits == count($route['route_sections'])) {
        $exec_start = microtime(TRUE);
        $retval     = call_user_func_array($route['function_name'], $function_parameters);
        $exec_end   = microtime(TRUE);

        $route['execution_time_start']  = $exec_start;
        $route['execution_time_end']    = $exec_start;
        $route['execution_time_length'] = (float) $exec_end - (float) $exec_start;

        $found_match = TRUE;

        $route_info = $route;
      }
    }

    $routes_temp[] = $route;
  }

  return $found_match;
}
