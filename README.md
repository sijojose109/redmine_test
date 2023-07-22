* Ensure the variable 'DATA_SOURCE' in .env file, possible values are 'db' and 'redmine', this variable decides whether the data is loding from local DB or Redmine.
    Example: DATA_SOURCE=redmine

* Ensure the variable REDMINE_URL_WITH_AUTH in .env file, this variable should be holded the value of your readmine root url with basic auth. 
Example: 
    REDMINE_URL_WITH_AUTH='http://admin:Qazxsw321@192.168.18.229:8080'

* Make sure other env variables are configured correctly and used ports are available. Sample .env file is uploaded in the project folder, take it as the reference.

* Run the below commands sequentially.
    composer install
    npm install
    ./vendor/bin/sail build
    ./vendor/bin/sail up

* Run below commands sequentially in another terminal tab.
    ./vendor/bin/sail artisan migrate
    ./vendor/bin/sail artisan db:seed --class=ProjectSeeder
    ./vendor/bin/sail artisan db:seed --class=CategorySeeder
    ./vendor/bin/sail artisan db:seed --class=StatusSeeder
    ./vendor/bin/sail artisan db:seed --class=TrackerSeeder
    ./vendor/bin/sail artisan db:seed --class=IssuePrioritySeeder
    npm run dev

* If you are loading data from redmine, follow the steps given below.
    1. Login to redmine.
    2. Take administration menu and create issue statuses, trackers and priorities.
    3. Create projects.
    4. Assign the logged in user to the project created.
