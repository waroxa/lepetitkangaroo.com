<?php

if ( $value ):
    $value = intval( $value );
    $product = QuickCal_WC_Product::get( $value );
    
    if(!$product || !isset($product->data)){
        //there will be no data if the product has been trashed or removed
        return false;
    }
    ?><option value="<?php echo htmlentities($value, ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"><?php echo esc_html($product->title); ?></option><?php
endif;