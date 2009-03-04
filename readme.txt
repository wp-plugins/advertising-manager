=== Advertising Manager ===
Contributors: switzer, mutube
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=martin%2efitzpatrick%40gmail%2ecom&item_name=Donation%20to%20mutube%2ecom&currency_code=USD&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: adsense, ad, link, referral, manage, widget, google, adbrite, adify, cj, adpinion, shoppingads, ypn, widgetbucks, openx, adroll, affiliate, crispads, adgridwork
Tested up to: 2.7
Requires at least: 2.5.0
Stable tag: 3.2.13

Advertising Manager simplifies managing Google Adsense and other Ads Networks on your blog.


== Description ==

Advertising Manager is a Wordpress plugin for managing Google Adsense and many other ad networks ads on your blog. It automatically recognises many ad networks, and allows positioning with widgets, code or inline tags.

Version 3.3.x now supports
[Google Adsense](http://www.google.com/adsense), [AdBrite](http://www.adbrite.com/mb/landing_both.php?spid=51549&afb=120x60-1-blue), [Adify](http://www.adify.com), [AdGridWork](http://www.adgridwork.com/?r=18501), [Adpinion](http://www.adpinion.com/), [Adroll](http://re.adroll.com/a/D44UNLTJPNH5ZDXTTXII7V/7L73RCFU5VCG7FRNNIGH7O/d6ca1e265e654df2010a2153d5c42ed4.re), [Commission Junction](http://www.cj.com/), [CrispAds](http://www.crispads.com/), [OpenX](http://www.openx.org/), [ShoppingAds](http://www.shoppingads.com/refer_1ebff04bf5805f6da1b4), [Yahoo!PN](http://ypn.yahoo.com/), and [WidgetBucks](http://www.widgetbucks.com/home.page?referrer=468034).

Automatic Ad Code Importer for all supported networks.
Widgets & Sidebar Modules compatible (as used in the popular K2 theme).
Automatic limiting of Ads to meet network T&Cs (Google 3 units/page)

This plugin is under active development: if you experience problems, please first make sure you have the latest version installed. More detailed information, including documentation, as well as bug and feature request trackers can be found [here](http://code.openx.org/projects/show/advertising-manager).


== Screenshots ==

[WP Advertising Manager List Screen in WP 2.7](http://blog.openx.org/wp-content/uploads/advertising-manager-list-wp-27.png "WP Advertising Manager List Screen in WP 2.7")
[WP Advertising Manager Edit Screen in WP 2.7](http://blog.openx.org/wp-content/uploads/advertising-manager-edit-wp-27.png "WP Advertising Manager Edit Screen in WP 2.7")
[WP Advertising Manager List Screen in WP 2.6](http://blog.openx.org/wp-content/uploads/picture-10.png "WP Advertising Manager List Screen in WP 2.6")


== Installation ==

1. Unzip the downloaded package and transfer the advertising-manager folder into your wp-content/plugins folder
1. Log into your WordPress admin panel
1. Go to Plugins and “Activate” the Advertising Manager plug-in

More detailed installation instructions can be found [here](http://code.openx.org/wiki/advertising-manager/Installation_Instructions).

If you are upgrading from Adsense Manager, Adsense Deluxe, or a previous version of Advertising Manager, upgrade instructions can be found [here](http://code.openx.org/wiki/advertising-manager/Upgrading_Instructions).


== Frequently Asked Questions ==

= Does Advertising Manager support Wordpress MU (multi-user)? =
Yes.


= How do I display an ad? =
There are a number of ways.  Some of them include:
1.  From a post or page, enter [ad] for the default ad, or [ad#name] where 'name' is the name of the ad you want to display.
2.  From a template, enter <?php advman_ad() ?> for the default ad, or <?php advman_ad('name') ?> where 'name' is the name of the ad you want to display.
3.  Other legacy methods of calling an ad include:  <?php adsensem_ad() ?> or <?php adsensem_ad('name') ?> for backwards compatibility with Adsense Manager; and <!--adsense#name--> for backwards compatibility with Adsense Deluxe.

More detailed instuctions can be found in the documentation:  [Concepts - Placing ads on your blog](http://code.openx.org/wiki/advertising-manager/Placing_ads_on_your_blog)

= Can Advertising Manager work in my language? =
Yes.  Advertising Manager is Localised.  If your blog is in another language, and Advertising Manager shows in English, then it is most likely that a translation has not been done.
Don't worry - if you are a native speaker in English as well as your local language, you can register here:

http://translate.openx.org/projects/advman/

Your translation will be included in the next version of Advertising manager.  For more information, my contact details can be found here:
http://forum.openx.org/index.php?showuser=3


= Why does changing Ad Format/Dimensions sometimes not change the size of the ad? =

For some ad networks (e.g. WidgetBucks, Adroll, etc.) the dimensions of ads are managed through the online interface. There is no way to change these settings from within the WordPress system that would work reliably. You do not have to update these dimension settings if you update your Ad online, however, it can be useful in correctly identifying 'Alternate Ads' for AdSense blocks.


= Do I still need Advertising Manager now I can manage ads through Google's system? =

Advertising manager allows you to rotate ads, easily turn on/off ads, place them in your blog, and many features other than configuration of the ad colors and format.  It a critical tool to use if you want to make more from your advertising.


= How do I place Ad code at the top, bottom, left, right, etc. of the page? =

There is a (nice tutorial here)[http://www.tamba2.org.uk/wordpress/adsense/] which explains positioning using code in template files. You can use this together with Advertising Manager: just place the ad code tags <?php advman_ad('name'); ?> where it says "place code here". 


= Upgrading has gone a bit wrong... What can I do? =

To revert to an old copy of your Ad database, go to your Dashboard and add ?advman-revert-db=X to your URL. Replace X with the major version that you want to revert to.
 
If the latest version to work was 2.1, enter: ?advman-revert-db=2

Load the page and Advertising Manager will revert to that version of the database and re-attempt the upgrade.


= How can I share revenue with my authors? =

1.  Load YOUR ad into Advertising Manager.
1.  Load your AUTHOR's ad into Advertising Manager
1.  In your author's ad, be sure to select the authors username in Display Options
1.  Set the weights of the ads according to your revenue share.  The easiest way to do this is to set YOUR ad weight to 36, and then set the weight of your author's ad according to the revenue share deal.  For 10% revenue share, set the author ad weight to 4; 20% = 9; 33% = 18; 40% = 24; 50% = 36; 60% = 54; 66.7% = 72; 70% = 84; 80% = 144; 90% = 324.  For the nerdy wonks out there, the formula is (AUTHOR AD WEIGHT) = (MY AD WEIGHT * REVENUE SHARE) / (1 - REVENUE SHARE)


== To Do ==

* New list screen look and feel
* Re-introduce 'limit ads per page' (per Adsense T&C)
* Link into OpenX Sync and OpenX Market for optimisation
* Test Widget / Sidebar functionality through the K2 Theme
* More testing the upgrade functionality from Adsense Deluxe, Adsense Manager, OpenX WP Plug-in
* Ad Zones to allow grouping of ads at a particular location, and switching depending on the visitors language, country, etc.
* Auto-inserting of ads into posts based on configurable rules (i.e. All Posts, 2nd Paragraph)
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