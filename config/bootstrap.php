<?php
/**
 * Bootstrap the application.
 *
 * @author Jeffrey T. Palmer <jtpalmer@ccr.buffalo.edu>
 * @version $Id$
 * @copyright Center for Computational Research, University at Buffalo, 2011
 * @package UBMoD
 */

require_once 'constants.php';

ini_alter('include_path', ini_get('include_path') . ':' . BASE_DIR . '/lib');


/**
 * Autoload implementation.
 *
 * @return void
 */
function __autoload($className)
{
  $file = LIB_DIR . '/' . str_replace('_', '/', $className) . '.php';
  if (file_exists($file))
  {
    require_once($file);
  }
  else
  {
    throw new Exception('Cannot find class: ' . $className);
  }
}

session_start();

$GLOBALS['options'] = new Zend_Config_Ini(CONFIG_FILE);