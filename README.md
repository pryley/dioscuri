# Dioscuri

This is a package to quickly setup a local WordPress installation on a Mac using [Laravel Valet](https://laravel.com/docs/master/valet#installation). It is assumed the you are using `.test` for the local TLD.

## Requirements
- [Laravel Valet](https://laravel.com/docs/6.x/valet)
- MySQL 5.6
- Perl
- PHP 7.1

## Installation

```bash
git clone https://github.com/pryley/dioscuri.git [projectdir]
cd [projectdir]
sh install
```

## Directory Structure

```
dioscuri
├── deploy                  This dir contains the deployment configuration
├── public                  This is the webroot
│   ├── app                 This dir replaces the wp-content dir
│   │   ├── mu-plugins      Must-use plugins are installed here
│   │   ├── plugins         Plugins are installed here
│   │   ├── themes          Themes are installed here
│   │   └── uploads         Uploads are stored here
│   ├── wp                  This is the WordPress directory
│   │   ├── wp-admin
│   │   ├── wp-content      This dir is unused except for the default themes shipped with WordPress
│   │   ├── wp-includes
│   │   └── etc.
│   └── wp-config.php       This file loads the env.php file which is located outside on the webroot
├── deploy.php              This is the deployment script
├── env.php                 This file is where you define your database information, etc.
└── LocalValetDriver.php    This file allows you to use the dioscuri dir structure with Laravel Valet
```

## What's Installed

The following boilerplate theme is installed and activated:
- [Castor](https://github.com/pryley/castor)

The following plugins are installed and activated:
- [Black Bar](https://wordpress.org/plugins/blackbar/)
- [Imsanity](https://wordpress.org/plugins/imsanity/)
- [Pollux](https://wordpress.org/plugins/pollux/)
- [Two Factor](https://wordpress.org/plugins/two-factor/)

The following plugins are installed:
- [Disable Comments](https://wordpress.org/plugins/disable-comments/)
- [Easy Updates Manager](https://wordpress.org/plugins/stops-core-theme-and-plugin-updates)
- [Machete](https://wordpress.org/plugins/machete/)
- [Members](https://wordpress.org/plugins/members/)
- [Meta Box](https://wordpress.org/plugins/meta-box/)
- [Paste as Plain Text](https://wordpress.org/plugins/paste-as-plain-text/)
- [Post Type Archive Link](https://wordpress.org/plugins/post-type-archive-links/)
- [Powerful Posts Per Page](https://wordpress.org/plugins/pppp/)
- [Safe Redirect Manager](https://wordpress.org/plugins/safe-redirect-manager/)
- [Safe SVG](https://wordpress.org/plugins/safe-svg/)
- [SendGrid](https://wordpress.org/plugins/sendgrid-email-delivery-simplified/)
- [Simple Custom Post Order](https://wordpress.org/plugins/simple-custom-post-order/)
- [Simple Page Sidebars](https://wordpress.org/plugins/simple-page-sidebars)
- [Simple Post Type Permalinks](https://wordpress.org/plugins/simple-post-type-permalinks)
- [The SEO Framework](https://wordpress.org/plugins/autodescription/)
- [UpdraftPlus WordPress Backup Plugin](https://wordpress.org/plugins/updraftplus)
- [User Menus](https://wordpress.org/plugins/user-menus)

The following Must-Use plugins are installed:
- [Bedrock Autoloader](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/bedrock-autoloader.php)
- [Better Thumbnail Sizes](https://github.com/pryley/better-thumbnail-sizes)
- [Disallow Indexing](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/disallow-indexing.php)
- [Password bcrypt](https://wordpress.org/plugins/password-bcrypt/)
- [Register Theme Directory](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/register-theme-directory.php)
- [Soil](https://github.com/roots/soil)
- [Stage Switcher](https://github.com/roots/wp-stage-switcher)
