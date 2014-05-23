"""
Django settings for research-questions project.

For more information on this file, see
https://docs.djangoproject.com/en/1.6/topics/settings/

For the full list of settings and their values, see
https://docs.djangoproject.com/en/1.6/ref/settings/
"""

import secret

# Build paths inside the project like this: os.path.join(BASE_DIR, ...)
import os
BASE_DIR = os.path.dirname(os.path.dirname(__file__))


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/1.6/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = secret.SECRET_KEY

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = True

TEMPLATE_DEBUG = False

ALLOWED_HOSTS = ['127.0.0.1']

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
    'website.filters',
    'config',
)

MIDDLEWARE_CLASSES = (
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
    'cas.middleware.CASMiddleware',
)

ROOT_URLCONF = 'researchquestions.urls'

WSGI_APPLICATION = 'researchquestions.wsgi.application'

MEDIA_URL = '/media/'
MEDIA_ROOT = (os.path.join(BASE_DIR, 'media/'))
MEDIAFILES_DIRS = (
)

STATIC_URL = '/static/'
STATIC_ROOT = os.path.join(BASE_DIR, 'static/')
STATICFILES_DIRS = (
)

STATICFILES_FINDERS = (
    'django.contrib.staticfiles.finders.FileSystemFinder',
    'django.contrib.staticfiles.finders.AppDirectoriesFinder',
)

TEMPLATE_DIRS = (
    (os.path.join(BASE_DIR, 'templates/')),
    'templates',
)

TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.Loader',
    'django.template.loaders.app_directories.Loader',
)

TEMPLATE_CONTEXT_PROCESSORS = (
    'django.contrib.auth.context_processors.auth',
    'django.core.context_processors.debug',
    'django.core.context_processors.i18n',
    'django.core.context_processors.media',
    'django.core.context_processors.static',
    'django.core.context_processors.tz',
    'django.contrib.messages.context_processors.messages',
    'django.core.context_processors.request',

    'researchquestions.context_processors.branding',
)

# Database
# https://docs.djangoproject.com/en/1.6/ref/settings/#databases

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': secret.DB_NAME,
        'USER' : secret.DB_USER,
        'PASSWORD' : secret.DB_PASSWORD,
        'HOST' : secret.DB_HOST,
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


# Install-specific configurations.
from config import config

DICTIONARY_ADJECTIVES = (os.path.join(STATIC_ROOT, config.DICTIONARY_ADJECTIVES))
DICTIONARY_NOUNS = (os.path.join(STATIC_ROOT, config.DICTIONARY_NOUNS))
PAGE_TITLE_PREFIX = config.PAGE_TITLE_PREFIX
ORGANIZATION = config.ORGANIZATION
ORGANIZATION_URL = config.ORGANIZATION_URL
ORGANIZATION_EMAIL_DOMAIN = config.ORGANIZATION_EMAIL_DOMAIN
BRANDING = config.BRANDING
AUTH_MODE = config.AUTH_MODE



# Authentication
# http://pythonhosted.org/django-auth-ldap

LOGIN_URL = '/login'
LOGOUT_URL = '/logout'
LOGIN_REDIRECT_URL = '/'

AUTHENTICATION_BACKENDS = (
    'django.contrib.auth.backends.ModelBackend',
)

if AUTH_MODE.lower() == 'cas':
    # CAS authentication settings
    CAS_SERVER_URL = config.CAS_SERVER_URL
    CAS_LOGOUT_COMPLETELY = True
    CAS_PROVIDE_URL_TO_LOGOUT = True

    AUTHENTICATION_BACKENDS += (
        'cas.backends.CASBackend',
    )

    CAS_RESPONSE_CALLBACKS = (
        'website.cas_callbacks.create_user',
    )

if AUTH_MODE.lower() == 'ldap':
    # LDAP authentication settings
    import ldap

    AUTHENTICATION_BACKENDS = (
        'django_auth_ldap.backend.LDAPBackend',
        'django.contrib.auth.backends.ModelBackend',
    )

    AUTH_LDAP_SERVER_URI = config.AUTH_LDAP_SERVER_URI  # server url
    AUTH_LDAP_BIND_DN = config.AUTH_LDAP_BIND_DN        # bind DN
    AUTH_LDAP_BIND_AS_AUTHENTICATING_USER = True            # use the user
    AUTH_LDAP_USER_DN_TEMPLATE = config.AUTH_LDAP_USER_DN_TEMPLATE
    AUTH_LDAP_GLOBAL_OPTIONS = {                            # ignore UAC cert.
        ldap.OPT_X_TLS : ldap.OPT_X_TLS_DEMAND,
        ldap.OPT_X_TLS_REQUIRE_CERT : ldap.OPT_X_TLS_NEVER,
    }

    AUTH_LDAP_USER_ATTR_MAP = {
        "first_name": "givenName",
        "last_name": "sn",
        "email": "mail"
    }

    AUTH_LDAP_ALWAYS_UPDATE_USER = True

