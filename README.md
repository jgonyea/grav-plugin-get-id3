# Get ID3 Plugin

The **Get ID3** Plugin is for [Grav CMS](http://github.com/getgrav/grav). Adds functionality for the getID3 library.

## Installation

Installing the Get ID3 plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install get-id3

This will install the Get ID3 plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/get-id3`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `get-id3`. You can find these files on [GitHub](https://github.com/jeremy-gonyea/grav-plugin-get-id3) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/get-id3
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Post Installation
Download the getID3 PHP library from its [homepage](http://www.getid3.org/).  Extract the files and copy the contents of getid3 subfolder to this plugin's library folder.  The structure should look something like this:

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
  

## Configuration

Before configuring this plugin, you should copy the `user/plugins/get-id3/get-id3.yaml` to `user/config/plugins/get-id3.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

## Usage

**Describe how to use the plugin.**

## Credits

- Inspired by the Drupal module [getid3](https://www.drupal.org/project/getid3/)

## To Do

- [ ] Future plans, if any

