# Kohana LogReader

## A Kohana 3.x module for viewing and searching logs

Easily view and search Kohana log messages on a simple Bootstrap interface.

Copyright &copy; 2014. Licensed under the [MIT license](LICENSE.md).

### GITHUB

https://github.com/siloor/kohana-logreader

Please, don't forget to star the repository if you like (and use) the plugin. This will let me know how many users it has and then how to proceed with further development :)

### DEMO

http://siloor.com/logreader/demo/?message=&date-from=2014-01-01

### Installation

1. Download this module and add the **logreader** folder in to your `MODPATH`
2. Enable it in the `bootstrap` file ``` Kohana::modules(array( 'logreader' => MODPATH.'logreader', // LogReader )); ```
3. Go to `http://your-app-root/logreader`
4. You are done! 

![Kohana LogReader interface](http://siloor.com/logreader/logreader.v1.png "Kohana LogReader interface")

### How to use?

On the Messages interface you can see the daily log messages by default.

Use filters to get what you want

- Use regular expression in the message field to filter messages
- Set date filters
- Select the levels of the log messages

### Configuration

You can change the following options

- `limit` (default:  `40`) - Number of messages per page
- `auto_refresh_interval` (default:  `5`) - The interval for auto refresh in seconds
- `store` (default:  `File`) - You can easily write your own store if yo use other log solution than the default one. There is an example Store called SQLExample to help you to create your own binding.
- `route` (default: `logreader`) - The route to the LogReader interface - `http://your-app-root/logreader`
- `static_route` (default: `logreader/media`) - The route to the LogReader static files (it could be a remote url) - `http://your-app-root/logreader/media`
- `tester` (default:  `FALSE`) - Show log message tester button
- `authentication` (default:  `FALSE`) - Use HTTP Basic Authetication - Autheticate user by the users array in the config file

### Notes

- If you want to improve, please fork and participate. 
- If you have a suggestion or found a bug, please let me know at - milan.magyar(at)gmail.com
