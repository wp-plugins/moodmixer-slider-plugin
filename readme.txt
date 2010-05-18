===dynamic-slider-plugin ===


Contributors: fwolf
Donate link: http://fwolf.info/
Tags: slider, javascript, ad rotator, earn money, flash, affiliate, widget, banner, plugin, sidebar, posts
Requires at least: 2.8
Tested up to: 2.9.2 
Stable tag: trunk

Add Moodmixer affiliate product sliders(javascript/flash banners) to your weblog article using shortcodes, widgets or a template function.



== Description ==

Add Moodmixer Slider affiliate product presentations (javascript/flash affiliate banners) to your weblog using shortcodes, widgets and even a template function. With Moodmixer product sliders you can earn money with your blog by advertising products directly that match your web site's context (ie. pet supplies on a pet site, wine in a wine blog, health products or fashion etc. Dynamic sliders produce a nonstop flow of products matching a keyword that you can set. 
See examples at http://www.dynamic-slider.com

By the way, this plugin can be used to add any kind of javascript/flash banner into your blog

 

You have to become an affiliate of http://www.zanox.com to earn money

Currently  available in German/English - all in one.


== Installation ==



If you are already registered at zanox, please login with your credentials at

http://apps.zanox.com/ (top right)

select slider:

3 products: Format: 160x600: http://apps.zanox.com/web/guest/home?productID=12691 

or

1 product at a time Format: 160x125 or 120x240 http://apps.zanox.com/web/guest/home?productID=29774 

or

pageflipping catalog 300x250 or 400x260 looks like a miniature catalogue which flips pages automatically or if you click into the catalogue corners. available at: https://apps.zanox.com/web/guest/home?productID=61265 

more formats coming soon ( i.e.  and leaderboard 728x90 )

press the "get it" button(doesn't cost you anything) and go through the configurator page to produce the code. Copy code to clipboard or into a text file for the meantime




This section describes how to install the plugin and get it working.

= into article =

1. Upload the 'moodmixer-slider' directory to your plugins directory. make sure it is called 'moodmixer-slider'
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add a new slider
4. Insert it into your post or page using the following syntax: `[slider code="my-slider-code"]`


= Widgets =



1. In the WP Admin section, go to Design > Widgets
2. Drag the Moodmixer Slider widget to your desired content container / sidebar
3. Select a slider from the pull-down menu.
4. Save it.
5. Done ;)



= Theme implementation =



1. Open the specific template where you'd like to add a slider
2. Add the following code: `<?php moodmixerSlider('my-slider-code'); ?>`
3. Replace 'my-slider-code' with the code of the specific slider you want to get displayed.
4. Save it - and you're done ;)


== Frequently Asked Questions ==

= 

Is this plugin available in my language? 

=

Currently only German/ Englisch language is supported



= Any questions? =

[Just ask us](EN: http://www.dynamic-slider.com/en/contact_us.php  or Deutsch: http://www.dynamic-slider.com/de/kontaktformular.php ) ;)

== Plugin page ==

DE : http://www.dynamic-slider.com/de/Slider_in_Wordpress.php
EN: http://www.dynamic-slider.com/en/Add_Slider_to_Wordpress.php

== Screenshots ==



1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). 

Note that the screenshot is taken from 
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot



== Changelog ==

= 

0.5.2 =
* Fix: Version is not displayed correctly (ie. 0.4 instead of 0.5).
* Fix: If the plugin directory is placed outside the plugins directory and linked back into it using a symbolic link, it works properly. But not, if directly - ie. physically - placed inside the plugins directory.

== Upgrade Notice ==
= 0.5 =
* First public release 
cc 

