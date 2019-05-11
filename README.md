# Pretty Plugins

**INACTIVE NOTICE: This plugin is unsupported by WPMUDEV, we've published it here for those technical types who might want to fork and maintain it for their needs.**

## Translations

Translation files can be found at https://github.com/wpmudev/translations

## Pretty Plugins lets you group plugins into categories, give them eye-catching feature images and display them in an easy-to-use grid.

Make finding and installing plugins a breeze. Pretty Plugins is an essential tool for any network owner. It offers centralized control and configuration of every site's Plugins page. Combine with [Pro Sites](http://premium.wpmudev.org/project/pro-sites/ "Read more about the Pro Sites plugin ") to create an awesome paid plugin store.   

Large icons and a familiar grid layout provide for a far more visually appealing layout compared to the out-of-the-box basic listing. 

![screengrab of the plugins page with the grid layout](http://premium.wpmudev.org/wp-content/uploads/2013/11/prettyplugins-ss12.jpg)

 Large icons and a grid layout makes plugin discovery a breeze

 Plugins can be grouped into multiple categories, defined by the network admin, making it easier for site owners to discover and install new features. 

![Screengrab of plugin categories](http://premium.wpmudev.org/wp-content/uploads/2013/11/prettyplugins-ss2.jpg)

 Group plugins into categories for easier discovery

 A comprehensive settings page allows a network owner to manage all aspects of the Plugins page including title, sub-title, images and the visibility of descriptions. The settings can even be exported and imported! 

![Screengrab of the settings page](http://premium.wpmudev.org/wp-content/uploads/2013/11/prettyplugins-ss11.jpg)

 Configure all aspects of the Plugins page behavior and look

 Finer control is provided for each plugin. As a network admin, you can override the plugin name, link and description – you can assign the plugin to any number of existing categories or create new ones – and you can load a featured image. 

![Screengrab of the plugin edit details function](http://premium.wpmudev.org/wp-content/uploads/2013/11/prettyplugins-ss41.jpg)

 Override any plugin attribute

 Pretty Plugins gives you unparalleled control over the look and behavior of the Plugins page, making it easier than ever for site owners to self-manage their plugins and find and add new features to their site.

## Usage

### To Get Started

Start by reading [Installing plugins](../wpmu-manual/installing-regular-plugins-on-wpmu/) section in our comprehensive [WordPress and WordPress Multisite Manual](https://premium.wpmudev.org/manuals/) if you are new to WordPress. Note that this plugin is designed for multisite installations only, and will not work on single sites.

### Configuring the Settings

Once installed and activated, you'll see a new menu item in your Network Settings menu: Pretty Plugins. Click the Pretty Plugins menu item now. 

![pretty-plugins-1000-menu](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-menu.png)

 The settings are quite straightforward. But let's go over them anyway, shall we? 

![1\. Enable/disable Setup mode. 2\. Select your preferred theme. 3\. Select plugin link destination. 4\. Select your screenshot preferences. 5\. Show/hide plugin descriptions. 6\. Customize the labels.](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-settings.png)

 1\. Enable/disable Setup mode.  
2\. Select your preferred theme.  
3\. Select plugin link destination.  
4\. Select your screenshot preferences.  
5\. Show/hide plugin descriptions.  
6\. Customize the labels.

 1\. You'll likely want to leave _Enable Setup Mode_ set to True while you are getting things configured.

*   With Setup Mode enabled, you can preview things as you go on the main site only. The sub-sites will not be affected.
*   Once you're done and are happy with your configuration, set it to False to activate your new Pretty Plugins page on all sub-sites in your network.

2\. _Select Theme For Plugin Page_ is exactly that: you can select the theme that will be used to display available plugins on all sub-sites.

*   Currently, this plugin comes with one theme included: QuickSand.
*   To make your own custom theme, simply copy the QuickSand theme in /pretty-plugins/themes/ and rename it. Then you can edit the layout and style-sheet, and upload your custom theme to your /wp-content/uploads/prettyplugins/themes/ folder. It will then be available for selection.

3\. To _Select Where Plugin Link Points To_, you have 4 options:

*   _Plugin original URL_ will point to the URL the plugin author has included in the plugin.
*   _Plugin custom URL_ will point to the URLs you set for each plugin (we'll get to that below).
*   _Plugin custom URL or if custom does not exist, original URL_ gives you the best of both: if you do not enter a custom URL for a plugin the link will default to the one the plugin author has included.
*   _Disable_ will effectively disable all plugin links.

4\. This is where we select which screenshots to use and display for plugins.

*   If set to True, _Auto Load First Screenshot_ will use the first available screenshot in the plugin folder. This tells Pretty Plugins to look for a file called _screenshot-1.png_. If that file does not exist in a plugin folder, nothing will display for that plugin.
*   If set to True, _Auto Load Screenshot With Correct Name_ will tell Pretty Plugins to look in your /wp-content/upload/prettyplugins/screenshots/ folder for files that _you_ have uploaded there with the correct names.
    *   For example, if the plugin is located at _wp-content/plugins/akismet/akismet.php_, the screenshot name should be _akismet-akismet.png_ Note that only png images will work with this method.

5\. You can set _Minimize Descriptions_ to True to display only screenshots on sub-site plugins pages. This will enable the display of a link your users can click to toggle the description if they want more information about the plugin. 6\. The _Manage Labels For Plugin Page_ settings allow you customize how plugins are presented to your sub-site users.

*   _Plugin Page Title_ changes the name of the menu item and the top-of-page title on the plugins page. For example, you may prefer the word "Addons".
*   _Plugin Page Description_ is the custom descriptive text that appears just beneath the plugins page title.
*   _Custom Link Label_ is what users click on to visit the plugin URL that you set at _Select where plugin link points to_ above. If you had set that to "Disable", this will be hidden.

### Additional Tools

Some additional tools are provided so you can manage plugin data and settings. 

![1\. Export/Import config.xml file. 2\. Delete all data.](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-tools.png)

 1\. Export/Import config.xml file.  
2\. Delete all data.

 1\. You can _Export_ a config.xml file to backup your settings to your computer, then _Import_ it to your /wp-content/uploads/prettyplugins/ folder. 
 
 2\. The _Reset_ feature is handy if you want to start over. :)

### Editing Plugin Details

Now let's take a look at customizing how individual plugins appear in the plugins list on all sites. In your network admin, go to Plugins > Installed Plugins, and click the Edit Details link of any plugin you'd like to edit. 

![pretty-plugins-1000-edit](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-edit.png)

 That will open a familiar section much like the Quick Edit area when editing posts. Let's take a closer look at the first part now. 

![pretty-plugins-1000-edit-details](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-edit-details.png)

 Enter a custom _Name_ to replace the display name of the plugin. If nothing is entered, the actual name of the plugin will display just as it always has. You can enter a _Custom URL_ to replace the one the plugin author has included. Note that this URL will only be used if you had selected either of the Custom URL display options at _Select where plugin link points to_ above. You can also upload or link to a custom _Image URL_. This sets the image that will display for this plugin.

*   For uploads, you can choose an image from your Media Library or type the name of file that you have uploaded to /wp-content/uploads/prettyplugins/screenshots/.
*   If you prefer to link to an image hosted elsewhere, simply enter the URL.
*   Recommended image dimensions are 600px by 450px.

Now let's look at the other part. 

![pretty-plugins-1000-edit-details-2](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-edit-details-2.png)

 To add _Categories_, click the _New Category_ link, enter your category name and click _Add_. You can then select your category from the list.

*   You can select multiple categories for each plugin if you wish.
*   Any categories not used by any plugins will be automatically deleted.

You can enter a custom _Description_ that will replace the description included by the plugin author. Leave blank to use that description instead. When you're done editing and have clicked the Update button, you'll see your customizations right in the plugin listing in your network admin. 

![pretty-plugins-1000-edit-details-3](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-edit-details-3.png)

### Site Admin Experience

Once everything is configured, and you set _Enable Setup Mode_ to False in the General Settings, your site admins will see something very much like this image. 

![pretty-plugins-1000-site-view](https://premium.wpmudev.org/wp-content/uploads/2013/11/pretty-plugins-1000-site-view.png)

 Active plugins display with a green ribbon across the image. In our example, we've set the descriptions to not display, so site admins can view them by clicking the details link. If you prefer to use your own images for the ribbons, simply replace the default ones in /pretty-plugins/themes/quick-sand/images/. Of course, if you have made your own theme, you would replace the ones in that /images/ folder instead. Along the top (and in the Plugins menu) are the filtering options. Those are all the categories we set up for our plugins. Clicking any of those links will display only plugins assigned to that category.

### Pro Sites Integration

Oh yes, we did! Any plugins that you have added to [Pro Sites](https://premium.wpmudev.org/project/pro-sites/ "WordPress Pro Sites Plugin - WPMU DEV") levels in the Premium Plugins module will display on all sites with a "Pro" ribbon in the corner of the plugin image ("!"). Hover your mouse pointer over any plugin you have set for Pro Sites levels in the Premium Plugins module and you will get a message saying "Upgrade to [level]". If the site has not been upgraded to a Pro Site level, and the site admin clicks on any of those plugins, they will be redirected to your Pro Sites upgrade page. How cool is that? You may also have noticed in the above screenshot that we have created Pretty Plugins categories that mirror our Pro Sites levels: Free, Premium & Super. That makes it just too easy for your site admins to see, at a glance, the benefits of upgrading their site to a paid Pro Sites level in your network. You're welcome. :) We hope you and your users enjoy and get the most out of Pretty Plugins!
