=== Car Rental & Reservation platform by Workadu ===
Contributors: xitegr
Tags: car rental, booking engine, rent a car,  yacht bookings, doctors booking system, hotels bookings system, hairstylist, transfers, appointments, lawyer, reservation system, cleaning services
Donate link: https://workadu.com
Requires at least: 4.0
Tested up to: 5.0
Stable tag: 2.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make your car rental bookings, reservations, appointments, tickets, easy, with fully pricing customization and payment gateway, with Workadu (former Reservation Engine) Booking engine plugin. 

== Description ==
Workadu  Plugin integrates your wordpress website with workadu platform. 
You can get your credentials here: [workadu.com/pricing](https://workadu.com/pricing)

> IMPORTANT: Workadu plugin for Wordpress requires login credentials to workadu.com platform. You can get them free for 30 days by [sign up here](hhttps://workadu.com/pricing)

Workadu plugin requires PHP 5.6 and over to work properly. You should login at workadu's platform [login here](https://app.rese.io) with your company admin credentials and add a new user with "API" role. 

[SIGN UP FOR FREE(30 days) HERE](https://workadu.com/pricing)

[LIVE FRONTEND DEMO](http://demo.reservationengine.net)

= Features: =
1. Add as many pickup and return places you want. All places are base in geographic coordinates (configurable at wordpress backend)
2. Configure plugin to paypal live or sandbox mode (configurable at wordpress backend)
3. Add Terms & Conditions (configurable at wordpress backend)
4. Add services with attributes and extras (configurable at Workadu's Backend)
5. Add rates & availability for each day/hour/mile (configurable at Workadu's Backend)
6. Add dynamic policies (offers) for vehicle (configurable at Workadu's Backend)
7. Manage your bookings and payments (managed at Workadu's Backend)
8. Issue Rentals Agreements and invoices (managed at Workadu's Backend)
9. Proceed to checkout and checkin process (managed at Workadu's Backend)
10. View (with the use of gps device) the current location of each service (managed at Workadu's Backend)
11. Choose from 4 types of search form (Appointments, rentals, hotels, transfers)



= Backend Features =
-	General configuration (Paypal, Form Settings)
-	Places configuration
- 	Pages setup

= Frontend Features =
*	Search form shortcode
*	Results shortcode 
*	Vehicle and Driver revision shortcode
*	Payment revision shortcode 
*	Payment succes or failure

= Suitable for =
[Car rental & fleet management](https://workadu.com/car-rental-software)
[Medical appointments software](https://workadu.com/medical-appointments)
[Legal Services & Consultancy](https://workadu.com/legal-services-and-consultancy)
[Restaurant & bar reservations](https://workadu.com/restaurant-and-bar-reservations)
[Beauty and spa appointments](https://workadu.com/beauty-and-spa-appointments)
[Educational and fitness classes](https://workadu.com/educational-and-fitness-classes)
[Maintenance and cleaning services](https://workadu.com/maintenance-and-cleaning-services)
[Hotel booking system](https://workadu.com/hospitality-management-software)



You can contact us at hello@workadu.com.

== Installation ==
Before activating the plugin be sure you are working with php5.6 and up, and your theme is using bootstrap 4+ .

1.	Install Workadu (former Reservation Engine) Plugin
2.	Activate it 
3.	Go to WP Workadu menu and then go to configuration panel. Add there your Workadu Credentials provided to you. It is preferred to add a new user account for the plugin.
4.	Don't forget to change the paypal mode to Sandbox at your testing period.
5.	Set up at least one place. Don\'t forget to add the real geographic coordinates for this place.

= Shortcodes: =
*	'[WorkaduForm group="{reservationengine groupid}" type="{activity}"]' : The search form (includes pickup-return place, datetime from, datetime to)
*	'[Workadu-search-results]': Search Results shortcode. You can add it to an any page and display all the results of your form query.
*	'[Workadu-car-driver-details]'' : 2nd Step of the booking process. With this shortcode you can get a revision of your selection and add extra services to your booking
*	'[Workadu-order-revision]': 3rd step of the booking process. Add it o a custom page where the system returns the PAYPAL transaction ID.
*	'[Workadu-payment]': Final step that indicates the success or failure of the payment process.

Don't forget to at the search results widget to the results page!
 


== Frequently Asked Questions ==
= Is any extra fees for using the plugin =
No there are no extra fees for using wordpress plugin. You should only have your [workadu.com](https://workadu.com) credentials. 

= Where i can get my credentials? =
You should have subscribe to workadu.com website. Login at [workadu.com](https://workadu.com) platform and add a user with role Api. Use the credentials of this user at the get started panel on wordpress. 

= I am getting an error when activate the plugin? =
Workadu plugin is available only for PHP 5.6 and over. If you get an installation issue, please check and change you php version at your hosting panel. 



== Screenshots ==
1. The screenshot describes the booking form that will be displayed at your website.
2. The screenshot describes the search results shorcode screen.
3. The screenshot describes the booking revices shortcode screen.
4. The screenshot describes the back-end panel places screen.
5. The screenshot describes the back-end panel configuration screen.

== Changelog ==
= 2.3.1 =
* Bug Fixes

= 2.3.0 =
* Optimisations 
* Check out form redesign 

= 2.2.9 =
* Bug Fixes
* Add time field separately from date range 

= 2.2.8 =
* Fix email confirmation when using deposit 

= 2.2.7 =
* Remove trunk entry

= 2.2.5 =
* New Backend View
* Add api key functionality


= 2.2.4 =
* Documentation changes
* Fix plugin conflicts
* Fix Gutenburg compatibility

= 2.2.3 =
* Documentation changes
* stripe integration bug fixed

= 2.2.2 =
* Documentation changes
* jQuery conflicts fix

= 2.2.1 =
* Documentation changes
* Vendor Upgrade

= 2.2.0 =
* Documentation changes
* PHP 7.2 compatible

= 2.1.9 =
* Documentation changes
* Bug fixes

= 2.1.8 =
* Documentation changes
* Add category selection field in search form
* Bug fixes

= 2.1.7 =
* Documentation changes
* Bug fixes for predefined location

= 2.1.6 =
* Documentation changes
* Bug fixes

= 2.1.5 =
* Documentation changes
* Add google maps api key

= 2.1.4 =
* Documentation changes
* Add demo accounts per business type

= 2.1.3 =
* Documentation changes
* Bug fixes

= 2.1.2 =
* Documentation changes
* Bug fixes

= 2.1.0 =
* Documentation changes
* Add predefined demo user

= 2.0.9 =
* Documentation changes
* Bug fixes

= 2.0.8 =
* Documentation changes
* namespacing 

= 2.0.7 =
* Documentation changes
* Bug fixes

= 2.0.6 =
* Documentation changes
* Bootstrap 4.0 support
* Bug fixes

= 2.0.5 =
* Documentation changes
* adding new version screens 

= 2.0.4 =
* Documentation changes
* New asset images and logo 

= 2.0.3 =
* Documentation changes
* Bug Fixes

= 2.0.2 =
* Documentation changes
* Bug Fixes

= 2.0.1 =
* Documentation changes
* Bug Fixes
* Support per mile charge
* Support per interval charge 
* Styling refinements
* Suitable for all reservation types (bookings, reservations, appointments, ticketing)

= 1.6.9 =
* Documentation changes
* Bug Fixes

= 1.6.8 =
* Bug Fixes

= 1.6.7 =
* new api endpoint version

= 1.6.6 =
* Bug Fixes

= 1.6.5 =
* Hot Fix

= 1.6.4 =
* Bug Fixes

= 1.6.3 =
* Add Recapcha supprt
* Bug Fixes

= 1.6.2 =
* Timezone support 
* Bug Fixes

= 1.6.1 =
* Hot fix

= 1.6.0 =
* Add calendar support on results
* Add more configuration settings for results pages
* Add multilanguge support with Polylang to all pages
* Bug Fixes

= 1.5.9 =
* Styling Fixed
* Extras default selection
* Add more translation texts
* Bug Fixes

= 1.5.8 =
* Add Viva Payment Gateway integration
* Bug Fixes

== Changelog ==
= 1.5.7 =
* Add bootstrap 4 compatibility
* Bug Fixes

= 1.5.6 =
* Add Cash on Delivery feature
* Bug Fixes

= 1.5.5 =
* Styling fixes

= 1.5.4 =
* Language files update 
* Bug fixes 

= 1.5.3 =
* Bug fixes 

= 1.5.2 =
* PayZen.eu Payment Gateway Integration
* Add coupons code at booking process
* Add page selection for the shortcode pages
* Bug fixes 

= 1.5.1 =
* Bug fixes 

= 1.5.0 =
* Stripe Payment Gateway Integration

= 1.4.6 =
* Add the ability to show more than 2 offers 

= 1.4.5 =
* Bug fixes 

= 1.4.4 =
* Speed Optimization
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