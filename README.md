# SPTransfer v1.7

phpVMS v7 module for a HUB Transfer feature

* Module supports **only** php8.2+ and laravel 11
* Minimum required phpVMS v7 version is `phpVMS 7.1.0-alpha.0+250112.fee20d` / 12. January 2025

The frontend module blade is designed for the **Original** and **Disposable Theme**.
[Disposable Theme (By FatihKoz)](https://github.com/FatihKoz/DisposableTheme)

* If you are using the original Theme, you need nothing to do.
* If you are using the Disposable Theme, just **rename** `/modules/SPTransfer/Resources/views/index.blade.php` to `/modules/SPTransfer/Resources/views/original_index.blade.php` **and** `/modules/SPTransfer/Resources/views/disposable_index.blade.php` to `/modules/SPTransfer/Resources/views/index.blade.php`

## What you get

I know, the basic system allows pilots to change their HUB from the profile settings page. But if you want a little bit more variety and interaction with your pilots and finances and want to disable the freedom of changing HUBs every time, than this module is for you. This module provides an admin page where you can work on all requests including settings for the price per request and block time between multiple requests.

_**Note:** If you want to disable the original HUB select in pilots profile settings, you can do this in `/resources/views/layouts/default/profile/fields.blade.php`. The Disposable Theme provides a theme.json, just look for `user_disable_hub`._

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

## Link removal

You can remove the link from your main menu by adding ``//`` in front of row 49 in your ``Providers/AppServiceProvider.php`` file.

## Admin Widget

You now have the opportunity to add a widget to your admin area that will show you all pending transfer requests. To add the widget to your admin area please follow the instructions.

* Open the file `/resources/views/admin/dashboard/index.blade.php`
* Add `@widget('SPTransfer::InfoBox')` after ``@endcomponent (row 30)``
* Close and save the file

![hub_transfer_pending_admin](https://github.com/user-attachments/assets/cfabdd11-80a7-48bc-8288-da07a8ad010a)

## License Compatibility & Attribution Link

Do **not** remove the Link that is visible in your admin center. Feel free to publish a link on your public pages if you want to.

## Do you have any suggestions or need help?
Please use the GitHub [issue](https://github.com/PaintSplasher/phpvms7_SPTransfer/issues) tracker.

## Release / Update Notes

12.January.25
* Languages ES, FR, IT, PT, JP, TR added
* Smaller bootstrap 5 icon and template changes
* Some StyleCI fixes
* phpVMS minimum version change

29.October.24
* Added an option for different charge types
* Added a widget for showing pending requests

11.July.24
* Added discord notification
* Added reject reason
* Added sortable table
* Source code optimizations
* StyleFixes

04.JULY.24
* Initial Release
* New default table value
