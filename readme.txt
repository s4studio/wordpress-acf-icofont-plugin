=== Advanced Custom Fields: IcoFont Field ===
Contributors: ekawalec
Tags: Advanced Custom Fields, ACF, IcoFont
Requires at least: 3.5
Tested up to: 4.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a new 'IcoFont Icon' field to the popular Advanced Custom Fields plugin.

== Description ==

Add a [IcoFont](http://icofont.com/) icon field type to Advanced Custom Fields.

* Optionally set a default icon
* Returns Icon Element, Icon Class, Icon Unicode, or an Object including the element, class, and unicode value
* Optionally enqueues IconFont in footer where needed (when a IconFont field is being used on the page)
* Includes filters to override the which version of IconFont is loaded (See Optional Configuration)

Note: It is recommended to let this plugin enqueue the latest version of IconFont on your front-end; or include the latest version by some other means; so that available icons in the admin area will be displayed properly on your sites front-end.

= Compatibility =

This ACF field type is compatible with:
* ACF 5
* ACF 4

== Installation ==

1. Copy the `advanced-custom-fields-icofont` folder into your `wp-content/plugins` folder
2. Activate the IconFont plugin via the plugins admin page
3. Create a new field via ACF and select the IconFont type

== Optional Configuration ==

=== Filters ===

* **ACFICFNT_always_enqueue_icfnt**: Return true to always enqueue IconFont on the frontend, even if no ACF IconFont fields are in use on the page. This will enqueue IconFont in the header instead of the footer.
* **ACFICFNT_admin_enqueue_icfnt**: Return false to stop enqueueing IconFont in the admin area. Useful if you already have IconFont enqueued by some other means.
* **ACFICFNT_load_chosen**: Return false to stop loading the [Chosen JS](https://harvesthq.github.io/chosen/) library in the admin area. Used in v4 of ACF only.
* **ACFICFNT_get_icons**: Filter the array of icons and icon details loaded from the database
* **ACFICFNT_get_icfnt_url**: Filter the URL used for enqueuing IconFont in the frontend and admin areas of the site.

== Screenshots ==

1. Set a default icon, and choose how you want icon data to be returned.
2. Searchable list of all icons, including large live preview

== Changelog ==

= 1.0.0 =
* Initial Release.
