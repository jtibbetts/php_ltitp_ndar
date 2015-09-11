<?php
/**
 * Created by IntelliJ IDEA.
 * User: johntibbetts
 * Date: 3/31/15
 * Time: 10:17 AM
 */

openlog('php', LOG_CONS | LOG_NDELAY | LOG_PID, LOG_USER | LOG_PERROR);
error_reporting(LOG_INFO);

syslog(LOG_WARNING, 'warning is logged...begin');

echo 'before';
phpinfo();
echo 'after';

syslog(LOG_WARNING, 'warning is logged...begin');

closelog();

