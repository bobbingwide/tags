=== tags ===
Contributors: bobbingwide
Donate link: https://www.oik-plugins.com/oik/oik-donate/
Tags: CPTs, golf, events, players, results
Requires at least: 5.0.0
Tested up to: 6.0.1
Stable tag: 0.4.0
License: GPL v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

TAGS - The Anchor Golf Society's WordPress plugin.


Used in conjunction with the Genesis child theme genesis-tags this
provides the functionality to display the events for The Anchor Golf Society.

It is dependent upon:, oik, oik-fields, oik-bob-bing-wide, oik-css, oik-dates, oik-types, oik-user
and the theme relies on oik-rwd for some responsive behaviour.



== Installation ==
1. Upload the contents of the tags plugin to the `/wp-content/plugins/tags' directory
1. Activate the tags plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==
None

== Upgrade Notice 
= 0.4.0 =
Upgrade for NTP on 27 holes, Results entry improvement and display of Events by Course.

= 0.3.1 = 
Upgrade to support competitors requiring Buggies.

= 0.3.0 =
Upgrade for enhanced Player display using [tags_achievements] and  yearly results [tags_results] shortcode.

= 0.2.0 =
Upgrade for enhanced Player display - with Results and Attendance

= 0.1.0 =
Upgrade for peaceful coexistence with WordPress 5.0 and/or Gutenberg

= 0.0.2 =
Improvements for display of tabs for Events

= 0.0.1 = 
17 - 21 Apr 2016 - improvements for SEO, display all events and bunging onto GitHub as GPL-3.0

= 0.0.0 =
New bespoke plugin. Originally developed to migrate content from Drupal to WordPress.

== Changelog ==
= 0.4.0 =
* Changed: Automatically display Events for Courses #14  
* Changed: Allow for 27 holes #15
* Fixed: Set event_date on/before the current time so it doesn't become scheduled #13
* Tested: With WordPress 6.0.1
* Tested: With PHP 8.0

= 0.3.1 = 
* Changed: Added Buggy option for competitors #11
* Fixed: Avoid Warning for null result_type on the 8 new rows #12
* Fixed: Cater for most recent update in bw_get_posts() by using exclude => -1 #12
* Tested: With WordPress 5.8.2
* Tested: With PHP 8.0

= 0.3.0 =
* Changed: Implement [tags_achievements] shortcode to replace the results [bw_related] shortcode #8
* Changed: Change orderby=result_type to orderby=ID #10
* Added: Implement [tags_results] shortcode #9
* Tested: With WordPress 5.8.2
* Tested: With PHP 8.0

= 0.2.0 =
* Added: Results and Attendance section automatically added for a Player, [github bobbingwide tags issues 8]

= 0.1.0=
* Fixed: Determine lat and long automatically from address and post code,[github bobbingwide tags issue 5]
* Changed: Support for WordPress 5.0 and the new block editor, [github bobbingwide tags issue 6]
* Changed: Improve the initial selection of the Select the event selection list,[github bobbingwide tags issue 7]
* Tested: With WordPress 5.0.3
* Tested: With Gutenberg 4.9.0
* Tested: With PHP 7.2

= 0.0.2 = 
* Changed: Improve display of Events [github bobbingwide tags issue 1]

= 0.0.1 = 
* Added: GPL v3
* Changed: Improved SEO for Course post type
* Changed: Change label for playing_status from Statuses to Playing?
 
= 0.0.0 =
* Added: New plugin, November 2015, just before the AGM 2015

== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](https://www.oik-plugins.com/oik)
**"the oik plugin - for often included key-information"**

