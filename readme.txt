=== Plugin Name ===
Contributors: elliotkendall
Tags: admin
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 0.1

On a Wordpress MU install, lets admins of individual blogs grant access to
users external to Wordpress.

== Description ==

If you're using LDAP or some other external authentication source for your
Wordpress MU install, it can be annoying for blog admins to grant access to
other people.  The usual process requires sending an email and (depending on
whether or not the user exists in Wordpress yet) setting a password, but
that doesn't make sense in this context.  Users don't need local passwords,
there's no need to verify their email addresses, and it shouldn't really
matter whether or not a user already exists.

This plugin adds a new entry to the Users section of the dashboard that lets
blog admins simply enter a username, select a role, and grant access.

There are a few options that can be set by a Wordpress MU site admin.

*   "Label for menu item" lets you specify a different label for the item
that appears under Users.  If you use the plugin at Foo University, for
example, you might want to use "Add Foo University User." The customized
text should encourage site admins to use the plugin instead of the built-in
user management functionality.

*   "Description of username" lets you specify a different label for the
label for the username field in the blog admin form.  Again, Foo University
might use something like "Foo LoginID."

*   "Email address domain" is what DNS domain name the plugin will use to
create email addresses for users that it creates in Wordpress.  Foo
University might use "foo.edu" so that someone with username joeschmo would
have a Wordpress account created with "joeschmo@foo.edu" as his email
address.

== Installation ==

1. Upload `add_external_users.php` to the `/wp-content/mu-plugins/` directory
1. Log in as a site admin
1. Go to `Site Admin -> Add External Users`
1. Enter your email domain, and optionally fill in the other two fields.
See Description for details.

== Frequently Asked Questions ==

= Will it work in languages other than English? =

It should. Wherever possible, it uses messages that are used elsewhere in
Wordpress and so should already be translated by a standard language pack. 
Plugin-specific messages are only used in the site admin interface, so it
should be possible to use it with non-English speaking blog admins without
doing any additional translation.

== Screenshots ==

1. The site admin interface
2. Adding a user to a blog. The text is customizeable, so you can
tailor it to your institution.

== Changelog ==

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.1 =
* Initial release
