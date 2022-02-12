# Scalable Product Manager

*Provides a secure scalable cloud based solution to upload and view store products.*

** Example **

0 products
![alt text](https://github.com/sf-dub/project_image_assets/blob/8417b08f6828e6cfb017d5f88ad3a8853b84abf1/productmanager1.jpg)


Page 1 of 2
![alt text](https://github.com/sf-dub/project_image_assets/blob/8417b08f6828e6cfb017d5f88ad3a8853b84abf1/productmanager2.jpg)


Page 2 of 2
![alt text](https://github.com/sf-dub/project_image_assets/blob/8417b08f6828e6cfb017d5f88ad3a8853b84abf1/productmanager3.jpg)


End of page with dynamically generated pie charts
![alt text](https://github.com/sf-dub/project_image_assets/blob/8417b08f6828e6cfb017d5f88ad3a8853b84abf1/productmanager4.jpg)


## SETUP

1) Create an empty database named 'crm'
	- If your hosting provider prefixes this name you must change 'crm' in db.php
 
2) Run both sql queries located in the folder named database_setup:

	- Table / structure for crm_products and crm_login will be generated if they do not already exist 
 
3) Enter the database username and password in db.php lines 13 and 14 (give permissions if necessary)


4) Enter a username and password in the crm_login table:

	- This is the username and password used to login.
	- Make sure you do not use the database username and password for security reasons.

5) Run the application by going to the products directory - index.php will run by default e.g. yourwebsite.com/admin/product/

### Documentation

This project is a one page web application written in php, html, css and javascript.
It provides a secure scalable cloud based solution to upload and view store products.
It is written proceedurally using one php object which is created in db.php and has a short life-cycle.
This application could be refactored to include a frontend framework or more php objects.
The architecture of this web application allows it to handle unlimited products.
The reason for using minimal objects is performance related.
