from django.conf import settings

def branding( request ):
    return {
        'navbar' : settings.BRANDING,
        'page_title_prefix' : settings.PAGE_TITLE_PREFIX,
    }
