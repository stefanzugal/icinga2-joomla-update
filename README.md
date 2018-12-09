# icinga2-joomla-update
Allows for using Icinga to monitor whether a Joomla installation requires updates. I was looking for an Icinga plugin for monitoring our Joomla installations, but could not really find any plugins that matched our requirements. This repository is pretty much an extension of [this blog post](https://blog.pregos.info/2012/11/09/nagiosicinga-plugin-to-check-joomla-update-status-passive-check/) with the shell script and inspiration from [Nagios-WordPress-Update](https://github.com/jinjie/Nagios-WordPress-Update).

#### Installation Instructions

- Upload the joomla-version.php at the root of your Joomla installation
- Adapt the `$allowed_ips` variable to fit your needs
- Copy the check-joomla-update.sh to you nagios plugins folder
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
