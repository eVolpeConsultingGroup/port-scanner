# Port Scanner

A web-based tool for monitoring your network security by scanning IP addresses for open ports and sending notifications about potentially unwanted open ports.

![Port Scanner interface](https://evolpe.pl/wp-content/uploads/2025/04/port-scanner-preview.webp)

## Overview

Port Scanner helps you maintain network security by regularly checking specified IP addresses for open ports. The system:

- Maintains a database of IP addresses to monitor
- Allows exclusion of specific ports from scanning (e.g., known services)
- Runs automated scans using cron jobs
- Sends notifications when open ports are detected
- Uses Nmap for reliable port scanning

This tool is designed to help detect potential security vulnerabilities in your network by identifying unexpected open ports that could indicate unauthorized services or security leaks.

## Features

- **Web Interface**: Easy-to-use interface for managing IP addresses and excluded ports
- **Port Exclusions**: Specify ports to exclude from scanning for each IP address
- **Scheduled Scanning**: Automatic daily scans via cron jobs
- **Parallel Scanning**: Multi-threaded scanning for better performance
- **Notifications**: Sends scan results via webhook (currently supports Rocket Chat)
- **Docker Support**: Easy deployment using Docker

## Installation

### Docker Installation (Recommended)

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/port-scanner.git
   cd port-scanner
   ```

2. Copy the environment template and edit variables:
   ```
   cp .env_template .env
   nano .env
   ```

3. Build and run the Docker container:
   ```
   docker build -t port-scanner .
   docker run --name=port-scanner -p 80:80 -dit port-scanner
   ```

4. Access the web interface at `http://localhost` or your server's IP address.

### Manual Installation

1. Install required dependencies:
   ```
   apt-get update && apt-get install -y php apache2 nmap cron
   ```

2. Clone the repository to your web server directory:
   ```
   git clone https://github.com/yourusername/port-scanner.git /var/www/html/port-scanner
   ```

3. Set up the environment file:
   ```
   cp .env_template .env
   nano .env
   ```

4. Set appropriate permissions:
   ```
   chown -R www-data:www-data /var/www/html/port-scanner
   chmod 755 /var/www/html/port-scanner/*.php
   ```

5. Set up the cron job:
   ```
   echo "0 12 * * * cd /var/www/html/port-scanner && /usr/bin/php cron.php >> /var/log/cron.log 2>&1" | crontab -
   ```

6. Access the web interface at `http://localhost/port-scanner` or your server's IP address.

## Configuration

### Environment Variables

Create a `.env` file based on the `.env_template` with the following variables:

```
WEBHOOK_URL=https://your-rocketchat-webhook-url
```

### Adding IP Addresses to Monitor

1. Access the web interface
2. Fill in the IP address, name, and any excluded ports
3. Click "Add"

### Excluding Ports

You can exclude specific ports from scanning (e.g., legitimate services you run):
1. When adding a new IP, enter port numbers in the "Excluded Ports" field
2. Click "+" to add more excluded ports
3. For existing entries, click "Edit" to modify excluded ports

## Scan Results

Scan results are sent to the configured notification channel (default: Rocket Chat webhook) with the following information:

- IP address and name
- List of open ports found
- Any errors encountered during scanning

## Scheduled Scans

By default, scans run daily at 12:00 PM. To modify the schedule:

### Docker Installation
1. Edit the Dockerfile and change the cron schedule:
   ```
   RUN echo "0 12 * * * cd /var/www/html && /usr/local/bin/php cron.php >> /var/log/cron.log 2>&1" > /etc/cron.d/mycron
   ```

2. Rebuild the Docker image:
   ```
   docker build -t port-scanner .
   ```

### Manual Installation
1. Edit the crontab:
   ```
   crontab -e
   ```

2. Modify the schedule as needed:
   ```
   0 12 * * * cd /var/www/html/port-scanner && /usr/bin/php cron.php >> /var/log/cron.log 2>&1
   ```

## Security Considerations

- This tool uses Nmap for port scanning, which should only be used on networks you own or have permission to scan
- Ensure the web interface is properly secured (e.g., behind a firewall or authentication)
- Be aware that port scanning can generate significant network traffic

## Upcoming Features

- Email notification support
- Generic webhook notification templates
- Scan history and reporting
- IP ranges and subnet scanning
- Custom scan schedules per IP
- Authentication for the web interface

## License

[MIT License](LICENSE)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
