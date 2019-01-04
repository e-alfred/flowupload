Description
-----------
Large File Uploader to upload files and folders* with unlimited size. It uses Angular File Upload (https://github.com/flowjs/ng-flow) for handling.

Early alpha state, not approved for security. This app does not support server side encryption at the moment.

*) Folder upload only works with Firefox, Edge and Chromium-based (Google Chrome, Opera, Vivaldi) browsers

![](https://raw.githubusercontent.com/e-alfred/flowupload/master/appinfo/flowupload.gif)

Installation
------------
Clone the contents of the repository into your `apps` directory and rename it to `flowupload`, if necessary. Otherwise simply download it from the Nextcloud app store within your Nextcloud instance: https://apps.nextcloud.com/apps/flowupload

Version information
-------------

Version 0.0.9:
- Added Russian translation (thanks to @DmDS)
- Added support for Nextcloud 15
- Fix deprecated calls for compatibility with new Nextcloud releases

Version 0.0.8:
- Fixed deprecated calls for compatibility with Nextcloud 14
- Added translation for Polish, Czech, Spanish (thanks to @joebordes @CHazz @mzary)
- Updated Angular.js to version 1.6.9

Version 0.0.7:
- Added localisation (thanks @NastuzziSamy)
- Added folder upload support for Firefox, Chrome and Edge
- Updated ng-flow-standalone.js version to 2.7.7
- Added character sanitation to prevent upload of files with unsupported characters (especially foreign languages)

Licenses
-------
Flowupload: [GNU Affero General Public License](http://www.gnu.org/licenses/agpl-3.0.html)

Flowjs/NG-Flow: [MIT License](https://opensource.org/licenses/MIT)

Angularjs: [MIT License](https://opensource.org/licenses/MIT)

Bootstrap: [Apache License v2.0](http://www.apache.org/licenses/LICENSE-2.0)
