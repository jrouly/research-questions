# Research Questions

This is a crowd-sourcing tool for peer review of essay theses and research
questions. It allows students to anonymously submit their proposals for
in-depth review by their peers.

## Getting Started

### Install basic requirements

This project requires Git, Python, and Pip.

    $ sudo apt-get install git python python-dev python-pip

### Setting up your environment

This application is built on Python and Django, and styled using Bootstrap
& Bootswatch. Begin first by cloning a copy of this repository or
downloading a .zip file.

    $ git clone http://github.com/jrouly/research-questions.git
    $ cd research-questions

To get started, you will need to install a number of Python and MySQL
dependencies. On Ubuntu, that might look like:

    $ sudo apt-get install python-dev
    $ sudo apt-get install mysql-server mysql-client
    $ sudo apt-get install libmysqlclient-dev
    $ sudo apt-get install libsasl2-2 libsasl2-dev
    $ sudo apt-get install libldap2-dev libldap-2.4-2
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

### Remaining configurations

Start by copying the `config.py.template` file to `config.py`.

    $ cp config/config.py.template config/config.py

Now that you have your own configurations file, you can set the remaining
configs. Each directive is documented in the `config.py.template` file
itself.

#### User access

This application is set up to allow user access from LDAP or CAS
authentication backends. It's also easy to use simple Django model
authentication, but there is no way to register users other than
manual administrative intervention.

Set `AUTH_MODE` to either `CAS` or `LDAP`. Depending on what you set,
fill in the remaining authentication settings. CAS provides the simplest
login model.


#### Secret file

You will additionally need to define a `secret.py` file in the
`researchquestions` directory. This must contain the following directives:

    SECRET_KEY  = " ... "
    DB_NAME     = " ... "
    DB_USER     = " ... "
    DB_PASSWORD = " ... "
    DB_HOST     = " ... "

You can create the file from an existing template by copying the
`secret.py.template` file.

    $ cp researchquestions/secret.py.template researchquestions/secret.py

### Static files

Once you get all the settings set up, execute the following command
to generate the proper static directories.

    $ python manage.py collectstatic

#### Feedback file

Feedback is currently stored in a writeable text file for simplicity.
Enable write access to that file.

    $ mkdir media
    $ touch media/feedback.txt
    $ chmod 600 media/feedback.txt

Make sure that `media` is created in the same folder that contains `static`.

### Setting up the database

The database is one of the most important parts of this application.
Without a database backend, no user data could be stored. By default, this
application is configured to use a MySQL or MariaDB backend, but any
standard database software can be used as a replacement. Make sure you've
set the credentials correctly in the `secret.py` file.

Begin by synchronizing the database.

    $ python manage.py syncdb

Then migrate the website schema.

    $ python manage.py migrate website


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

Note that this configuration requires the module `proxy_http` to be
installed and enabled. Enabling this module will differ from OS to OS, but
the command looks like this on Ubuntu:

    $ sudo a2enmod proxy_http
    $ sudo service apache2 restart

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

        location /media/ {
            alias /path/to/install/research-questions/researchquestions/media/;
        }

    }

Note that if your Django application is being hosted in a subdirectory of
the root server (eg. http://yourdomain.com/myapp/) then your config will
need to look like this:

    server {
        ...
        location /myapp/ {
            proxy_pass     http://127.0.0.1:8001/myapp/;
            proxy_redirect http://127.0.0.1:8001/myapp/ /myapp/;
            ...
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

### Starting the application server (nginx only)

If you use nginx to proxy pass to an application server on port 8001, you
will need to start that application server.

The project requirements include the `gunicorn` module, so let's use this.

    $ gunicorn researchquestions.wsgi -b 127.0.0.1:8001

To send the web server to the background (ie. run it as a daemon) use

    $ gunicorn researchquestions.wsgi -b 127.0.0.1:8001 -D

Make sure to execute this command in the same folder containing `manage.py`.

This step is not required for configurations using **only** Apache, since
those configurations use Apache to serve the entire Python application.
Note, however, that you will need to restart Apache entirely every time a
modification is made to the application / system.


## Application Structure

The Research Questions application has a simple, best-practices Django
project structure with highlights outlined below.

    .
    ├── config/                   # custom configurations directory
    ├── media/                    # for user uploaded media, should be r/w
    ├── researchquestions/        # main django project app
    │   ├── context_processors.py # allows configurations to be used in templates
    │   ├── secret.py             # secret_key and DB credentials. r/o
    │   ├── settings.py           # complete django configurations
    │   └── urls.py               # top level routing file
    ├── static/                   # static file storage
    └── website/                  # standard website application
        ├── cas_callbacks.py      # used to create users via CAS
        ├── filters/              # sub-application for question filtering
        │   ├── models.py         # filter models
        │   ├── urls.py           # filter URL routing
        │   └── views.py          # filter logic
        ├── forms.py              # forms used in the website
        ├── migrations/           # database migratiosn
        ├── models.py             # website data structures (questions, comments)
        ├── templates/            # HTML templates
        │   ├── helptext/         # modular help text directory
        │   │   ├── content/      # text of the help sections
        │   │   └── index/        # index files of the help sections
        │   └── layouts/          # abstract template files
        ├── templatetags/         # custom template tags used in the website
        ├── urls.py               # mid-level URL routing
        └── views.py              # main website logic

## Contributing

There are a few main places contribution or maintenance may need to occur,
so this documentation will attempt to address the interesting ones.

### Visual styling

All static website assets are stored in `/static/` under the appropriate
directory. The `/static/css/bootstrap.min.css` file is from Bootswatch, but
was customized to match with the GMU colorscheme. Feel free to replace this
with a compatible Bootstrap install.

Page structure is maintained by the Django templating system, so see
the top-level templates `base.html`, `footer.html`, and `navbar.hmtl` in
the directory `/website/templates/layouts/`.

### URL Engineering

All URL routing takes place in `urls.py` files within various apps. The top
level routing occurs in `/researchquestions/urls.py` and is delegated to
`/website/urls.py` and further to `/website/filters/urls.py`.

### Writing help text

The help file is modular. It is relatively simple to add and remove parts
that are necessary between installs. To add a new section of help contents,
add a new index file and content file in
`/website/templates/helptext/content` and
`/website/templates/helptext/index`. Then integrate these files within the
`/website/templates/help.html` top level template file.

### Moderation

Moderation and site administration are both done using the administrator
interface. Approved user accounts will have access to this web interface
and can moderate discussion from there.

## ToDo

* Find more features to add
* User registration?
