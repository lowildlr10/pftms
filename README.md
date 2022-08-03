![image](https://user-images.githubusercontent.com/26521475/182509110-e5b9a1e2-ec61-49f3-9bc3-de013d91d8e6.png)

# Demo

__URL:__ https://dev-pftms.dostcar.ph<br>
__Username:__ admin<br>
__Password:__ 5!dIPPG9PfKQbY/Z<br>


# Installation and Configuration

1. Open command prompt or bash terminal.
1. Go to the directory where the repository is being stored/cloned by using 
   the "cd" command.
   
   example: 
   ```
   cd /var/www
   cd C:\xampp\htdocs
   ```
        
2. Clone this repository using "git clone <URL> <folder_name>" command
   
   example: 
   ``` 
   git clone https://github.com/lowildlr10/pftms_v2.git pftms
   ```
   
3. Navigate inside the cloned repository by using "cd" command
   
   example:
   ```
   cd pftms
   ```
        
4. Run this command "cp .env.examples .env" (Linux or MAC) or 
   "copy .env.examples .env" or simply duplicate ".env.examples"
   then rename it to ".env".
  
5. Open .env file by using the text editor or the terminal text 
   editors (vim, nano, and etc.), then edit this fields APP_NAME,
   APP_DEBUG, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and 
   DB_PASSWORD
    
    example:
    ```
    APP_NAME=PFTMS
    APP_DEBUG=false
    
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pftms_db
    DB_USERNAME=pftms
    DB_PASSWORD=pftms
    ```
       
 6. Then run this following commands:
 
    ```
    "composer install"
    "php artisan key:generate"
    "php artisan migrate:fresh --seed"
    "php artisan storage:link"
    "php artisan cache:clear"
    "php artisan config:clear"
    "php artisan view:clear"
    ```
      
 7. Access the site then login this admin credentials:
 
    ```
    Username: admin
    Password: admin
    ```
      
8. Change immediately the password then entry all the
   necessary data in the following libraries:
     - Inventory Classifications
     - Item Classifications
     - Modes of Procurement
     - Source of Funds
     - Signatories
     - Supplier Classifications
     - Suppliers
     - Unit of Issues
     - Paper Sizes
     - Divisions
     - Roles
     - Groups
     - Accounts
 
