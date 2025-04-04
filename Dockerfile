# Use the official PHP 8.3 Apache image as the base
FROM php:8.3-apache

# Install nmap, cron and other necessary packages
RUN apt-get update && apt-get install -y nmap cron && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set the working directory in the container
WORKDIR /var/www/html

# Copy all files from the current directory to the working directory in the container
COPY . .

# Create a cron job
RUN echo "0 12 * * * cd /var/www/html && /usr/local/bin/php cron.php >> /var/log/cron.log 2>&1" > /etc/cron.d/mycron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/mycron
RUN chmod 0755 /var/www/html/*
RUN chown www-data:www-data /var/www/html/*

# Apply cron job
RUN crontab /etc/cron.d/mycron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Expose port 80 for Apache
EXPOSE 80

# Create a startup script
RUN echo '#!/bin/bash\nservice cron start\nexec apache2-foreground' > /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Start Apache and cron using the startup script
CMD ["/usr/local/bin/startup.sh"]
