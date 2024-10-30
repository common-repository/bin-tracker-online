=== Bin Tracker Online === 

Requires at least: 4.9 
Requires PHP: 5.6.4 
Tested up to: 6.5.3 
Stable tag: 1.0.1 
License: GPLv2 or later 
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

The plug in provides integration with a private web application for Bin their Dump That, a franchisor in the waste hauling industry.

== External services ==

https://maps.googleapis.com/maps/api/js?key=$b1nT_google_api_key&libraries=places: a google api used to validate the address a user inputs in to the plugin. A valid and existing address is very important when providing a service in the waste hauling industry. The terms of use can be found at https://developers.google.com/maps/documentation/places/web-service/overview.

https://www.Bintracker.software/controller.html: allows the plugin to request/send information from/to the franchisee database. It also, does all the security checks as a first line of defence to make sure that the module in quesiton is valid and authorized to an external party. After all security checks are done, the module is called where a second layer of security checks is done; verifying the data received. The terms of use can be found at https://www.bintracker.software/forms/EULA.html.

https://maps.google.com/maps/api/geocode/json: allows the plugin to get the longitude and latitude of the address a user inputs in to the plugin. This corralates with another service by google; https://maps.googleapis.com/maps/api/distancematrix/json: expects two sets of longitude and latitude. One belonging to the address the user inputs and the other to the franchisee providing service. With this information the driving distance to/from the jobsite is calculated allowing the plugin to properly provide the correct prices for the service. Terms of use can be found at https://developers.google.com/maps/documentation/geocoding/overview and https://developers.google.com/maps/documentation/distance-matrix/overview.

== External documentation ==

https://developers.google.com/places/web-service/get-api-key: provides information to the user admin about obtaining a google api key. Without an api key the plugin will not be able to validate address, calculate distance, or get longitude and latitude.

https://www.bintracker.software/api/word-press-plugin.html: provides information about plugin and it's features.

https://www.cloud-computing.rocks: provides information about the plugin author.

https://jqueryui.com/themeroller: part of the jquery-ui.css which we had to include locally since wordpress does not include it with the core jquery.

== Importing Plugin == 

To import “Bin Tracker” plugin into WordPress. User needs to navigate to “Upload Plugin” feature under the Plugins area in the WordPress dashboard. 
 
== Activating Plugin == 

To activate the “Bin Tracker” plugin user need to navigate to “Installed Plugins” section under the Plugins area in the WordPress dashboard.  
Here user will get list of all the installed plugins, from this list user need to find “BinTracker Online” and click on “Activate”. 
 
== Setting up BinTracker Online == 

To update setting of “BinTracker Online” user need to access plugin setting using  “Bin Tracker” feature from the sidebar are of admin panel, and then update all required fields.

Amongts those required fields is the google api key. You will need it or the plugin wont function. To obtain a google api key follow the steps bellow:

1. Go to "https://cloud.google.com/maps-platform/" and click on "Get Started"
2. A pop up window will open, asking you to select a product(s); select maps and places.
3. Login to or create a google account, then enter a project name and create a billing account.
4. After you billing account is created, you will be prompt with another option, allowing you to
   enable google maps platform. Click next, and google will respond with an api key. This method will
   activate all google maps platform API(s). However, you can go back and remove the ones you wont need
   for this plugin. The API(s) you will need are the followings: Directions API, Distance Matrix API, 
   Geocoding API, Maps Elevation API, Maps JavaScript API and Places API.

You will find an option called API Mode. in TEST mode, the system will return a URL with which you can validate
the variables sent to Bin Tracker. Mode TEST posts will not render for the hauler. 
 
== Using Plugin in front end == 
 
To use the plugin at front of the WordPress site user needs to create a new page and use the shortcode [bin-tracker-online] in the page or the user can add the shortcode [b1nT_bin-tracker-online] in any existing page where user wants to use the plugin. 
