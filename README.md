# SPTransfer v1.0

phpVMS v7 module for a HUB Transfer feature

* Module supports **only** php8.1+ and laravel10
* Minimum required phpVMS v7 version is `phpVMS 7.0.0-dev+240613.77cd59` / 13.June.2024

The frontend module blade is designed for the **Original** and **Disposable Theme**.
[Disposable Theme (By FatihKoz)](https://github.com/FatihKoz/DisposableTheme)

* If you are using the original Theme, you need nothing to do.
* If you are using the Disposable Theme, just **rename** `/modules/SPTransfer/Resources/views/index.blade.php` to `/modules/SPTransfer/Resources/views/original_index.blade.php` **and** `/modules/SPTransfer/Resources/views/disposable_index.blade.php` to `/modules/SPTransfer/Resources/views/index.blade.php`

## What you get

I know, the basic system allows pilots to change their HUB from the profile settings page. But if you want a little bit more variety and interaction with your pilots and finances and want to disable the freedom of changing HUBs every time, than this module is for you. This module provides an admin page where you can work on all requests including settings for the price per request and block time between multiple requests.

_**Note:** If you want to disable the original HUB select in pilots profile settings, you can do this in `/resources/views/layouts/Disposable_v3/profile/fields.blade.php`. The Disposable Theme provides a theme.json, just look for `user_disable_hub`._

## Compatibility

This module is fully compatible with phpVMS v7 and will work with any other module you have installed.

## Installation and Updates

_Make sure the name of the folder you upload is **SPTransfer**._
* Manual Install : Upload contents of the package to your phpVMS root `/modules` folder via ftp or your control panel's file manager
* GitHub Clone : Clone/pull repository to your phpVMS root `/modules/SPTransfer` folder
* phpVMS Module Installer : Go to admin > addons/modules , click Add New , select downloaded file then click Add Module

* Go to admin > addons/modules enable the module
* Go to admin > dashboard (or /update) to trigger module migrations
* When migration is completed, go to admin > maintenance and clean `application` cache

## License Compatibility & Attribution Link

Do **not** remove the Link that is visible in your admin center. Feel free to publish a link on your public pages if you want to.

## Do you have any suggestions or need help?
Please use the GitHub [issue](https://github.com/PaintSplasher/phpvms7_SPTransfer/issues) tracker.

## Release / Update Notes

04.JULY.24
* Initial Release
