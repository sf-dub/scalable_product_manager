** Documentation **

This project is a one page web application written in php, html, css and javascript.
It provides a secure scalable cloud based solution to upload and view store products.
It is written procedurally using one php object which is created in db.php.
This application could be refactored to include a frontend framework or more php objects.
The architecture of this web application allows it to handle unlimited products.

** SETUP **

1) Create an empty database named 'crm'
	- If your hosting provider prefixes this name you must change 'crm' in db.php
 
2) Run both sql queries located in the folder named database_setup:

	- Table / structure for crm_products and crm_login will be generated if they do not already exist 
 
3) Enter the database username and password in db.php lines 13 and 14 (give permissions if necessary)

 
4) Enter a username and password in the crm_login table:

	- This is the username and password used to login.
	- Make sure you do not use the database username and password for security reasons.

5) Run the application by going to the products directory - index.php will run by default e.g. yourwebsite.com/admin/product/


 
 

