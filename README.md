VYchan - Cryptocurrency and Coinhive Pass system for vichan.
========================================================

About
------------
VYchan is a free fork addon to the vichan image board software. It is based on the vichan source code and adds the ability for site admins to sell passes using cryptocurrency and/or use Coinhive site monetization to give passes.

Development was commisioned by VidYen, LLC to be used freely to promote the use of cryptocurrenices and Coinhive for site monetization.

As VidYen is busy with other project, this is a proof of concept modification and will receive little updates going further and there is a general idea to create a WordPress version from scratch in the future.

History
------------
See below in vichan history. VYchan is a fork in 2018 of existing vichan and the defunct Tinyboard with the additions of pass system.

Installation
-------------
Installation is a bit tricky and not like vichan as we had to do a few things to get it to work. Hopefully someone will make this better.

Currently, install all the files in the public_html folder you wan to use.
Manually install the "install.sql" file and the run the mod.php file with the user name: admin and pass: password (yes you should change this right away) and then rebuild the boards.

When in doubt rebuild the boards.

Also you will need to delete the boards that do not actually exist.

Also the crypto system is under the "pass" folder in the "templates" folder. Just install, add they required keys, install, and rebuild.
You will need a Coinpayments.net and Coinhive.com account to get this to work properly.

Eventually we will write a better instructions but for now... This is what you get.

Support
--------
Very little support will be given as VidYen, LLC is focused on WordPress development projects, but if you join our Discord we generally would have interest in seeing this actually used so would help out if possible.

Our discord is currently: https://discord.gg/6svN5sS

Eventually https://www.vidyen.com will be a permanent link.

Also we would not be opposed to having the pass, ch, and ad rotation added back into vichan proper if any of them notice and want to add that back in as it would help our goal of promoting the use of Coinhive for legitimate purposes (As long as it always used with explanation and consent)

Yes, we should have did a proper git fork but we developed this out of github and putting it back in and would take a bit of time and just wanted this out there.



vichan - A lightweight and full featured PHP imageboard.
========================================================

About
------------
vichan is a free light-weight, fast, highly configurable and user-friendly
imageboard software package. It is written in PHP and has few dependencies.

In November 2017, Marcin Łabanowski (@czaks) retired as maintainer for personal reasons. His retirement may be temporary, but in his absence, Fredrick Brennan (@ctrlcctrlv), co-maintainer since 2013, and #3 in terms of number of commits, took his place as interim sole maintianer and point of contact. (See [issue #266](https://github.com/vichan-devel/vichan/issues/266))

*Security problems can be reported to Fredrick Brennan at his email: COPYPASTE \<AT\> KITTENS \<DOT\> PH.*
	
Vichan is still accepting patches, but there is at the moment no active development besides fixing security problems as they emerge. Given the lack of active development, we strongly urge you to consider other imageboard packages. It is the opinion of the vichan development team that no new vichan imageboards should be deployed, and other imageboard packages, such as lynxchan, used instead.

History
------------
vichan is a fork of (now defunc'd) [Tinyboard](http://github.com/savetheinternet/Tinyboard),
a great imageboard package, actively building on it and adding a lot of features and other
improvements.

Support and announcements: https://engine.vichan.net/

Requirements
------------
1.	PHP >= 5.4 (we still try to keep compatibility with php 5.3 as much as possible)
        PHP 7.0 is explicitly supported.
2.	MySQL/MariaDB server
3.	[mbstring](http://www.php.net/manual/en/mbstring.installation.php) 
4.	[PHP GD](http://www.php.net/manual/en/intro.image.php)
5.	[PHP PDO](http://www.php.net/manual/en/intro.pdo.php)

We try to make sure vichan is compatible with all major web servers and
operating systems. vichan does not include an Apache ```.htaccess``` file nor does
it need one.

### Recommended
1.	MySQL/MariaDB server >= 5.5.3
2.	ImageMagick (command-line ImageMagick or GraphicsMagick preferred).
3.	[APC (Alternative PHP Cache)](http://php.net/manual/en/book.apc.php),
	[XCache](http://xcache.lighttpd.net/) or
	[Memcached](http://www.php.net/manual/en/intro.memcached.php)

Contributing
------------
You can contribute to vichan by:
*	Developing patches/improvements/translations and using GitHub to submit pull requests
*	Providing feedback and suggestions
*	Writing/editing documentation

If you need help developing a patch, please join our IRC channel.

Installation
-------------
1.	Download and extract vichan to your web directory or get the latest
	development version with:

        git clone git://github.com/vichan-devel/vichan.git
	
2.	Navigate to ```install.php``` in your web browser and follow the
	prompts.
3.	vichan should now be installed. Log in to ```mod.php``` with the
	default username and password combination: **admin / password**.

Please remember to change the administrator account password.

See also: [Configuration Basics](https://web.archive.org/web/20121003095922/http://tinyboard.org/docs/?p=Config).

Upgrade
-------
To upgrade from any version of Tinyboard or vichan:

Either run ```git pull``` to update your files, if you used git, or
backup your ```inc/instance-config.php```, replace all your files in place
(don't remove boards etc.), then put ```inc/instance-config.php``` back and
finally run ```install.php```.

To migrate from a Kusaba X board, use http://github.com/vichan-devel/Tinyboard-Migration

Support
--------
vichan is still beta software -- there are bound to be bugs. If you find a
bug, please report it.

If you need assistance with installing, configuring, or using vichan, you may
find support from a variety of sources:

*	If you're unsure about how to enable or configure certain features, make
	sure you have read the comments in ```inc/config.php```.
*	Check out an [official vichan board](http://int.vichan.net/devel/).
*	You can join vichan's IRC channel for support
	[irc.6irc.net #vichan-devel](irc://irc.6irc.net/vichan-devel)

### Tinyboard support
vichan is based on a Tinyboard, so both engines have very much in common. These
links may be helpful for you as well: 

*	Tinyboard documentation can be found [here](https://web.archive.org/web/20121016074303/http://tinyboard.org/docs/?p=Main_Page).

Donations
---------
Do you like our work? You can motivate us financially to do better ;)
* Bitcoin: 1GjZEdLaTQ8JWVFGZW921Yv4x59f9oiZME

You can also ask us to develop some feature specially for you <3. Join our IRC
channel and ask for a quote (there are a few of us, who work with the codebase
and are skilled enough to develop such features pretty quickly).

CLI tools
-----------------
There are a few command line interface tools, based on Tinyboard-Tools. These need
to be launched from a Unix shell account (SSH, or something). They are located in a ```tools/```
directory.

You actually don't need these tools for your imageboard functioning, they are aimed
at the power users. You won't be able to run these from shared hosting accounts
(i.e. all free web servers).

Localisation
------------
Wanting to have vichan in your language? You can contribute your translations at this URL:

https://www.transifex.com/projects/p/tinyboard-vichan-devel/

Oekaki
------
vichan makes use of [wPaint](https://github.com/websanova/wPaint) for oekaki. After you pull the repository, however, you will need to download wPaint separately using git's `submodule` feature. Use the following commands:

```
git submodule init
git submodule update
```

To enable oekaki, add all the scripts listed in `js/wpaint.js` to your `instance-config.php`.

WebM support
------------
Read `inc/lib/webm/README.md` for information about enabling webm.

vichan API
----------
vichan provides by default a 4chan-compatible JSON API. For documentation on this, see:
https://github.com/vichan-devel/vichan-API/ .

License
--------
See [LICENSE.md](http://github.com/vichan-devel/vichan/blob/master/LICENSE.md).

