<?php

/**
 * REDAXO Classic-Theme
 *
 * @author Umsetzung
 * @author thomas[dot]blum[at]redaxo[dot]de Thomas Blum
 * @author <a href="http://www.blumbeet.com">www.blumbeet.com</a>
 *
 * @package redaxo5
 * @version svn:$Id$
 */

$mypage = 'classic_skin';

$REX['ADDON']['version'][$mypage] = '1.3';
$REX['ADDON']['author'][$mypage] = 'Umsetzung: Thomas Blum';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum/';

if($REX["REDAXO"])
{

	function rex_be_style_classic_skin_css_add($params)
	{
	  $params["subject"] .= '
    <link rel="stylesheet" href="'. rex_path::pluginAssets('be_style', 'classic', 'css_import.css', rex_path::RELATIVE) .'" type="text/css" media="screen, projection, print" />
	  <!--[if lte IE 7]>
	      <link rel="stylesheet" href="'. rex_path::pluginAssets('be_style', 'classic', 'css_ie_lte_7.css', rex_path::RELATIVE) .'" type="text/css" media="screen, projection, print" />
	    <![endif]-->
	    <!--[if lte IE 6]>
	      <link rel="stylesheet" href="'. rex_path::pluginAssets('be_style', 'classic', 'css_ie_lte_6.css', rex_path::RELATIVE) .'" type="text/css" media="screen, projection, print" />
	    <![endif]-->';
	  return $params["subject"];
	}

	rex_extension::register('PAGE_HEADER', 'rex_be_style_classic_skin_css_add');

	function rex_be_style_classic_skin_css_body($params)
	{
	  $params["subject"]["class"][] = "be-style-classic-skin";
	  return $params["subject"];
	}

	rex_extension::register('PAGE_BODY_ATTR', 'rex_be_style_classic_skin_css_body');

}