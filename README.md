# Description
Large File Uploader to upload files and folders* with unlimited size. It uses [flow.js](https://github.com/flowjs/flow.js) for handling.

This app is in a stable state, but not thoroughly tested for security

*) Folder upload only works with Firefox, Edge and Chromium-based (Google Chrome, Opera, Brave, Vivaldi) browsers

![](https://raw.githubusercontent.com/e-alfred/flowupload/master/appinfo/flowupload.gif)

# Installation
Clone the contents of the repository into your `apps` directory and rename it to `flowupload`, if necessary. Otherwise simply download it from the Nextcloud app store within your Nextcloud instance: https://apps.nextcloud.com/apps/flowupload

# Known Bugs
- __External Storages are generally supported, but problems can still appear__ (Please don't create new issues we know what the problem is. For updates on External Storage Support see #46 )
- This app does not support server side encryption.

# Configuration
## Increasing/Limiting the uploadspeed

You can edit the Flow.js paramteres [in the your_nextclouds_app_folder/flowupload/src/App.vue file](https://github.com/e-alfred/flowupload/blob/c9a6fb974bd67f65767dfda6c6b41fe68e985f56/src/App.vue#L336).

After that you will have to run
````
npm ci
npm run build
````
in the flowupload folder.

The config parameters you want to set are:

> **chunkSize** The size in bytes of each uploaded chunk of data. This can be a number or a function. If a function, it will be passed a FlowFile. The last uploaded chunk will be at least this size and up to two the size, see Issue #51 for details and reasons. (Default: 1*1024*1024, 1MB)

> **simultaneousUploads** Number of simultaneous uploads (Default: 4)

More configuration options can be found in the [Flow.js documentation](https://github.com/flowjs/flow.js#configuration).

If you increase the simultaneousUploads and chunkSize the upload speed will increase.

If you decrease these parameters the upload speed will decrease.
(NOTE: Decreasing the chunkSize might increase the CPU usage)

# Development
## Try it on Gitpod
[![Open in Gitpod](https://gitpod.io/button/open-in-gitpod.svg)](https://gitpod.io/#https://github.com/e-alfred/flowupload/)

It will automatically spin up and configure a full Nextcloud, MariaDB and PhpMyAdmin server.

## Nextcloud Login:
**Username:** dev

**Password:** t2qQ1C6ktYUv7

## PhpMyAdmin Login:
**Username:** nextcloud

**Password:** wdGq73jQB0p373gLdf6yLRj5

(It is fine to have these static logins, because gitpod has acess control built in and no sensitive data is stored in these dev servers)

# Licenses
Flowupload: [GNU Affero General Public License](http://www.gnu.org/licenses/agpl-3.0.html)

Flowjs: [MIT License](https://opensource.org/licenses/MIT)
