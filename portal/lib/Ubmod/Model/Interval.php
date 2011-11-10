<?php
/*
 * The contents of this file are subject to the University at Buffalo Public
 * License Version 1.0 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.ccr.buffalo.edu/licenses/ubpl.txt
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 *
 * The Original Code is UBMoD.
 *
 * The Initial Developer of the Original Code is Research Foundation of State
 * University of New York, on behalf of University at Buffalo.
 *
 * Portions created by the Initial Developer are Copyright (C) 2007 Research
 * Foundation of State University of New York, on behalf of University at
 * Buffalo.  All Rights Reserved.
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 (the "GPL"), or the GNU
 * Lesser General Public License Version 2.1 (the "LGPL"), in which case the
 * provisions of the GPL or the LGPL are applicable instead of those above. If
 * you wish to allow use of your version of this file only under the terms of
 * either the GPL or the LGPL, and not to allow others to use your version of
 * this file under the terms of the UBPL, indicate your decision by deleting
 * the provisions above and replace them with the notice and other provisions
 * required by the GPL or the LGPL. If you do not delete the provisions above,
 * a recipient may use your version of this file under the terms of any one of
 * the UBPL, the GPL or the LGPL.
 */

/**
 * Time interval model.
 *
 * @author Jeffrey T. Palmer <jtpalmer@ccr.buffalo.edu>
 * @version $Id$
 * @copyright Center for Computational Research, University at Buffalo, 2011
 * @package Ubmod
 */

/**
 * Time interval Model
 *
 * @package Ubmod
 */
class Ubmod_Model_Interval
{

  /**
   * Returns time interval data.
   *
   * @param Ubmod_Model_QueryParams $params The query parameters.
   *
   * @return array
   */
  public static function getByParams(Ubmod_Model_QueryParams $params)
  {
    $sql = '
      SELECT
        time_interval_id               AS interval_id,
        display_name                   AS time_interval,
        start IS NULL OR end IS NULL   AS custom,
        DATE_FORMAT(start, "%m/%d/%Y") AS start,
        DATE_FORMAT(end,   "%m/%d/%Y") AS end
      FROM time_interval
      WHERE time_interval_id = ?
    ';
    $dbh = Ubmod_DbService::dbh();
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute(array($params->getTimeIntervalId()));
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    $timeInterval = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($timeInterval['custom']) {
      $timeInterval['start'] = $params->getStartDate();
      $timeInterval['end']   = $params->getEndDate();
    }

    // Check if the interval contains data for multiple months
    $timeInterval['multi_month'] = count(self::getMonths($params)) > 1;

    return $timeInterval;
  }

  /**
   * Returns an array of all time intervals.
   *
   * @return array
   */
  public static function getAll()
  {
    $dbh = Ubmod_DbService::dbh();
    $sql = '
      SELECT
        time_interval_id               AS interval_id,
        display_name                   AS time_interval,
        DATE_FORMAT(start, "%m/%d/%Y") AS start,
        DATE_FORMAT(end,   "%m/%d/%Y") AS end
        FROM time_interval
    ';
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute();
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Returns the corresponding where clause for use in a SQL query
   *
   * @param array $params An array with these keys:
   *   - interval_id
   *   - start_date (only required for custom intervals)
   *   - end_date (only requried for custom intervals)
   *
   * @return string
   */
  public static function getWhereClause(Ubmod_Model_QueryParams $params)
  {

    // If the given parameters don't include a interval ID, it is
    // assumed that a specific month is being queried.
    if (!$params->hasTimeIntervalId()) {
      return "year = {$params->getYear()} AND month = {$params->getMonth()}";
    }

    $sql = '
      SELECT where_clause, start, end
      FROM time_interval
      WHERE time_interval_id = :time_interval_id
    ';
    $dbh = Ubmod_DbService::dbh();
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute(array(
      ':time_interval_id' => $params->getTimeIntervalId()
    ));
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Custom date range
    if ($row['start'] === null || $row['end'] === null) {
      $start = self::convertDate($params->getStartDate());
      $end   = self::convertDate($params->getEndDate());
      return sprintf($row['where_clause'], $start, $end);
    } else {
      return $row['where_clause'];
    }
  }

  /**
   * Return an array of months that are included in the query parameter
   * date range.
   *
   * Months that are only partially included in the date range are
   * included in the returned list of months.
   *
   * @param Ubmod_Model_QueryParams $params The query parameters.
   *
   * @return array
   */
  public static function getMonths(Ubmod_Model_QueryParams $params)
  {
    $timeClause = Ubmod_Model_Interval::getWhereClause($params);

    $sql = "
      SELECT DISTINCT month, year
      FROM dim_date
      WHERE $timeClause
      ORDER BY year, month
    ";

    $dbh = Ubmod_DbService::dbh();
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute();
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Convert a date string from MM/DD/YYYY to YYYY-MM-DD
   *
   * @param string $date A date in MM/DD/YYYY format
   *
   * @return string
   */
  private static function convertDate($date)
  {
    if (preg_match('# ^ (\d?\d) / (\d?\d) / (\d{4}) $ #x', $date, $matches)) {
      return sprintf('%04d-%02d-%02d', $matches[3], $matches[1], $matches[2]);
    } else {
      throw new Exception("Invalid date format: '$date'");
    }
  }
}
