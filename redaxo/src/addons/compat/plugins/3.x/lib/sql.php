<?php

/**
 * @see rex_select
 *
 * @deprecated 4.0
 */
class sql extends rex_sql
{
	protected $select;

  function __construct($DBID = 1)
  {
    parent::__construct($DBID);
    // Altes feld wurde umbenannt, deshalb hier als Alias speichern
    $this->select =& $this->query;
  }

  /**
   * @see rex_select::getArray()
   *
   * @deprecated 4.0
   */
  function get_array($sql = "", $fetch_type = MYSQL_ASSOC)
  {
    return $this->getArray($sql, $fetch_type);
  }

  /**
   * @see rex_select::getLastId()
   *
   * @deprecated 4.0
   */
  function getLastID()
  {
    return $this->getLastId();
  }

  /**
   * @see rex_select::next()
   *
   * @deprecated 4.0
   */
  function nextValue()
  {
  	$this->next();
  }

  /**
   * @see rex_select::reset()
   *
   * @deprecated 4.0
   */
  function resetCounter()
  {
    $this->reset();
  }

  /**
   * @see rex_select::setWhere()
   *
   * @deprecated 4.0
   */
  function where($where)
  {
    $this->setWhere($where);
  }

  /**
   * @see rex_select::setQuery()
   *
   * @deprecated 4.0
   */
  function query($qry)
  {
    return $this->setQuery($qry);
  }
}