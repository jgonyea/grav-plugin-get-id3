<?php
namespace Grav\Plugin;


use Grav\Common\Plugin;
use Grav\Common\Grav;
use getID3;

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
    // Don't proceed if we are in the admin plugin
    if ($this->isAdmin()) {
      $this->findGetID3();
    return;
  }

  // Enable the main event we are interested in
  $this->enable([
   
  ]);
  }

  /**
   * Ensure that the getID3 library can be found.
   * 
   */
  public function findGetID3() {
    $grav = new Grav();
    
    $library_files = array(
      'getid3.lib.php',
      'getid3.php',
      'module.audio.mp3.php',
      'module.tag.id3v1.php',
      'module.tag.id3v2.php',
    );

    foreach ($library_files as $file){
      $path = __DIR__ . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $file;
      if(!file_exists($path)){
        dump($path );
        $grav::instance()['log']->error("$file from getid3 library not found. Please download it from http://www.getid3.org/");
        return FALSE;
      }
    }

    return TRUE;  
  }
  
  /**
   * Create and initialize an instance of getID3 class.
   * 
   * @return getID3
   */
  public function getid3_instance() {
    if (findGetID3()) {
      $id3 = new getID3();
      // MD5 is a big performance hit. Disable it by default.
      $id3->option_md5_data = FALSE;
      $id3->option_md5_data_source = FALSE;
      $id3->encoding = 'UTF-8';
  }
  return $id3;
  }
  
  
  /**
   * Takes a file entity and returns ID3 data.
   * @param type $file
   */
  public function analyzeFile($file){
    $getID3 = $this->getid3_instance();
    dump($file);
    $file_path = $file;
    if(file_exists($file_path)){
      return $getID3->analyze($file_path);
    }
    return NULL;
  }
}
