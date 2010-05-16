=== Plugin Name ===
Contributors: Hetjens
Tags: security, login, user, restrict, access, feed, rss, atom
Requires at least: 2.8.0
Tested up to: 2.9.2
Stable tag: 0.4


This plug-in restricts the access to blog and feed. Visitors need to login before accessing the blog. It offers a private feed for every user.

== Description ==

This plug-in restricts access to blog and feed. All anonymous visitors will be forwarded to the login page of Wordpress
before accessing the blog content. If access is restricted, rss and atom feeds will be disabled, too.

The feed can be activated as a private feed for each user. Every user will have a unique feed url to access it. That
url is based on the username and an md5 hash of the hashed password stored in database. This plug-in will modify all
by wordpress inserted feed urls to the user specific ones. But be carefull. If you content is confidential you should
not activate the feed. There is no way to check which services are reading (and maybe publishing) the feed’s content.

== Installation ==

1. Upload the directory hetjens-registered-only to your '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Settings' -> 'Reading', there you find the settings to restrict the access to blog and/or feed.

== Changelog ==

= 0.4 =
* First directory version
