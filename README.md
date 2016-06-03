## beeo
Yet another collaborative platform for PHP and PostgreSQL

This is a simple web-based collaborative platform in a restrictive input basis.
The content is in a thread-like and the input is defined by someone with rights
for it. You can consider, if you like, that this is another questionnaire-like
platform where the submitions are showed in a tree where the root is something.
By now the root is a patient as this platform was developped to be used with 
patients. I hope I'll have time to make it more generic.

This was developped using PHP 7, PostgreSQL 9.5 and chromium. Sometimes I try it
on Mozilla Firefox and I fix some thing I see. I _officially_ support chromium.

You are welcome to get a copy and use it or modify it to meet your needs. Feel
free to pull request any sugestion you have.

#### Notice
This is in very early progress. You may encounter bugs and malfunctioning.
Please [let me know](https://github.com/Noeljunior/beeo/issues) if you find any.

Get [latest release from GitHub](https://github.com/Noeljunior/beeo/releases/latest).

### Features
 * User's groups
   * per group permissions
 * Organizations associated to users
 * Multiple language support (english and portuguese, by now)
 * Multiple theme support
 * Something else I don't remember by now

### Screenshots
[![screenshot](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc1-thumb.png)](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc1.png)
[![screenshot](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc2-thumb.png)](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc2.png)
[![screenshot](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc3-thumb.png)](https://raw.githubusercontent.com/Noeljunior/beeo/master/screenshots/sc3.png)


### Build and install
After downloading it you should build it to get a working webapp directory.
_make will do the job for you and leave a working webapp under _build_
directory. After that you can install it manually, copying the files, or the
_make install_ or _make update_ can do it for you. The _make install_ will
**delete** installation directory and copy the files. The _make update_ will do
pretty much the samething but it will also backup and restore your _config.php_
file. You can change the intallatoin directory by passing it to the make like
_make install DIR=/path/to/install_.

Make sure to modify the _/path/to/install/config.php_ to configure the
PostgreSQL login and other small configurations.

### Documentation
It will have one some day. I'm sorry for the reverse-engineering that you will
have to do by now.

### History
Someone needed somthing like this to be developped. I developped this and he
gave me permission to pusblish it under some copyleft license. Here I am
publishing it.

### License
beeo is licensed under the terms of the [GPLv3](LICENSE) license.
beeo may be available in other options, please contact me if you have special
needs.

