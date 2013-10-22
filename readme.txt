=== Plugin Name ===
Contributors: webzunft
Tags: ads, ad, adblock, adblocker, ad blocker, ad block, adblock count, adblock counter, ad analysis, ad optimization
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.2.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Count the number of visits from users with an ad blocker.

== Description ==

*BlockAlyzer* counts how many of your visitors are using an ad blocker. You might use this to estimate the impact of adblock add-ons on your income from advertisement.

**compare your data with others**

It is already nice to know your own data. But to get the idea of how bad you are affected, I added a benchmark feature to compare your data with the data from other sites with the same language and topic.
The benchmark feature is completely voluntarily. No data will be send without you knowing.

**these numbers are included**

* total page views
* total unique visitors
* relative and absolute number of page views with ad blocker enabled
* relative and absolute number of unique visitors with ad block enabled

The benchmark feature will show you the share of visitors with adblock from other sites in your language and with a similar topic.

**other features**

* desktop widget

**localization**

English, German

(please contact me before sending a translation)

**further instructions**

Developers can use BlockAlyzer’s adblock detection for their own purposes from this [tutorial](http://webgilde.com/en/blockalyzer/developers/).

Please find further instructions on the [BlockAlyzer homepage](http://webgilde.com/en/blockalyzer/)

== Installation ==

1. Upload `blockalyzer`-folder to the `/wp-content/plugins/` directory
1. Activate blockalyzer through the 'Plugins' menu in WordPress
1. activate the stats method under _Settings > BlockAlyzer_

== Screenshots ==

1. Admin panel, where to choose stats method and site topic
2. statistics table
3. statistics table with benchmark data

== Changelog ==

= 1.2.6 =

* added dashboard widget with short statistics
* added German translation
* set basic method to active by default on plugin activation
* please [review and vote for the plugin](http://wordpress.org/support/view/plugin-reviews/blockalyzer-adblock-counter "BlockAlyzer Reviews")
* Analytics Import, Debugging, ...? vote for the next major feature [here](http://webgilde.com/en/blockalyzer-1-2-6/)

= 1.2.5.1 =

* fixed some rogue css affecting all admin pages

= 1.2.5 =

* optimized styling on statistics page
* fixed language issue for multisite
* added locale to benchmark data
* added plugin version when sending benchmark
* added plugin banner

= 1.2.4.1 =

* fixed a bug when benchmark topic wasn't sent and therefore not returned

= 1.2.4 =

* initial start (after tested and developed for my own purposes)