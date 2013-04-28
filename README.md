better-password
===============

This plugin is intended to provide Wordpress with the new password_* functions.

It will automatically update alle old passwords when the users login.
It will also use password_rehash and update the password when the password needs rehashing.

Requirements
============

This plugin requires `PHP >= 5.3.7`

The reason for this is that PHP prior to 5.3.7 contains a security issue with its BCRYPT implementation. Therefore, it's highly recommended that you upgrade to a newer version of PHP prior to using this layer.

Installation
============

To install, simply put the files in the wp-content/plugins folder.

Usage
=====

Enable the plugin and... no that's it.

Warning
=====

After enabling the plugin it will replace users passwords. If you delete or disable the plugin these users will not be able to login anymore. You'll need to reset their passwords.
