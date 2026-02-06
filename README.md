# NYU - Preserving New Forms of Scholarship

This is a Grav skeleton package, which essentially means this is the entire `/user` folder of a Grav site. That means it contains everything your site needs to run:

* All content via `user/pages`
* Custom theme via `user/themes/ny-lib`
* All plugins required including custom `user/plugins/fontawesome-pro-icons` plugin
* Configuration overrides in `user/config`
* A test 'trilby' user in `user/accounts` that you can delete

## Installation

If you have an existing installation on your server, the process very simple:

1. Simply backup/download/save your current user folder for posterity
2. Then extract the skeleton package zip
3. Upload the files to your server in your Grav root folder
4. Rename the folder 'user'

After completion your reload with the new site data.

#### NOTE: I didn't include the admin (and the associated plugins: form, login, email, flex-objects), but the theme already has blueprints defined if you ever do want to add the admin plugin.  At that point, simply install it via `bin/gpm install admin` and it will install everything you need.  To be able to create a user via the web, simply delete the `trilby` user and it will then prompt you to create an admin.
