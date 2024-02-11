# Vigilare
![image](https://github.com/Guyverix/Vigilare-NMS-GUI/assets/1209259/b988a2fa-8009-43af-80c0-595ab5919693)

Welcome to Vigilare, an open-source fault management system designed to provide comprehensive monitoring and alerting capabilities. Vigilare is built to ensure high availability and reliability of your systems, offering real-time insights and proactive fault resolution.

## Features

- **Real-Time Monitoring:** Continuous surveillance of system performance and health.
- **Alerting Mechanism:** Immediate notifications for any system irregularities or failures.
- **Customizable Dashboards:** Tailor-made views to monitor vital metrics.
- **Historical Data Analysis:** In-depth analysis of past performance for better future planning.
- **User-Friendly Interface:** Easy to navigate UI for efficient management.
- **Template system:** Extend functionality beyond initial design with adding templates.
- **Independent GUI:** Use your own UI for in-house branding and looks.
- **Multiple Graph options available:** Support for rrd, and graphite are built in.
- **Database authentication:** Discrete access levels per user are supported.


## Getting Started
- [ ] All testing done on Ubuntu / Debian systems
- [ ] Latest testing Ubuntu 20.04.6 LTS
- [ ] If the GUI is running on the same host as the API server and that is already installed, no additional packages are needed specifically for the GUI.

### Prerequisites
- [ ] Successful installation of the API repository
- [ ] Successful configuration of the API repository
- [ ] Successful build and coniguration of the database

### Install instructions
- [ ] Clone this repository to somewhere that Apache can serve from.  I phrase it that way because Apache gets grouchy on Ubuntu when you are not in /var/www
- [ ] Leverage the example Apache config files to create a working Apache config and install them
- [ ] Use SSL!  the non-SSL version does work, but the system overall expects SSL (And TBH, Lets Encrypt is cheap ;) )
- [ ] configure the config/api.php file with the settings you are going to use
```
/*
  Do NOT EVER use localhost or 127.0.0.1 for the API.
  CORS will make you cry.
*/

$apiHttp='https://';                                  // valid to make http as well if you have to
$apiHostname="FQDN of the API server";                // Caution, DNS failures will make this choke, duh!
$apiUrl= $apiHttp . $apiHostname;                     // Complete URL minus port number
$apiPort=8002;                                        // Port API is listening on.  Do not use less than 1024 due to scanners easily finding this port

/*
// These should not be needed for V1.  V2 may leverage these to "bless" the UI in even talking to the API server.
$apiKey ='82f758fa-fdf8-4f22-a1a8-276c95b4570a';      // Created with uuidgen :)  Should match api key in api if same host
$apiUser="apiUser";                                   // Alternate auth if key is borked or redis oos (maybe?)
$apiPass="apiPass";                                   // alternate auth if key is borked or redis oos (maybe?)
*/


/*
 For use in building GUI URL's etc..
 If you are using haproxy or something like that
 this is where you would put the proxy URL
 Things like login need to be explict.  Other
 URL's can be a little more loosy-goosey.
*/
$uiPort=443;
$uiHostname="FQDN of the GUI";
$uiUrl="https://" . $uiHostname;
```



# Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

    Fork the Project
    Create your Feature Branch (git checkout -b feature/AmazingFeature)
    Commit your Changes (git commit -m 'Add some AmazingFeature')
    Push to the Branch (git push origin feature/AmazingFeature)
    Open a Pull Request


# License
Distributed under the MIT License. See LICENSE for more information.

# Contact

Chris Hubbard – <chubbard@iwillfearnoevil.com>

- [ ] Project Link: https://github.com/Guyverix/Vigilare-NMS-GUI
- [ ] Project Link: https://github.com/Guyverix/Vigilare-NMS-API
- [ ] Project Link: https://github.com/Guyverix/Vigilare-NMS-POLLER

# Acknowledgements
This project has been built using the following frameworks and libraries

- [ ] Bootstrap 5.X
- [ ] Simple datatables
- [ ] Font Awesome
- [ ] jquery
