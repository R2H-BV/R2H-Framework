<?php

declare(strict_types=1);

/**
 * R2H License Plugin.
 *
 * @author      Michael Snoeren <michael@r2h.nl>
 * @copyright   R2H Marketing & Internet Solutions © 2019
 * @license     GNU/GPLv3
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class PlgSystemR2HLicenseInstallerScript
{
    /**
     * Enable the plugin after installation and put the license key in the right directory if found.
     *
     * @return bool
     */
    public function postflight()
    {
        jimport('joomla.filesystem.file');

        // Check if the key exists and if so, move it aswell.
        $currentLocation = __DIR__.'/license.key';
        $targetLocation = JPATH_ROOT.'/plugins/system/r2hlicense/license.key';

        if (JFile::exists($currentLocation)) {
            JFile::move($currentLocation, $targetLocation);
        }

        // Enable the plugin.
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
        ->update('#__extensions')
        ->set($db->qn('enabled').' = 1')
        ->where([
            $db->qn('type').' = '.$db->q('plugin'),
            $db->qn('element').' = '.$db->q('r2hlicense'),
        ]);

        $db->setQuery($query);

        return (bool) $db->execute();
    }
}
