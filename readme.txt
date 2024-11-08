﻿=== Buddypress Who clicked at my Profile? === 
Contributors: Florian Schießl
Donate link: http://ifs-net.de/donate.php
Tags: buddypress, profile, social network
Requires at least: 4.2
Tested up to: 4.7.1
Stable Tag: trunk
License: GPLv2
License URI: http://www.opensource.org/licenses/GPL-2.0

This plugin will notify your members about other members that visited their profile.
This plugin also provides a widget that shows last profile visitors for the logged in user.

== Description ==

**Do you want to increase your buddypress user's interaction?**

Tell them if other visited their profile!

This plugin will notify your members about other members that visited their profile via buddypress notification system.
This plugin also provides a widget that shows last profile visitors for the logged in user.
This plugin provides a shortcode that can be used anywhere to display the logged in user's visitors

Shortcode usage:

`[buddypresswcamp_show_visits]`

Use Parameter to show avatars insted of links or configure how many last visitors should be shown.

`[buddypresswcamp_show_visits showAvatars=1 amount=5]`

If you use bbpress < 2.6 please apply the changes described there: https://bbpress.trac.wordpress.org/ticket/2779 to get the notifications working

**More about me and my plugins**

Since the year 1999 I do administration, customizing and programming for several forums, communities and social networks. In the year 2013 I switched from another PHP framework to Wordpress.
Because not all plugins I'd like to have exist already I wrote some own plugins and I think I'll continue to do so.

If you have the scope at forums or social networks my other modules might also be interesting for you. [Just take a look at my Wordpress Profile to see all my Plugins.](http://wordpress.org/plugins/search.php?q=quan_flo "ifs-net / quan_flo Wordpress Plugins") Use them and if my work helps you to save time, earn money or just makes you happy feel free to donate - Thanks. The donation link can be found at the right sidebar next to this text.

== Installation ==

1. Upload the files to the `/wp-content/plugins/buddypress-who-clicked-at-my-profile/` directory or install through WordPress directly.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If you use bbpress < 2.6 please apply the changes described there: https://bbpress.trac.wordpress.org/ticket/2779 to get the notifications working

== Frequently Asked Questions ==

= This plugin does not work? =

This will only work with standard buddypress profile.
If you use another profile plugin make sure that the action "bp_before_member_header" is called.
This plugin will hook into this action and do it's magic stuff...

= You want to increase of change the number of visits that get tracked? =

You want to change the number of visits that should be tracked? Use the 'buddypress_wcamp_quantity' filter.

Add the following code to your functions.php

`add_filter('buddypress_wcamp_quantity','my_buddypress_wcamp_quantity');
function my_buddypress_wcamp_quantity() {
    return 25;
}`

This sets the value of users that get tracked to 25 for example.

= Exclude some users from being tracked? =

Some users should not be tracked? No problem!

Add the following code to your functions.php

`add_filter('buddypress_wcamp_excludeUsers','my_buddypress_wcamp_excludeUsers');
function my_buddypress_wcamp_excludeUsers() {
    return array(1,5,8,23); // exclude (as example) Users with ID 1, 5, 8 and 23
}`

This sets the value of users that get tracked to 50 for example.

= You do not want to use the buddypress notification system for "who clicked at my profile" notifications? =

Add the following code to your functions.php to disable the usage of buddypress notifications system for this plugin

`add_filter('buddypress_wcamp_usenotifications','my_buddypress_wcamp_usenotifications');
function my_buddypress_wcamp_usenotifications() { 
    return false; 
}`

= You have questions? =

Please use the plugins support forum

== Screenshots ==

1. screenshot-1.jpg

== Changelog ==

= 3.6 =
* improved buddypress compatibility

= 3.5 =
* Added shortcode support - now you can include last visitors into every wordpress page! See plugin website for shortcode usage

= 3.4 =
* Added filter for excluding specified users from being tracked, see FAQ for usage
* amount of tracked visits now per default set to 25 (use a filter to change this)
* widget that shows last visits has new options, aount of visits that should be displayed can be configured now.

= 3.3 =
* code optimation and removing deprecated widget structure (thanks to mcpalls)

= 3.2 =
* Adding update notifications with version hints

= 3.1 =
* The usage of buddypress notification system can now be controlled via filter, see FAQ section for more details

= 3.0 =
* New major release with new functionality
* Uses buddypress notification system to tell your users that others have visited their profile
* Some technical changes (timestamp of each visit tracked now)
* If you use bbpress < 2.6 please apply the changes described there: https://bbpress.trac.wordpress.org/ticket/2779 to get the notifications working 
* Removed some php notices

= 2.0 =
* Included filter "buddypress_wcamp_quantity" that lets you control the number of visits that should be tracked for each user. To use this filter see readme.txt file

= 1.9 =
* increased number of shown profile visits to 15 (need some customization? change the value of the $numberOfVisitsShown variable insider buddypress-who-.clicked-at-my-profile.php for your needs)
* fixed minor bug (own profile might be shown as visitor, no more existend users that visited a profile before they got deleted might have caused little display errors in widget

= 1.8 =
* fixed html bug

= 1.7 =
* fixed minor bug (php notice removed)

= 1.6 =
* fixed minor bug

= 1.5 =
* new option for widget: You can now choose if avatars or only links to visiting users should be shown inside each widget

= 1.4 =
* minor bugfixes (removed PHP Warning)

= 1.3 =
* fixed bug (wrong username if user name contained spaces, using native buddypress userlink method now to create userlink)

= 1.2 =
* minor bugfixes (removed PHP Warning)

= 1.1 =
* minor bugfixes

= 1.0 = 
* First version.
