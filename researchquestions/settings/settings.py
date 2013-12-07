"""
Django settings for research-questions project.

For more information on this file, see
https://docs.djangoproject.com/en/1.6/topics/settings/

For the full list of settings and their values, see
https://docs.djangoproject.com/en/1.6/ref/settings/
"""

DEVELOPMENT = True

# Build paths inside the project like this: os.path.join(BASE_DIR, ...)
import os
BASE_DIR = os.path.dirname(os.path.dirname(__file__))


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/1.6/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = '6$(!s156i38csj@a+@)t4731v@+ig800g8__^0+%8imy=yt3*^'

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = True

TEMPLATE_DEBUG = True

ALLOWED_HOSTS = []

APPEND_SLASH = True


# Application definition

INSTALLED_APPS = (
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
    'south',
    'website',
)

MIDDLEWARE_CLASSES = (
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
)

ROOT_URLCONF = 'settings.urls'

WSGI_APPLICATION = 'settings.wsgi.application'

MEDIA_URL = '/media/'
MEDIA_ROOT = (os.path.join(BASE_DIR, 'media/'))
MEDIAFILES_DIRS = (
)

STATIC_URL = '/static/'
if DEVELOPMENT:
    STATIC_ROOT = ''
    STATICFILES_DIRS = (
        (os.path.join(BASE_DIR, 'static/')),
    )
else:
    STATIC_ROOT = (os.path.join(BASE_DIR, 'static/'))
    STATICFILES_DIRS = (
    )

STATICFILES_FINDERS = (
    'django.contrib.staticfiles.finders.FileSystemFinder',
    'django.contrib.staticfiles.finders.AppDirectoriesFinder',
)

TEMPLATE_DIRS = (
    (os.path.join(BASE_DIR, 'templates/')),
    #'/www/http/research-questions/researchquestions/templates/',
    'templates',
)

TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
)

LOGIN_URL = '/login'
LOGOUT_URL = '/logout'
LOGIN_REDIRECT_URL = '/'

# Database
# https://docs.djangoproject.com/en/1.6/ref/settings/#databases

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'prm_django',
        'USER' : 'prm',
        'PASSWORD' : 'jXufqNJAZ6B4S43G',
        'HOST' : '',
        'PORT': '',
    }
}

# Internationalization
# https://docs.djangoproject.com/en/1.6/topics/i18n/

LANGUAGE_CODE = 'en-us'

TIME_ZONE = 'America/New_York'

USE_I18N = True

USE_L10N = True

USE_TZ = True

# Authentication
# http://pythonhosted.org/django-auth-ldap

import ldap
# Baseline configuration

# Keep ModelBackend around for per-user permissions and maybe a local
# superuser.
AUTHENTICATION_BACKENDS = (
    'django_auth_ldap.backend.LDAPBackend',
    'django.contrib.auth.backends.ModelBackend',
)

AUTH_LDAP_SERVER_URI = "ldaps://directory.gmu.edu:636"  # server url

AUTH_LDAP_BIND_DN = "ou=people,o=gmu.edu"               # bind DN

AUTH_LDAP_BIND_AS_AUTHENTICATING_USER = True            # use the user

AUTH_LDAP_USER_DN_TEMPLATE = "uid=%(user)s,ou=people,o=gmu.edu"

AUTH_LDAP_GLOBAL_OPTIONS = {                            # ignore UAC cert.
    ldap.OPT_X_TLS : ldap.OPT_X_TLS_DEMAND,
    ldap.OPT_X_TLS_REQUIRE_CERT : ldap.OPT_X_TLS_NEVER,
}

# Populate the Django user from the LDAP directory.
AUTH_LDAP_USER_ATTR_MAP = {
    "first_name": "givenName",
    "last_name": "sn",
    "email": "mail"
}
#AUTH_LDAP_USER_FLAGS_BY_GROUP = {
#    "is_active": "cn=active,ou=django,ou=groups,dc=example,dc=com",
#    "is_staff": "cn=staff,ou=django,ou=groups,dc=example,dc=com",
#    "is_superuser": "cn=superuser,ou=django,ou=groups,dc=example,dc=com"
#}
#AUTH_LDAP_PROFILE_FLAGS_BY_GROUP = {
#    "is_awesome": "cn=awesome,ou=django,ou=groups,dc=example,dc=com",
#}

# This is the default, but I like to be explicit.
AUTH_LDAP_ALWAYS_UPDATE_USER = True

# Use LDAP group membership to calculate group permissions.
#AUTH_LDAP_FIND_GROUP_PERMS = True

# Cache group memberships for an hour to minimize LDAP traffic
#AUTH_LDAP_CACHE_GROUPS = True
#AUTH_LDAP_GROUP_CACHE_TIMEOUT = 3600


# Auth Logging (off by default)
#import logging, logging.handlers
#logfile = "/tmp/django-ldap-debug.log"
#my_logger = logging.getLogger('django_auth_ldap')
#my_logger.setLevel(logging.DEBUG)
# 
#handler = logging.handlers.RotatingFileHandler(
#logfile, maxBytes=1024 * 500, backupCount=5)
# 
#my_logger.addHandler(handler)
