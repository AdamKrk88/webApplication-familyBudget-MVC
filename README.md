# Budget Manager - web application
Budget Manager is a web application to register your expenses and incomes in one place. Using the application can help to manage your budget more effectively. 

## Programming technologies used
It is application built with PHP + MVC framework. This project uses PHP model-view-controller framework from "Write PHP like a pro: build an MVC framework from scratch" course on Udemy (license and README file for MVC framework located in folder "MVC_information").
Other information: application uses Bootstrap framework and Twig - template engine for PHP. 

## How to run Budget Manager?
1. Download a Repository to your computer
2. Install Visual Studio Code
3. Install XAMPP - select the components: Apache, MySQL, PHP, phpMyAdmin 
4. Configure your PHP, go to terminal in Visual Studio Code and run command `php -v` to check if php is available
5. Install Composer
6. Use command `composer install` in terminal - this should create vendor directory to store all dependencies for PHP web application
7. Change the name of the file "ConfigSample.php" to "Config.php" (in "App" folder). Change the name of the class ConfigSample to Config
8. Import database in phpMyAdmin - take file to import from database folder
9. Create user for this database and password
10. Update file "Config.php" with all required information
11. Copy all files and folders of web application to XAMPP folder "htdocs" - folder "htdocs" should be inside folder where XAMPP is installed, for example: C:\xampp\htdocs
12. Change root folder for Apache to public folder - update DocumentRoot and Directory in "httpd.conf" file to set public folder as Apache root folder. To find "httpd.conf" file please try to use path similar to C:\xampp\apache\conf
13. If all steps above are completed successfully, http://localhost/ will point to the public folder and web application should be availabe under http://localhost/
