Translate Mate extension for Opencart 2

-------------------------------
INTRODUCTION:
-------------------------------

A tool to help translate Opencart into multiple languages.

Design based on the Magic Translations extension: http://www.opencart.com/index.php?route=extension/extension/info&extension_id=13548

Since that extension appears to be no longer maintained, I created this one for Opencart 2 users. The code is different, but the functionality is very similar, so those of you familiar with the Magic Translations extension should have no problem getting into this one.


-------------------------------
IMPORTANT NOTES:
-------------------------------
There are a few things to be aware of while using this module.

**CROSS-SITE SCRIPTING VULNERABILITIES (XSS)**

Only allow trusted users to use this module. Since Opencart translations permit HTML tags, XSS vulnerabilities are a big concern. 

I've tried to minimize the risk as much as I can in this module, but it's really difficult for me to filter safe code from harmful code. I'll see about adding HTML Purifier later on, but if anyone else has ideas about how to improve security, let me know!

**SPECIAL CHARACTERS**

The module can't really know when text is meant for HTML output vs use in code, emails, etc, so when you save a translation, special characters are NOT converted into their HTML equivalents. So a text such as "A&ntilde;adir transacci&oacute;n" will be saved as "Añadir transacción".

Fortunately, most modern browsers correctly display these characters even if they're not html encoded.

**ENGLISH**

Since English translations come preinstalled with Opencart, be aware that if you edit them, any future update to Opencart could overwrite your changes.


-------------------------------
ERRORS:
-------------------------------
Since this is version 1.0, there are bound to be a number of errors that I haven't caught. If you find any, here are some things to check:

**PARSE ERRORS**

These errors usually appear if the PHP code in your language files has syntax errors. Often an unescaped or missing single quote (') is the culprit. Please check that first before asking me for help.

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


-------------------------------
INSTALLATION INSTRUCTIONS:
-------------------------------

1. If you've renamed your "admin" folder, rename the "admin" folder in this package's "upload" directory to match it
2. Copy the files in the "upload" folder into your Opencart directory
3. In the Opencart admin interface, find "Translate Mate" on the Modules page and activate it


-------------------------------
USAGE INSTRUCTIONS:
-------------------------------
On the Modules page (in the admin interface of Opencart), select the "Edit" option (the pencil) for the Translate Mate module.

From there, just click on any translation you wish to edit and edit away!


-------------------------------
UNINSTALLATION INSTRUCTIONS:
-------------------------------
1. On the Modules page, uninstall the module by pressing the red "Uninstall" button. You can delete it if you wish, but step 2 should take care of that.
2. Delete the following files from your Opencart directory:

admin/controller/module/cr_translate_mate.php
admin/language/english/module/cr_translate_mate.php
admin/model/module/cr_translate_mate.php
admin/view/javascript/cr_translate_mate
admin/view/template/module/cr_translate_mate.tpl
admin/view/template/module/cr_translate_mate_table.tpl