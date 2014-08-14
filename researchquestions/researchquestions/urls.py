from django.conf.urls import patterns, include, url
from django.contrib import admin
from django.contrib import auth
from django.conf import settings

admin.autodiscover()

handler404 = 'website.views.error_404'
handler500 = 'website.views.error_500'

prefix = settings.URL_PREFIX.lstrip('/')
print( prefix )

urlpatterns = patterns('',

    #### Dynamic website pages
    url(r'^' + prefix + '/', include('website.urls')),
    url(r'^' + prefix + '/filter/', include('website.filters.urls')),

    #### Admin pages
    url(r'^' + prefix + '/admin/', include(admin.site.urls)),
)

urlpatterns += patterns('django.contrib.auth.views',
    #### AUTH PAGES ####
    url(r'^' + prefix + '/login$', 'login', name='website_login'),
    url(r'^' + prefix + '/logout$', 'logout', {'next_page': '/'}, name='website_logout'),
)
