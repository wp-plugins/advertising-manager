=== Advertising Manager ===
Contributors: switzer, mutube
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=martin%2efitzpatrick%40gmail%2ecom&item_name=Donation%20to%20mutube%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: adsense, ad, link, referral, manage, widget, google, adbrite, cj, adpinion, shoppingads, ypn, widgetbucks, openx, adroll, affiliate, crispads, adgridwork
Tested up to: 2.7
Requires at least: 2.5.0
Stable tag: 3.2.13

Advertising Manager simplifies managing Google Adsense and other Ads Networks on your blog.


== Description ==

Advertising Manager is a Wordpress plugin for managing Google Adsense and many other ad networks ads on your blog. It automatically recognises many ad networks, and allows positioning with widgets, code or inline tags.

Version 3.3.x now supports
[Google Adsense](http://www.google.com/adsense), [AdBrite](http://www.adbrite.com/mb/landing_both.php?spid=51549&afb=120x60-1-blue), [AdGridWork](http://www.adgridwork.com/?r=18501), [Adpinion](http://www.adpinion.com/), [Adroll](http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/7L73RCFU5VCG7FRNNIGH7O/d6ca1e265e654df2010a2153d5c42ed4.re), [Commission Junction](http://www.cj.com/), [CrispAds](http://www.crispads.com/), [OpenX](http://www.openx.org/), [ShoppingAds](http://www.shoppingads.com/refer_1ebff04bf5805f6da1b4), [Yahoo!PN](http://ypn.yahoo.com/), and [WidgetBucks](http://www.widgetbucks.com/home.page?referrer=468034).

Automatic Ad Code Importer for all supported networks.
Widgets & Sidebar Modules compatible (as used in the popular K2 theme).
Automatic limiting of Ads to meet network T&Cs (Google 3 units/page)

[Extended instructions are available here...](http://www.mutube.com/mu/getting-started-with-advertising-manager-3x).

This plugin is under active development: if you experience problems, please first make sure you have the latest version installed. Feature requests, bug reports and comments can be submitted [here](http://forum.openx.org/index.php?showforum=74).


== Screenshots ==

[WP Advertising Manager List Screen in WP 2.7](http://blog.openx.org/wp-content/uploads/advertising-manager-list-wp-27.png "WP Advertising Manager List Screen in WP 2.7")
[WP Advertising Manager Edit Screen in WP 2.7](http://blog.openx.org/wp-content/uploads/advertising-manager-edit-wp-27.png "WP Advertising Manager Edit Screen in WP 2.7")
[WP Advertising Manager List Screen in WP 2.6](http://blog.openx.org/wp-content/uploads/picture-10.png "WP Advertising Manager List Screen in WP 2.6")


== Installation ==

1. Unzip the downloaded package and upload the Adsense Manager folder into your Wordpress plugins folder
1. Log into your WordPress admin panel
1. Go to Plugins and “Activate” the plugin
1. Previous installations will be updated and a notice displayed. If you have not used Advertising Manager before but have used Adsense Manager or Adsense Deluxe, you will be offered the change to import those ads.
1. “Advertising Manager” will now be displayed in your Options section and “Ads” appears under Manage.
1. Import, create and modifty your Ad blocks under Manage » Ad Units


== Frequently Asked Questions ==

= Why does changing Ad Format/Dimensions sometimes not change the size of the ad? =

For some ad networks (e.g. WidgetBucks, Adroll, etc.) the dimensions of ads are managed through the online interface. There is no way to change these settings from within the WordPress system that would work reliably. You do not have to update these dimension settings if you update your Ad online, however, it can be useful in correctly identifying 'Alternate Ads' for AdSense blocks.

= Do I still need Advertising Manager now I can manage ads through Google's system? =

Yes. While the original purpose of being able to modify colours etc. without digging into code is now gone (although still supported) there are other advantages to Advertising Manager. For example: positioning, and placement of networks other than Google Adsense.  Additionally there are some plans afoot to provide intelligent ad placing methods to make all this work even better.

= How do I place Ad code at the top, bottom, left, right, etc. of the page? =

There is a (nice tutorial here)[http://www.tamba2.org.uk/wordpress/adsense/] which explains positioning using code in template files. You can use this together with Advertising Manager: just place the ad code tags <?php advman_ad(); ?> where it says "place code here". 

= Upgrading has gone a bit wrong... What can I do? =

To revert to an old copy of your Ad database, go to your Dashboard and add ?advman-revert-db=X to your URL. Replace X with the major version that you want to revert to.
 
If the latest version to work was 2.1, enter: ?advman-revert-db=2

Load the page and Advertising Manager will revert to that version of the database and re-attempt the upgrade.

= How can I share revenue with my authors? =
1.  Load YOUR ad into Advertising manager.
1.  Set the weight of this ad to be 100.
1.  Configure the other parameters of the ad as you see fit.
1.  Copy this ad.  Make sure to keep the name the same (so the ad will rotate).
1.  In 'Display Options', select the author who you want to share revenue with.
1.  In 'Account Details' section, replace the ID with your partner's ID.
1.  Depending on what % you want to show, set the weight appropriately.  For example, if you want to show 2 ads from your author for every 1 ad for you, then set the author ad weight to 200 (e.g. 200 / (200 + 100) = 66.7% rev share)

= Where can I get more information? =

[Complete usage instructions are available here.](http://www.mutube.com/mu/getting-started-with-advertising-manager-3x)

== To Do ==

* New list screen look and feel
* Re-introduce 'limit ads per page' (per Adsense T&C)
* Link into OpenX Sync and OpenX Market for optimisation
* Test Widget / Sidebar functionality through the K2 Theme
* Test with WP 2.7
* More testing the upgrade functionality from Adsense Deluxe, Adsense Manager, OpenX WP Plug-in
* Make it able to translate in different languages
* Ad Zones to allow grouping of ads at a particular location, and switching depending on the visitors language, country, etc.
* Auto-inserting of ads into posts based on configurable rules (i.e. All Posts, 2nd Paragraph)
* Localisation: multi-language support
* Support for Amazon Affiliates and any other networks I hear about.

== Change Log ==

By popular demand, below are the changes for versions listed. Use this to determine whether it is worth upgrading and also to see when bugs you've reported have been fixed.

As a general rule the version X.Y.Z increments Z with bugfixes, Y with additional features, and X with major overhaul.

* **3.3.4** Beta version with ad rotating and optimisation functionality
* **3.2.13** Fix for WordPress 2.3.3 compatibility.
* **3.2.11** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.10** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.9** Database/bugfixing code, only neccessary if you're experiencing errors.
* **3.2.8** Upgrade fixes, should fix ->network errors, see plugin homepage for instructions how to fix if you're stuck here.
* **3.2.7** Fixes to Javascript errors (minor, will not impact plugin function). Upgrade fix. Prevents error on 2.5>3.2
* **3.2.6** Default ad checking fix. Ads will continue to work even if default-ad not set. Fixed Javascript errors.
* **3.2.5** Fix to widgets to match updated WordPress code. May require replacement of widgets again. Fix to default ad selection, prevents errors in Widgets & ensures ads appear on site.
* **3.2.4** Bugfixes to upgrade path from 2.5, prevents requirement to open/save each ad unit. Account ID is now copied across correctly during upgrades.