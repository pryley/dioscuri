# Dioscuri

This is a package to quickly setup a local WordPress installation on a Mac using [Laravel Valet](https://laravel.com/docs/master/valet#installation). It is assumed the you are using `.test` for the local TLD.

## Requirements
- Laravel Valet
- MySQL

## Installation

1. git clone https://github.com/geminilabs/dioscuri.git [projectdir]
2. cd [projectdir]
3. rm -rf .git && git init .
4. composer install

## What's Installed

The following plugins are installed:
- [BlackBox Debug Bar](https://bitbucket.org/geminilabs/blackbox) @activated
- [Disable Comments](https://wordpress.org/plugins/disable-comments/)
- [Easy Updates Manager](https://wordpress.org/plugins/stops-core-theme-and-plugin-updates)
- [Imsanity](https://wordpress.org/plugins/imsanity/) @activated
- [Machete](https://wordpress.org/plugins/machete/)
- [Meta Box](https://wordpress.org/plugins/meta-box/) @activated
- [Paste as Plain Text](https://wordpress.org/plugins/paste-as-plain-text/)
- [Pollux](https://wordpress.org/plugins/pollux/) @activated
- [Post Type Archive Link](https://wordpress.org/plugins/post-type-archive-links/) @activated
- [Powerful Posts Per Page](https://wordpress.org/plugins/pppp/)
- [Safe Redirect Manager](https://wordpress.org/plugins/safe-redirect-manager/)
- [SendGrid](https://wordpress.org/plugins/sendgrid-email-delivery-simplified/)
- [Simple Custom Post Order](https://wordpress.org/plugins/simple-custom-post-order/) @activated
- [Simple Page Sidebars](https://wordpress.org/plugins/simple-page-sidebars)
- [Simple Post Type Permalinks](https://wordpress.org/plugins/simple-post-type-permalinks)
- [The SEO Framework – Extension Manager](https://wordpress.org/plugins/the-seo-framework-extension-manager/) @activated
- [The SEO Framework](https://wordpress.org/plugins/autodescription/)
- [UpdraftPlus WordPress Backup Plugin](https://wordpress.org/plugins/updraftplus)
- [User Menus](https://wordpress.org/plugins/user-menus)
- [User Role Editor](https://wordpress.org/plugins/user-role-editor)

The following must-use plugins are installed:
- [Bedrock Autoloader](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/bedrock-autoloader.php)
- [Disallow Indexing](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/disallow-indexing.php)
- [Register Theme Directory](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/register-theme-directory.php)
- [Soil](https://github.com/roots/soil)
- [Stage Switcher](https://github.com/roots/wp-stage-switcher)
- [WP Password bcrypt](https://github.com/roots/wp-password-bcrypt)
- [WP Thumb](https://bitbucket.org/geminilabs/wp-thumb)

The following boilerplate theme is installed:
- [Castor](https://github.com/geminilabs/castor)
