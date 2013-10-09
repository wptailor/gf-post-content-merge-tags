<?php
/**
* Gravity Form Merge Tags in your Post Content (when an entry ID is passed)
* http://gravitywiz.com/
* 
* Video:        http://screencast.com/t/g6Y12zOf4
* Description:  Setup your confirmation page (requires GFv1.8) or confirmation URL "Redirect Query String" setting to 
*               include this parameter: 'eid={entry_id}'. You can then use any entry-based merge tag in your post content.
* 
*/
class GWPostContentMergeTags {
    
    public static $_entry = null;
    
    function __construct() {
        
        if( ! class_exists( 'GFForms' ) )
            return;
        
        add_filter( 'the_content', array( $this, 'replace_merge_tags' ) );
        
    }
    
    function replace_merge_tags( $post_content ) {
        
        $entry = $this->get_entry();
        if( ! $entry )
            return $post_content;
        
        $form = GFFormsModel::get_form_meta( $entry['form_id'] );
        $post_content = GFCommon::replace_variables( $post_content, $form, $entry );
        
        return $post_content;
    }
    
    function get_entry() {
        
        if( ! self::$_entry ) {
            
            $entry_id = rgget( 'eid' );
            if( ! $entry_id )
                return false;
            
            $entry = GFFormsModel::get_lead( $entry_id );
            if( empty( $entry ) )
                return false;
                
            self::$_entry = $entry;
                
        }
        
        return self::$_entry;
    }
    
}

new GWPostContentMergeTags();