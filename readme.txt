=== Car Rental Reservation Engine ===
Contributors: xitegr
Tags: car rental, booking engine, rent a car, car hire, booking system
Donate link: https://reservationengine.net
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make your bookings easy, fast and reliable with Reservationengine Booking engine plugin. 

== Description ==
Reservationengine Plugin integrates your wordpress website with ReservationEngine car rental platform. 
You can get your credentials here: https://reservationengine.net

R-Engine requires PHP 5.6 and over to work properly. You should login at Reservation engine platform https://secure.reservationengine.net/cloud with your company admin credentials and add a new user with "API" role. 

LIVE FRONTEND DEMO: http://demo.reservationengine.net 

= Features: =
1. Add as many pickup and return places you want. All places are base in geographic coordinates (configurable at wordpress backend)
2. Configure plugin to paypal live or sandbox mode (configurable at wordpress backend)
3. Add Terms & Conditions (configurable at wordpress backend)
4. Add vechicles with attributes and extras (configurable at Reservationengine Backend)
5. Add rates & availability for each day/hour/mile (configurable at Reservationengine Backend)
6. Add dynamic policies (offers) for vehicle (configurable at Reservationengine Backend)
7. Manage your bookings and payments (managed at Reservationengine Backend)
8. Issue Rentals Agreements and invoices (managed at Reservationengine Backend)
9. Proceed to checkout and checkin process (managed at Reservationengine Backend)
10. View (with the use of gps device) the current location of each vehicle (managed at Reservationengine Backend)
11. *new: New type of booking form for activities 



= Backend Features =
-	General configuration (Paypal, Form Settings)
-	Places configuration

= Frontend Features =
*	Search form shortcode
*	Results shortcode 
*	Vehicle and Driver revision shortcode
*	Payment revision shortcode 
*	Payment succes or failure

https://reservationengine.net "A complete car rental fleet management platform".

You can contact us at info@reservationengine.net.

== Installation ==
1.	Install Reservation Engine Plugin
2.	Activate it 
3.	Go to WP R-Engine menu and then go to configuration panel. Add there your Reservationengine Credentials provided to you. It is preferred to add a new user account for the plugin.
4.	Don't forget to change the paypal mode to Sandbox at your testing period.
5.	Set up at least one place. Don\'t forget to add the real geographic coordinates for this place.

= Shortcodes: =
*	'[WPRengineForm group="{reservationengine groupid}" hide_locations="{true/false}" type="{activity}"]' : The search form (includes pickup-return place, datetime from, datetime to)
*	'[wp-rengine-search-results]': Search Results shortcode. You can add it to an any page and display all the results of your form query.
*	'[wp-rengine-car-driver-details]'' : 2nd Step of the booking process. With this shortcode you can get a revision of your selection and add extra services to your booking
*	'[wp-rengine-order-revision]': 3rd step of the booking process. Add it o a custom page where the system returns the PAYPAL transaction ID.
*	'[wp-rengine-payment]': Final step that indicates the success or failure of the payment process.

Don't forget to at the search results widget to the results page!
 


== Frequently Asked Questions ==
= Is any extra fees for using the plugin =
No there are no extra fees for using wordpress plugin. You should only have a Reservationengine credentials. 

= Where i can get my credentials? =
You should have subscribe to reservationengine.net website. Then you should add a user with role Api. Use the credentials of this user at the configuration panel. 

= I am getting an error when activate the plugin? =
R-Engine is available only for PHP 5.6 and over. If you get an installation issue, please check and change you php version at your hosting panel. 





== Screenshots ==
1. The screenshot describes the booking form that will be displayed at your website.
2. The screenshot describes the search results shorcode screen.
3. The screenshot describes the booking revices shortcode screen.
4. The screenshot describes the back-end panel places screen.
5. The screenshot describes the back-end panel configuration screen.

== Changelog ==
= 1.4.2 =
* Bug fixes 

= 1.4.1 =
* Add multi bookings feature 
* Bug fixes 

= 1.4.0 =
* Add new booking form for activity services 
* Bug fixes 

= 1.3.9 =
* Redirection to Paypal 
* Bug fixes 

= 1.3.8 =
* Mobile prediction results tap fix

= 1.3.7 =
* Bug fixes

= 1.3.6 =
* Language files for Hungarian - Magyar (hu_HU) & Français (fr_FR)

= 1.3.5 =
* Bug fixes

= 1.3.4 =
* Location google autocomplete feature
* Bug fixes
* Get current position feature
* Sortcode params added [group and hide-locations]
* Multiforms support 
* Form shortcode parameter added. You can now add paramenter with the group id you want to filter the results. eg [WPrengineForm group="1111"].
* Many calendar features

= 1.3.3 =
* Security hot fix.

= 1.3.2 =
* Calendar inline or popup mode.
* Bug fixes.

= 1.3.1 =
* Bug fixes.

= 1.3 =
* More features about the calendar in backend
* Bug fixes.

= 1.2 =
* Terms and conditions added to the configuration panel.
* Another change.

= 0.1 =
* Initial release.

== Upgrade Notice ==
= 0.2 =
We add extra fields to customise you the booking button and the terms & Coditions

= 0.1 =
Initial Commit