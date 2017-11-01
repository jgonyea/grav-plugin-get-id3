# GetID3 Plugin

The **GetID3** Plugin is for [Grav CMS](http://github.com/getgrav/grav). It integrates the getID3 library into Grav CMS.

## Installation

Installing the GetID3 plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install get-id3

This will install the GetID3 plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/get-id3`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `get-id3`. You can find these files on [GitHub](https://github.com/jeremy-gonyea/grav-plugin-get-id3) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/get-id3
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Post Installation
After enabling the plugin, the getID3 library attempts to install itself automatically.  In the event of an error, you will need to install it manually from its [homepage](http://www.getid3.org/).  

Extract the files and copy the contents of getid3 subfolder to this plugin's library folder.  The structure should look something like this:

```
| user
  |--  plugins
    |--  get-id3
      |--  library
        |--  extension.cache.dbm.php
        |--  extension.cache.mysql.php
        |--  extension.cache.mysqli.php
        |--  extension.cache.sqlite3.php
        |--  getid3.lib.php
        |--  getid3.php
        |--  ...
```
The php files with names "write.*.php" are not needed for this plugin, but there's no harm in including them.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/get-id3/get-id3.yaml` to `user/config/plugins/get-id3.yaml` and only edit that copy.

```yaml
enabled: true
```

## Usage

This plugin provides no visual functionality.  It integrates the getID3 PHP library into GravCMS for other plugins (i.e. the Podcast plugin) to use.

File metadata can be retrieved by code similar to:

```
$meta = GetID3Plugin::analyzeFile($file)
```

where $file is a path to a media file.

## Credits

- Initially inspired by the Drupal module [getid3](https://www.drupal.org/project/getid3/)
- Ole Vik, who was patient with my Slack questions.
- James Heinrich, for his php library.

## Licensing Notes
This plugin is licensed under the MIT license.  The php library getID3 is licensed under various licenses, as seen here https://github.com/JamesHeinrich/getID3.

## To Do

- None, currently.
