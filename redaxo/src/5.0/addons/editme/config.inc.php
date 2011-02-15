<?php

/**
 * Editme
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version svn:$Id$
 * 
 * 
 * TODO:
 * - translate bei den Eingabefeldern setzen
 * - export einbauen, sollte direkt auch als import gehen
 * - import umbauen so dass, wenn Id gesetzt ist, Datensaetze ersetzt werden
 * - wenn medien im medienpool geloescht werden �ber EP auch pr�fen ob in EM etwas vorhanden ist
 * - Einfaches OOF f�r EM bauen, Datensaetze, Listen, Relationen, Export und Import
 * - Caching einbauen
 * - L�sung finden um einfach spezifische Feldtypen definieren zu k�nnen, INT, VARCHAR, FLOAT etc.
 * - onDelete bei Feldern einbauen
 * - Mehrsprachige Felder besser einbauen, XForm erweitern
 * - weitere XForm-Klassen umbauen f�r EM.
 * - Generate All immer nach �nderungen bei Tabellen + Feldern
 * - �bersetzung vervollst�ndigen, nur noch tables.inc.php und englisch
 * 
 */



$mypage = 'editme';

if($REX["REDAXO"] && !$REX['SETUP'])
{
	$REX['ADDON']['name'][$mypage] = $I18N->msg("editme");

	// Credits
	$REX['ADDON']['version'][$mypage] = '0.8';
	$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
	$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
	$REX['ADDON']['navigation'][$mypage] = array(
	  // rootPage nur aktivieren wenn sie direkt ausgewaehlt ist
	  // da alle pages main-pages und daher separate oberpunkte sind
      'activateCondition' => array('page' => $mypage, 'subpage' => ''),
      'hidden' => FALSE
	);
  
	if($REX['USER'] && !$REX['USER']->isAdmin())
    {
      $REX['ADDON']['navigation'][$mypage]['hidden'] = TRUE;
    }
	
	include $REX['SRC_PATH'] .'/addons/editme/functions/functions.inc.php';

	$REX['ADDON']['tables'][$mypage] = rex_em_getTables();

	$subpages = array();
	if(is_array($REX['ADDON']['tables'][$mypage]))
	{
		foreach($REX['ADDON']['tables'][$mypage] as $table)
		{
			// Recht um das AddOn ueberhaupt einsehen zu koennen
			$table_perm = 'em['.$table["name"].']';
			$REX['EXTPERM'][] = $table_perm;

			// check active-state and permissions
			if($table['status'] == 1 && $table['hidden'] != 1 &&
			$REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm($table_perm)))
			{
				 
				// include page
				$be_page = new rex_be_page($table['label'], array('page'=>$mypage, 'subpage' => $table['name']));
				$be_page->setHref('index.php?page=editme&subpage='.$table['name']);
				$subpages[] = new rex_be_page_main($mypage, $be_page);
				// $subpages[] = array($table['name'],$table['label']); // f�r rex 4.2.1
			}
		}
	}

	$REX['ADDON']['pages'][$mypage] = $subpages;
	// $REX['ADDON'][$mypage]['SUBPAGES'] = $subpages; // f�r rex 4.2.1

	function rex_editme_assets($params){
		$params['subject'] .= "\n  ".'<script src="../files/addons/editme/em.js" type="text/javascript"></script>';
		return $params['subject'];
	}
	rex_register_extension('PAGE_HEADER', 'rex_editme_assets');
}

rex_register_extension('ADDONS_INCLUDED', 'rex_em_xform_add');
function rex_em_xform_add($params){
	global $REX;
	$REX['ADDON']['xform']['classpaths']['action'][] = $REX['SRC_PATH'] ."/addons/editme/xform/action/";
	$REX['ADDON']['xform']['classpaths']['value'][] = $REX['SRC_PATH'] ."/addons/editme/xform/value/";

}