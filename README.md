# Piwik PiwikDebugger Plugin

## Description

### WARNING DO NOT INSTALL THIS PLUGIN ON ANY SERVER IN PRODUCTION
This plugin is only meant for developing or for debugging Piwik instances where you do not have any SSH/FTP access.

### Features

* Edit all Piwik files and restore all changed files with one click
* Browse the Piwik database
* Execute database queries and see the result
* Monitor server stats
* Execute any system command in a terminal (if server allows you to execute it)
* Check PHP info
* Package Piwik or any directory as ZIP and download it
* See all configured config values and change some of them
* See all log messages
* See all executed SQL queries during a request, how long they took and which parameters were used
* See how long it took to generate a page on the server and how much memory it needed

### Used libraries

* https://github.com/mattpass/ICEcoder (Standard Open Source Initiative MIT License)
* https://github.com/afaqurk/linux-dash (The MIT License (MIT))
* https://github.com/vrana/adminer/ (Apache License 2.0 or GPL 2)
* https://github.com/maximebf/php-debugbar (MIT)

### License

PiwikDebugger is released under the GPL v3 (or later) license, see [LICENSE](LICENSE)


## FAQ

__Where can I find those features?__

Most of them are visible in the Admin/Settings area of Piwik under the section "Diagonse". The debug bar should be visible on the bottom of the page. In case it is closed there is an icon on the bottom left to open it. 

__How do I get to the terminal?__

Open "Edit files", move your mouse to the left and a navigation will open where you can select the terminal.

__Where is my ZIP file that I have generated in the file editor?__

If the browser doesn't offer you to download the ZIP it'll be placed in `plugins/PiwikDebugger/libs/icecoder/backups`

__When I edit a file in the file editor, where can I find an untouched copy of the file?__

Before changing a file the first time we will generate a backup within the folder `plugins/PiwikDebugger/libs/icecoder/backups`

__How can I restore all edited files?__

Open the file editor and click on the menu entry "File => Restore all edited files"

## Changelog

0.1.0 Initial release

## Support

Please direct any feedback to hello@piwik.org - http://piwik.org
