<?php
/**
* Gravity Wiz // Gravity Forms Post Content Merge Tags 
* 
* Adds support for using Gravity Form merge tags in your post content. This functionality requires that the entry ID is
* is passed to the post via the "eid" parameter.
* 
* Setup your confirmation page (requires GFv1.8) or confirmation URL "Redirect Query String" setting to 
* include this parameter: 'eid={entry_id}'. You can then use any entry-based merge tag in your post content.
* 
* @version	 1.0
* @author    David Smith <david@gravitywiz.com>
* @license   GPL-2.0+
* @link      http://gravitywiz.com/...
* @video     http://screencast.com/t/g6Y12zOf4
* @copyright 2014 Gravity Wiz
*/
class GW_Post_Content_Merge_Tags {
    
    public static $_entry = null;

    private static $instance = null;

    public static function get_instance( $args = array() ) {

        if( self::$instance == null )
            self::$instance = new self( $args );

        return self::$instance;
    }

    function __construct( $args ) {
        
        if( ! class_exists( 'GFForms' ) )
            return;

        $this->_args = wp_parse_args( $args, array(
            'auto_append_eid' => false // true, false or array of form IDs
        ) );

        add_filter( 'the_content', array( $this, 'replace_merge_tags' ) );

        if( ! empty( $this->_args['auto_append_eid'] ) )
            add_filter( 'gform_confirmation', array( $this, 'append_eid_parameter' ), 20, 3 );

    }
    
    function replace_merge_tags( $post_content ) {
        
        $entry = $this->get_entry();
        if( ! $entry )
            return $post_content;
        
        $form = GFFormsModel::get_form_meta( $entry['form_id'] );

        $post_content = $this->replace_field_label_merge_tags( $post_content, $form );
        $post_content = GFCommon::replace_variables( $post_content, $form, $entry, false, false, false );
        
        return $post_content;
    }

    function replace_field_label_merge_tags( $text, $form ) {

        preg_match_all( '/{([^:]+)}/', $text, $matches, PREG_SET_ORDER );
        if( empty( $matches ) )
            return $text;

        foreach( $matches as $match ) {

            list( $search, $field_label ) = $match;
            foreach( $form['fields'] as $field ) {

                $matches_admin_label = rgar( $field, 'adminLabel' ) == $field_label;
                $matches_field_label = GFFormsModel::get_label( $field ) == $field_label;
                if( ! $matches_admin_label && ! $matches_field_label )
                    continue;

                $replace = sprintf( '{%s:%d}', $field_label, $field['id'] );
                $text = str_replace( $search, $replace, $text );

                break;
            }

        }

        return $text;
    }

    function append_eid_parameter( $confirmation, $form, $entry ) {

        if( ! $this->is_auto_eid_enabled( $form ) || ! isset( $confirmation['redirect'] ) )
            return $confirmation;

        $confirmation['redirect'] = add_query_arg( array( 'eid' => $entry['id'] ), $confirmation['redirect'] );

        return $confirmation;
    }

    function get_entry() {
        
        if( ! self::$_entry ) {
            
            $entry_id = $this->get_entry_id();
            if( ! $entry_id )
                return false;
            
            $entry = GFFormsModel::get_lead( $entry_id );
            if( empty( $entry ) )
                return false;
                
            self::$_entry = $entry;
                
        }
        
        return self::$_entry;
    }

    function get_entry_id() {

        $entry_id = rgget( 'eid' );
        if( $entry_id )
            return $entry_id;

        $post = get_post();
        if( $post )
            $entry_id = get_post_meta( $post->ID, '_gform-entry-id', true );

        if( $entry_id )
            return $entry_id;

        return false;
    }

    function is_auto_eid_enabled( $form ) {

        $auto_append_eid = $this->_args['auto_append_eid'];

        if( is_bool( $auto_append_eid ) && $auto_append_eid === true )
            return true;

        if( is_array( $auto_append_eid ) && in_array( $form['id'], $auto_append_eid ) )
            return true;

        return false;
    }
    
}

function gw_post_content_merge_tags( $args = array() ) {
    return GW_Post_Content_Merge_Tags::get_instance( $args );
}

gw_post_content_merge_tags( array(
    'auto_append_eid' => true
) );