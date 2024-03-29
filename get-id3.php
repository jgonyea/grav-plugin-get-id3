<?php
namespace Grav\Plugin;

use Grav\Common\Cache;
use Grav\Common\Grav;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\File\File;

/**
 * Class GetID3Plugin
 * @package Grav\Plugin
 */
class GetID3Plugin extends Plugin
{

    private $getID3_ver = "1.9.23";

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *   that the plugin wants to listen to. The key of each
     *   array section is the event that the plugin listens to
     *   and the value (in the form of an array) contains the
     *   callable (or function) as well as the priority. The
     *   higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
        'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

  /**
   * Initialize the plugin
   */
    public function onPluginsInitialized()
    {
        if (!$this->verifyGetID3()) {
            $this->downloadGetID3();
        }
    }

    /**
     * Ensure that the getID3 library can be found.
     */
    public function verifyGetID3(): bool
    {
        $required_library_files = array(
            'getid3.lib.php',
            'getid3.php',
            'module.audio.mp3.php',
            'module.tag.id3v1.php',
            'module.tag.id3v2.php',
            'module.tag.apetag.php',
        );
        $locator = Grav::instance()['locator'];
        foreach ($required_library_files as $filename) {
            $path = $locator->findResource('plugin://' . $this->name . '/library//' . $filename);
            if (!file_exists($path)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create and initialize an instance of getID3 class.
     *
     * @return getID3
     */
    public static function getID3Instance()
    {
        $locator = Grav::instance()['locator'];
        include_once($locator->findResource('plugin://get-id3/library/getid3.php'));
        $id3 = new \getID3();
        // MD5 is a big performance hit. Disable it by default.
        $id3->option_md5_data = false;
        $id3->option_md5_data_source = false;
        $id3->encoding = 'UTF-8';
        return $id3;
    }

    /**
     * Takes a file entity and returns ID3 data.
     * @param type $file
     */
    public static function analyzeFile($file)
    {
        $getID3 = GetID3Plugin::getID3Instance();
        return $getID3->analyze($file);
    }

    /**
     * Disables the plugin.
     */
    public function disablePlugin($message, $level = 'info')
    {
        $config_file_path = $this->grav['locator']->findResource('config://plugins/get-id3.yaml');
        $file = File::instance($config_file_path);
        if ($file->writable()) {
            if ($file->exists()) {
                $file->delete();
            }
            $file->save("enabled: false");
            $this->grav['log']->info($message);
            $grav_messages = $this->grav['messages'];
            $grav_messages->add($message, $level);
            $grav_messages->add("Files are missing from the getID3 library. Please see the grav log files for more information", 'error');
            $grav_messages->add("Please manually download the library from http://www.getid3.org/ and follow the installation instructions in the README.md file for this plugin.", 'error');
        }
        $grav_messages->add('Disabled GetId3 Plugin', 'info');
    }

    /**
     * Downloads and extracts the getID3 PHP library.
     */
    public function downloadGetID3()
    {
        // Locate library file online.
        try {
            $url = "https://github.com/JamesHeinrich/getID3/archive/refs/tags/v" . $this->getID3_ver .  ".zip";
            $library_dir = $this->grav['locator']->findResource('plugin://' . $this->name) . "/library";
            if (!file_exists($library_dir)) {
                mkdir($this->grav['locator']->findResource('plugin://' . $this->name) . '/library//', 0755, true);
            }

            // Make sure the url is reachable.
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // $retcode >= 400 -> not found; 200 -> found; 0 -> server not found.
            if ($retcode >= 400 || $retcode == 0) {
                $message = "HTTP Error ($retcode) while attempting to locate getID3.zip file '$url' .";
                $this->disablePlugin($message, 'error');
                return false;
            }
        } catch (\Exception $e) {
            $message = "Couldn't locate the getID3.zip file at " . $url;
            $this->disablePlugin($message, 'error');
            return false;
        }

        // Download zip file to library temp location.
        try {
            if ($remote_file = fopen($url, 'rb')) {
                $tmp_dir = $this->grav['locator']->findResource('tmp://', true, true);
                $local_file = tempnam($tmp_dir, 'getid3');

                $handle = fopen($local_file, "w");
                $contents = stream_get_contents($remote_file);

                fwrite($handle, $contents);
                fclose($remote_file);
                fclose($handle);
            }
        } catch (\Exception $e) {
            $message = "Failed to download file";
            $this->disablePlugin($message, 'error');
            return false;
        }

        // Unzip archive.
        try {
            $zip_obj = new \ZipArchive();
            if ($zip_obj->open($local_file) == true) {
                $zip_obj->extractTo($tmp_dir . '/getid3-extracted');
                $zip_obj->close();
            }
        } catch (\Exception $e) {
            $message = "Failed to extract library to " . $tmp_dir . "/getid3-extracted";
            $this->disablePlugin($message, 'error');
            return false;
        }

        // Move archive to library.
        try {
            // Remove existing library.
            if (file_exists($library_dir)) {
                rename($library_dir, $tmp_dir . "/old-getid3-library");
            }

            rename($tmp_dir . '/getid3-extracted/getID3-' . $this->getID3_ver . '/getid3', $library_dir);
            touch($library_dir . "/.gitkeep");
            Cache::clearCache("tmp-only");
            return true;
        } catch (\Exception $e) {
            $message = "Unable to move the library files to " . $library_dir;
            $this->disablePlugin($message, 'error');
            return false;
        }
    }
}
