=== Doorzz Real Estate ===
Author: Evgeny Kolyakov
Contributors: freaker2k17
Tags: real-estate, doorzz.com, worldwide, sell, buy, rent, house, apartment, commercial, agent, shortcode
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://doorzz.com/auto/pricing
Requires PHP: 5.4
Requires at least: 4.6
Tested up to: 4.9.1
Stable tag: 1.0
Version: 1.0

Doorzz.com Real Estate Wordpress Plugin.

== Description ==

Doorzz real estate listings plug-in. Customize lists to show in your website. 
Enter your user Id to show only your listings and customize the results by entering your preferred category or filters. 
Showcase all the listings over or under a certain price, price per unit, size and more.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/doorzz-real-estate` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

* To configure the plugin's template, go to the "Settings" -> "Doorzz Real Estate" page.

== How to Use ==

Once the plugin is properly configured you can add the [listings] shortcodes to your posts.

** Parameters: **

* lang: Language [auto / en / he / ru / ar / sr / fr / el / es / it / zh / zh-Hant / id / pt / nl / de / se / ja / uk] (default: auto)
* limit: Results limit, max 50 [1+] (default: 50)
* page: The current page [0+] (default: 0)
* zoom: Zoom level [2 - 20] (default: 3)
* hid: House ID from doorzz.com [string] (default: null)
* hids: List of house ID's from doorzz.com [string of ID's separated by comma] (default: null)
* qid: Query ID from doorzz.com [string] (default: null)
* uid: User ID from doorzz.com [string] (default: null)
* lat: Latitude [float] (default: 32)
* lng: Longitude [float] (default: 34)
* filters: List of filters [agency, sell, rent, house, apartment, commercial, photo, short_term, handicap, bathtub, elevator, parking, washer, dryer, smoking, tax, garage, doorman, buzzer, yard, detached, duplex, triplex, historic, rooftop, pool, gym, pets, roommates, investment, full_of_light, security_bars, ocean_view, panoramic_view, high_ceilings, close_to_transit, close_to_school, aircon, wifi, heat, fireplace, studio, land, penthouse, industrial, shelter, basement, loft, office, auction, preconstruction, foreclosure, special, luxury, agricultural, building_permit, commission, smart_home, storage] [string of key=[1|0] pairs separated by comma] (default: null)
* params: List of parameters [price, size, size_out_of, rooms, bedrooms, livingrooms, floor, floor_out_of, units, bathrooms, balconies, kitchens, condition, furnished, quiet, building_year] [string of *key=min_value~max_value* threesomes separated by comma] (default: null)


** Examples: **

* [listings limit=11 lang='auto']
* [listings lat=37.23444 lng=45.53434 zoom=15]
* [listings lat=37.23444 lng=45.53434 zoom=8  filters='sell=1,photo=1,short_term=1,land=0' params='price=100~2000,size=50~,floor=~4']
* [listings lang='ru' uid='myUserId' zoom=7]

== Screenshots ==

1. Custom listings using a simple shortcode.
2. The final result is a scrollable bar with all the listings.

== Changelog ==

= 1.0 =
* Release :)

== Upgrade Notice ==

No upgrade available yet.

== Frequently Asked Questions ==

No question available yet.
