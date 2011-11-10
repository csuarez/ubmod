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
 * Queue model.
 *
 * @author Jeffrey T. Palmer <jtpalmer@ccr.buffalo.edu>
 * @version $Id$
 * @copyright Center for Computational Research, University at Buffalo, 2011
 * @package Ubmod
 */

/**
 * Queue Model.
 *
 * @package Ubmod
 */
class Ubmod_Model_Queue
{

  /**
   * Return the number of queues with activity for the given parameters.
   *
   * @param Ubmod_Model_QueryParams $params The query parameters.
   *
   * @return int
   */
  public static function getActivityCount(Ubmod_Model_QueryParams $params)
  {
    $timeClause = Ubmod_Model_Interval::getWhereClause($params);

    $sql = "
      SELECT COUNT(DISTINCT dim_queue_id)
      FROM fact_job
      JOIN dim_queue USING (dim_queue_id)
      JOIN dim_date  USING (dim_date_id)
      WHERE
            dim_cluster_id = :cluster_id
        AND $timeClause
    ";

   $dbParams = array(':cluster_id' => $params->getClusterId());

    if ($params->hasFilter()) {
      $sql .= ' AND name LIKE :filter';
      $dbParams[':filter'] = '%' . $params->getFilter() . '%';
    }

    $dbh = Ubmod_DbService::dbh();
    $sql = Ubmod_DataWarehouse::optimize($sql);
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute($dbParams);
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    $result = $stmt->fetch();

    return $result[0];
  }

  /**
   * Retuns an array of queues joined with their activiy.
   *
   * @param Ubmod_Model_QueryParams $params The query parameters.
   *
   * @return array
   */
  public static function getActivity(Ubmod_Model_QueryParams $params)
  {
    $timeClause = Ubmod_Model_Interval::getWhereClause($params);

    $sql = "
      SELECT
        dim_queue_id                 AS queue_id,
        name                         AS queue,
        COUNT(*)                     AS jobs,
        ROUND(SUM(wallt) / 86400, 1) AS wallt,
        ROUND(SUM(cput)  / 86400, 1) AS cput,
        ROUND(AVG(mem)   / 1024,  1) AS avg_mem,
        ROUND(AVG(wait)  / 3600,  1) AS avg_wait,
        ROUND(AVG(cpus),          1) AS avg_cpus
      FROM fact_job
      JOIN dim_queue USING (dim_queue_id)
      JOIN dim_date  USING (dim_date_id)
      WHERE
            dim_cluster_id = :cluster_id
        AND $timeClause
    ";

    $dbParams = array(':cluster_id' => $params->getClusterId());

    if ($params->hasFilter()) {
      $sql .= ' AND name LIKE :filter';
      $dbParams[':filter'] = '%' . $params->getFilter() . '%';
    }

    $sql .= ' GROUP BY queue_id';

    $sortFields
      = array('queue', 'jobs', 'avg_cpus', 'avg_wait', 'wallt', 'avg_mem');

    if ($params->hasOrderByColumn()) {
      $column = $params->getOrderByColumn();
      if (!in_array($column, $sortFields)) { $column = 'wallt'; }
      $dir = $params->isOrderByDescending() ? 'DESC' : 'ASC';
      $sql .= sprintf(' ORDER BY %s %s', $column, $dir);
    }

    if ($params->hasLimitRowCount()) {
      $sql .= sprintf(' LIMIT %d', $params->getLimitRowCount());
      if ($params->hasLimitOffset()) {
        $sql .= sprintf(' OFFSET %d', $params->getLimitOffset());
      }
    }

    $dbh = Ubmod_DbService::dbh();
    $sql = Ubmod_DataWarehouse::optimize($sql);
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute($dbParams);
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Returns the queue for a given id and parameters.
   *
   * @param Ubmod_Model_QueryParams $params The query parameters.
   *
   * @return array
   */
  public static function getActivityById(Ubmod_Model_QueryParams $params)
  {
    $timeClause = Ubmod_Model_Interval::getWhereClause($params);

    $sql = "
      SELECT
        dim_queue_id                 AS queue_id,
        dim_queue.name               AS queue,
        COUNT(*)                     AS jobs,
        COUNT(DISTINCT dim_user_id)  AS user_count,
        ROUND(SUM(wallt) / 86400, 1) AS wallt,
        ROUND(AVG(wallt) / 86400, 1) AS avg_wallt,
        ROUND(MAX(wallt) / 86400, 1) AS max_wallt,
        ROUND(SUM(cput)  / 86400, 1) AS cput,
        ROUND(AVG(cput)  / 86400, 1) AS avg_cput,
        ROUND(MAX(cput)  / 86400, 1) AS max_cput,
        ROUND(AVG(mem)   / 1024,  1) AS avg_mem,
        ROUND(MAX(mem)   / 1024,  1) AS max_mem,
        ROUND(AVG(vmem)  / 1024,  1) AS avg_vmem,
        ROUND(MAX(vmem)  / 1024,  1) AS max_vmem,
        ROUND(AVG(wait)  / 3600,  1) AS avg_wait,
        ROUND(AVG(exect) / 3600,  1) AS avg_exect,
        ROUND(MAX(nodes),         1) AS max_nodes,
        ROUND(AVG(nodes),         1) AS avg_nodes,
        ROUND(MAX(cpus),          1) AS max_cpus,
        ROUND(AVG(cpus),          1) AS avg_cpus
      FROM fact_job
      JOIN dim_queue USING (dim_queue_id)
      JOIN dim_date  USING (dim_date_id)
      JOIN dim_user  USING (dim_user_id)
      WHERE
            dim_queue_id   = :queue_id
        AND dim_cluster_id = :cluster_id
        AND $timeClause
      GROUP BY queue_id
    ";

    $dbParams = array(
      ':cluster_id' => $params->getClusterId(),
      ':queue_id'   => $params->getQueueId(),
    );

    $dbh = Ubmod_DbService::dbh();
    $sql = Ubmod_DataWarehouse::optimize($sql);
    $stmt = $dbh->prepare($sql);
    $r = $stmt->execute($dbParams);
    if (!$r) {
      $err = $stmt->errorInfo();
      throw new Exception($err[2]);
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
