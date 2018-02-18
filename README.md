# Translate Mate extension for OpenCart 3

## INTRODUCTION:

A tool to help translate OpenCart into multiple languages.

Inspired by the no-longer-maintained Magic Translations extension

If you're looking for Translate Mate for OpenCart 2, check out this branch: https://github.com/chrisrollins65/cr_translate_mate/tree/OpenCart2 or this release: https://github.com/chrisrollins65/cr_translate_mate/releases/tag/1.0.2

## IMPORTANT NOTES:

There are a few things to be aware of while using this module.

**CROSS-SITE SCRIPTING VULNERABILITIES (XSS)**

Only allow trusted users to use this module. 
OpenCart allows html code in translations, so this extension is forced to permit them as well.
This means malicious users can insert malicious code anywhere translations are used throughout OpenCart.

**SPECIAL CHARACTERS**

This extension does not replace HTML entities in translations.
Accents and other special characters may not appear correctly in some browsers unless you make sure to convert them where they are used (using htmlentities() or html_special_chars(), for example).
Fortunately, most modern browsers correctly display these characters even if they're not html encoded.

**Default language**

Be aware that if you edit the translations of the default language OpenCart was installed with, any future update to OpenCart could overwrite your changes.


## ERRORS:

Errors that occur should be saved to the OpenCart error log (**System > Maintenance > Error Logs**). Be sure to look there if you are experiencing problems. Here's a list of common errors:

**PARSE ERRORS**

These errors usually appear if the PHP code in your language files has syntax errors. Often an unescaped or missing single quote (') is the culprit.

**INVALID TOKEN SESSION**

If you see this, it means you have to re-login to use the module. Try refreshing the page and you should see the login prompt.

**PERMISSION ERRORS**

If you get an error saying that a translation file couldn't be saved, you probably need to fix the permissions of the language directories and files. Contact your webhost (or do a google search) if you need help with that.
I never recommend setting permissions to 777. I'd try 750 first, and if that's not enough 755, and then 775 if necessary.

**OTHER ERRORS**

If you can't figure it out or you're sure it's a problem with my extension, please let me know at:

1. Github: https://github.com/chrisrollins65/cr_translate_mate
   (I'll probably be more active here, so I prefer this method of contact)

2. The Opencart Extensions page: http://www.opencart.com/index.php?route=extension/extension/info&extension_id=23098

3. Email - cr.dev.testing@gmail.com

## INSTALLATION INSTRUCTIONS:
1. Download the latest version of this extension from the [OpenCart page](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=23098) or [GitHub](https://github.com/chrisrollins65/cr_translate_mate/releases)
2. Back up your admin/language and catalog/language folders in your Opencart directory. (optional but always a good idea)
3. If you've renamed your "admin" folder, rename the "admin" folder in this package's "upload" directory to match it
4. Copy the files in the "upload" folder into your OpenCart directory
5. In the OpenCart admin interface, find "Translate Mate" on the Modules page (Extensions > Extensions > Modules) and activate it
6. Go to **System > Users > User Groups** and give both **Access Permission** and **Modify Permission** for Translate Mate (**extension/module/cr_translate_mate**) to the user groups that will be using the extension.

## USAGE INSTRUCTIONS:

Go to the Modules page in the admin interface of OpenCart (Extensions > Extensions > Modules).
On the Modules page, select the "Edit" option (the pencil) for the Translate Mate module.

From there, just click on any translation you wish to edit and edit away!

## UNINSTALLATION INSTRUCTIONS:
1. On the Modules page, uninstall the module by pressing the red "Uninstall" button.
2. Delete the following files and folders from your OpenCart directory:
admin/CrTranslateMate
admin/controller/extension/module/cr_translate_mate.php
admin/language/en-gb/extension/module/cr_translate_mate.php
admin/model/extension/module/cr_translate_mate.php
admin/view/javascript/cr_translate_mate
admin\view\template\extension\module\cr_translate_mate.twig
admin\view\template\extension\module\cr_translate_mate_table.twig

## DEVELOPERS
If someone wishes to take over this project, let me know. I just don't have the time to invest much in it anymore.
To develop this extension:
1. Download the desired version of OpenCart you wish to work with and set it up in your development environment.
2. Clone this project into that OpenCart installation. A few files, such as the README.md, .gitignore and composer.json are overwritten.
3. Run composer update
4. To run unit tests, run the following command from your project directory: php vendor/phpunit/phpunit/phpunit