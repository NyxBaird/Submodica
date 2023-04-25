Submodica ReadMe! I'll probably add more and more to this as time goes on but to start; the basics.

Submodica is running Laravel (a PHP framework) on its backend and Vue 3 on its front end. Its database is mysql and all of its styling is done in sass.

INSTALLATION README AT THE BOTTOM


## About Laravel 9

Laravel 9 is a fast, intuitive PHP framework that aims to bring PHPs somewhat abnormal syntax more in line with what's expected by developers. I believe it was originally built to mimic C#'s structure but its since moved on. It's hands down my favorite backend as it's free to use, incredibly flexible, and has some of the best documentation I've ever seen out there.

- [Laravel 9.x Docs](https://laravel.com/docs/9.x).


## About Vue 3 (not to be confused with Vue 2 which is like...way different)

The frontend runs on a javascript based Single Page Application (SPA) framework called Vue 3. SPA's are special in that while the address in the address bar and the page content may change the users page never actually refreshes, but rather javascript is used to asynchronously fetch and update pages.

What this means functionally for Submodica is that all frontend routes get directed to a single view (resources/views/master.blade.php) and then javascript handles all page loading and such after that. The Vue pages/components can be found under the "resources/vue/" directory.

- [Vue 3 Docs](https://v3.vuejs.org/guide/introduction.html).

## About the styling situation

All the styling is done in SASS. If you're not familiar with SASS it's basically CSS+ and you can check it out here 

- [SASS Docs](https://sass-lang.com/documentation).

Please note that the files are included as .scss files and not .sass files.

I originally started by including all the styling as individual files in the resources/sass/ directory but I quickly gave up on that for most vue pages as there's simply not enough styling to warrant a whole file it's easier to just throw that specific components css in its component file. Either way it should be easy to locate the styling source any Vue component is using by navigating to the Vue component itself and checking out its styling tags.


# Installation
 - Install PHP 8.0.0 or higher on a virtual machine (if using linux just use your actual machine. If using windows I recommend WSL2, VirtualBox or XAMPP)
 - Clone the repo onto your virtual machine (probably into something like /var/www/submodica)
 - Set up apache and create a new vhost pointing to the submodica directory 
 - Point to your new virtual machine in your hosts file. (C:\Windows\System32\drivers\etc\hosts) You can do this by adding a line like such "{virtual machine ip} www.modnautica.local modnautica.local". Please note on windows you have to open notepad as an admin and then open the hosts file from there in order to edit it.
 - Set up mysqlserver/mysql-cli and create a new database 
 - Set your .env file point to your new mysql database
 - Add a new Discord ID/Secret in the env if desired to enable discord login
 - Add a new Github ID/Secret in the env if desired to enable github login
 - Add a Google cloud vision api key if desired to enable image recognition to allow uploading mod images (this is how the nsfw filter on submodica works)
 - Install composer (https://getcomposer.org/download/), a common package manager for php on your virtual machine
 - Run "composer install" in the virtual host submodica directory
 - Install nodejs (https://nodejs.org/en/download/), a common package manager for javascript on your virtual machine
 - Inside your virtual machines submodica folder, run "vendor/bin/phinx migrate" to migrate the database
 - Don't forget to enable Apaches Rewrite module to make URI's work!
 - The following commands clear caching so if you change something and that change doesn't reflect try running this in your submodica virtual machine directory;

```php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan config:clear```

Please note that without a bunny CDN key you'll be unable to upload or download images from the Submodica CDN. I don't have a great solution for this atm so...leave the images to me for now? We can discuss this more in the future if neccessary.
