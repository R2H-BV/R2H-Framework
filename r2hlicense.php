<?php
/**
 * R2H License Plugin
 * @author      Michael Snoeren <michael@r2h.nl>
 * @copyright   R2H Marketing & Internet Solutions © 2019
 * @license     GNU/GPLv3
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class PlgSystemR2HLicense extends CMSPlugin
{
    /**
     * @var     boolean $autoloadLanguage Autoloads the language files.
     * @access  protected
     */
    protected $autoloadLanguage = true;

    /**
     * Appends the license key to the url to allow purchased packages to be downloaded.
     * @param   string $url     The url of the package.
     * @param   array  $headers Additional headers for the request.
     * @access  public
     * @return  boolean
     */
    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        $uri = Uri::getInstance($url);
        $host = strtolower($uri->getHost());

        // Check if the host matches.
        if (!in_array($host, ['r2h.nl', 'www.r2h.nl'])) {
            return true;
        }

        // Do not proceed if the package is a zip.
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        if (in_array($extension, ['zip', 'rar'])) {
            return true;
        }

        // Get the key from the installation.
        $key = $this->getKey();

        // Add the key to the url if found.
        if (!empty($key)) {
            $uri->setVar('key', $key);
        }

        // Update the url with the new URL.
        $url = $uri->toString();

        return true;
    }

    /**
     * Get the key from this installation.
     * @access  protected
     * @return  string
     */
    protected function getKey(): string
    {
        jimport('joomla.filesystem.file');

        // Set the paths to the known locations of the key.
        $oldLocation = JPATH_LIBRARIES . '/r2hframework/license.key';
        $newLocation = JPATH_ROOT . '/plugins/system/r2hlicense/license.key';

        // Determine the file where it's located at.
        $file = JFile::exists($oldLocation)
            ? $oldLocation
            : (JFile::exists($newLocation)
                ? $newLocation
                : null);

        // Return the key or nothing if the file is not found.
        return $file !== null
            ? trim(file_get_contents($file))
            : '';
    }
}
