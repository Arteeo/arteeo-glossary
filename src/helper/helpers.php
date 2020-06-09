<?php
/**
 * Helpers
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Take current url and convert it by changing the provided get parameter
 *
 * @param string $parameters array of values
 *                           $key
 *                              The parameter which should be adjusted
 *                           $parameters[$key]
 *                              the value which should be set for the
 *                              parameter if set to string 'null' 
 * 															the parameter will be removed
 * @return string the resulting url after adjusting the parameters
 */
function generate_url($parameters){
	global $glossary_page_id;

  $url = '?page='.$glossary_page_id;

	foreach($_GET as $key => $value) {
		if (isset($parameters[$key])) {
			if($parameters[$key] != 'null') {
				$url = $url.'&'.$key.'='.$parameters[$key];
      }
      $parameters[$key] = null;
		} else if ($key != 'page' && $key != 'message' && $key != 'message_type'){
			$url = $url.'&'.$key.'='.$value;
		}
	}

  foreach ($parameters as $key => $parameter) {
    if ($parameter != null && $parameter != 'null') {
      $url = $url.'&'.$key.'='.$parameter;
    }
  }

	return $url;
}