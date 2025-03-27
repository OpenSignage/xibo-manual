# Introduction
Welcome to the Xibo Manual.

This repository contains the source content for the Xibo manual in markdown format. 

## Support
Please track all issues in this repository here: https://github.com/OpenSignage/xibo-manual/issues

## Building
The manual is built by running generate.php.

```
php genarate.php [template]
```
If template is not spacified default template will be use.

### Docker
It is also possible to build the manual using Docker, resulting in a Docker
image which hosts the complete manual and a web server.

To do this issue the command:

```
./build.sh -t default -r xibo-manual
```

Where `-t` is your theme name and `-r` is the name with which to tag the 
container.

Themes must exist in `/template/custom/<theme_name>` to be built. They 
are build using inheritance from the default theme.

## Translations
To translate English manual to Japanese, translator.php can provide machine translation.

```
php translate.php [translation target md file]
```
If target is not spacified, all .md files are translate.

We are using Google translate API, you need to get API KEY to run this program.

Japanese translation is handled by Open Source Digital Signage Initiative.

## AI assistant (Custom function)

## Google Search Engine (Custom function)
You can search within the manual pages using the Google programmable search engine.
To use this function, you need to register your search site.
[Google Programmable Search Engine](https://programmablesearchengine.google.com)

### Google Search Console
t takes a significant amount of time for Google to crawl the manual pages you host on your site.
To make your site searchable as quickly as possible, we recommend that you register the URL of your manual page in the [Google Search Console](https://search.google.com/search-console).

## TODO
1. Support <feat> and <video> tag in manual.
1. Support translation dictionary.
