<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The Original Code is UBMoD.
 *
 * The Initial Developer of the Original Code is Research Foundation of State
 * University of New York, on behalf of University at Buffalo.
 *
 * Portions created by the Initial Developer are Copyright (C) 2007 Research
 * Foundation of State University of New York, on behalf of University at
 * Buffalo.  All Rights Reserved.
 */

/**
 * Storage model.
 *
 * @author Sara García Ortiz 
 * @version $Id$
 * @copyright CETA Ciemat, 2013
 * @package Ubmod
 */

/**
 * Storage Model.
 *
 * @package Ubmod
 */
class Ubmod_Model_Storage
{

  /**
   * Return the number of users with activity in storage for the given parameters.
   *
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return int
   */
  public static function getActivityCount(Ubmod_Model_QueryParams $params)
  {
    $qb = new Ubmod_DataWarehouse_QueryBuilder();
    $qb->setFactTable('fact_storage');
    $qb->setQueryParams($params);

    list($sql, $dbParams) = $qb->buildCountQuery();

    $dbh = Ubmod_DbService::dbh();
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute($dbParams);
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
    return $result['count'];
  }

  /**
   * Returns storage activity.
   *
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return array
   */
  public static function getActivityList(Ubmod_Model_QueryParams $params)
  {
    $qb = new Ubmod_DataWarehouse_QueryBuilder();

    $qb->setFactTable('fact_storage');

    // Common fields
    $qb->addSelectExpressions(array(
      'storages'              => 'COUNT(*)',
      'user_count'            => 'COUNT(DISTINCT dim_user_id)',
      'group_count'           => 'COUNT(DISTINCT dim_group_id)',
      'space_used'            => 'ROUND(SUM(space_used)          , 1)',
      'avg_space_used'        => 'ROUND(AVG(space_used)          , 1)',
      'max_space_used'        => 'ROUND(MAX(space_used)          , 1)',
      'space_available'       => 'ROUND(SUM(space_available)     , 1)',
      'avg_space_available'   => 'ROUND(AVG(space_available)     , 1)',
      'max_space_available'   => 'ROUND(MAX(space_available)     , 1)',
      'space_quota'           => 'ROUND(SUM(space_quota)         , 1)',
      'max_space_quota'       => 'ROUND(MAX(space_quota)         , 1)',
      'avg_space_quota'       => 'ROUND(AVG(space_quota)         , 1)',
      'inodes_used'           => 'ROUND(SUM(inodes_used)         , 1)',
      'avg_inodes_used'       => 'ROUND(AVG(inodes_used)         , 1)',
      'max_inodes_used'       => 'ROUND(MAX(inodes_used)         , 1)',
      'inodes_available'      => 'ROUND(SUM(inodes_available)    , 1)',
      'avg_inodes_available'  => 'ROUND(AVG(inodes_available)    , 1)',
      'max_inodes_available'  => 'ROUND(MAX(inodes_available)    , 1)',
      'inodes_quota'          => 'ROUND(SUM(inodes_quota)        , 1)',
      'max_inodes_quota'      => 'ROUND(MAX(inodes_quota)        , 1)',
      'avg_inodes_quota'      => 'ROUND(AVG(inodes_quota)        , 1)',
    ));

    $qb->setQueryParams($params);

    list($sql, $dbParams) = $qb->buildQuery();

    $dbh = Ubmod_DbService::dbh();
    $stmt = $dbh->prepare($sql);
    
    $r = $stmt->execute($dbParams);
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Returns a single array of activity.
   *
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return array
   */
  public static function getActivity(Ubmod_Model_QueryParams $params)
  {
    $activity = self::getActivityList($params);

    if (count($activity) > 0) {
      return $activity[0];
    } else {
      return null;
    }
  }

  /**
   * Returns a single array with the activity with the specific model
   * data added.
   *
   * @param string $type The model type (user, group, queue or cluster).
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return array
   */
  public static function getEntity($type, Ubmod_Model_QueryParams $params)
  {
    $params->setModel($type);
    return self::getActivity($params);
  }

  /**
   * Return the standard columns for given query parameters.
   *
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return array
   */
  public static function getColumns(Ubmod_Model_QueryParams $params)
  {
    if (!$params->hasModel()) {
      throw new Exception('No model specified');
    }

    switch ($params->getModel()) {
    case 'user':
      return array(
        'name'                  => 'User',
        'display_name'          => 'Name',
        'group'                 => 'Group',
        'storage'               => '# Storage',
        'avg_space_used'        => 'Avg. Space Used (MB)',
        'avg_space_available'   => 'Avg.Space Used (MB)',
        'space_quota'           => 'Space Quota (MB)',
        'avg_inodes_used'       => 'Avg. Inodes Used (inodes)',
        'avg_inodes_available'  => 'Avg. Inodes Availables (inodes)',
        'inodes_quota'          => 'Inodes Quota (inodes)',
      );
      break;
      //ToDo: case 'group', case 'proyect' 
    case 'group':
      return array(
        'name'                  => 'Group',
        'display_name'          => 'Name',
        'storage'               => '# Storage',
        'avg_space_used'        => 'Avg. Space Used (MB)',
        'avg_space_available'   => 'Avg.Space Used (MB)',
        'space_quota'           => 'Space Quota (MB)',
        'avg_inodes_used'       => 'Avg. Inodes Used (inodes)',
        'avg_inodes_available'  => 'Avg. Inodes Availables (inodes)',
        'inodes_quota'          => 'Inodes Quota (inodes)',
      );
      break;
    case 'queue':
      throw new Exception('no support model');
      break;
    default:
      throw new Exception('Unknown model');
      break;
    }
  }

  /**
   * Return a suitable filename for the given query parameters.
   *
   * @param Ubmod_Model_QueryParams $params The parameters for the query.
   *
   * @return string
   */
  public static function getFilename(Ubmod_Model_QueryParams $params)
  {
    if (!$params->hasModel()) {
      throw new Exception('No model specified');
    }

    switch ($params->getModel()) {
    case 'user':
      return 'users';
      break;
    case 'group':
      return 'groups';
      break;
    case 'queue':
       throw new Exception('Unknown model');
      break;
    default:
      throw new Exception('Unknown model');
      break;
    }
  }
}
