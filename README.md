#This test task without TSL connections.

#After download project 
1. Create .env file

For example:
- APP_NAME=VodWork
- APP_ENV=local
- APP_DEBUG=true
- APP_KEY=base64:aakmWF/flO1VTKUGOaeClmF737TWwu5FQrutgNmuyg4=
- APP_URL=http://localhost:32900

#How to run 
1. Need to install docker
2. docker-compose build --no-cache
3. docker-compose up -d

 - 3.1 ./exec n - npm container
    - 3.1.1 run in container npm i
 - 3.2 ./exec p - php container
    - 3.2.1 run in container composer install

4. Go to http://localhost:32900/
5. Play game