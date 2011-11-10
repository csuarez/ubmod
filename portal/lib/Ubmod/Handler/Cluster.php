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
 * Cluster REST handler.
 *
 * @author Jeffrey T. Palmer <jtpalmer@ccr.buffalo.edu>
 * @version $Id$
 * @copyright Center for Computational Research, University at Buffalo, 2011
 * @package Ubmod
 */

/**
 * Cluster REST Handler.
 *
 * @package Ubmod
 */
class Ubmod_Handler_Cluster
{

  /**
   * Factory method.
   *
   * @return Ubmod_Handler_Cluster
   */
  public static function factory()
  {
    return new Ubmod_Handler_Cluster();
  }

  /**
   * Return help for the "list" action.
   *
   * @return Ubmod_RestResponse
   */
  public function listHelp()
  {
    $desc = 'List all clusters.  Results will be an array where individual'
      . ' records will consist of (cluster_id, display_name, host).';
    return Ubmod_RestResponse::factory(TRUE, $desc);
  }

  /**
   * List clusters.
   *
   * @param array $arguments Request GET data
   * @param array $postData  Request POST data
   *
   * @return Ubmod_RestResponse
   */
  public function listAction(array $arguments, array $postData = NULL)
  {
    $clusters = Ubmod_Model_Cluster::getAll();
    return Ubmod_RestResponse::factory(TRUE, NULL, array(
      'data'  => $clusters,
      'total' => count($clusters),
    ));
  }
}
