if( typeof jQuery == 'function' ){
  /** Set the onload listener */
  jQuery( document ).ready( function() {
    "use strict";
    var $ = jQuery;
    /** Listen for the click even for toggling advanced options */
    $( '#rly_advanced_options_link' ).click( function( e ){
      $( '#rly_advanced_options_link' ).hide();
      $( '#rly_advanced_options' ).show();
    } );
  } );
}