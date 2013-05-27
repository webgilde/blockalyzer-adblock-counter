=== Plugin Name ===
Contributors: webzunft
Tags: ads, ad, adblock, ad blocker, ad block, adblock count, adblock counter
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.2.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Count how many of your visitors actually use an ad blocker.

== Description ==

This plugin counts how many of your users actually use an ad blocker.

The following statistics are included

* total page views
* total unique visitors
* relative and absolute number of page views with ad blocker enabled
* relative and absolute number of unique visitors with ad block enabled

Used methods
* is img/ads/banner.gif included?
* is js/advertisment.js loaded?

**Localization**

English

**Instructions**

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `adblock-counter`-folder to the `/wp-content/plugins/` directory
1. Activate adblock-counter through the 'Plugins' menu in WordPress

== Screenshots ==

== Changelog ==

= 1.2.2 =

* fixed inconsistant naming in the plugin; use 'ba_' prefix only

= 1.2.1 =

* solved some errors with sending data
* allow one compare request each 3 hours
* cache last compare values locally

= 1.2 =

* added stat exchange to get benchmark compared to your own adblock stats
* both counting methods (banner.gif and advertising.js) now deliver single result value
* minified and moved some js code
* 

= 1.1.2 = 

* separated option panel and result page
* separated standard statistic method from checking for adblock
* allow adding additional statistic methods without relation to each other
* added hooks and filters to allow multiple stat methods

= 1.1.1 =

* added the constant ABC_ADBLOCK_ENABLED to retrieve, if adblock is enabled or not (use for other plugins), since v. 1.2.2 it is BA_ADBLOCK_ENABLED

= 1.1.0 =

* added second method to count adblock by missing banner.gif
* merged ajax calls into one
* added some hooks for other plugins

= 1.0.0 =

* initial start

== Instructions ==