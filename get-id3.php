<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Grav;
use RocketTheme\Toolbox\File\File;

/**
 * Class GetID3Plugin
 * @package Grav\Plugin
 */
class GetID3Plugin extends Plugin
{
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
        if ($this->findGetID3()) {
            // Don't proceed if we are in the admin plugin
            if ($this->isAdmin()) {
                return;
            }

          // Enable the main event we are interested in
            $this->enable([

            ]);
        }
    }

  /**
   * Ensure that the getID3 library can be found.
   *
   */
    public function findGetID3()
    {
        $grav = new Grav();
    
        $required_library_files = array(
            'getid3.lib.php',
            'getid3.php',
            'module.audio.mp3.php',
            'module.tag.id3v1.php',
            'module.tag.id3v2.php',
            'module.tag.apetag.php',
        );

        foreach ($required_library_files as $file) {
            $path = __DIR__ . DS . 'library' . DS . $file;
            if (!file_exists($path)) {
                $message = "Files are missing from the getid3 library. Please download it from http://www.getid3.org/ and follow the installation instructions in the README.md file for this plugin.";
                $grav::instance()['log']->error($message);
                $grav_messages = $this->grav['messages'];
                $grav_messages->add($message, 'error');
                // Disable the plugin if the library cannot be loaded.
                $this->disablePlugin();
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
        include_once(__DIR__ . DS . "library" . DS . "getid3.php");
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
    public function disablePlugin()
    {
        $grav = new Grav();
        $config_file_path = USER_DIR . 'config' . DS . 'plugins' . DS . 'get-id3.yaml';
        $file = File::instance($config_file_path);
        if ($file->writable()) {
            if($file->exists()){
                $file->delete();
            }
            $file->save("enabled: false");
            $message = "Successfully disabled GetID3Plugin.";
            $grav::instance()['log']->info($message);
            $grav_messages = $this->grav['messages'];
            $grav_messages->add($message, 'info');
        }
    }
}
