#build docker image
docker build -t php-calendar .

#run docker container at localhost on port 8081
docker run -d -p 8081:80 --name php-calendar-container php-calendar