=== osCommerce ===
Contributors: TalOrlik
Donate Link: http://www.none.com
Tags: plugin, widget, admin, sidebar, osCommerce, shopping cart
Requires at least: 2.5 or higher
Tested up to: 2.6.3
Version: 1.0
Stable tag: 1.0

This plugin pulls the category and product listing from your osCommerce site.

== Description ==

This pulgin/widget enable you to pull the category and product listing from you osCommerce site.
Once activated (follow instructions on installation tab) you get a new menu item in your administration section called 'osCommerce'. You'll
then be able to add multiple 'shops', edit and delete them. After you add at least one shop the front end widget uses that information to display
the categories from your osCommerce shop. Once you click the shop name that you've provided it'll display the categories available.
Once any category is clicked on it'll display the products in that category in the product page that you've created (see installation instructions).
On the product page when you click the 'buy now' button itll open your osCommerce shop in the checkout_shipping.php page with
the product already added in your shpping cart. If you leave that window open and continue to click 'buy now' on other products on your
Wordpress site you'll be able to see them adding on your cart in the osCommerce window.
The plugin uses hard coded 'language_id', 'country_id', 'zone_id', 'country_code', (English = 1, South Africa = 193, Zone = (none), Code = 'ZAR')
for the product prices hence you'll need to do a search and replace on those values and put the ones you wish to use. Those values are taken directly
from your osCommerce installation from the configuration table in the DB.
The sql statements are taken directly from oscommerce to produce the same results with the exception of minor ammendments.
I didn't take this plugin/widget further for the mere fact that a new OO version of osCommerce is imminent and that for sure will
bring through much better ways to communicate with OSC and pull information from it.

== Installation ==

1. Upload the osCommerce directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once activated use the osCommerce admin section to add your shops' details as per the fields available (They are self explanetory).
4. In 'Design' -> 'Widgets' add the widget to your sidebar (Only after step 3 will you see anything displaying in the widget).
   The recommended placement is just under your wordpress category listing.
5. Create a page call it what you wish e.g. 'Products' and add the following to its content section: [[oscProductListing]]
6. Publish the page and now you're ready to display product listings.

And you're done!!!

		When updating you will need to deactivate and reactivate the plugin.

== Screenshots ==

1. osCommerce Admin -> Listing
2. osCommerce Admin -> Add Page
3. osCommerce Admin -> Edit Page
4. osCommerce Widget Options
5. osCommerce Page Management
6. osCommerce Front-> Widget (Shop/Category listing) + Product Page


== Directory Structure and File Listing ==

osCommerce (root)

	classes
		osc_categories.class.php (for category listing)
		osc_currencies.class.php (for product price calculation like in osc)
		osc_db.class.php	 (for db connection as well as extended table and field information)
		osc_management.class.php (Listing, Add, Edit, Delete of 'shops')
		osc_products.class.php   (for product listing)
		osc_widget.class.php     (widget management)
	css
		osc_front.css
		osc_management.css
	images
		button_buy_now.gif
		cross.png
		no_image.gif
	js
		java_script.js

	osCommerce.php   (main file)
	readme.txt
	screenshot-1.jpg
	screenshot-2.jpg
	screenshot-3.jpg
	screenshot-4.jpg
	screenshot-5.jpg
	screenshot-6.jpg

== PROBLEMS ==
I was unable to implement pagination on the product page in the front end. I got it to displays
the correct paging links however doesn't execute the paging once clicked on.
im not sure if im just missing something small like a hook or something. The code is in
(commented out) for all to see maybe someone can help fix it up and let me know.

== Frequently Asked Questions ==

1. How do i change the currency specification for my country?

You chenge it by searching for the following values and replacing them with the your ones
taken from your osCommerce configuration:

'language_id', 'country_id', 'zone_id', 'country_code', (English = 1, South Africa = 193, Zone = (none), Code = 'ZAR')

== Special Thanks ==

I'd like to thank 'Luke Howell' who wrote the 'Event Calendar' plugin/widget
URL: http://www.lukehowell.com/events-calendar/ OR http://wordpress.org/extend/plugins/events-calendar/
who unknowingly helped me in putting together this plugin/widget. I followed his structure and code and based mine on it.
