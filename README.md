# PHP Calendar
basic implementation of calendar using Docker, PHP and Tailwind.css for styling

## How to use
```
#build docker image
docker build -t php-calendar .

#run docker container at localhost on port 8081
docker run -d -p 8081:80 --name php-calendar-container php-calendar
```

## For development
```
#run docker container with volume for development
docker run -d -p 8081:80 -v /app:/var/www/html --name php-calendar-container php-calendar
```