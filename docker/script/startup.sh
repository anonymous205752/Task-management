#!/bin/bash

# Start PHP-FPM
service php8.4-fpm start

# Start nginx
service nginx start

# Start supervisor for queues and cron
supervisord -c /etc/supervisor/conf.d/supervisord.conf

# Keep container running
tail -f /dev/null
