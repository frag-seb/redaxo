<?php

rex_extension::register('OUTPUT_FILTER', array('rex_sql_debug', 'doLog'));

/**
 * Class to monitor sql queries
 *
 * @author staabm
 */
class rex_sql_debug extends rex_sql
{
  private static
    $count = 0,
    $queries = array();

  public function execute($params)
  {
    self::$count++;
    $qry = $this->stmt->queryString;

    $timer = new rex_timer();
    $res = parent::execute($params);

    self::$queries[] = array($qry, $timer->getFormattedTime(rex_timer::MILLISEC));

    return $res;
  }

  static public function doLog($params)
  {
    $firephp = FirePHP::getInstance(true);
    foreach(self::$queries as $qry)
    {
      $firephp->log('Query: '. $qry[0]. ' ' .$qry[1] . 'ms', __CLASS__);
    }
  }
}