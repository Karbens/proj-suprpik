=== Useful Banner Manager ===
Contributors: s_ruben
Donate link: http://rubensargsyan.com/donate/
Tags: banner manager, ads manager, banners, ads, advertisement, buddypress
Requires at least: 3.0
Tested up to: 3.5.1

This banner manager plugin helps to manage the banners easily over the WordPress blog. It works with BuddyPress too.

== Description ==

There are many WordPress blogs which have or need to have banners on them. So a banner manager plugin is very useful for those blogs. This plugin is created for it. The plugin helps to manage the banners over the Wordpress blog. It is very easy to use. Try it and be assured.

P.S.

Wishing you to earn much money by banners advertising. :)

[Plugin Homepage](http://rubensargsyan.com/wordpress-plugin-useful-banner-manager)

== Installation ==

1. Download useful-banner-manager.zip, unzip it and upload the useful-banner-manager directory (including all files within) to the /wp-content/plugins/ directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Go to "Banner Manager" panel and add banners.

= Add banners =

* To show the banners in the sidebar, go to the "Appearance"->"Widgets" panel and drag-and-drop the "UBM banners" box into your sidebar, configure options and save them.
* To show the banners in a post or a page, add [useful_banner_manager banners=2,6 count=1] (where the numbers 2 and 6 (banners=2,6) are the IDs of the banners which would be shown, the number 1 (count=1) is the count of the banners which would be shown) into the post or the page.
* Also the banners can be shown by adding `<?php if ( function_exists( 'useful_banner_manager_banners' ) ) { useful_banner_manager_banners( '2,6', 1 ); } ?>` (where the first argument ('2,6') is a string of the banners' IDs, separated by commas, and the second argument is the banners' count which would be shown).

= Add banners rotations =

* To show the banners rotations in the sidebar, go to the "Appearance"->"Widgets" panel and drag-and-drop the "UBM banners rotation" box into your sidebar, configure the options and save them.
* To show the banners in a post or a page, add [useful_banner_manager_banner_rotation banners=2,6 interval=5 width=468 height=60 orderby=rand] (where the numbers 2 and 6 (banners=2,6) are the IDs of the banners which would be shown, the number 5 (interval=1) is the seconds of the delay between banners rotations, the numbers 468 and 60 are the width and height of the banners which will be rotating and set "orderby" option to "rand" to show the banners in random order or "order" (orderby=rand or orderby=order)) into the post or the page.
* Also the banners rotations can be shown by adding `<?php if ( function_exists( 'useful_banner_manager_banners_rotation' ) ) { useful_banner_manager_banners_rotation( '2,6', 5, 468, 60, 'rand' ); } ?>` (where the first argument ('2,6') is a string of the banners' IDs, separated by commas,  the second argument (5) is the seconds of the delay between banners rotations, the third and forth arguments (468 and 60) are the width and height of the banners which will be rotating and put the fifth argument ('rand'), which is optional, to show the banners in random order).

== Frequently Asked Questions ==

For questions contact with the plugin author - [Ruben Sargsyan](http://rubensargsyan.com/contact).

== Screenshots ==

1. Manage Banners

== Changelog ==

= 1.3 =
* Added new options for banners - "Wrapper ID" (ID of the tag "div" wrapping the banner) and "Wrapper Class" (Class or classes of the tag "div" wrapping the banner).
* Fixed some bugs.

= 1.2.1 =
* Fixed some bugs.

= 1.2 =
* Now you can select all banners in the shortcodes without adding their ids.
* Flash banners and lightboxes conflict is removed.
* Fixed some bugs.

= 1.1 =
* Added banners rotation function.
* Added new options for banners - "Image Alt" and "Link Rel".
* Fixed some bugs.

= 1.0 =
* First release.