# icinga2-joomla-update
Allows for monitoring whether an Joomla installation requires an update using Icinga2. I was looking for an Icinga to monitor our Joomla installations, but could not really find any plugins that matched our requirements. This repository is pretty much an extension of [this blog post](https://blog.pregos.info/2012/11/09/nagiosicinga-plugin-to-check-joomla-update-status-passive-check/) with the Shell Script and inspiration from [Nagios-WordPress-Update](https://github.com/jinjie/Nagios-WordPress-Update).

To install:
- Upload the joomla-version.php at the root of your joomla installation
- Adapt the `$allowed_ips` variable to fit your needs
- Copy the check-joomla-update.sh to you nagis plugins folder
- Create a service command template
- Create a service check on your host

#### Command Template
```
define command {
	command_name    check_joomla_update
	command_line    $USER1$/check-joomla-update.sh $ARG1$
}
```
#### Service Check
```
define service {
	use                     generic-service
	host_name               example.com
	service_description     My Joomla Installation
	check_command           check_joomla_update!http://example.com/joomla-version.php
}
```

Inspired from [Nagios-WordPress-Update](https://github.com/jinjie/Nagios-WordPress-Update) and [pregos blog](https://blog.pregos.info/2012/11/09/nagiosicinga-plugin-to-check-joomla-update-status-passive-check/).
