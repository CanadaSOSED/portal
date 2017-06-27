<?php
/*********************************************************************************/
/**
 * iCalcreator class v2.6
 * copyright (c) 2007-2008 Kjell-Inge Gustafsson kigkonsult
 * www.kigkonsult.se/iCalcreator/index.php
 * ical@kigkonsult.se
 *
 * Description:
 * This file is a PHP implementation of RFC 2445.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/*********************************************************************************/
/*********************************************************************************/
/*         A little setup                                                        */
/*********************************************************************************/
            /* your local language code */
// define( 'ICAL_LANG', 'sv' );
            // alt. autosetting
/*
$langstr     = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$pos         = strpos( $langstr, ';' );
if ($pos   !== false) {
  $langstr   = substr( $langstr, 0, $pos );
  $pos       = strpos( $langstr, ',' );
  if ($pos !== false) {
    $pos     = strpos( $langstr, ',' );
    $langstr = substr( $langstr, 0, $pos );
  }
  define( 'ICAL_LANG', $langstr );
}
*/
            /* only for phpversion 5.x, date management, default timezone setting */
if( substr( phpversion(), 0, 1) >= '5' ){ // && ( 'UTC' == date_default_timezone_get() )) {
//  date_default_timezone_set( 'UTC' );
  }
            /* version string, do NOT remove!! */
if( ! defined('HC_ICALCREATOR_VERSION') ){
	define( 'HC_ICALCREATOR_VERSION', 'iCalcreator 2.6' );
}
/*********************************************************************************/
/*********************************************************************************/
/**
 * vcalendar class
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.13 - 2007-12-30
 */
class hc_vcalendar {
            //  calendar property variables
  var $method;
  var $prodid;
  var $version;
  var $xprop;
            //  container for calendar components
  var $components;
            //  component config variables
  var $allowEmpty;
  var $unique_id;
  var $language;
  var $url;
  var $delimiter;
  var $nl;
  var $format;
            //  component internal variables
  var $attributeDelimiter;
  var $valueInit;
            //  component xCal declaration container
  var $xcaldecl;
/*
 * constructor for calendar object
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.13 - 2007-12-30
 * @return void
 */
  function hc_vcalendar () {
    $this->_makeVersion();
    $this->method     = null;
    $this->_makeUnique_id();
    $this->prodid     = null;
    $this->xprop      = array();
/**
 *   language = <Text identifying a language, as defined in [RFC 1766]>
 */
    if( defined( 'ICAL_LANG' ))
      $this->setConfig( 'language', ICAL_LANG );
    $this->setConfig( 'allowEmpty', TRUE );
    // $this->setConfig( 'nl',         "\n" );
    $this->setConfig( 'nl',         "\r\n" );
    $this->setConfig( 'format',     'iCal');
    $this->url        = null;
    $this->setConfig( 'delimiter',  DIRECTORY_SEPARATOR );
    $this->xcaldecl   = array();
    $this->components = array();
  }
/*********************************************************************************/
/**
 * Property Name: METHOD
 */
/**
 * creates formatted output for calendar property method
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @return string
 */
  function createMethod() {
    if( empty( $this->method )) return FALSE;
    switch( $this->format ) {
      case 'xcal':
        return ' method="'.$this->method.'"'.$this->nl;
        break;
      default:
        return 'METHOD:'.$this->method.$this->nl;
        break;
    }
  }
/**
 * set calendar property method
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-20-23
 * @param string $value
 * @return bool
 */
  function setMethod( $value ) {
    if( empty( $value )) return FALSE;
    $this->method = strtoupper($value);
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: PRODID
 *
 *  The identifier is RECOMMENDED to be the identical syntax to the
 * [RFC 822] addr-spec. A good method to assure uniqueness is to put the
 * domain name or a domain literal IP address of the host on which.. .
 */
/**
 * creates formatted output for calendar property prodid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @return string
 */
  function createProdid() {
    if( !isset( $this->prodid ))
      $this->_makeProdid();
    switch( $this->format ) {
      case 'xcal':
        return ' prodid="'.$this->prodid.'"'.$this->nl;
        break;
      default:
        return 'PRODID:'.$this->prodid.$this->nl;
        break;
    }
  }
/**
 * make default value for calendar prodid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.3.0 - 2006-08-10
 * @return void
 */
  function _makeProdid() {
    $this->prodid  = '-//'.$this->unique_id.'//NONSGML '.HC_ICALCREATOR_VERSION.'//'.strtoupper( $this->language );
  }
/**
 * Conformance: The property MUST be specified once in an iCalendar object.
 * Description: The vendor of the implementation SHOULD assure that this
 * is a globally unique identifier; using some technique such as an FPI
 * value, as defined in [ISO 9070].
 */
/**
 * make default unique_id for calendar prodid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.3.0 - 2006-08-10
 * @return void
 */
  function _makeUnique_id() {
    $this->unique_id  = ( isset( $_SERVER['SERVER_NAME'] )) ? gethostbyname( $_SERVER['SERVER_NAME'] ) : 'localhost';
  }
/*********************************************************************************/
/**
 * Property Name: VERSION
 *
 * Description: A value of "2.0" corresponds to this memo.
 */
/**
 * creates formatted output for calendar property version

 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @return string
 */
  function createVersion() {
    if( empty( $this->version ))
      $this->_makeVersion();
    switch( $this->format ) {
      case 'xcal':
        return ' version="'.$this->version.'"'.$this->nl;
        break;
      default:
        return 'VERSION:'.$this->version.$this->nl;
        break;
    }
  }
/**
 * set default calendar version
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.3.0 - 2006-08-10
 * @return void
 */
  function _makeVersion() {
    $this->version = '2.0';
  }
/**
 * set calendar version
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param string $value
 * @return void
 */
  function setVersion( $value ) {
    if( empty( $value )) return FALSE;
    $this->version = $value;
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: x-prop
 */
/**
 * creates formatted output for calendar property x-prop, iCal format only
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.11 - 2008-11-03
 * @return string
 */
  function createXprop() {
    if( 'xcal' == $this->format )
      return false;
    if( 0 >= count( $this->xprop ))
      return;
    $output = null;
    $toolbox = new hc_calendarComponent();
    $toolbox->setConfig( 'language', $this->getConfig( 'language' ));
    $toolbox->setConfig( 'nl',       $this->getConfig( 'nl' ));
    $toolbox->_createFormat(         $this->getConfig( 'format' ));
    foreach( $this->xprop as $label => $xpropPart ) {
      if( empty( $xpropPart['value'] )) {
        $output  .= $toolbox->_createElement( $label );
        continue;
      }
      $attributes = $toolbox->_createParams( $xpropPart['params'], array( 'LANGUAGE' ));
      if( is_array( $xpropPart['value'] )) {
        foreach( $xpropPart['value'] as $pix => $theXpart )
          $xpropPart['value'][$pix] = $toolbox->_strrep( $theXpart );
        $xpropPart['value']  = implode( ',', $xpropPart['value'] );
      }
      else
        $xpropPart['value'] = $toolbox->_strrep( $xpropPart['value'] );
      $output    .= $toolbox->_createElement( $label, $attributes, $xpropPart['value'] );
    }
    return $output;
  }
/**
 * set calendar property x-prop
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.11 - 2008-11-04
 * @param string $label
 * @param string $value
 * @param array $params optional
 * @return bool
 */
  function setXprop( $label, $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    if( empty( $label )) return FALSE;
    $xprop           = array( 'value' => $value );
    $toolbox         = new hc_calendarComponent();
    $xprop['params'] = $toolbox->_setParams( $params );
    if( !is_array( $this->xprop )) $this->xprop = array();
    $this->xprop[strtoupper( $label )] = $xprop;
    return TRUE;
  }

 /**
 * get calendar property value/params
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-02
 * @param string $propName, optional
 * @param int @propix, optional, if specific property is wanted in case of multiply occurences
 * @param bool $inclParam=FALSE
 * @return mixed
 */
  function getProperty( $propName=FALSE, $propix=FALSE, $inclParam=FALSE ) {
    $propName = ( $propName ) ? strtoupper( $propName ) : 'X-PROP';
    if( 'X-PROP' == $propName ) {
      if( !$propix )
        $propix = ( isset( $this->propix[$propName] )) ? $this->propix[$propName] + 2 : 1;
      $this->propix[$propName] = --$propix;
    }
    switch( $propName ) {
      case 'METHOD':
        return ( !empty( $this->method )) ? $this->method : null;
        break;
      case 'PRODID':
        if( empty( $this->prodid ))
          $this->_makeProdid();
        return $this->prodid;
        break;
      case 'VERSION':
        return ( !empty( $this->version )) ? $this->version : null;
        break;
      default:
        if( $propName != 'X-PROP' ) {
          if( !isset( $this->xprop[$propName] )) return FALSE;
          return ( $inclParam ) ? array( $propName, $this->xprop[$propName] )
                                : array( $propName, $this->xprop[$propName]['value'] );
        }
        else {
          if( empty( $this->xprop )) return FALSE;
          $xpropno = 0;
          foreach( $this->xprop as $xpropkey => $xpropvalue ) {
            if( $propix == $xpropno )
              return ( $inclParam ) ? array( $xpropkey, $this->xprop[$xpropkey] )
                                    : array( $xpropkey, $this->xprop[$xpropkey]['value'] );
            else
              $xpropno++;
          }
          return FALSE; // not found ??
        }
    }
    return FALSE;
  }
/**
 * general vcalendar property setting
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.13 - 2007-11-04
 * @param mixed $args variable number of function arguments,
 *                    first argument is ALWAYS component name,
 *                    second ALWAYS component value!
 * @return bool
 */
  function setProperty () {
    $numargs    = func_num_args();
    if( 1 > $numargs )
      return FALSE;
    $arglist    = func_get_args();
    $arglist[0] = strtoupper( $arglist[0] );
    switch( $arglist[0] ) {
      case 'METHOD':
        return $this->setMethod( $arglist[1] );
      case 'VERSION':
        return $this->setVersion( $arglist[1] );
      default:
        if( !isset( $arglist[1] )) $arglist[1] = null;
        if( !isset( $arglist[2] )) $arglist[2] = null;
        return $this->setXprop( $arglist[0], $arglist[1], $arglist[2] );
    }
    return FALSE;
  }
/*********************************************************************************/
/**
 * get vcalendar config values or * calendar components
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.10 - 2008-10-23
 * @param string $config
 * @return value
 */
  function getConfig( $config ) {
    switch( strtoupper( $config )) {
      case 'ALLOWEMPTY':
        return $this->allowEmpty;
        break;
      case 'COMPSINFO':
        unset( $this->compix );
        $info = array();
        foreach( $this->components as $cix => $component ) {
          if( empty( $component )) continue;
          unset( $component->propix );
          $info[$cix]['ordno'] = $cix + 1;
          $info[$cix]['type']  = $component->objName;
          $info[$cix]['uid']   = $component->getProperty( 'uid' );
          $info[$cix]['props'] = $component->getConfig( 'propinfo' );
          $info[$cix]['sub']   = $component->getConfig( 'compsinfo' );
          unset( $component->propix );
        }
        return $info;
        break;
      case 'DELIMITER':
        return $this->delimiter;
        break;
      case 'FORMAT':
        return $this->format;
        break;
      case 'LANGUAGE':
         /* get language for calendar component as defined in [RFC 1766] */
        return $this->language;
        break;
      case 'NL':
      case 'NEWLINECHAR':
        return $this->nl;
        break;
      case 'UNIQUE_ID':
        return $this->unique_id;
        break;
      case 'URL':
        if( !empty( $this->url ))
          return $this->url;
        else
          return FALSE;
        break;
    }
  }
/**
 * general vcalendar config setting
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-24
 * @param string $config
 * @param string $value
 * @return void
 */
  function setConfig( $config, $value ) {
    $res = FALSE;
    switch( strtoupper( $config )) {
      case 'ALLOWEMPTY':
        $this->allowEmpty = $value;
        $subcfg  = array( 'ALLOWEMPTY' => $value );
        $res = TRUE;
        break;
      case 'DELIMITER':
        $this->delimiter = $value;
        return TRUE;
        break;
      case 'FORMAT':
        $value   = trim( $value );
        if( 'xcal' == strtolower( $value )) {
          $this->format             = 'xcal';
          $this->attributeDelimiter = $this->nl;
          $this->valueInit          = null;
        }
        else {
          $this->format             = null;
          $this->attributeDelimiter = ';';
          $this->valueInit          = ':';
        }
        $subcfg  = array( 'FORMAT' => $value );
        $res = TRUE;
        break;
      case 'LANGUAGE':
         // set language for calendar component as defined in [RFC 1766]
        $value   = trim( $value );
        $this->language = $value;
        $subcfg  = array( 'LANGUAGE' => $value );
        $res = TRUE;
        break;
      case 'NL':
      case 'NEWLINECHAR':
        $this->nl = $value;
        $subcfg  = array( 'NL' => $value );
        $res = TRUE;
        break;
      case 'UNIQUE_ID':
        $value   = trim( $value );
        $this->unique_id = $value;
        $subcfg  = array( 'UNIQUE_ID' => $value );
        $res = TRUE;
        break;
    }
    if( !$res ) return FALSE;
    if( isset( $subcfg ) && !empty( $this->components )) {
      foreach( $subcfg as $cfgkey => $cfgvalue ) {
        foreach( $this->components as $cix => $component ) {
          $res = $component->setConfig( $cfgkey, $cfgvalue );
          if( !$res )
            break 2;
          $this->components[$cix] = $component->copy(); // PHP4 compliant
        }
      }
    }
    return $res;
  }
/*********************************************************************************/
/**
 * add calendar component to container
 *
 * alias to setComponent
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 1.x.x - 2007-04-24
 * @param object $component calendar component
 * @return void
 */
  function addComponent( $component ) {
    $this->setComponent( $component );
  }
/**
 * get calendar component from container
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.10 - 2008-08-06
 * @param mixed $arg1 optional, ordno/component type/ component uid
 * @param mixed $arg2 optional, ordno if arg1 = component type
 * @return object
 */
  function getComponent( $arg1=FALSE, $arg2=FALSE ) {
    $index = $argType = null;
    if ( !$arg1 ) {
      $argType = 'INDEX';
      $index   = $this->compix['INDEX'] =
        ( isset( $this->compix['INDEX'] )) ? $this->compix['INDEX'] + 1 : 1;
    }
    elseif ( ctype_digit( (string) $arg1 )) {
      $argType = 'INDEX';
      $index   = (int) $arg1;
      unset( $this->compix );
    }
    elseif(( strlen( $arg1 ) <= strlen( 'vfreebusy' )) && ( FALSE === strpos( $arg1, '@' ))) {
      unset( $this->compix['INDEX'] );
      $argType = strtolower( $arg1 );
      if( !$arg2 )
        $index = $this->compix[$argType] =
        ( isset( $this->compix[$argType] )) ? $this->compix[$argType] + 1 : 1;
      else
        $index = (int) $arg2;
    }
    $index  -= 1;
    $ckeys =  array_keys( $this->components );
    if( !empty( $index) && ( $index > end(  $ckeys )))
      return FALSE;
    $cix1gC = 0;
    foreach ( $this->components as $cix => $component) {
      if( empty( $component )) continue;
      unset( $component->propix );
      if(( 'INDEX' == $argType ) && ( $index == $cix ))
        return $component->copy();
      elseif( $argType == $component->objName ) {
         if( $index == $cix1gC )
           return $component->copy();
         $cix1gC++;
      }
      elseif( !$argType && ($arg1 == $component->getProperty( 'uid' ))) {
        unset( $component->propix );
        return $component->copy();
      }
    }
            /* not found.. . */
    unset( $this->compix );
    return FALSE;
  }

 /**
 * add calendar component to container
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.10 - 2008-08-06
 * @param object $component calendar component
 * @param mixed $arg1 optional, ordno/component type/ component uid
 * @param mixed $arg2 optional, ordno if arg1 = component type
 * @return void
 */
  function setComponent( $component, $arg1=FALSE, $arg2=FALSE  ) {
    if( '' >= $component->getConfig( 'language'))
      $component->setConfig( 'language',  $this->getConfig( 'language' ));
    $component->setConfig( 'allowEmpty',  $this->getConfig( 'allowEmpty' ));
    $component->setConfig( 'nl',          $this->getConfig( 'nl' ));
    $component->setConfig( 'unique_id',   $this->getConfig( 'unique_id' ));
    $component->setConfig( 'format',      $this->getConfig( 'format' ));
    if( !in_array( $component->objName, array( 'hc_valarm', 'hc_vtimezone' ))) {
      unset( $component->propix );
            /* make sure dtstamp and uid is set */
      $dummy1 = $component->getProperty( 'dtstamp' );
      $dummy2 = $component->getProperty( 'uid' );
    }
    if( !$arg1 ) {
      $this->components[] = $component->copy();
      return TRUE;
    }
    $argType = $index = null;
    if ( ctype_digit( (string) $arg1 )) {
      $argType = 'INDEX';
      $index   = (int) $arg1 - 1;
    }
    elseif(( strlen( $arg1 ) <= strlen( 'vfreebusy' )) && ( FALSE === strpos( $arg1, '@' ))) {
      $argType = strtolower( $arg1 );
      $index = ( ctype_digit( (string) $arg2 )) ? ((int) $arg2) - 1 : 0;
    }
    $cix1sC = 0;
    foreach ( $this->components as $cix => $component2) {
      if( empty( $component2 )) continue;
      unset( $component2->propix );
      if(( 'INDEX' == $argType ) && ( $index == $cix )) {
        $this->components[$cix] = $component->copy();
        return TRUE;
      }
      elseif( $argType == $component2->objName ) {
        if( $index == $cix1sC ) {
          $this->components[$cix] = $component->copy();
          return TRUE;
        }
        $cix1sC++;
      }
      elseif( !$argType && ( $arg1 == $component2->getProperty( 'uid' ))) {
        $this->components[$cix] = $component->copy();
        return TRUE;
      }
    }
            /* not found.. . insert last in chain anyway .. .*/
    $this->components[] = $component->copy();
    return TRUE;
  }

/*********************************************************************************/
/**
 * creates formatted output for calendar object instance
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.10 - 2008-08-06
 * @return string
 */
  function createCalendar() {
    $calendarInit1 = $calendarInit2 = $calendarxCaldecl = $calendarStart = $calendar = null;
    switch( $this->format ) {
      case 'xcal':
        $calendarInit1 = '<?xml version="1.0" encoding="UTF-8"?>'.$this->nl.
                         '<!DOCTYPE iCalendar PUBLIC "-//IETF//DTD XCAL/iCalendar XML//EN"'.$this->nl.
                         '"http://www.ietf.org/internet-drafts/draft-ietf-calsch-many-xcal-01.txt"';
        $calendarInit2 = '>'.$this->nl;
        $calendarStart = '<vcalendar';
        break;
      default:
        $calendarStart = 'BEGIN:VCALENDAR'.$this->nl;
        break;
    }
    $calendarStart .= $this->createMethod();
    $calendarStart .= $this->createProdid();
    $calendarStart .= $this->createVersion();
    switch( $this->format ) {
      case 'xcal':
        $nlstrlen = strlen( $this->nl );
        if( $this->nl == substr( $calendarStart, ( 0 - $nlstrlen )))
          $calendarStart = substr( $calendarStart, 0, ( strlen( $calendarStart ) - $nlstrlen ));
        $calendarStart .= '>'.$this->nl;
        break;
      default:
        break;
    }
    $calendar .= $this->createXprop();
    foreach( $this->components as $component ) {
      if( empty( $component )) continue;
      if( '' >= $component->getConfig( 'language'))
        $component->setConfig( 'language',  $this->getConfig( 'language' ));
      $component->setConfig( 'allowEmpty',  $this->getConfig( 'allowEmpty' ));
      $component->setConfig( 'nl',          $this->getConfig( 'nl' ));
      $component->setConfig( 'unique_id',   $this->getConfig( 'unique_id' ));
      $component->setConfig( 'format',      $this->getConfig( 'format' ));
      $calendar .= $component->createComponent( $this->xcaldecl );
    }
    if(( 0 < count( $this->xcaldecl )) && ( 'xcal' == $this->format )) { // xCal only
      $calendarInit1 .= $this->nl.'['.$this->nl;
      $old_xcaldecl = array();
      foreach( $this->xcaldecl as $declix => $declPart ) {
        if(( 0 < count( $old_xcaldecl)) &&
           ( in_array( $declPart['uri'],      $old_xcaldecl['uri'] )) &&
           ( in_array( $declPart['external'], $old_xcaldecl['external'] )))
          continue; // no duplicate uri and ext. references
        $calendarxCaldecl .= '<!';
        foreach( $declPart as $declKey => $declValue ) {
          switch( $declKey ) {                    // index
            case 'xmldecl':                       // no 1
              $calendarxCaldecl .= $declValue.' ';
              break;
            case 'uri':                           // no 2
              $calendarxCaldecl .= $declValue.' ';
              $old_xcaldecl['uri'][] = $declValue;
              break;
            case 'ref':                           // no 3
              $calendarxCaldecl .= $declValue.' ';
              break;
            case 'external':                      // no 4
              $calendarxCaldecl .= '"'.$declValue.'" ';
              $old_xcaldecl['external'][] = $declValue;
              break;
            case 'type':                          // no 5
              $calendarxCaldecl .= $declValue.' ';
              break;
            case 'type2':                         // no 6
              $calendarxCaldecl .= $declValue;
              break;
          }
        }
        $calendarxCaldecl .= '>'.$this->nl;
      }
      $calendarInit2 = ']'.$calendarInit2;
    }
    switch( $this->format ) {
      case 'xcal':
        $calendar .= '</vcalendar>'.$this->nl;
        break;
      default:
        $calendar .= 'END:VCALENDAR'.$this->nl;
        break;
    }
    return $calendarInit1.$calendarxCaldecl.$calendarInit2.$calendarStart.$calendar;
  }
 }

/*********************************************************************************/
/*********************************************************************************/
/**
 *  abstract class for calendar components
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.19 - 2008-10-12
 */
class hc_calendarComponent {
            //  component property variables
  var $uid;
  var $dtstamp;

            //  component config variables
  var $allowEmpty;
  var $language;
  var $nl;
  var $unique_id;
  var $format;
  var $objName; // created automatically at instance creation
            //  component internal variables
  var $componentStart1;
  var $componentStart2;
  var $componentEnd1;
  var $componentEnd2;
  var $elementStart1;
  var $elementStart2;
  var $elementEnd1;
  var $elementEnd2;
  var $intAttrDelimiter;
  var $attributeDelimiter;
  var $valueInit;
            //  component xCal declaration container
  var $xcaldecl;
/**
 * constructor for calendar component object
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.19 - 2008-10-23
 */
  function hc_calendarComponent() {
    $this->objName         = ( isset( $this->timezonetype )) ?
                          strtolower( $this->timezonetype )  :  get_class ( $this );

	if( substr($this->objName, 0, strlen('hc_')) == 'hc_' ){
		$this->objName = substr($this->objName, strlen('hc_'));
	}

    $this->uid             = array();
    $this->dtstamp         = array();

    $this->language        = null;
    $this->nl              = null;
    $this->unique_id       = null;
    $this->format          = null;
    $this->allowEmpty      = TRUE;
    $this->xcaldecl        = array();

    $this->_createFormat();
    $this->_makeDtstamp();
  }
/*********************************************************************************/
/**
 * Property Name: ATTENDEE
 */
/**
 * creates formatted output for calendar component property attendee
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-09-23
 * @return string
 */
  function createAttendee() {
    if( empty( $this->attendee )) return FALSE;
    $output = null;
    foreach( $this->attendee as $attendeePart ) {                      // start foreach 1
      if( empty( $attendeePart['value'] )) {
        if( $this->getConfig( 'allowEmpty' ))
          $output .= $this->_createElement( 'ATTENDEE' );
        continue;
      }
      $attendee1 = $attendee2 = $attendeeLANG = $attendeeCN = null;
      foreach( $attendeePart as $paramlabel => $paramvalue ) {         // start foreach 2
        if( 'value' == $paramlabel )
          $attendee2  .= 'MAILTO:'.$paramvalue;
        elseif(( 'params' == $paramlabel ) && ( is_array( $paramvalue ))) { // start elseif
          foreach( $paramvalue as $optparamlabel => $optparamvalue ) { // start foreach 3
            $attendee11 = $attendee12 = null;
            if( is_int( $optparamlabel )) {
              $attendee1 .= $this->intAttrDelimiter.$optparamvalue;
              continue;
            }
            switch( $optparamlabel ) {                                 // start switch
              case 'CUTYPE':
              case 'PARTSTAT':
              case 'ROLE':
              case 'RSVP':
                $attendee1 .= $this->intAttrDelimiter.$optparamlabel.'="'.$optparamvalue.'"';
                break;
              case 'SENT-BY':
                $attendee1 .= $this->intAttrDelimiter.'SENT-BY="MAILTO:'.$optparamvalue.'"';
                break;
              case 'CN':
                $attendeeCN .= $this->intAttrDelimiter.'CN="'.$optparamvalue.'"';
                break;
              case 'DIR':
                $attendee1 .= $this->intAttrDelimiter.'DIR="'.$optparamvalue.'"';
                break;
              case 'LANGUAGE':
                $attendeeLANG .= $this->intAttrDelimiter.'LANGUAGE='.$optparamvalue;
                break;
              default:
                $attendee1 .= $this->intAttrDelimiter."$optparamlabel=$optparamvalue";
                break;
            }    // end switch
          }      // end foreach 3
        }        // end elseif
      }          // end foreach 2
      $output .= $this->_createElement( 'ATTENDEE', $attendee1.$attendeeLANG.$attendeeCN, $attendee2 );
    }              // end foreach 1
    return $output;
  }
/**
 * set calendar component property attendee
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param string $value
 * @param array $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setAttendee( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $value = str_replace ( 'MAILTO:', '', $value );
    $value = str_replace ( 'mailto:', '', $value );
    $params2 = array();
    if( is_array($params )) {
      $optarrays = array();
      foreach( $params as $optparamlabel => $optparamvalue ) {
        $optparamlabel = strtoupper( $optparamlabel );
        switch( $optparamlabel ) {
          case 'MEMBER':
            if( is_array( $optparamvalue )) {
              foreach( $optparamvalue as $part ) {
                $part = str_replace( 'MAILTO:', '', $part );
                $part = str_replace( 'mailto:', '', $part );
                if(( '"' == $part{0} ) && ( '"' == $part{strlen($part)-1} ))
                  $part = substr( $part, 1, ( strlen($part)-2 ));
                $optarrays[$optparamlabel][] = $part;
              }
            }
            else {
              $part = str_replace( 'MAILTO:', '', $optparamvalue );
              $part = str_replace( 'mailto:', '', $part );
              if(( '"' == $part{0} ) && ( '"' == $part{strlen($part)-1} ))
                $part = substr( $part, 1, ( strlen($part)-2 ));
              $optarrays[$optparamlabel][] = $part;
            }
            break;
          default:
            if( 'SENT-BY' ==  $optparamlabel ) {
              $optparamvalue = str_replace( 'MAILTO:', '', $optparamvalue );
              $optparamvalue = str_replace( 'mailto:', '', $optparamvalue );
            }
            if(( '"' == substr( $optparamvalue, 0, 1 )) &&
               ( '"' == substr( $optparamvalue, -1 )))
              $optparamvalue = substr( $optparamvalue, 1, ( strlen( $optparamvalue ) - 2 ));
            $params2[$optparamlabel] = $optparamvalue;
            break;
        } // end switch( $optparamlabel.. .
      } // end foreach( $optparam.. .
      foreach( $optarrays as $optparamlabel => $optparams )
        $params2[$optparamlabel] = $optparams;
    }
        // remove defaults
    $this->_existRem( $params2, 'CUTYPE',   'INDIVIDUAL' );
    $this->_existRem( $params2, 'PARTSTAT', 'NEEDS-ACTION' );
    $this->_existRem( $params2, 'ROLE',     'REQ-PARTICIPANT' );
    $this->_existRem( $params2, 'RSVP',     'FALSE' );
        // check language setting
    if( isset( $params2['CN' ] )) {
      $lang = $this->getConfig( 'language' );
      if( !isset( $params2['LANGUAGE' ] ) && !empty( $lang ))
        $params2['LANGUAGE' ] = $lang;
    }
    $this->_setMval( $this->attendee, $value, $params2, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: COMMENT
 */
/**
 * creates formatted output for calendar component property comment
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-22
 * @return string
 */
  function createComment() {
    if( empty( $this->comment )) return FALSE;
    $output = null;
    foreach( $this->comment as $commentPart ) {
      if( empty( $commentPart['value'] )) {
        if( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( 'COMMENT' );
        continue;
      }
      $attributes = $this->_createParams( $commentPart['params'], array( 'ALTREP', 'LANGUAGE' ));
      $content    = $this->_strrep( $commentPart['value'] );
      $output    .= $this->_createElement( 'COMMENT', $attributes, $content );
    }
    return $output;
  }
/**
 * set calendar component property comment
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-06
 * @param string $value
 * @param array $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setComment( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->_setMval( $this->comment, $value, $params, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: COMPLETED
 */
/**
 * creates formatted output for calendar component property completed
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-22
 * @return string
 */
  function createCompleted( ) {
    if( empty( $this->completed )) return FALSE;
    if( !isset( $this->completed['value']['year'] )  &&
        !isset( $this->completed['value']['month'] ) &&
        !isset( $this->completed['value']['day'] )   &&
        !isset( $this->completed['value']['hour'] )  &&
        !isset( $this->completed['value']['min'] )   &&
        !isset( $this->completed['value']['sec'] ))
      if( $this->getConfig( 'allowEmpty' ))
        return $this->_createElement( 'COMPLETED' );
      else return FALSE;
    $formatted  = $this->_format_date_time( $this->completed['value'], 7 );
    $attributes = $this->_createParams( $this->completed['params'] );
    return $this->_createElement( 'COMPLETED', $attributes, $formatted );
  }
/**
 * set calendar component property completed
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @return bool
 */
  function setCompleted( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    if( empty( $year )) {
      if( $this->getConfig( 'allowEmpty' )) {
        $this->completed = array( 'value' => null, 'params' => $this->_setParams( $params ));
        return TRUE;
      }
      else
        return FALSE;
    }
    $this->completed = $this->_setDate2( $year, $month, $day, $hour, $min, $sec, $params );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: CONTACT
 */
/**
 * creates formatted output for calendar component property contact
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @return string
 */
  function createContact() {
    if( empty( $this->contact )) return FALSE;
    $output = null;
    foreach( $this->contact as $contact ) {
      if( !empty( $contact['value'] )) {
        $attributes = $this->_createParams( $contact['params'], array( 'ALTREP', 'LANGUAGE' ));
        $content    = $this->_strrep( $contact['value'] );
        $output    .= $this->_createElement( 'CONTACT', $attributes, $content );
      }
      elseif( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( 'CONTACT' );
    }
    return $output;
  }
/**
 * set calendar component property contact
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param string $value
 * @param array $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setContact( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->_setMval( $this->contact, $value, $params, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: CREATED
 */
/**
 * creates formatted output for calendar component property created
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createCreated() {
    if( empty( $this->created )) return FALSE;
    $formatted  = $this->_format_date_time( $this->created['value'], 7 );
    $attributes = $this->_createParams( $this->created['params'] );
    return $this->_createElement( 'CREATED', $attributes, $formatted );
  }
/**
 * set calendar component property created
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param mixed $year optional
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param mixed $params optional
 * @return bool
 */
  function setCreated( $year=FALSE, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    if( !isset( $year )) {
      $year = date('Ymd\THis', mktime( date( 'H' ), date( 'i' ), date( 's' ) - date( 'Z'), date( 'm' ), date( 'd' ), date( 'Y' )));
    }
    $this->created = $this->_setDate2( $year, $month, $day, $hour, $min, $sec, $params );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: DESCRIPTION
 */
/**
 * creates formatted output for calendar component property description
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-22
 * @return string
 */
  function createDescription() {
    if( empty( $this->description )) return FALSE;
    $output       = null;
    foreach( $this->description as $description ) {
      if( !empty( $description['value'] )) {
        $attributes = $this->_createParams( $description['params'], array( 'ALTREP', 'LANGUAGE' ));
        $content    = $this->_strrep( $description['value'] );
        $output    .= $this->_createElement( 'DESCRIPTION', $attributes, $content );
      }
      elseif( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( 'DESCRIPTION' );
    }
    return $output;
  }
/**
 * set calendar component property description
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param string $value
 * @param array $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setDescription( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) { if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE; }
    $this->_setMval( $this->description, $value, $params, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: DTEND
 */
/**
 * creates formatted output for calendar component property dtend
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createDtend() {
    if( empty( $this->dtend )) return FALSE;
    if( !isset( $this->dtend['value']['year'] )  &&
        !isset( $this->dtend['value']['month'] ) &&
        !isset( $this->dtend['value']['day'] )   &&
        !isset( $this->dtend['value']['hour'] )  &&
        !isset( $this->dtend['value']['min'] )   &&
        !isset( $this->dtend['value']['sec'] ))
      if( $this->getConfig( 'allowEmpty' ))
        return $this->_createElement( 'DTEND' );
      else return FALSE;
    $formatted  = $this->_format_date_time( $this->dtend['value'] );
    $attributes = $this->_createParams( $this->dtend['params'] );
    return $this->_createElement( 'DTEND', $attributes, $formatted );
  }
/**
 * set calendar component property dtend
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param string $tz optional
 * @param array params optional
 * @return bool
 */
  function setDtend( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $tz=FALSE, $params=FALSE ) {
    if( empty( $year )) {
      if( $this->getConfig( 'allowEmpty' )) {
        $this->dtend = array( 'value' => null, 'params' => $this->_setParams( $params ));
        return TRUE;
      }
      else
        return FALSE;
    }
    $this->dtend = $this->_setDate( $year, $month, $day, $hour, $min, $sec, $tz, $params );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: DTSTAMP
 */
/**
 * creates formatted output for calendar component property dtstamp
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.4 - 2008-03-07
 * @return string
 */
  function createDtstamp() {
    if( !isset( $this->dtstamp['value']['year'] )  &&
        !isset( $this->dtstamp['value']['month'] ) &&
        !isset( $this->dtstamp['value']['day'] )   &&
        !isset( $this->dtstamp['value']['hour'] )  &&
        !isset( $this->dtstamp['value']['min'] )   &&
        !isset( $this->dtstamp['value']['sec'] ))
      $this->_makeDtstamp();
    $formatted  = $this->_format_date_time( $this->dtstamp['value'], 7 );
    $attributes = $this->_createParams( $this->dtstamp['params'] );
    return $this->_createElement( 'DTSTAMP', $attributes, $formatted );
  }
/**
 * computes datestamp for calendar component object instance dtstamp
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 1.x.x - 2007-05-13
 * @return void
 */
  function _makeDtstamp() {
    $this->dtstamp['value'] = array( 'year'  => date( 'Y' )
                                   , 'month' => date( 'm' )
                                   , 'day'   => date( 'd' )
                                   , 'hour'  => date( 'H' )
                                   , 'min'   => date( 'i' )
                                   , 'sec'   => date( 's' ) - date( 'Z' ));
    $this->dtstamp['params'] = null;
  }
/**
 * set calendar component property dtstamp
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @return TRUE
 */
  function setDtstamp( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    if( empty( $year ))
      $this->_makeDtstamp();
    else
      $this->dtstamp = $this->_setDate2( $year, $month, $day, $hour, $min, $sec, $params );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: DTSTART
 */
/**
 * creates formatted output for calendar component property dtstart
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-26
 * @return string
 */
  function createDtstart() {
    if( empty( $this->dtstart )) return FALSE;
    if( !isset( $this->dtstart['value']['year'] )  &&
        !isset( $this->dtstart['value']['month'] ) &&
        !isset( $this->dtstart['value']['day'] )   &&
        !isset( $this->dtstart['value']['hour'] )  &&
        !isset( $this->dtstart['value']['min'] )   &&
        !isset( $this->dtstart['value']['sec'] ))
    if( $this->getConfig( 'allowEmpty' ))
      return $this->_createElement( 'DTSTART' );
    else return FALSE;
    if( in_array( $this->objName, array( 'hc_vtimezone', 'standard', 'daylight' )))
      unset( $this->dtstart['value']['tz'], $this->dtstart['params']['TZID'] );
    $formatted  = $this->_format_date_time( $this->dtstart['value'] );
    $attributes = $this->_createParams( $this->dtstart['params'] );
    return $this->_createElement( 'DTSTART', $attributes, $formatted );
  }
/**
 * set calendar component property dtstart
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-11-04
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param string $tz optional
 * @param array $params optional
 * @return bool
 */
  function setDtstart( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $tz=FALSE, $params=FALSE ) {
    if( empty( $year )) {
      if( $this->getConfig( 'allowEmpty' )) {
        $this->dtstart = array( 'value' => null, 'params' => $this->_setParams( $params ));
        return TRUE;
      }
      else
        return FALSE;
    }
    $this->dtstart = $this->_setDate( $year, $month, $day, $hour, $min, $sec, $tz, $params, 'dtstart' );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: DURATION
 */
/**
 * creates formatted output for calendar component property duration
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createDuration() {
    if( empty( $this->duration )) return FALSE;
    if( !isset( $this->duration['value']['week'] ) &&
        !isset( $this->duration['value']['day'] )  &&
        !isset( $this->duration['value']['hour'] ) &&
        !isset( $this->duration['value']['min'] )  &&
        !isset( $this->duration['value']['sec'] ))
      if( $this->getConfig( 'allowEmpty' ))
        return $this->_createElement( 'DURATION', array(), null );
      else return FALSE;
    $attributes = $this->_createParams( $this->duration['params'] );
    return $this->_createElement( 'DURATION', $attributes, $this->_format_duration( $this->duration['value'] ));
  }
/**
 * set calendar component property duration
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param mixed $week
 * @param mixed $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @return bool
 */
  function setDuration( $week, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    if( empty( $week )) if( $this->getConfig( 'allowEmpty' )) $week = null; else return FALSE;
    if( is_array( $week ) && ( 1 <= count( $week )))
      $this->duration = array( 'value' => $this->_duration_array( $week ), 'params' => $this->_setParams( $day ));
    elseif( is_string( $week ) && ( 3 <= strlen( trim( $week )))) {
      $week = trim( $week );
      if( in_array( substr( $week, 0, 1 ), array( '+', '-' )))
        $week = substr( $week, 1 );
      $this->duration = array( 'value' => $this->_duration_string( $week ), 'params' => $this->_setParams( $day ));
    }
    elseif( empty( $week ) && empty( $day ) && empty( $hour ) && empty( $min ) && empty( $sec ))
      return FALSE;
    else
      $this->duration = array( 'value' => $this->_duration_array( array( $week, $day, $hour, $min, $sec )), 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: LAST-MODIFIED
 */
/**
 * creates formatted output for calendar component property last-modified
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createLastModified() {
    if( empty( $this->lastmodified )) return FALSE;
    $attributes = $this->_createParams( $this->lastmodified['params'] );
    $formatted  = $this->_format_date_time( $this->lastmodified['value'], 7 );
    return $this->_createElement( 'LAST-MODIFIED', $attributes, $formatted );
  }
/**
 * set calendar component property completed
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param mixed $year optional
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @return boll
 */
  function setLastModified( $year=FALSE, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    if( empty( $year ))
      $year = date('Ymd\THis', mktime( date( 'H' ), date( 'i' ), date( 's' ) - date( 'Z'), date( 'm' ), date( 'd' ), date( 'Y' )));
    $this->lastmodified = $this->_setDate2( $year, $month, $day, $hour, $min, $sec, $params );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: LOCATION
 */
/**
 * creates formatted output for calendar component property location
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-22
 * @return string
 */
  function createLocation() {
    if( empty( $this->location )) return FALSE;
    if( empty( $this->location['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'LOCATION' ) : FALSE;
    $attributes = $this->_createParams( $this->location['params'], array( 'ALTREP', 'LANGUAGE' ));
    $content    = $this->_strrep( $this->location['value'] );
    return $this->_createElement( 'LOCATION', $attributes, $content );
  }
/**
 * set calendar component property location
 '
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param array params optional
 * @return bool
 */
  function setLocation( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->location = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: ORGANIZER
 */
/**
 * creates formatted output for calendar component property organizer
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createOrganizer() {
    if( empty( $this->organizer )) return FALSE;
    if( empty( $this->organizer['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'ORGANIZER' ) : FALSE;
    $attributes = $this->_createParams( $this->organizer['params']
                                      , array( 'CN', 'DIR', 'LANGUAGE', 'SENT-BY' ));
    $content    = 'MAILTO:'.$this->organizer['value'];
    return $this->_createElement( 'ORGANIZER', $attributes, $content );
  }
/**
 * set calendar component property organizer
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param array params optional
 * @return bool
 */
  function setOrganizer( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $value = str_replace ( 'MAILTO:', '', $value );
    $value = str_replace ( 'mailto:', '', $value );
    $this->organizer = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    if( isset( $this->organizer['params']['SENT-BY'] )) {
      if( 'MAILTO' == strtoupper( substr( $this->organizer['params']['SENT-BY'], 0, 6 )))
        $this->organizer['params']['SENT-BY'] = substr( $this->organizer['params']['SENT-BY'], 7 );
    }
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: PRIORITY
 */
/**
 * creates formatted output for calendar component property priority
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createPriority() {
    if( empty( $this->priority )) return FALSE;
    if( empty( $this->priority['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'PRIORITY' ) : FALSE;
    $attributes = $this->_createParams( $this->priority['params'] );
    return $this->_createElement( 'PRIORITY', $attributes, $this->priority['value'] );
  }
/**
 * set calendar component property priority
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param int $value
 * @param array $params optional
 * @return bool
 */
  function setPriority( $value, $params=FALSE  ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->priority = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: RESOURCES
 */
/**
 * creates formatted output for calendar component property resources
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @return string
 */
  function createResources() {
    if( empty( $this->resources )) return FALSE;
    $output = null;
    foreach( $this->resources as $resource ) {
      if( empty( $resource['value'] )) {
        if( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( 'RESOURCES' );
        continue;
      }
      $attributes  = $this->_createParams( $resource['params'], array( 'ALTREP', 'LANGUAGE' ));
      if( is_array( $resource['value'] )) {
        foreach( $resource['value'] as $rix => $resourcePart )
          $resource['value'][$rix] = $this->_strrep( $resourcePart );
        $content   = implode( ',', $resource['value'] );
      }
      else
        $content   = $this->_strrep( $resource['value'] );
      $output     .= $this->_createElement( 'RESOURCES', $attributes, $content );
    }
    return $output;
  }
/**
 * set calendar component property recources
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param mixed $value
 * @param array $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setResources( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->_setMval( $this->resources, $value, $params, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: SEQUENCE
 */
/**
 * creates formatted output for calendar component property sequence
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @return string
 */
  function createSequence() {
    if( empty( $this->sequence )) return FALSE;
    if( empty( $this->sequence['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'SEQUENCE' ) : FALSE;
    $attributes = $this->_createParams( $this->sequence['params'] );
    return $this->_createElement( 'SEQUENCE', $attributes, $this->sequence['value'] );
  }
/**
 * set calendar component property sequence
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param int $value optional
 * @param array $params optional
 * @return bool
 */
  function setSequence( $value=FALSE, $params=FALSE ) {
    if( empty( $value ))
      $value = ( isset( $this->sequence['value'] ) && ( 0 < $this->sequence['value'] )) ? $this->sequence['value'] + 1 : 1;
    $this->sequence = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: STATUS
 */
/**
 * creates formatted output for calendar component property status
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createStatus() {
    if( empty( $this->status )) return FALSE;
    if( empty( $this->status['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'STATUS' ) : FALSE;
    $attributes = $this->_createParams( $this->status['params'] );
    return $this->_createElement( 'STATUS', $attributes, $this->status['value'] );
  }
/**
 * set calendar component property status
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param array $params optional
 * @return bool
 */
  function setStatus( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->status = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: SUMMARY
 */
/**
 * creates formatted output for calendar component property summary
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createSummary() {
    if( empty( $this->summary )) return FALSE;
    if( empty( $this->summary['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'SUMMARY' ) : FALSE;
    $attributes = $this->_createParams( $this->summary['params'], array( 'ALTREP', 'LANGUAGE' ));
    $content    = $this->_strrep( $this->summary['value'] );
    return $this->_createElement( 'SUMMARY', $attributes, $content );
  }
/**
 * set calendar component property summary
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return bool
 */
  function setSummary( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->summary = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: TZID
 */
/**
 * creates formatted output for calendar component property tzid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createTzid() {
    if( empty( $this->tzid )) return FALSE;
    if( empty( $this->tzid['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'TZID' ) : FALSE;
    $attributes = $this->_createParams( $this->tzid['params'] );
    return $this->_createElement( 'TZID', $attributes, $this->_strrep( $this->tzid['value'] ));
  }
/**
 * set calendar component property tzid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param array $params optional
 * @return bool
 */
  function setTzid( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->tzid = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * .. .
 * Property Name: TZNAME
 */
/**
 * creates formatted output for calendar component property tzname
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createTzname() {
    if( empty( $this->tzname )) return FALSE;
    $output = null;
    foreach( $this->tzname as $theName ) {
      if( !empty( $theName['value'] )) {
        $attributes = $this->_createParams( $theName['params'], array( 'LANGUAGE' ));
        $output    .= $this->_createElement( 'TZNAME', $attributes, $this->_strrep( $theName['value'] ));
      }
      elseif( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( 'TZNAME' );
    }
    return $output;
  }
/**
 * set calendar component property tzname
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param string $value
 * @param string $params, optional
 * @param integer $index, optional
 * @return bool
 */
  function setTzname( $value, $params=FALSE, $index=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->_setMval( $this->tzname, $value, $params, FALSE, $index );
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: TZOFFSETFROM
 */
/**
 * creates formatted output for calendar component property tzoffsetfrom
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createTzoffsetfrom() {
    if( empty( $this->tzoffsetfrom )) return FALSE;
    if( empty( $this->tzoffsetfrom['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'TZOFFSETFROM' ) : FALSE;
    $attributes = $this->_createParams( $this->tzoffsetfrom['params'] );
    return $this->_createElement( 'TZOFFSETFROM', $attributes, $this->tzoffsetfrom['value'] );
  }
/**
 * set calendar component property tzoffsetfrom
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return bool
 */
  function setTzoffsetfrom( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->tzoffsetfrom = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: TZOFFSETTO
 */
/**
 * creates formatted output for calendar component property tzoffsetto
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createTzoffsetto() {
    if( empty( $this->tzoffsetto )) return FALSE;
    if( empty( $this->tzoffsetto['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'TZOFFSETTO' ) : FALSE;
    $attributes = $this->_createParams( $this->tzoffsetto['params'] );
    return $this->_createElement( 'TZOFFSETTO', $attributes, $this->tzoffsetto['value'] );
  }
/**
 * set calendar component property tzoffsetto
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return bool
 */
  function setTzoffsetto( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->tzoffsetto = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: TZURL
 */
/**
 * creates formatted output for calendar component property tzurl
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createTzurl() {
    if( empty( $this->tzurl )) return FALSE;
    if( empty( $this->tzurl['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'TZURL' ) : FALSE;
    $attributes = $this->_createParams( $this->tzurl['params'] );
    return $this->_createElement( 'TZURL', $attributes, $this->tzurl['value'] );
  }
/**
 * set calendar component property tzurl
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return boll
 */
  function setTzurl( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->tzurl = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: UID
 */
/**
 * creates formatted output for calendar component property uid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @return string
 */
  function createUid() {
    if( 0 >= count( $this->uid ))
      $this->_makeuid();
    $attributes = $this->_createParams( $this->uid['params'] );
    return $this->_createElement( 'UID', $attributes, $this->uid['value'] );
  }
/**
 * create an unique id for this calendar component object instance
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.7 - 2007-09-04
 * @return void
 */
  function _makeUid() {
    $date   = date('Ymd\THisT');
    $unique = substr(microtime(), 2, 4);
    $base   = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPrRsStTuUvVxXuUvVwWzZ1234567890';
    $start  = 0;
    $end    = strlen( $base ) - 1;
    $length = 6;
    $str    = null;
    for( $p = 0; $p < $length; $p++ )
      $unique .= $base{mt_rand( $start, $end )};
    $this->uid = array( 'params' => null );
    $this->uid['value']  = $date.'-'.$unique.'@'.$this->getConfig( 'unique_id' );
  }
/**
 * set calendar component property uid
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return bool
 */
  function setUid( $value, $params=FALSE ) {
    if( empty( $value )) return FALSE; // no allowEmpty check here !!!!
    $this->uid = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: URL
 */
/**
 * creates formatted output for calendar component property url
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-21
 * @return string
 */
  function createUrl() {
    if( empty( $this->url )) return FALSE;
    if( empty( $this->url['value'] ))
      return ( $this->getConfig( 'allowEmpty' )) ? $this->_createElement( 'URL' ) : FALSE;
    $attributes = $this->_createParams( $this->url['params'] );
    return $this->_createElement( 'URL', $attributes, $this->url['value'] );
  }
/**
 * set calendar component property url
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-11-04
 * @param string $value
 * @param string $params optional
 * @return bool
 */
  function setUrl( $value, $params=FALSE ) {
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $this->url = array( 'value' => $value, 'params' => $this->_setParams( $params ));
    return TRUE;
  }
/*********************************************************************************/
/**
 * Property Name: x-prop
 */
/**
 * creates formatted output for calendar component property x-prop
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.11 - 2008-10-22
 * @return string
 */
  function createXprop() {
    if( empty( $this->xprop )) return FALSE;
    $output = null;
    foreach( $this->xprop as $label => $xpropPart ) {
      if( empty( $xpropPart['value'] )) {
        if( $this->getConfig( 'allowEmpty' )) $output .= $this->_createElement( $label );
        continue;
      }
      $attributes = $this->_createParams( $xpropPart['params'], array( 'LANGUAGE' ));
      if( is_array( $xpropPart['value'] )) {
        foreach( $xpropPart['value'] as $pix => $theXpart )
          $xpropPart['value'][$pix] = $this->_strrep( $theXpart );
        $xpropPart['value']  = implode( ',', $xpropPart['value'] );
      }
      else
        $xpropPart['value'] = $this->_strrep( $xpropPart['value'] );
      $output    .= $this->_createElement( $label, $attributes, $xpropPart['value'] );
    }
    return $output;
  }
/**
 * set calendar component property x-prop
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
	* @since 2.4.11 - 2008-11-04
 * @param string $label
 * @param mixed $value
 * @param array $params optional
 * @return bool
 */
  function setXprop( $label, $value, $params=FALSE ) {
    if( empty( $label )) return;
    if( empty( $value )) if( $this->getConfig( 'allowEmpty' )) $value = null; else return FALSE;
    $xprop           = array( 'value' => $value );
    $toolbox         = new hc_calendarComponent();
    $xprop['params'] = $toolbox->_setParams( $params );
    if( !is_array( $this->xprop )) $this->xprop = array();
    $this->xprop[strtoupper( $label )] = $xprop;
    return TRUE;
  }
/*********************************************************************************/
/*********************************************************************************/
/**
 * create element format parts
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.0.6 - 2006-06-20
 * @return string
 */
  function _createFormat() {
    $objectname                   = null;
    switch( $this->format ) {
      case 'xcal':
        $objectname               = ( isset( $this->timezonetype )) ?
                                 strtolower( $this->timezonetype )  :  strtolower( $this->objName );
        $this->componentStart1    = $this->elementStart1 = '<';
        $this->componentStart2    = $this->elementStart2 = '>';
        $this->componentEnd1      = $this->elementEnd1   = '</';
        $this->componentEnd2      = $this->elementEnd2   = '>'.$this->nl;
        $this->intAttrDelimiter   = '<!-- -->';
        $this->attributeDelimiter = $this->nl;
        $this->valueInit          = null;
        break;
      default:
        $objectname               = ( isset( $this->timezonetype )) ?
                                 strtoupper( $this->timezonetype )  :  strtoupper( $this->objName );
        $this->componentStart1    = 'BEGIN:';
        $this->componentStart2    = null;
        $this->componentEnd1      = 'END:';
        $this->componentEnd2      = $this->nl;
        $this->elementStart1      = null;
        $this->elementStart2      = null;
        $this->elementEnd1        = null;
        $this->elementEnd2        = $this->nl;
        $this->intAttrDelimiter   = '<!-- -->';
        $this->attributeDelimiter = ';';
        $this->valueInit          = ':';
        break;
    }
    return $objectname;
  }
/**
 * creates formatted output for calendar component property
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-23
 * @param string $label property name
 * @param string $attributes property attributes
 * @param string $content property content (optional)
 * @return string
 */
  function _createElement( $label, $attributes=null, $content=FALSE ) {
    $label  = $this->_formatPropertyName( $label );
    $output = $this->elementStart1.$label;
    $categoriesAttrLang = null;
    $attachInlineBinary = FALSE;
    $attachfmttype      = null;
    if( !empty( $attributes ))  {
      $attributes  = trim( $attributes );
      if ( 'xcal' == $this->format) {
        $attributes2 = explode( $this->intAttrDelimiter, $attributes );
        $attributes  = null;
        foreach( $attributes2 as $attribute ) {
          $attrKVarr = explode( '=', $attribute );
          if( empty( $attrKVarr[0] ))
            continue;
          if( !isset( $attrKVarr[1] )) {
            $attrValue = $attrKVarr[0];
            $attrKey   = null;
          }
          elseif( 2 == count( $attrKVarr)) {
            $attrKey   = strtolower( $attrKVarr[0] );
            $attrValue = $attrKVarr[1];
          }
          else {
            $attrKey   = strtolower( $attrKVarr[0] );
            unset( $attrKVarr[0] );
            $attrValue = implode( '=', $attrKVarr );
          }
          if(( 'attach' == $label ) && ( in_array( $attrKey, array( 'fmttype', 'encoding', 'value' )))) {
            $attachInlineBinary = TRUE;
            if( 'fmttype' == $attrKey )
              $attachfmttype = $attrKey.'='.$attrValue;
            continue;
          }
          elseif(( 'categories' == $label ) && ( 'language' == $attrKey ))
            $categoriesAttrLang = $attrKey.'='.$attrValue;
          else {
            $attributes .= ( empty( $attributes )) ? ' ' : $this->attributeDelimiter.' ';
            $attributes .= ( !empty( $attrKey )) ? $attrKey.'=' : null;
            if(( '"' == substr( $attrValue, 0, 1 )) && ( '"' == substr( $attrValue, -1 ))) {
              $attrValue = substr( $attrValue, 1, ( strlen( $attrValue ) - 2 ));
              $attrValue = str_replace( '"', '', $attrValue );
            }
            $attributes .= '"'.htmlspecialchars( $attrValue ).'"';
          }
        }
      }
      else {
        $attributes = str_replace( $this->intAttrDelimiter, $this->attributeDelimiter, $attributes );
      }
    }
    if(((( 'attach' == $label ) && !$attachInlineBinary ) ||
         ( in_array( $label, array( 'tzurl', 'url' ))))      && ( 'xcal' == $this->format)) {
      $pos = strrpos($content, "/");
      $docname = ( $pos !== false) ? substr( $content, (1 - strlen( $content ) + $pos )) : $content;
      $this->xcaldecl[] = array( 'xmldecl'  => 'ENTITY'
                               , 'uri'      => $docname
                               , 'ref'      => 'SYSTEM'
                               , 'external' => $content
                               , 'type'     => 'NDATA'
                               , 'type2'    => 'BINERY' );
      $attributes .= ( empty( $attributes )) ? ' ' : $this->attributeDelimiter.' ';
      $attributes .= 'uri="'.$docname.'"';
      $content = null;
      if( 'attach' == $label ) {
        $attributes = str_replace( $this->attributeDelimiter, $this->intAttrDelimiter, $attributes );
        $content = $this->_createElement( 'extref', $attributes, null );
        $attributes = null;
      }
    }
    elseif(( 'attach' == $label ) && $attachInlineBinary && ( 'xcal' == $this->format)) {
      $content = $this->nl.$this->_createElement( 'b64bin', $attachfmttype, $content ); // max one attribute
    }
    $output .= $attributes;
    if( !$content ) {
      switch( $this->format ) {
        case 'xcal':
          $output .= ' /';
          $output .= $this->elementStart2;
          return $output;
          break;
        default:
          $output .= $this->elementStart2.$this->valueInit;
          return $this->_size75( $output );
          break;
      }
    }
    $output .= $this->elementStart2;
    $output .= $this->valueInit.$content;
    switch( $this->format ) {
      case 'xcal':
        return $output.$this->elementEnd1.$label.$this->elementEnd2;
        break;
      default:
        return $this->_size75( $output );
        break;
    }
  }
/**
 * creates formatted output for calendar component property parameters
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.22 - 2007-04-10
 * @param array $params  optional
 * @param array $ctrKeys optional
 * @return string
 */
  function _createParams( $params=array(), $ctrKeys=array() ) {
    $attrLANG = $attr1 = $attr2 = null;
    $CNattrKey   = ( in_array( 'CN',       $ctrKeys )) ? TRUE : FALSE ;
    $LANGattrKey = ( in_array( 'LANGUAGE', $ctrKeys )) ? TRUE : FALSE ;
    $CNattrExist = $LANGattrExist = FALSE;
    if( is_array( $params )) {
      foreach( $params as $paramKey => $paramValue ) {
        if( is_int( $paramKey ))
          $attr2            .= $this->intAttrDelimiter.$paramValue;
        elseif(( 'LANGUAGE' == $paramKey ) && $LANGattrKey ) {
          $attrLANG         .= $this->intAttrDelimiter."LANGUAGE=$paramValue";
          $LANGattrExist     = TRUE;
        }
        elseif(( 'CN'       == $paramKey ) && $CNattrKey ) {
          $attr1             = $this->intAttrDelimiter.'CN="'.$paramValue.'"';
          $CNattrExist       = TRUE;
        }
        elseif(( 'ALTREP'   == $paramKey ) && in_array( $paramKey, $ctrKeys ))
          $attr2            .= $this->intAttrDelimiter.'ALTREP="'.$paramValue.'"';
        elseif(( 'DIR'      == $paramKey ) && in_array( $paramKey, $ctrKeys ))
          $attr2            .= $this->intAttrDelimiter.'DIR="'.$paramValue.'"';
        elseif(( 'SENT-BY'  == $paramKey ) && in_array( $paramKey, $ctrKeys ))
          $attr2            .= $this->intAttrDelimiter.'SENT-BY="MAILTO:'.$paramValue.'"';
        else
          $attr2            .= $this->intAttrDelimiter."$paramKey=$paramValue";
      }
    }
    if( !$LANGattrExist ) {
      $lang = $this->getConfig( 'language' );
      if(( $CNattrExist || $LANGattrKey ) && $lang )
        $attrLANG .= $this->intAttrDelimiter.'LANGUAGE='.$lang;
    }
    return $attrLANG.$attr1.$attr2;
  }
/**
 * ensures internal date-time/date format for input date-time/date in array format
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.3.0 - 2006-08-15
 * @param array $datetime
 * @param int $parno optional, default FALSE
 * @return array
 */
  function _date_time_array( $datetime, $parno=FALSE ) {
    $output = array();
    foreach( $datetime as $dateKey => $datePart ) {
      switch ( $dateKey ) {
        case '0': case 'year':   $output['year']  = $datePart; break;
        case '1': case 'month':  $output['month'] = $datePart; break;
        case '2': case 'day':    $output['day']   = $datePart; break;
      }
      if( 3 != $parno ) {
        switch ( $dateKey ) {
          case '0':
          case '1':
          case '2': break;
          case '3': case 'hour': $output['hour']  = $datePart; break;
          case '4': case 'min' : $output['min']   = $datePart; break;
          case '5': case 'sec' : $output['sec']   = $datePart; break;
          case '6': case 'tz'  : $output['tz']    = $datePart; break;
        }
      }
    }
    if( 3 != $parno ) {
      if( !isset( $output['hour'] ))
        $output['hour'] = 0;
      if( !isset( $output['min']  ))
        $output['min'] = 0;
      if( !isset( $output['sec']  ))
        $output['sec'] = 0;
    }
    return $output;
  }
/**
 * ensures internal date-time/date format for input date-time/date in string fromat
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.10 - 2007-10-19
 * @param array $datetime
 * @param int $parno optional, default FALSE
 * @return array
 */
  function _date_time_string( $datetime, $parno=FALSE ) {
    $datetime = (string) trim( $datetime );
    $tz  = null;
    $len = strlen( $datetime ) - 1;
    if( 'Z' == substr( $datetime, -1 )) {
      $tz = 'Z';
      $datetime = trim( substr( $datetime, 0, $len ));
    }
    elseif( ( ctype_digit( substr( $datetime, -2, 2 ))) && // time or date
                  ( '-' == substr( $datetime, -3, 1 )) ||
                  ( ':' == substr( $datetime, -3, 1 )) ||
                  ( '.' == substr( $datetime, -3, 1 ))) {
      $continue = TRUE;
    }
    elseif( ( ctype_digit( substr( $datetime, -4, 4 ))) && // 4 pos offset
            ( ' +' == substr( $datetime, -6, 2 )) ||
            ( ' -' == substr( $datetime, -6, 2 ))) {
      $tz = substr( $datetime, -5, 5 );
      $datetime = substr( $datetime, 0, ($len - 5));
    }
    elseif( ( ctype_digit( substr( $datetime, -6, 6 ))) && // 6 pos offset
            ( ' +' == substr( $datetime, -8, 2 )) ||
            ( ' -' == substr( $datetime, -8, 2 ))) {
      $tz = substr( $datetime, -7, 7 );
      $datetime = substr( $datetime, 0, ($len - 7));
    }
    elseif( ( 6 < $len ) && ( ctype_digit( substr( $datetime, -6, 6 )))) {
      $continue = TRUE;
    }
    elseif( 'T' ==  substr( $datetime, -7, 1 )) {
      $continue = TRUE;
    }
    else {
      $cx  = $tx = 0;    //  19970415T133000 US-Eastern
      for( $cx = -1; $cx > ( 9 - $len ); $cx-- ) {
        if(( ' ' == substr( $datetime, $cx, 1 )) || ctype_digit( substr( $datetime, $cx, 1 )))
          break; // if exists, tz ends here.. . ?
        elseif( ctype_alpha( substr( $datetime, $cx, 1 )) ||
             ( in_array( substr( $datetime, $cx, 1 ), array( '-', '/' ))))
          $tx--; // tz length counter
      }
      if( 0 > $tx ) {
        $tz = substr( $datetime, $tx );
        $datetime = trim( substr( $datetime, 0, $len + $tx + 1 ));
      }
    }
    if( 0 < substr_count( $datetime, '-' )) {
      $datetime = str_replace( '-', '/', $datetime );
    }
    elseif( ctype_digit( substr( $datetime, 0, 8 )) &&
           ( 'T' ==      substr( $datetime, 8, 1 )) &&
            ctype_digit( substr( $datetime, 9, 6 ))) {
      $datetime = substr( $datetime,  4, 2 )
             .'/'.substr( $datetime,  6, 2 )
             .'/'.substr( $datetime,  0, 4 )
             .' '.substr( $datetime,  9, 2 )
             .':'.substr( $datetime, 11, 2 )
             .':'.substr( $datetime, 13);
    }
    $datestring = date( 'Y-m-d H:i:s', strtotime( $datetime ));
    $tz                = trim( $tz );
    $output            = array();
    $output['year']    = substr( $datestring, 0, 4 );
    $output['month']   = substr( $datestring, 5, 2 );
    $output['day']     = substr( $datestring, 8, 2 );
    if(( 6 == $parno ) || ( 7 == $parno )) {
      $output['hour']  = substr( $datestring, 11, 2 );
      $output['min']   = substr( $datestring, 14, 2 );
      $output['sec']   = substr( $datestring, 17, 2 );
      if( !empty( $tz ))
        $output['tz']  = $tz;
    }
    elseif( 3 != $parno ) {
      if(( '00' < substr( $datestring, 11, 2 )) ||
         ( '00' < substr( $datestring, 14, 2 )) ||
         ( '00' < substr( $datestring, 17, 2 ))) {
        $output['hour']  = substr( $datestring, 11, 2 );
        $output['min']   = substr( $datestring, 14, 2 );
        $output['sec']   = substr( $datestring, 17, 2 );
      }
      if( !empty( $tz ))
        $output['tz']  = $tz;
    }
    return $output;
  }
/**
 * ensures internal duration format for input in array format
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.1.1 - 2007-06-24
 * @param array $duration
 * @return array
 */
  function _duration_array( $duration ) {
    $output = array();
    if(    is_array( $duration )        &&
       ( 1 == count( $duration ))       &&
              isset( $duration['sec'] ) &&
              ( 60 < $duration['sec'] )) {
      $durseconds  = $duration['sec'];
      $output['week'] = floor( $durseconds / ( 60 * 60 * 24 * 7 ));
      $durseconds  =           $durseconds % ( 60 * 60 * 24 * 7 );
      $output['day']  = floor( $durseconds / ( 60 * 60 * 24 ));
      $durseconds  =           $durseconds % ( 60 * 60 * 24 );
      $output['hour'] = floor( $durseconds / ( 60 * 60 ));
      $durseconds  =           $durseconds % ( 60 * 60 );
      $output['min']  = floor( $durseconds / ( 60 ));
      $output['sec']  =      ( $durseconds % ( 60 ));
    }
    else {
      foreach( $duration as $durKey => $durValue ) {
        if( empty( $durValue )) continue;
        switch ( $durKey ) {
          case '0': case 'week': $output['week']  = $durValue; break;
          case '1': case 'day':  $output['day']   = $durValue; break;
          case '2': case 'hour': $output['hour']  = $durValue; break;
          case '3': case 'min':  $output['min']   = $durValue; break;
          case '4': case 'sec':  $output['sec']   = $durValue; break;
        }
      }
    }
    if( isset( $output['week'] ) && ( 0 < $output['week'] )) {
      unset( $output['day'], $output['hour'], $output['min'], $output['sec'] );
      return $output;
    }
    unset( $output['week'] );
    if( empty( $output['day'] ))
      unset( $output['day'] );
    if ( isset( $output['hour'] ) || isset( $output['min'] ) || isset( $output['sec'] )) {
      if( !isset( $output['hour'] )) $output['hour'] = 0;
      if( !isset( $output['min']  )) $output['min']  = 0;
      if( !isset( $output['sec']  )) $output['sec']  = 0;
      if(( 0 == $output['hour'] ) && ( 0 == $output['min'] ) && ( 0 == $output['sec'] ))
        unset( $output['hour'], $output['min'], $output['sec'] );
    }
    return $output;
  }
/**
 * convert duration to date in array format based on input or dtstart value
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-30
 * @param array $startdate, optional
 * @param array $duration, optional
 * @return array, date format
 */
  function duration2date( $startdate=FALSE, $duration=FALSE ) {
    if( $startdate && $duration ) {
      $d1               = $startdate;
      $dur              = $duration;
    }
    elseif( isset( $this->dtstart['value'] ) && isset( $this->duration['value'] )) {
      $d1               = $this->dtstart['value'];
      $dur              = $this->duration['value'];
    }
    else
      return null;
    $dateOnly         = ( isset( $d1['hour'] ) || isset( $d1['min'] ) || isset( $d1['sec'] )) ? FALSE : TRUE;
    $d1['hour']       = ( isset( $d1['hour'] )) ? $d1['hour'] : 0;
    $d1['min']        = ( isset( $d1['min'] ))  ? $d1['min']  : 0;
    $d1['sec']        = ( isset( $d1['sec'] ))  ? $d1['sec']  : 0;
    $dtend = mktime( $d1['hour'], $d1['min'], $d1['sec'], $d1['month'], $d1['day'], $d1['year'] );
    if( isset( $dur['week'] ))
      $dtend += ( $dur['week'] * 7 * 24 * 60 * 60 );
    if( isset( $dur['day'] ))
      $dtend += ( $dur['day'] * 24 * 60 * 60 );
    if( isset( $dur['hour'] ))
      $dtend += ( $dur['hour'] * 60 *60 );
    if( isset( $dur['min'] ))
      $dtend += ( $dur['min'] * 60 );
    if( isset( $dur['sec'] ))
      $dtend +=   $dur['sec'];
    $dtend2 = array();
    $dtend2['year']   = date('Y', $dtend );
    $dtend2['month']  = date('m', $dtend );
    $dtend2['day']    = date('d', $dtend );
    $dtend2['hour']   = date('H', $dtend );
    $dtend2['min']    = date('i', $dtend );
    $dtend2['sec']    = date('s', $dtend );
    if( isset( $d1['tz'] ))
      $dtend2['tz']   = $d1['tz'];
    if( $dateOnly && (( 0 == $dtend2['hour'] ) && ( 0 == $dtend2['min'] ) && ( 0 == $dtend2['sec'] )))
      unset( $dtend2['hour'], $dtend2['min'], $dtend2['sec'] );
    return $dtend2;
  }
/**
 * ensures internal duration format for input in string format
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.0.5 - 2007-03-14
 * @param string $duration
 * @return array
 */
  function _duration_string( $duration ) {
   $duration = (string) trim( $duration );
   while( 'P' != strtoupper( substr( $duration, 0, 1 ))) {
     if( 0 < strlen( $duration ))
       $duration = substr( $duration, 1 );
     else
       return false; // no leading P !?!?
   }
   $duration = substr( $duration, 1 ); // skip P
   $duration = str_replace ( 't', 'T', $duration );
   $duration = str_replace ( 'T', '', $duration );
   $output = array();
   $val    = null;
   for( $ix=0; $ix < strlen( $duration ); $ix++ ) {
     switch( strtoupper( $duration{$ix} )) {
      case 'W':
        $output['week'] = $val;
        $val            = null;
        break;
      case 'D':
        $output['day']  = $val;
        $val            = null;
        break;
      case 'H':
        $output['hour'] = $val;
        $val            = null;
        break;
      case 'M':
        $output['min']  = $val;
        $val            = null;
        break;
      case 'S':
        $output['sec']  = $val;
        $val            = null;
        break;
      default:
        if( !ctype_digit( $duration{$ix} ))
          return false; // unknown duration controll character  !?!?
        else
          $val .= $duration{$ix};
     }
   }
   return $this->_duration_array( $output );
  }
/**
 * if not preSet, if exist, remove key with expected value from array and return hit value else return elseValue
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-11-08
 * @param array $array
 * @param string $expkey, expected key
 * @param string $expval, expected value
 * @param int $hitVal optional, return value if found
 * @param int $elseVal optional, return value if not found
 * @param int $preSet optional, return value if already preset
 * @return int
 */
  function _existRem( &$array, $expkey, $expval=FALSE, $hitVal=null, $elseVal=null, $preSet=null ) {
    if( $preSet )
      return $preSet;
    if( !is_array( $array ) || ( 0 == count( $array )))
      return $elseVal;
    foreach( $array as $key => $value ) {
      if( strtoupper( $expkey ) == strtoupper( $key )) {
        if( !$expval || ( strtoupper( $expval ) == strtoupper( $array[$key] ))) {
          unset( $array[$key] );
          return $hitVal;
        }
      }
    }
    return $elseVal;
  }
/**
 * creates formatted output for calendar component property data value type date/date-time
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-30
 * @param array   $datetime
 * @param int     $parno, optional, default 6
 * @return string
 */
  function _format_date_time( $datetime, $parno=6 ) {
    if( !isset( $datetime['year'] )  &&
        !isset( $datetime['month'] ) &&
        !isset( $datetime['day'] )   &&
        !isset( $datetime['hour'] )  &&
        !isset( $datetime['min'] )   &&
        !isset( $datetime['sec'] ))
      return ;
    $output = null;
    // if( !isset( $datetime['day'] )) { $o=''; foreach($datetime as $k=>$v) {if(is_array($v)) $v=implode('-',$v);$o.=" $k=>$v";} echo " day SAKNAS : $o <br />\n"; }
    foreach( $datetime as $dkey => $dvalue ) {
      if( 'tz' != $dkey )
        $datetime[$dkey] = (integer) $dvalue;
    }
    $output = date('Ymd', mktime( 0, 0, 0, $datetime['month'], $datetime['day'], $datetime['year']));
    if( isset( $datetime['hour'] )  ||
        isset( $datetime['min'] )   ||
        isset( $datetime['sec'] )   ||
        isset( $datetime['tz'] )) {
      if( isset( $datetime['tz'] )  &&
         !isset( $datetime['hour'] ))
        $datetime['hour'] = 0;
      if( isset( $datetime['hour'] )  &&
         !isset( $datetime['min'] ))
        $datetime['min'] = 0;
      if( isset( $datetime['hour'] )  &&
          isset( $datetime['min'] )   &&
         !isset( $datetime['sec'] ))
        $datetime['sec'] = 0;
      $date = mktime( $datetime['hour'], $datetime['min'], $datetime['sec'], $datetime['month'], $datetime['day'], $datetime['year']);
      $output .= date('\THis', $date );
      if( isset( $datetime['tz'] ) && ( '' < trim ( $datetime['tz'] ))) {
        $datetime['tz'] = trim( $datetime['tz'] );
        if( 'Z' == $datetime['tz'] )
          $output .= 'Z';
        $offset = $this->_tz2offset( $datetime['tz'] );
        if( 0 != $offset ) {
          $date = mktime( $datetime['hour'], $datetime['min'], ($datetime['sec'] + $offset), $datetime['month'], $datetime['day'], $datetime['year']);
          $output    = date( 'Ymd\THis\Z', $date );
        }
      }
      elseif( 7 == $parno )
        $output .= 'Z';
    }
    return $output;
  }
/**
 * creates formatted output for calendar component property data value type duration
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-10
 * @param array $duration ( week, day, hour, min, sec )
 * @return string
 */
  function _format_duration( $duration ) {
    if( !isset( $duration['week'] ) &&
        !isset( $duration['day'] )  &&
        !isset( $duration['hour'] ) &&
        !isset( $duration['min'] )  &&
        !isset( $duration['sec'] ))
      return;
    $output = 'P';
    if( isset( $duration['week'] ) && ( 0 < $duration['week'] ))
      $output   .= $duration['week'].'W';
    else {
      if( isset($duration['day'] ) && ( 0 < $duration['day'] ))
        $output .= $duration['day'].'D';
      if(( isset( $duration['hour']) && ( 0 < $duration['hour'] )) ||
         ( isset( $duration['min'])  && ( 0 < $duration['min'] ))  ||
         ( isset( $duration['sec'])  && ( 0 < $duration['sec'] ))) {
        $output .= 'T';
        $output .= ( isset( $duration['hour']) && ( 0 < $duration['hour'] )) ? $duration['hour'].'H' : '0H';
        $output .= ( isset( $duration['min'])  && ( 0 < $duration['min'] ))  ? $duration['min']. 'M' : '0M';
        $output .= ( isset( $duration['sec'])  && ( 0 < $duration['sec'] ))  ? $duration['sec']. 'S' : '0S';
      }
    }
    return $output;
  }
/**
 * create property name case - lower/upper
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 0.9.7 - 2006-11-20
 * @param string $propertyName
 * @return string
 */
  function _formatPropertyName( $propertyName ) {
    switch( $this->format ) {
      case 'xcal':
        return strtolower( $propertyName );
        break;
      default:
        return strtoupper( $propertyName );
        break;
    }
  }
/**
 * checks if input array contains a date
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-25
 * @param array $input
 * @return bool
 */
  function _isArrayDate( $input ) {
    if( isset( $input['week'] ) || ( !in_array( count( $input ), array( 3, 6, 7 ))))
      return FALSE;
    if( 7 == count( $input ))
      return TRUE;
    if( isset( $input['year'] ) && isset( $input['month'] ) && isset( $input['day'] ))
      return checkdate( (int) $input['month'], (int) $input['day'], (int) $input['year'] );
    if( isset( $input['day'] ) || isset( $input['hour'] ) || isset( $input['min'] ) || isset( $input['sec'] ))
      return FALSE;
    if( in_array( 0, $input ))
      return FALSE;
    if(( 1970 > $input[0] ) || ( 12 < $input[1] ) || ( 31 < $input[2] ))
      return FALSE;
    if(( isset( $input[0] ) && isset( $input[1] ) && isset( $input[2] )) &&
         checkdate( (int) $input[1], (int) $input[2], (int) $input[0] ))
      return TRUE;
    $input = $this->_date_time_string( $input[1].'/'.$input[2].'/'.$input[0], 3 ); //  m - d - Y
    if( isset( $input['year'] ) && isset( $input['month'] ) && isset( $input['day'] ))
      return checkdate( (int) $input['month'], (int) $input['day'], (int) $input['year'] );
    return FALSE;
  }
/**
 * checks if input array contains a timestamp date
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-18
 * @param array $input
 * @return bool
 */
  function _isArrayTimestampDate( $input ) {
    return ( is_array( $input ) && isset( $input['timestamp'] )) ? TRUE : FALSE ;
  }
/**
 * controll if input string contains traling UTC offset
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-19
 * @param string $input
 * @return bool
 */
  function _isOffset( $input ) {
    $input         = trim( (string) $input );
    if( 'Z' == substr( $input, -1 ))
      return TRUE;
    elseif((   5 <= strlen( $input )) &&
       ( in_array( substr( $input, -5, 1 ), array( '+', '-' ))) &&
       (   '0000'  < substr( $input, -4 )) && (   '9999' >= substr( $input, -4 )))
      return TRUE;
    elseif((    7 <= strlen( $input )) &&
       ( in_array( substr( $input, -7, 1 ), array( '+', '-' ))) &&
       ( '000000'  < substr( $input, -6 )) && ( '999999' >= substr( $input, -6 )))
      return TRUE;
    return FALSE;

  }
/**
 * check if property not exists within component
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-15
 * @param string $propName
 * @return bool
 */
  function _notExistProp( $propName ) {
    if( empty( $propName )) return FALSE; // when deleting x-prop, an empty propName may be used=allowed
    $propName = strtolower( $propName );
    if(     'last-modified'    == $propName )  { if( !isset( $this->lastmodified ))    return TRUE; }
    elseif((       'x-' != substr($propName,0,2)) && !isset( $this->$propName ))       return TRUE;
    return FALSE;
  }

/**
 * convert format for input date to internal date with parameters
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.17 - 2008-10-31
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @param string $caller optional
 * @return array
 */
  function _setDate( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $tz=FALSE, $params=FALSE, $caller=null ) {
    $input = $parno = null;
    $localtime = (( 'dtstart' == $caller ) && in_array( $this->objName, array( 'hc_vtimezone', 'standard', 'daylight' ))) ? TRUE : FALSE;
    if( $this->_isArrayDate( $year )) {
      if( $localtime ) unset ( $month['VALUE'], $month['TZID'] );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ));
      if( isset( $input['params']['TZID'] )) {
        $input['params']['VALUE'] = 'DATE-TIME';
        unset( $year['tz'] );
      }
      $hitval          = (( !empty( $year['tz'] ) || !empty( $year[6] ))) ? 7 : 6;
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE-TIME', $hitval );
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE', 3, count( $year ), $parno );
      $input['value']  = $this->_date_time_array( $year, $parno );
    }
    elseif( $this->_isArrayTimestampDate( $year )) {
      if( $localtime ) unset ( $month['VALUE'], $month['TZID'] );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ));
      if( isset( $input['params']['TZID'] )) {
        $input['params']['VALUE'] = 'DATE-TIME';
        unset( $year['tz'] );
      }
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE', 3 );
      $hitval          = ( isset( $year['tz'] )) ? 7 : 6;
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE-TIME', $hitval, $parno );
      $input['value']  = $this->_timestamp2date( $year, $parno );
    }
    elseif( 8 <= strlen( trim( $year ))) { // ex. 2006-08-03 10:12:18
      if( $localtime ) unset ( $month['VALUE'], $month['TZID'] );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ));
      if( isset( $input['params']['TZID'] )) {
        $input['params']['VALUE'] = 'DATE-TIME';
        $parno = 6;
      }
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE-TIME', 7, $parno );
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE', 3, $parno, $parno );
      $input['value']  = $this->_date_time_string( $year, $parno );
    }
    else {
      if( is_array( $params )) {
        if( $localtime ) unset ( $params['VALUE'], $params['TZID'] );
        $input['params'] = $this->_setParams( $params, array( 'VALUE' => 'DATE-TIME' ));
      }
      elseif( is_array( $tz )) {
        $input['params'] = $this->_setParams( $tz,     array( 'VALUE' => 'DATE-TIME' ));
        $tz = FALSE;
      }
      elseif( is_array( $hour )) {
        $input['params'] = $this->_setParams( $hour,   array( 'VALUE' => 'DATE-TIME' ));
        $hour = $min = $sec = $tz = FALSE;
      }
      if( isset( $input['params']['TZID'] )) {
        $tz            = null;
        $input['params']['VALUE'] = 'DATE-TIME';
      }
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE', 3 );
      $hitval          = ( !empty( $tz )) ? 7 : 6;
      $parno           = $this->_existRem( $input['params'], 'VALUE', 'DATE-TIME', $hitval, $parno, $parno );
      $input['value']  = array( 'year'  => $year, 'month' => $month, 'day'   => $day );
      if( 3 != $parno ) {
        $input['value']['hour'] = ( $hour ) ? $hour : '0';
        $input['value']['min']  = ( $min )  ? $min  : '0';
        $input['value']['sec']  = ( $sec )  ? $sec  : '0';
        if( !empty( $tz ))
          $input['value']['tz'] = $tz;
      }
    }
    if( 3 == $parno ) {
      $input['params']['VALUE'] = 'DATE';
      unset( $input['value']['tz'] );
      unset( $input['params']['TZID'] );
    }
    elseif( isset( $input['params']['TZID'] ))
      unset( $input['value']['tz'] );
    if( $localtime ) unset( $input['value']['tz'], $input['params']['TZID'] );
    if( isset( $input['value']['tz'] ))
      $input['value']['tz'] = (string) $input['value']['tz'];
    if( !empty( $input['value']['tz'] ) && ( 'Z' != $input['value']['tz'] ) &&
      ( !$this->_isOffset( $input['value']['tz'] )))
      $input['params']['TZID'] = $input['value']['tz'];
    return $input;
  }
/**
 * convert format for input date (UTC) to internal date with parameters
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.17 - 2008-10-31
 * @param mixed $year
 * @param mixed $month optional
 * @param int $day optional
 * @param int $hour optional
 * @param int $min optional
 * @param int $sec optional
 * @param array $params optional
 * @return array
 */
  function _setDate2( $year, $month=FALSE, $day=FALSE, $hour=FALSE, $min=FALSE, $sec=FALSE, $params=FALSE ) {
    $input = null;
    if( $this->_isArrayDate( $year )) {
      $input['value']  = $this->_date_time_array( $year, 7 );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ) );
    }
    elseif( $this->_isArrayTimestampDate( $year )) {
      $input['value']  = $this->_timestamp2date( $year, 7 );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ) );
    }
    elseif( 8 <= strlen( trim( $year ))) { // ex. 2006-08-03 10:12:18
      $input['value']  = $this->_date_time_string( $year, 7 );
      $input['params'] = $this->_setParams( $month, array( 'VALUE' => 'DATE-TIME' ) );
    }
    else {
      $input['value']  = array( 'year'  => $year
                              , 'month' => $month
                              , 'day'   => $day
                              , 'hour'  => $hour
                              , 'min'   => $min
                              , 'sec'   => $sec );
      $input['params'] = $this->_setParams( $params, array( 'VALUE' => 'DATE-TIME' ));
    }
    $parno = $this->_existRem( $input['params'], 'VALUE', 'DATE-TIME', 7 ); // remove default
    if( !isset( $input['value']['hour'] ))
      $input['value']['hour'] = 0;
    if( !isset( $input['value']['min'] ))
      $input['value']['min'] = 0;
    if( !isset( $input['value']['sec'] ))
      $input['value']['sec'] = 0;
    if( !isset( $input['value']['tz'] ) || !$this->_isOffset( $input['value']['tz'] ))
      $input['value']['tz'] = 'Z';
    return $input;
  }
/**
 * check index and set (an indexed) content in multiple value array
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-06
 * @param array $valArr
 * @param mixed $value
 * @param array $params
 * @param array $defaults
 * @param int $index
 * @return void
 */
  function _setMval( & $valArr, $value, $params=FALSE, $defaults=FALSE, $index=FALSE ) {
    if( !is_array( $valArr )) $valArr = array();
    if( $index )
      $index = $index - 1;
    elseif( 0 < count( $valArr )) {
      $index = end( array_keys( $valArr ));
      $index += 1;
    }
    else
      $index = 0;
    $valArr[$index] = array( 'value' => $value, 'params' => $this->_setParams( $params, $defaults ));
    ksort( $valArr );
  }
/**
 * set input (formatted) parameters- component property attributes
 *
 * default parameters can be set, if missing
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 1.x.x - 2007-05-01
 * @param array $params
 * @param array $defaults
 * @return array
 */
  function _setParams( $params, $defaults=FALSE ) {
    if( !is_array( $params))
      $params = array();
    $input = array();
    foreach( $params as $paramKey => $paramValue ) {
      if( is_array( $paramValue )) {
        foreach( $paramValue as $pkey => $pValue ) {
          if(( '"' == substr( $pValue, 0, 1 )) && ( '"' == substr( $pValue, -1 )))
            $paramValue[$pkey] = substr( $pValue, 1, ( strlen( $pValue ) - 2 ));
        }
      }
      elseif(( '"' == substr( $paramValue, 0, 1 )) && ( '"' == substr( $paramValue, -1 )))
        $paramValue = substr( $paramValue, 1, ( strlen( $paramValue ) - 2 ));
      if( 'VALUE' == strtoupper( $paramKey ))
        $input['VALUE']                 = strtoupper( $paramValue );
      else
        $input[strtoupper( $paramKey )] = $paramValue;
    }
    if( is_array( $defaults )) {
      foreach( $defaults as $paramKey => $paramValue ) {
        if( !isset( $input[$paramKey] ))
          $input[$paramKey] = $paramValue;
      }
    }
    return (0 < count( $input )) ? $input : null;
  }
/**
 * convert timestamp to date array
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-11-01
 * @param mixed $timestamp
 * @param int $parno
 * @return array
 */
  function _timestamp2date( $timestamp, $parno=6 ) {
    if( is_array( $timestamp )) {
      if(( 7 == $parno ) && !empty( $timestamp['tz'] ))
        $tz = $timestamp['tz'];
      $timestamp = $timestamp['timestamp'];
    }
    $output = array( 'year'  => date( 'Y', $timestamp )
                   , 'month' => date( 'm', $timestamp )
                   , 'day'   => date( 'd', $timestamp ));
    if( 3 != $parno ) {
             $output['hour'] =  date( 'H', $timestamp );
             $output['min']  =  date( 'i', $timestamp );
             $output['sec']  =  date( 's', $timestamp );
      if( isset( $tz ))
        $output['tz'] = $tz;
    }
    return $output;
  }
/**
 * convert (numeric) local time offset to seconds correcting localtime to GMT
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.16 - 2008-10-19
 * @param string $offset
 * @return integer
 */
  function _tz2offset( $tz ) {
    $tz           = trim( (string) $tz );
    $offset       = 0;
    if(((     5  != strlen( $tz )) && ( 7  != strlen( $tz ))) ||
       ((    '+' != substr( $tz, 0, 1 )) && ( '-' != substr( $tz, 0, 1 ))) ||
       (( '0000' >= substr( $tz, 1, 4 )) && ( '9999' < substr( $tz, 1, 4 ))) ||
           (( 7  == strlen( $tz )) && ( '00' > substr( $tz, 5, 2 )) && ( '99' < substr( $tz, 5, 2 ))))
      return $offset;
    $hours2sec    = (int) substr( $tz, 1, 2 ) * 3600;
    $min2sec      = (int) substr( $tz, 3, 2 ) *   60;
    $sec          = ( 7  == strlen( $tz )) ? (int) substr( $tz, -2 ) : '00';
    $offset       = $hours2sec + $min2sec + $sec;
    $offset       = ('-' == substr( $tz, 0, 1 )) ? $offset : -1 * $offset;
    return $offset;
  }
/*********************************************************************************/
/*********************************************************************************/
/**
 * get general component config variables or info about subcomponents
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-02
 * @param string $config
 * @return value
 */
  function getConfig( $config ) {
    switch( strtoupper( $config )) {
      case 'ALLOWEMPTY':
        return $this->allowEmpty;
        break;
      case 'COMPSINFO':
        unset( $this->compix );
        $info = array();
        if( isset( $this->components )) {
          foreach( $this->components as $cix => $component ) {
            if( empty( $component )) continue;
            unset( $component->propix );
            $info[$cix]['ordno'] = $cix + 1;
            $info[$cix]['type']  = $component->objName;
            $info[$cix]['uid']   = $component->getProperty( 'uid' );
            $info[$cix]['props'] = $component->getConfig( 'propinfo' );
            $info[$cix]['sub']   = $component->getConfig( 'compsinfo' );
            unset( $component->propix );
          }
        }
        return $info;
        break;
      case 'FORMAT':
        return $this->format;
        break;
      case 'LANGUAGE':
         // get language for calendar component as defined in [RFC 1766]
        return $this->language;
        break;
      case 'NL':
      case 'NEWLINECHAR':
        return $this->nl;
        break;
      case 'PROPINFO':
        $output = array();
        if( !in_array( $this->objName, array( 'hc_valarm', 'hc_vtimezone', 'standard', 'daylight' ))) {
          if( empty( $this->uid['value'] )) $this->_makeuid();
                                              $output['UID']              = 1;
        }
        if( !empty( $this->dtstamp ))         $output['DTSTAMP']          = 1;
        if( !empty( $this->summary ))         $output['SUMMARY']          = 1;
        if( !empty( $this->description ))     $output['DESCRIPTION']      = count( $this->description );
        if( !empty( $this->dtstart ))         $output['DTSTART']          = 1;
        if( !empty( $this->dtend ))           $output['DTEND']            = 1;
        if( !empty( $this->duration ))        $output['DURATION']         = 1;
        if( !empty( $this->attendee ))        $output['ATTENDEE']         = count( $this->attendee );
        if( !empty( $this->class ))           $output['CLASS']            = 1;
        if( !empty( $this->comment ))         $output['COMMENT']          = count( $this->comment );
        if( !empty( $this->completed ))       $output['COMPLETED']        = 1;
        if( !empty( $this->contact ))         $output['CONTACT']          = count( $this->contact );
        if( !empty( $this->created ))         $output['CREATED']          = 1;
        if( !empty( $this->lastmodified ))    $output['LAST-MODIFIED']    = 1;
        if( !empty( $this->location ))        $output['LOCATION']         = 1;
        if( !empty( $this->organizer ))       $output['ORGANIZER']        = 1;
        if( !empty( $this->priority ))        $output['PRIORITY']         = 1;
        if( !empty( $this->resources ))       $output['RESOURCES']        = count( $this->resources );
        if( !empty( $this->sequence ))        $output['SEQUENCE']         = 1;
        if( !empty( $this->status ))          $output['STATUS']           = 1;
        if( !empty( $this->tzid ))            $output['TZID']             = 1;
        if( !empty( $this->tzname ))          $output['TZNAME']           = count( $this->tzname );
        if( !empty( $this->tzoffsetfrom ))    $output['TZOFFSETTFROM']    = 1;
        if( !empty( $this->tzoffsetto ))      $output['TZOFFSETTO']       = 1;
        if( !empty( $this->tzurl ))           $output['TZURL']            = 1;
        if( !empty( $this->url ))             $output['URL']              = 1;
        if( !empty( $this->xprop ))           $output['X-PROP']           = count( $this->xprop );
        return $output;
        break;
      case 'UNIQUE_ID':
        if( empty( $this->unique_id ))
          $this->unique_id  = ( isset( $_SERVER['SERVER_NAME'] )) ? gethostbyname( $_SERVER['SERVER_NAME'] ) : 'localhost';
        return $this->unique_id;
        break;
    }
  }
/**
 * general component config setting
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.8 - 2008-10-24
 * @param string $config
 * @param string $value
 * @return void
 */
  function setConfig( $config, $value ) {
    $res = FALSE;
    switch( strtoupper( $config )) {
      case 'ALLOWEMPTY':
        $this->allowEmpty = $value;
        $subcfg = array( 'ALLOWEMPTY' => $value );
        $res    = TRUE;
        break;
      case 'FORMAT':
        $value  = trim( $value );
        $this->format = $value;
        $this->_createFormat();
        $subcfg = array( 'FORMAT' => $value );
        $res    = TRUE;
        break;
      case 'LANGUAGE':
         // set language for calendar component as defined in [RFC 1766]
        $value  = trim( $value );
        $this->language = $value;
        $subcfg = array( 'LANGUAGE' => $value );
        $res    = TRUE;
        break;
      case 'NL':
      case 'NEWLINECHAR':
        $this->nl = $value;
        $subcfg = array( 'NL' => $value );
        $res    = TRUE;
        break;
      case 'UNIQUE_ID':
        $value  = trim( $value );
        $this->unique_id = $value;
        $subcfg = array( 'UNIQUE_ID' => $value );
        $res    = TRUE;
        break;
    }
    if( !$res ) return FALSE;
    if( isset( $subcfg ) && !empty( $this->components )) {
      foreach( $subcfg as $cfgkey => $cfgvalue ) {
        foreach( $this->components as $cix => $component ) {
          $res = $component->setConfig( $cfgkey, $cfgvalue );
          if( !$res )
            break 2;
          $this->components[$cix] = $component; // PHP4 compliant
        }
      }
    }
    return $res;
  }

/**
 * get component property value/params
 *
 * if property has multiply values, consequtive function calls are needed
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-02
 * @param string $propName, optional
 * @param int @propix, optional, if specific property is wanted in case of multiply occurences
 * @param bool $inclParam=FALSE
 * @param bool $specform=FALSE
 * @return mixed
 */
  function getProperty( $propName=FALSE, $propix=FALSE, $inclParam=FALSE, $specform=FALSE ) {
    if( $this->_notExistProp( $propName )) return FALSE;
    $propName = ( $propName ) ? strtoupper( $propName ) : 'X-PROP';
    if( in_array( $propName, array( 'ATTENDEE', 'COMMENT',   'CONTACT', 'DESCRIPTION',   
                                    'RESOURCES', 'TZNAME', 'X-PROP'  ))) {
      if( !$propix )
        $propix = ( isset( $this->propix[$propName] )) ? $this->propix[$propName] + 2 : 1;
      $this->propix[$propName] = --$propix;
    }
    switch( $propName ) {
      case 'ATTENDEE':
        if( !isset( $this->attendee[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->attendee[$propix] : $this->attendee[$propix]['value'];
        break;
      case 'CLASS':
        if( !empty( $this->class['value'] )) return ( $inclParam ) ? $this->class : $this->class['value'];
        break;
      case 'COMMENT':
        if( !isset( $this->comment[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->comment[$propix] : $this->comment[$propix]['value'];
        break;
      case 'COMPLETED':
        if( !empty( $this->completed['value'] )) return ( $inclParam ) ? $this->completed : $this->completed['value'];
        break;
      case 'CONTACT':
        if( !isset( $this->contact[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->contact[$propix] : $this->contact[$propix]['value'];
        break;
      case 'CREATED':
        if( !empty( $this->created['value'] )) return ( $inclParam ) ? $this->created : $this->created['value'];
        break;
      case 'DESCRIPTION':
        if( !isset( $this->description[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->description[$propix] : $this->description[$propix]['value'];
        break;
      case 'DTEND':
        if( !empty( $this->dtend['value'] )) return ( $inclParam ) ? $this->dtend : $this->dtend['value'];
        break;
      case 'DTSTAMP':
        if( in_array( $this->objName, array( 'hc_valarm', 'hc_vtimezone', 'standard', 'daylight' )))
          return;
        if( !isset( $this->dtstamp['value'] ))
          $this->_makeDtstamp();
        return ( $inclParam ) ? $this->dtstamp : $this->dtstamp['value'];
        break;
      case 'DTSTART':
        if( !empty( $this->dtstart['value'] )) return ( $inclParam ) ? $this->dtstart : $this->dtstart['value'];
        break;
      case 'DURATION':
        if( !isset( $this->duration['value'] )) return FALSE;
        $value = ( $specform ) ? $this->duration2date() : $this->duration['value'];
        return ( $inclParam ) ? array( 'value' => $value, 'params' =>  $this->duration['params'] ) : $value;
        break;
      case 'LAST-MODIFIED':
        if( !empty( $this->lastmodified['value'] )) return ( $inclParam ) ? $this->lastmodified : $this->lastmodified['value'];
        break;
      case 'LOCATION':
        if( !empty( $this->location['value'] )) return ( $inclParam ) ? $this->location : $this->location['value'];
        break;
      case 'ORGANIZER':
        if( !empty( $this->organizer['value'] )) return ( $inclParam ) ? $this->organizer : $this->organizer['value'];
        break;
      case 'PRIORITY':
        if( !empty( $this->priority['value'] )) return ( $inclParam ) ? $this->priority : $this->priority['value'];
        break;
      case 'RESOURCES':
        if( !isset( $this->resources[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->resources[$propix] : $this->resources[$propix]['value'];
        break;
      case 'SEQUENCE':
        if( !empty( $this->sequence['value'] )) return ( $inclParam ) ? $this->sequence : $this->sequence['value'];
        break;
      case 'STATUS':
        if( !empty( $this->status['value'] )) return ( $inclParam ) ? $this->status : $this->status['value'];
        break;
      case 'SUMMARY':
        if( !empty( $this->summary['value'] )) return ( $inclParam ) ? $this->summary : $this->summary['value'];
        break;
      case 'TZID':
        if( !empty( $this->tzid['value'] )) return ( $inclParam ) ? $this->tzid : $this->tzid['value'];
        break;
      case 'TZNAME':
        if( !isset( $this->tzname[$propix] )) return FALSE;
        return ( $inclParam ) ? $this->tzname[$propix] : $this->tzname[$propix]['value'];
        break;
      case 'TZOFFSETFROM':
        if( !empty( $this->tzoffsetfrom['value'] )) return ( $inclParam ) ? $this->tzoffsetfrom : $this->tzoffsetfrom['value'];
        break;
      case 'TZOFFSETTO':
        if( !empty( $this->tzoffsetto['value'] )) return ( $inclParam ) ? $this->tzoffsetto : $this->tzoffsetto['value'];
        break;
      case 'TZURL':
        if( !empty( $this->tzurl['value'] )) return ( $inclParam ) ? $this->tzurl : $this->tzurl['value'];
        break;
      case 'UID':
        if( in_array( $this->objName, array( 'hc_valarm', 'hc_vtimezone', 'standard', 'daylight' )))
          return FALSE;
        if( empty( $this->uid['value'] ))
          $this->_makeuid();
        return ( $inclParam ) ? $this->uid : $this->uid['value'];
        break;
      case 'URL':
        if( !empty( $this->url['value'] )) return ( $inclParam ) ? $this->url : $this->url['value'];
        break;
      default:
        if( $propName != 'X-PROP' ) {
          if( !isset( $this->xprop[$propName] )) return FALSE;
          return ( $inclParam ) ? array( $propName, $this->xprop[$propName] )
                                : array( $propName, $this->xprop[$propName]['value'] );
        }
        else {
          if( empty( $this->xprop )) return FALSE;
          $xpropno = 0;
          foreach( $this->xprop as $xpropkey => $xpropvalue ) {
            if( $propix == $xpropno )
              return ( $inclParam ) ? array( $xpropkey, $this->xprop[$xpropkey] )
                                    : array( $xpropkey, $this->xprop[$xpropkey]['value'] );
            else
              $xpropno++;
          }
          return FALSE; // not found ??
        }
    }
    return FALSE;
  }
/**
 * general component property setting
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-05
 * @param mixed $args variable number of function arguments,
 *                    first argument is ALWAYS component name,
 *                    second ALWAYS component value!
 * @return void
 */
  function setProperty() {
    $numargs    = func_num_args();
    if( 1 > $numargs ) return FALSE;
    $arglist    = func_get_args();
    if( $this->_notExistProp( $arglist[0] )) return FALSE;
    if( !$this->getConfig( 'allowEmpty' ) && ( !isset( $arglist[1] ) || empty( $arglist[1] )))
      return FALSE;
    $arglist[0] = strtoupper( $arglist[0] );
    for( $argix=$numargs; $argix < 12; $argix++ ) {
      if( !isset( $arglist[$argix] ))
        $arglist[$argix] = null;
    }
    switch( $arglist[0] ) {
      case 'ATTENDEE':
        return $this->setAttendee( $arglist[1], $arglist[2], $arglist[3] );
      case 'COMMENT':
        return $this->setComment( $arglist[1], $arglist[2], $arglist[3] );
      case 'COMPLETED':
        return $this->setCompleted( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7] );
      case 'CONTACT':
        return $this->setContact( $arglist[1], $arglist[2], $arglist[3] );
      case 'CREATED':
        return $this->setCreated( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7] );
      case 'DESCRIPTION':
        return $this->setDescription( $arglist[1], $arglist[2], $arglist[3] );
      case 'DTEND':
        return $this->setDtend( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7], $arglist[8] );
      case 'DTSTAMP':
        return $this->setDtstamp( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7] );
      case 'DTSTART':
        return $this->setDtstart( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7], $arglist[8] );
      case 'DURATION':
        return $this->setDuration( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6] );
      case 'LAST-MODIFIED':
        return $this->setLastModified( $arglist[1], $arglist[2], $arglist[3], $arglist[4], $arglist[5], $arglist[6], $arglist[7] );
      case 'LOCATION':
        return $this->setLocation( $arglist[1], $arglist[2] );
      case 'ORGANIZER':
        return $this->setOrganizer( $arglist[1], $arglist[2] );
      case 'PRIORITY':
        return $this->setPriority( $arglist[1], $arglist[2] );
      case 'RESOURCES':
        return $this->setResources( $arglist[1], $arglist[2], $arglist[3] );
      case 'SEQUENCE':
        return $this->setSequence( $arglist[1], $arglist[2] );
      case 'STATUS':
        return $this->setStatus( $arglist[1], $arglist[2] );
      case 'SUMMARY':
        return $this->setSummary( $arglist[1], $arglist[2] );
      case 'TZID':
        return $this->setTzid( $arglist[1], $arglist[2] );
      case 'TZNAME':
        return $this->setTzname( $arglist[1], $arglist[2], $arglist[3] );
      case 'TZOFFSETFROM':
        return $this->setTzoffsetfrom( $arglist[1], $arglist[2] );
      case 'TZOFFSETTO':
        return $this->setTzoffsetto( $arglist[1], $arglist[2] );
      case 'TZURL':
        return $this->setTzurl( $arglist[1], $arglist[2] );
      case 'UID':
        return $this->setUid( $arglist[1], $arglist[2] );
      case 'URL':
        return $this->setUrl( $arglist[1], $arglist[2] );
      default:
        return $this->setXprop( $arglist[0], $arglist[1], $arglist[2] );
    }
    return FALSE;
  }
/*********************************************************************************/
/*********************************************************************************/
/**
 * return a copy of this component
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.16 - 2007-11-07
 * @return object
 */
  function copy() {
    $serialized_contents = serialize($this);
    $copy = unserialize($serialized_contents);
    unset( $copy->propix );
    return $copy;
 }
/**
 * get calendar component subcomponent from component container
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-15
 * @param mixed $arg1 optional, ordno/component type/ component uid
 * @param mixed $arg2 optional, ordno if arg1 = component type
 * @return object
 */
  function getComponent ( $arg1=FALSE, $arg2=FALSE ) {
    if( !isset( $this->components )) return FALSE;
    $index = $argType = null;
    if ( !$arg1 ) {
      $argType = 'INDEX';
      $index   = $this->compix['INDEX'] =
        ( isset( $this->compix['INDEX'] )) ? $this->compix['INDEX'] + 1 : 1;
    }
    elseif ( ctype_digit( (string) $arg1 )) {
      $argType = 'INDEX';
      $index   = (int) $arg1;
      unset( $this->compix );
    }
    elseif(( strlen( $arg1 ) <= strlen( 'vfreebusy' )) && ( FALSE === strpos( $arg1, '@' ))) {
      unset( $this->compix['INDEX'] );
      $argType = strtolower( $arg1 );
      if( !$arg2 )
        $index = $this->compix[$argType] =
        ( isset( $this->compix[$argType] )) ? $this->compix[$argType] + 1 : 1;
      else
        $index = (int) $arg2;
    }
    $index  -= 1;
    $ckeys = array_keys( $this->components );
    if( !empty( $index) && ( $index > end( $ckeys )))
      return FALSE;
    $cix2gC = 0;
    foreach( $this->components as $cix => $component ) {
      if( empty( $component )) continue;
      unset( $component->propix );
      if(( 'INDEX' == $argType ) && ( $index == $cix ))
        return $component->copy();
      elseif( $argType == $component->objName ) {
         if( $index == $cix2gC )
           return $component->copy();
         $cix2gC++;
      }
      elseif( !$argType && ( $arg1 == $component->getProperty( 'uid' ))) {
        unset( $component->propix );
        return $component->copy();
      }
    }
            /* not found.. . */
    unset( $this->compix );
    return false;
  }
/**
 * add calendar component as subcomponent to container for subcomponents
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.13 - 2008-09-24
 * @param object $component calendar component
 * @param mixed $arg1 optional, ordno/component type/ component uid
 * @param mixed $arg2 optional, ordno if arg1 = component type
 * @return bool
 */
  function setComponent( $component, $arg1=FALSE, $arg2=FALSE  ) {
    if( !isset( $this->components )) return FALSE;
    if( '' >= $component->getConfig( 'language'))
      $component->setConfig( 'language',  $this->getConfig( 'language' ));
    $component->setConfig( 'allowEmpty',  $this->getConfig( 'allowEmpty' ));
    $component->setConfig( 'nl',          $this->getConfig( 'nl' ));
    $component->setConfig( 'unique_id',   $this->getConfig( 'unique_id' ));
    $component->setConfig( 'format',      $this->getConfig( 'format' ));
    if( !in_array( $component->objName, array( 'hc_valarm', 'hc_vtimezone', 'standard', 'daylight' ))) {
      unset( $component->propix );
            /* make sure dtstamp and uid is set */
      $dummy = $component->getProperty( 'dtstamp' );
      $dummy = $component->getProperty( 'uid' );
    }
    if( !$arg1 ) {
      $this->components[] = $component->copy();
      return TRUE;
    }
    $argType = $index = null;
    if ( ctype_digit( (string) $arg1 )) {
      $argType = 'INDEX';
      $index   = (int) $arg1 - 1;
    }
    elseif(( strlen( $arg1 ) <= strlen( 'vfreebusy' )) && ( FALSE === strpos( $arg1, '@' ))) {
      $argType = strtolower( $arg1 );
      $index = ( ctype_digit( (string) $arg2 )) ? ((int) $arg2) - 1 : 0;
    }
    $cix2sC = 0;
    foreach ( $this->components as $cix => $component2 ) {
      if( empty( $component2 )) continue;
      unset( $component2->propix );
      if(( 'INDEX' == $argType ) && ( $index == $cix )) {
        $this->components[$cix] = $component->copy();
        return TRUE;
      }
      elseif( $argType == $component2->objName ) {
        if( $index == $cix2sC ) {
          $this->components[$cix] = $component->copy();
          return TRUE;
        }
        $cix2sC++;
      }
      elseif( !$argType && ($arg1 == $component2->getProperty( 'uid' ))) {
        $this->components[$cix] = $component->copy();
        return TRUE;
      }
    }
            /* not found.. . insert anyway.. .*/
    $this->components[] = $component->copy();
    return TRUE;
  }
/**
 * creates formatted output for subcomponents
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.4.10 - 2008-08-06
 * @return string
 */
  function createSubComponent() {
    $output = null;
    foreach( $this->components as $component ) {
      if( empty( $component )) continue;
      if( '' >= $component->getConfig( 'language'))
        $component->setConfig( 'language',  $this->getConfig( 'language' ));
      $component->setConfig( 'allowEmpty',  $this->getConfig( 'allowEmpty' ));
      $component->setConfig( 'nl',          $this->getConfig( 'nl' ));
      $component->setConfig( 'unique_id',   $this->getConfig( 'unique_id' ));
      $component->setConfig( 'format',      $this->getConfig( 'format' ));
      $output .= $component->createComponent( $this->xcaldecl );
    }
    return $output;
  }
/********************************************************************************/
/**
 * break lines at pos 75
 *
 * Lines of text SHOULD NOT be longer than 75 octets, excluding the line
 * break. Long content lines SHOULD be split into a multiple line
 * representations using a line "folding" technique. That is, a long
 * line can be split between any two characters by inserting a CRLF
 * immediately followed by a single linear white space character (i.e.,
 * SPACE, US-ASCII decimal 32 or HTAB, US-ASCII decimal 9). Any sequence
 * of CRLF followed immediately by a single linear white space character
 * is ignored (i.e., removed) when processing the content type.
 *
 * Edited 2007-08-26 by Anders Litzell, anders@litzell.se to fix bug where
 * the reserved expression "\n" in the arg $string could be broken up by the
 * folding of lines, causing ambiguity in the return string.
 * Fix uses var $breakAtChar=75 and breaks the line at $breakAtChar-1 if need be.
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.2.8 - 2006-09-03
 * @param string $value
 * @return string
 */
  function _size75( $string ) {
    $strlen = strlen( $string );
    $tmp    = $string;
    $string = null;
    while( $strlen > 75 ) {
       $breakAtChar = 75;
       if( substr( $tmp, ( $breakAtChar - 1 ), strlen( '\n' )) == '\n' )
         $breakAtChar = $breakAtChar - 1;
       $string .= substr( $tmp, 0, $breakAtChar );
       $string .= $this->nl;
       $tmp     = ' '.substr( $tmp, $breakAtChar );
       $strlen  = strlen( $tmp );
    } // while
    $string .= rtrim( $tmp ); // the rest
    if( $this->nl != substr( $string, ( 0 - strlen( $this->nl ))))
      $string .= $this->nl;
    return $string;
  }
/**
 * special characters management output
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.3.3 - 2007-12-20
 * @param string $string
 * @return string
 */
  function _strrep( $string ) {
    switch( $this->format ) {
      case 'xcal':
        $string = str_replace( '\n',  $this->nl, $string);
        $string = htmlspecialchars( strip_tags( stripslashes( urldecode ( $string ))));
        break;
      default:
        $pos = 0;
        while( $pos <= strlen( $string )) {
          $pos = strpos( $string, "\\", $pos );
          if( FALSE === $pos )
            break;
          if( !in_array( $string{($pos + 1)}, array( 'n', 'N', 'r', ',', ';' ))) {
            $string = substr( $string, 0, $pos )."\\".substr( $string, ( $pos + 1 ));
            $pos += 1;
          }
          $pos += 1;
        }
        if( FALSE !== strpos( $string, '"' ))
          $string = str_replace('"',   "'",       $string);
        if( FALSE !== strpos( $string, ',' ))
          $string = str_replace(',',   '\,',      $string);
        if( FALSE !== strpos( $string, ';' ))
          $string = str_replace(';',   '\;',      $string);
        if( FALSE !== strpos( $string, "\r\n" ))
          $string = str_replace( "\r\n", '\n',    $string);
        elseif( FALSE !== strpos( $string, "\r" ))
          $string = str_replace( "\r", '\n',      $string);
        if( FALSE !== strpos( $string, '\N' ))
          $string = str_replace( '\N', '\n',      $string);
//        if( FALSE !== strpos( $string, $this->nl ))
          $string = str_replace( $this->nl, '\n', $string);
        break;
    }
    return $string;
  }
/**
 * special characters management input (from iCal file)
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.3.3 - 2007-11-23
 * @param string $string
 * @return string
 */
  function _strunrep( $string ) {
    $string = str_replace( '\\\\', '\\',     $string);
    $string = str_replace( '\,',   ',',      $string);
    $string = str_replace( '\;',   ';',      $string);
//    $string = str_replace( '\n',  $this->nl, $string); // ??
    return $string;
  }
}
/*********************************************************************************/
/*********************************************************************************/
/**
 * class for calendar component VEVENT
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-12
 */
class hc_vevent extends hc_calendarComponent {
  var $attendee;
  var $comment;
  var $contact;
  var $class;
  var $created;
  var $description;
  var $dtend;
  var $dtstart;
  var $duration;
  var $lastmodified;
  var $location;
  var $organizer;
  var $priority;
  var $resources;
  var $sequence;
  var $status;
  var $summary;
  var $url;
  var $xprop;
            //  component subcomponents container
  var $components;
/**
 * constructor for calendar component VEVENT object
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-31
 * @return void
 */
  function hc_vevent() {
    $this->hc_calendarComponent();

    $this->attendee        = '';
    $this->class           = '';
    $this->comment         = '';
    $this->contact         = '';
    $this->created         = '';
    $this->description     = '';
    $this->dtstart         = '';
    $this->dtend           = '';
    $this->duration        = '';
    $this->lastmodified    = '';
    $this->location        = '';
    $this->organizer       = '';
    $this->priority        = '';
    $this->resources       = '';
    $this->sequence        = '';
    $this->status          = '';
    $this->summary         = '';
    $this->url             = '';
    $this->xprop           = '';

    $this->components      = array();

  }
/**
 * create formatted output for calendar component VEVENT object instance
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-11-07
 * @param array $xcaldecl
 * @return string
 */
  function createComponent( &$xcaldecl ) {
    $objectname    = $this->_createFormat();
    $component     = $this->componentStart1.$objectname.$this->componentStart2.$this->nl;
    $component    .= $this->createUid();
    $component    .= $this->createDtstamp();
    $component    .= $this->createAttendee();
    $component    .= $this->createComment();
    $component    .= $this->createContact();
    $component    .= $this->createCreated();
    $component    .= $this->createDescription();
    $component    .= $this->createDtstart();
    $component    .= $this->createDtend();
    $component    .= $this->createDuration();
    $component    .= $this->createLastModified();
    $component    .= $this->createLocation();
    $component    .= $this->createOrganizer();
    $component    .= $this->createPriority();
    $component    .= $this->createResources();
    $component    .= $this->createSequence();
    $component    .= $this->createStatus();
    $component    .= $this->createSummary();
    $component    .= $this->createUrl();
    $component    .= $this->createXprop();
    $component    .= $this->createSubComponent();
    $component    .= $this->componentEnd1.$objectname.$this->componentEnd2;
    if( is_array( $this->xcaldecl ) && ( 0 < count( $this->xcaldecl ))) {
      foreach( $this->xcaldecl as $localxcaldecl )
        $xcaldecl[] = $localxcaldecl;
    }
    return $component;
  }
}

/*********************************************************************************/
/*********************************************************************************/
/**
 * class for calendar component VALARM
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-12
 */
class hc_valarm extends hc_calendarComponent {
  var $attendee;
  var $description;
  var $duration;
  var $summary;
  var $trigger;
  var $xprop;
/**
 * constructor for calendar component VALARM object
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-31
 * @return void
 */
  function hc_valarm() {
    $this->hc_calendarComponent();

    $this->attendee        = '';
    $this->description     = '';
    $this->duration        = '';
    $this->summary         = '';
    $this->xprop           = '';
  }
/**
 * create formatted output for calendar component VALARM object instance
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-22
 * @param array $xcaldecl
 * @return string
 */
  function createComponent( &$xcaldecl ) {
    $objectname    = $this->_createFormat();
    $component     = $this->componentStart1.$objectname.$this->componentStart2.$this->nl;
    $component    .= $this->createAttendee();
    $component    .= $this->createDescription();
    $component    .= $this->createDuration();
    $component    .= $this->createSummary();
    $component    .= $this->createXprop();
    $component    .= $this->componentEnd1.$objectname.$this->componentEnd2;
    return $component;
  }
}
/**********************************************************************************
/*********************************************************************************/
/**
 * class for calendar component VTIMEZONE
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-12
 */
class hc_vtimezone extends hc_calendarComponent {
  var $timezonetype;

  var $comment;
  var $dtstart;
  var $lastmodified;
  var $tzid;
  var $tzname;
  var $tzoffsetfrom;
  var $tzoffsetto;
  var $tzurl;
  var $xprop;
            //  component subcomponents container
  var $components;
/**
 * constructor for calendar component VTIMEZONE object
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-31
 * @param string $timezonetype optional, default FALSE ( STANDARD / DAYLIGHT )
 * @return void
 */
  function hc_vtimezone( $timezonetype=FALSE ) {
    if( !$timezonetype )
      $this->timezonetype = 'VTIMEZONE';
    else
      $this->timezonetype = strtoupper( $timezonetype );
    $this->hc_calendarComponent();

    $this->comment         = '';
    $this->dtstart         = '';
    $this->lastmodified    = '';
    $this->tzid            = '';
    $this->tzname          = '';
    $this->tzoffsetfrom    = '';
    $this->tzoffsetto      = '';
    $this->tzurl           = '';
    $this->xprop           = '';

    $this->components      = array();
  }
/**
 * create formatted output for calendar component VTIMEZONE object instance
 *
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since 2.5.1 - 2008-10-25
 * @param array $xcaldecl
 * @return string
 */
  function createComponent( &$xcaldecl ) {
    $objectname    = $this->_createFormat();
    $component     = $this->componentStart1.$objectname.$this->componentStart2.$this->nl;
    $component    .= $this->createTzid();
    $component    .= $this->createLastModified();
    $component    .= $this->createTzurl();
    $component    .= $this->createDtstart();
    $component    .= $this->createTzoffsetfrom();
    $component    .= $this->createTzoffsetto();
    $component    .= $this->createComment();
    $component    .= $this->createTzname();
    $component    .= $this->createXprop();
    $component    .= $this->createSubComponent();

$tz_obj = new DateTimeZone( $this->tzid['value'] );
$from = $to = time();
$year = 86400 * 360;
$transitions = $tz_obj->getTransitions($from - $year, $to + $year);
// $transitions = array();
$components = array();

$std = null; $dst = null;
foreach( $transitions as $i => $trans ){
	$cmp = null;

	// skip the first entry...
	if ($i == 0) {
		// ... but remember the offset for the next TZOFFSETFROM value
		$tzfrom = $trans['offset'] / 3600;
		continue;
	}

	$cmp = array();
	// daylight saving time definition
	if( $trans['isdst'] ){
		$t_dst = $trans['ts'];
		// $dst = new VObject\Component('DAYLIGHT');
		// $cmp = $dst;
		$cmp_name = 'DAYLIGHT';
	}
	// standard time definition
	else {
		$t_std = $trans['ts'];
		// $std = new VObject\Component('STANDARD');
		// $cmp = $std;
		$cmp_name = 'STANDARD';
	}

	$dt = new DateTime($trans['time']);
	$offset = $trans['offset'] / 3600;

	$cmp['DTSTART'] = $dt->format('Ymd\THis');
	$tzfrom_mod = $tzfrom >= 0 ? $tzfrom : - $tzfrom;
	$offset_mod = $offset >= 0 ? $offset : - $offset;
	$tzoffsetfrom = sprintf('%s%02d%02d', $tzfrom >= 0 ? '+' : '-', floor($tzfrom_mod), ($tzfrom_mod - floor($tzfrom_mod)) * 60);
	$tzoffsetto = sprintf('%s%02d%02d', $offset >= 0 ? '+' : '-', floor($offset_mod), ($offset_mod - floor($offset_mod)) * 60);

	$cmp['TZOFFSETFROM'] = $tzoffsetfrom;
	$cmp['TZOFFSETTO']   = $tzoffsetto;

	// add abbreviated timezone name if available
	if (!empty($trans['abbr'])) {
		// $cmp['TZNAME'] = $trans['abbr'];
	}

	$tzfrom = $offset;

	$components[] = array( $cmp_name, $cmp );
	// $vt[$cmp_name] = $cmp;
		// $vt->add($cmp);

	// we covered the entire date range
	if ($std && $dst && min($t_std, $t_dst) < $from && max($t_std, $t_dst) > $to) {
		break;
	}
}

$vtz2 = array();
// $vtz2[] = 'BEGIN' . ':' . 'VTIMEZONE';
foreach( $components as $cmp_array ){
	list( $cmp_name, $cmp ) = $cmp_array;

	$vtz2[] = 'BEGIN' . ':' . $cmp_name;
	foreach( $cmp as $k => $v ){
		$vtz2[] = $k . ':' . $v;
	}
	$vtz2[] = 'END' . ':' . $cmp_name;
}
// $vtz2[] = 'END' . ':' . 'VTIMEZONE';
$vtz2 = join($this->nl, $vtz2) . $this->nl;

$component    .= $vtz2;

    $component    .= $this->componentEnd1.$objectname.$this->componentEnd2;
    if( is_array( $this->xcaldecl ) && ( 0 < count( $this->xcaldecl ))) {
      foreach( $this->xcaldecl as $localxcaldecl )
        $xcaldecl[] = $localxcaldecl;
    }
    return $component;
  }
}
?>