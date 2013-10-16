/**
 * Resize.ly jQuery Plugin
 * jQuery plugin that makes your images Retina and responsive when coupled
 * with the dynamic image delivery service Resize.ly
 * @copyright 2013 Resize.ly
 * @link https://github.com/UsabilityDynamics/resizely-jquery
 * @includes imagesLoaded v3.0.4
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * imagesLoaded PACKAGED v3.0.4
 * JavaScript is all like "You images are done yet or what?"
 * @link http://desandro.github.io/imagesloaded/
 * @author David DeSandro
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
(function(){function e(){}function n(e,t){var n=e.length;while(n--){if(e[n].listener===t){return n}}return-1}var t=e.prototype;t.getListeners=function(t){var n=this._getEvents();var r;var i;if(typeof t==="object"){r={};for(i in n){if(n.hasOwnProperty(i)&&t.test(i)){r[i]=n[i]}}}else{r=n[t]||(n[t]=[])}return r};t.flattenListeners=function(t){var n=[];var r;for(r=0;r<t.length;r+=1){n.push(t[r].listener)}return n};t.getListenersAsObject=function(t){var n=this.getListeners(t);var r;if(n instanceof Array){r={};r[t]=n}return r||n};t.addListener=function(t,r){var i=this.getListenersAsObject(t);var s=typeof r==="object";var o;for(o in i){if(i.hasOwnProperty(o)&&n(i[o],r)===-1){i[o].push(s?r:{listener:r,once:false})}}return this};t.on=t.addListener;t.addOnceListener=function(t,n){return this.addListener(t,{listener:n,once:true})};t.once=t.addOnceListener;t.defineEvent=function(t){this.getListeners(t);return this};t.defineEvents=function(t){for(var n=0;n<t.length;n+=1){this.defineEvent(t[n])}return this};t.removeListener=function(t,r){var i=this.getListenersAsObject(t);var s;var o;for(o in i){if(i.hasOwnProperty(o)){s=n(i[o],r);if(s!==-1){i[o].splice(s,1)}}}return this};t.off=t.removeListener;t.addListeners=function(t,n){return this.manipulateListeners(false,t,n)};t.removeListeners=function(t,n){return this.manipulateListeners(true,t,n)};t.manipulateListeners=function(t,n,r){var i;var s;var o=t?this.removeListener:this.addListener;var u=t?this.removeListeners:this.addListeners;if(typeof n==="object"&&!(n instanceof RegExp)){for(i in n){if(n.hasOwnProperty(i)&&(s=n[i])){if(typeof s==="function"){o.call(this,i,s)}else{u.call(this,i,s)}}}}else{i=r.length;while(i--){o.call(this,n,r[i])}}return this};t.removeEvent=function(t){var n=typeof t;var r=this._getEvents();var i;if(n==="string"){delete r[t]}else{if(n==="object"){for(i in r){if(r.hasOwnProperty(i)&&t.test(i)){delete r[i]}}}else{delete this._events}}return this};t.emitEvent=function(t,n){var r=this.getListenersAsObject(t);var i;var s;var o;var u;for(o in r){if(r.hasOwnProperty(o)){s=r[o].length;while(s--){i=r[o][s];u=i.listener.apply(this,n||[]);if(u===this._getOnceReturnValue()||i.once===true){this.removeListener(t,r[o][s].listener)}}}}return this};t.trigger=t.emitEvent;t.emit=function(t){var n=Array.prototype.slice.call(arguments,1);return this.emitEvent(t,n)};t.setOnceReturnValue=function(t){this._onceReturnValue=t;return this};t._getOnceReturnValue=function(){if(this.hasOwnProperty("_onceReturnValue")){return this._onceReturnValue}else{return true}};t._getEvents=function(){return this._events||(this._events={})};if(typeof module!=="undefined"&&module.exports){module.exports=e}else{this.EventEmitter=e}}).call(this);(function(e){var t=document.documentElement;var n=function(){};if(t.addEventListener){n=function(e,t,n){e.addEventListener(t,n,false)}}else{if(t.attachEvent){n=function(t,n,r){t[n+r]=r.handleEvent?function(){var t=e.event;t.target=t.target||t.srcElement;r.handleEvent.call(r,t)}:function(){var n=e.event;n.target=n.target||n.srcElement;r.call(t,n)};t.attachEvent("on"+n,t[n+r])}}}var r=function(){};if(t.removeEventListener){r=function(e,t,n){e.removeEventListener(t,n,false)}}else{if(t.detachEvent){r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(r){e[t+n]=undefined}}}}var i={bind:n,unbind:r};e.eventie=i})(this);(function(e){function i(e,t){for(var n in t){e[n]=t[n]}return e}function o(e){return s.call(e)==="[object Array]"}function u(e){var t=[];if(o(e)){t=e}else{if(typeof e.length==="number"){for(var n=0,r=e.length;n<r;n++){t.push(e[n])}}else{t.push(e)}}return t}function a(e,s){function o(e,n,r){if(!(this instanceof o)){return new o(e,n)}if(typeof e==="string"){e=document.querySelectorAll(e)}this.elements=u(e);this.options=i({},this.options);if(typeof n==="function"){r=n}else{i(this.options,n)}if(r){this.on("always",r)}this.getImages();if(t){this.jqDeferred=new t.Deferred}var s=this;setTimeout(function(){s.check()})}function f(e){this.img=e}o.prototype=new e;o.prototype.options={};o.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;e<t;e++){var n=this.elements[e];if(n.nodeName==="IMG"){this.addImage(n)}var r=n.querySelectorAll("img");for(var i=0,s=r.length;i<s;i++){var o=r[i];this.addImage(o)}}};o.prototype.addImage=function(e){var t=new f(e);this.images.push(t)};o.prototype.check=function(){function s(s,o){if(e.options.debug&&r){n.log("confirm",s,o)}e.progress(s);t++;if(t===i){e.complete()}return true}var e=this;var t=0;var i=this.images.length;this.hasAnyBroken=false;if(!i){this.complete();return}for(var o=0;o<i;o++){var u=this.images[o];u.on("confirm",s);u.check()}};o.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e);if(t.jqDeferred){t.jqDeferred.notify(t,e)}})};o.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=true;var t=this;setTimeout(function(){t.emit(e,t);t.emit("always",t);if(t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})};if(t){t.fn.imagesLoaded=function(e,n){var r=new o(this,e,n);return r.jqDeferred.promise(t(this))}}var a={};f.prototype=new e;f.prototype.check=function(){var e=a[this.img.src];if(e){this.useCached(e);return}a[this.img.src]=this;if(this.img.complete&&this.img.naturalWidth!==undefined){this.confirm(this.img.naturalWidth!==0,"naturalWidth");return}var t=this.proxyImage=new Image;s.bind(t,"load",this);s.bind(t,"error",this);t.src=this.img.src};f.prototype.useCached=function(e){if(e.isConfirmed){this.confirm(e.isLoaded,"cached was confirmed")}else{var t=this;e.on("confirm",function(e){t.confirm(e.isLoaded,"cache emitted confirmed");return true})}};f.prototype.confirm=function(e,t){this.isConfirmed=true;this.isLoaded=e;this.emit("confirm",this,t)};f.prototype.handleEvent=function(e){var t="on"+e.type;if(this[t]){this[t](e)}};f.prototype.onload=function(){this.confirm(true,"onload");this.unbindProxyEvents()};f.prototype.onerror=function(){this.confirm(false,"onerror");this.unbindProxyEvents()};f.prototype.unbindProxyEvents=function(){s.unbind(this.proxyImage,"load",this);s.unbind(this.proxyImage,"error",this)};return o}var t=e.jQuery;var n=e.console;var r=typeof n!=="undefined";var s=Object.prototype.toString;e.imagesLoaded=a(e.EventEmitter,e.eventie)})(window)

if( typeof jQuery == 'function' ){
  ( function( $ ){
    'use strict';

    /**
     * Setup our settings variable
     *
     */
    var s = {
      sw: screen.width, /** Override to set a manual screen width */
      sh: screen.height, /** Override to set a manual screen height */
      d: 'resize.ly', /** Change the domain that resize.ly is using (for debugging) */
      dpr: ( 'devicePixelRatio' in window ? devicePixelRatio : '1' ), /** Override to set a manual DPR */
      dbg: false, /** Enable debug mode to see extra console messages */
      bp: 250, /** Set the breakpoints for images */
      minbp: 500, /** Set the minimum breakpoint for images */
      fp: null, /** Try to force premium status on an image */
      fu: null, /** Force Resize.ly to rerender the image on each request */
      fw: null /** Force Resize.ly to watermark the image on render */
    },

    /**
     *
     * Setup our methods
     */
    f = {
      /**
       * Base64 Encoding Method
       * @license Public Domain
       * Based on public domain code by Tyler Akins <http://rumkin.com/>
       * Original code at http://rumkin.com/tools/compression/base64.php
       */
      base64_encode: function( data ){
        var out = "", c1, c2, c3, e1, e2, e3, e4, i,
          tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789*!~";
        for( i = 0; i < data.length; ){
          c1 = data.charCodeAt( i++ );
          c2 = data.charCodeAt( i++ );
          c3 = data.charCodeAt( i++ );
          e1 = c1 >> 2;
          e2 = ( ( c1 & 3 ) << 4 ) + ( c2 >> 4 );
          e3 = ( ( c2 & 15 ) << 2 ) + ( c3 >> 6 );
          e4 = c3 & 63;
          if( isNaN( c2 ) ){
            e3 = e4 = 64;
          }else if( isNaN( c3 ) ){
            e4 = 64;
          }
          out += tab.charAt( e1 ) + tab.charAt( e2 ) + tab.charAt( e3 ) + tab.charAt( e4 );
        }
        return out;
      },

      /**
       * RC4 symmetric cipher encryption/decryption
       * https://gist.github.com/2185197
       * @license Public Domain
       * @param {string} key - secret key for encryption/decryption
       * @param {string} str - string to be encrypted/decrypted
       * @return {string}
       */
      rc4: function( key, str ){
        var i, y, s = [], j = 0, x, res = '';
        for( i = 0; i < 256; i++ ){
          s[ i ] = i;
        }
        for( i = 0; i < 256; i++ ){
          j = ( j + s[ i ] + key.charCodeAt( i % key.length ) ) % 256;
          x = s[ i ];
          s[ i ] = s[ j ];
          s[ j ] = x;
        }
        i = 0;
        j = 0;
        for( y = 0; y < str.length; y++ ){
          i = ( i + 1 ) % 256;
          j = ( j + s[ i ] ) % 256;
          x = s[ i ];
          s[ i ] = s[ j ];
          s[ j ] = x;
          res += String.fromCharCode( str.charCodeAt( y ) ^ s[ ( s[ i ] + s[ j ] ) % 256 ] );
        }
        return res;
      },

      /**
       * This function takes the '_img' attribute for all images and uses it to call out to resize.ly to properly
       * size the other images
       * @param {int} i Counter
       * @param {object} e Image element as discovered by jQuery
       */
      changeSrc: function( i, e ){
        /** Init Vars */
        var $e = $( e ),
          src = $e.attr( 'data-src' ),
          x = s.sw + 'x' + s.sh + ',' + s.dpr,
          o = window.location.protocol + '//' + window.location.host,
          p = window.location.pathname,
          newSrc,
          ew = 0,
          eh = 0,
          dw = $e.attr( 'data-width' ),
          dh = $e.attr( 'data-height' );
        /** We can't find a width or a height, we should replace the image to find its width */
        var transparent_img = window.location.protocol + '//' + s.d + '/img/transparent_dot.png';
        if( s.dbg ){
          transparent_img = window.location.protocol + '//' + s.d + '/img/transparent_red_dot.png';
        }
        /** Listen for the image loaded event */
        imagesLoaded( e, function(){
          /** If our image is the transparent dot, we should just bail */
          if( $e.attr( 'src' ) != transparent_img ){
            return;
          }
          /** Get our calculated widths */
          ew = $e.width(), eh = $e.height();
          /** Debug */
          if( s.dbg ){
            console.log( '==== Image: ' + $e.attr( 'data-src' ) );
            console.log( 'Calculated dimensions: ' + ew + ' x ' + eh + ' on ' + x );
          }
          /** If the height and width are only 1px, then we know that we need to try setting the width inline */
          if( ( ew == 1 && eh == 1 ) || ( !ew && !eh ) ){
            if( s.dbg ){
              console.log( 'Could not determine dimensions. Setting manual width.' );
            }
            $e.attr( '_style', $e.attr( 'style' ) );
            $e.attr( 'style', ( typeof $e.attr( '_style' ) != 'undefined' ? $e.attr( '_style' ).toString() : '' ) + 'width: 100% !important;' );
            /** We do one final assignment, and that's it */
            ew = $e.width();
            /** Now restore it */
            $e.attr( 'style', ( typeof $e.attr( '_style' ) == 'undefined' ? '' : $e.attr( '_style' ) ) );
            $e.removeAttr( '_style' );
            /** Get rid of our height determination */
            eh = 0;
          }
          /** Ok, if the height and width are equal, change the width to some arbitrary number between 10 and 100 */
          if( ew == eh ){
            /** Backup the width, set the width, and check to see if width and height are still equal */
            $e.attr( '_style', $e.attr( 'style' ) );
            $e.attr( 'style', ( typeof $e.attr( '_style' ) != 'undefined' ? $e.attr( '_style' ).toString() : '' ) + 'width: ' + Math.floor( Math.random() * ( 100 - 10 + 1 ) + 10 ).toString() + 'px !important;' );
            /** Setup our test variables */
            var tw = $e.width(), th = $e.height();
            /** Restore the old widths */
            $e.attr( 'style', ( typeof $e.attr( '_style' ) == 'undefined' ? '' : $e.attr( '_style' ) ) );
            $e.removeAttr( '_style' );
            if( tw == th ){
              /** Ok, so they're equal - this is probably an auto-height element, however, check defined styles or attributes first */
              if( !$e.attr( 'height' ) ){
                eh = 0;
              }
            }
          }
          /** Ok, now we should check our breakpoints and see if we should be using them */
          if( ew >= s.minbp ){
            /** Ok, round up to the nearest breakpoint */
            var oew = ew;
            ew = ew - ( ew % s.bp ) + s.bp;
            /** Now if we have a height, calculate the new height */
            if( eh ){
              eh = Math.round( eh * ( ew / oew ) );
            }
          }
          /** Ok, lets see if we have data-width or data-height defined */
          if( dw ){
            if( s.dbg ){
              console.log( 'Manual dimensions found for width.' );
            }
            ew = dw;
          }
          if( dh ){
            if( s.dbg ){
              console.log( 'Manual dimensions found for height.' );
            }
            eh = dh;
          }
          /** Debug */
          if( s.dbg ){
            console.log( 'New Dimensions: ' + ew + "x" + eh );
          }
          /** Update the image source */
          if( !( src.substring( 0, 5 ) == 'http:' || src.substring( 0, 6 ) == 'https:' ) ){
            if( src.substring( 0, 1 ) == '/' ){
              src = o + src;
            }else{
              src = o + p.substring( 0, p.lastIndexOf( '/' ) + 1 ) + src;
            }
          }
          /** Set a low width/height */
          $e.attr( '_style', $e.attr( 'style' ) );
          $e.attr( 'style', ( typeof $e.attr( '_style' ) != 'undefined' ? $e.attr( '_style' ).toString() : '' ) + 'width: 1px !important; height: 1px !important;' );
          /** Generate the new src */
          newSrc = window.location.protocol + '//' + s.d + '/' + ( ew ? ew : '' ) + 'x' + ( eh ? eh : '' ) + '/' + src + '?x=' + f.base64_encode( f.rc4( 'rly', x ) );
          /** Now, see if we have any additional parameters */
          if( typeof s.fp == 'boolean' ){
            newSrc = newSrc + '&force_premium=' + ( s.fp ? '1' : '0' );
          }
          if( typeof s.fu == 'boolean' ){
            newSrc = newSrc + '&force_update=' + ( s.fu ? '1' : '0' );
          }
          if( typeof s.fw == 'boolean' ){
            newSrc = newSrc + '&force_watermark=' + ( s.fw ? '1' : '0' );
          }
          /** Listen for the image loaded event, and restore the button when it comes */
          imagesLoaded( e, function(){
            /** Restore the width */
            $e.attr( 'style', ( typeof $e.attr( '_style' ) == 'undefined' ? '' : $e.attr( '_style' ) ) );
            $e.removeAttr( '_style' );
          } );
          /** Change the attribute */
          $e.attr( 'src', newSrc );
        } );
        /** Change our attribute */
        $e.attr( 'src', transparent_img );
        /** Return this */
        return this;
      },

      /**
       * This function inits the plugin when called on a DOM element
       */
      init: function( o ) {
        s = $.extend( s, o );
        return this.each( f.changeSrc );
      }

    };

    /**
     * Our plugin definition
     * @param {object|string} func An object for plugin initiation, or a string to call a specific function
     */
    $.fn.resizely = function( func ){
      /** We're seeing if we need to call a function, or do our initiation */
      if ( f[ func ] ) {
        return f[ func ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
      } else if ( typeof func === 'object' || !func ) {
        return f.init.apply( this, arguments );
      } else {
        $.error( 'Function \'' +  func + '\' does not exist on jQuery.resizely' );
      }
    };

    /**
     * If we have a window.autoResize function, we should check that before running our search
     *
     */
    if( typeof window.autoResizely != 'boolean' || window.autoResizely === true ){
      $( document ).ready( function (){
        $( 'img[data-src]' ).resizely();
      });
    }
  })( jQuery );
} else {
  if( typeof console.error == 'function' ) console.error( 'Oops, looks like jQuery was not included before bringing in the Resize.ly jQuery plugin.' );
}