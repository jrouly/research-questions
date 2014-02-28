from django.conf import settings

def branding( request ):
    return {
        'page_title_prefix' : settings.PAGE_TITLE_PREFIX,
        'organization' : settings.ORGANIZATION,
        'organization_url' : settings.ORGANIZATION_URL,
    }
