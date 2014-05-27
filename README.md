# Research Questions

This is a crowd-sourcing tool for peer review of essay theses and research
questions. It allows students to anonymously submit their proposals for
in-depth review by their peers.

## Getting Started

### Setting up your environment

This application is built on Python and Django, and styled using Bootstrap
& Bootswatch. Begin first by cloning a copy of this repository or
downloading a .zip file.

    $ git clone http://github.com/jrouly/research-questions.git
    $ cd research-questions

To get started, you will need to install a number of Python and MySQL
dependencies. On Ubuntu, that might look like:

    $ sudo apt-get install python python-dev python-pip
    $ sudo apt-get install mysql-server mysql-client
    $ sudo apt-get install apache2  # optional dependency
    $ sudo apt-get install nginx    # optional dependency

More system dependencies may be requirend as this will vary from system to
system.

Once these are installed, use `pip` to install `virtualenv`, a tool to
manage Python virtual environments. This will likely need to happen
globally, so use `sudo`.

    $ sudo pip install virtualenv

Use `virtualenv` to create and activate a new virtual environment for this
project.

    $ mkdir .virtualenv
    $ virtualenv .virtualenv/researchquestions
    $ source .virtualenv/researchquestions/bin/activate

You should notice `(researchquestions)` at the beginning of your command
prompt now, indicating that the Python virtual environment was succesfully
activated. You can now install the listed requirements for this project.

    $ pip install -r requirements.txt

If any of the dependencies fail to install, make sure you have all the
system dependencies listed above installed. If the problem persists, your
system may be lacking an assumed dependency.

Once you get all the dependencies satisfied, execute the following command
to generate the proper static directories.

    $ python manage.py collectstatic

### Starting the test server

Now that your environment is configured, you can test out the Django test
server to make sure everything works. To do this, simply run the runserver
command.

    $ python manage.py runserver

You should be able to access the testing server on localhost:8000. However,
there will be no static file hosting. To rectify this, you will need to
install a proxy server to manage static files.

### Setting up a proxy server

You have three options here. Apache + nginx, pure Apache, or pure nginx. I
recommend Apache + nginx personally.

#### Apache + nginx (option 1)

One of the main benefits of Apache proxy passing to an nginx application
server is that you retain the flexibility of a front-facing Apache server
along with the easy of configuration of an internal nginx application
server.

Globally install the Apache and nginx webservers. The specific details of
configuring this software will not be covered here, but sample
configuration snippets might look like this:

##### Apache config

    <VirtualHost *:80>
        ServerName research-questions.yourdomain.com
        ProxyRequests Off
        <Proxy *>
            Require all granted
        </Proxy>

        ProxyPass / http://research-questions.yourdomain.com:8000/
        ProxyPassReverse / http://research-questions.yourdomain.com:8000/

        <Location />
            Require all granted
        </Location>
    </VirtualHost>

##### nginx config

    server {
        listen 8000;
        server_name research-questions.yourdomain.com;

        location / {
            proxy_pass     http://127.0.0.1:8001/;
            proxy_redirect http://127.0.0.1:8001/ /;
            server_name_in_redirect off;

            proxy_set_header  Host       $host;
            proxy_set_header  X-Real-IP  $remote_addr;
            proxy_set_header  X-Forwarded-For  $proxy_add_x_forwarded_for;
        }

        location /static/ {
            alias /path/to/install/research-questions/researchquestions/static/;
        }
    }


#### Pure Apache (option 2)

This option is slightly less simple to configure and requires Apache to be
restarted whenever a change is made, since Apache does not handle
`mod_wsgi` threading as well as nginx.

##### Apache config

    <VirtualHost *:80>
        ServerName research-questions.yourdomain.com

        Alias /static/ /path/to/install/researchquestions/static/
        Alias /media/  /path/to/install/researchquestions/media/

        <Directory /path/to/install/researchquestions/static>
            Options -Indexes
            Order deny,allow
            Allow from all
        </Directory>

        <Directory /path/to/install/researchquestions/media>
            Options -Indexes
            Order deny,allow
            Allow from all
        </Directory>

        WSGIScriptAlias / /path/to/install/researchquestions/researchquestions/wsgi.py
        WSGIDaemonProcess research-questions.yourdomain.com python-path=/path/to/install/researchquestions:/path/to/install/.virtualenv/researchquestions/lib/python2.7/site-packages
        WSGIProcessGroup research-questions.yourdomain.com

        <Directory /path/to/install/researchquestions/researchquestions>
            <Files wsgi.py>
                Options -Indexes
                Order deny,allow
                Allow from all
            </Files>
        </Directory>
    </VirtualHost>

#### Pure nginx (option 2)

This option is very simple to configure. Simply make use of the nginx
configuration from option 1, but direct the server to listen on port 80 for
standard http connections instead of 8000.


### Setting up the database

The database is one of the most important parts of this application.
Without a database backend, no user data could be stored. By default, this
application is configured to use a MySQL or MariaDB backend, but any
standard database software can be used as a replacement.

Begin by migrating the website schema.

    $ python manage.py migrate website

Then synchronize the rest of the database.

    $ python manage.py syncdb

### User access

This application is set up to allow user access from LDAP or CAS
authentication backends. It's also easy to use simple Django model
authentication, but there is no way to register users other than
manual administrative intervention.

Start by copying the `config.py.template` file to `config.py`.

    $ cp config/config.py.template config/config.py

Set `AUTH_MODE` to either `CAS` or `LDAP`. Depending on what you set,
fill in the remaining authentication settings. CAS provides the simplest
login model.

### Remaining configurations

Now that you have your own configurations file, you can set the remaining
configs. Each directive is documented in the `config.py.template` file
itself.


## ToDo

* Include comment functionality on index page allowing users to skip the discussion step.
