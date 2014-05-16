<?php
/**
* Appends the "eid" paramter to the redirect URL of any form ID in the $eid_enabled_forms array (defined inside the
* function). The "eid" paramter is then used by the "GWPostContentMergeTags" class to allow the use of entry merge
* tags in the post content.
*/
add_filter( 'gform_confirmation', 'ounce_add_eid_parameter', 11, 3 );
function ounce_add_eid_parameter( $confirmation, $form, $entry ) {

    if( ! isset( $confirmation['redirect'] ) )
        return $confirmation;

    $confirmation['redirect'] = add_query_arg( array( 'eid' => $entry['id'] ), $confirmation['redirect'] );

    return $confirmation;
}