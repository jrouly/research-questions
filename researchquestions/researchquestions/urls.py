from django.conf.urls import patterns, include, url
from django.contrib import admin
from django.contrib import auth

admin.autodiscover()

handler404 = 'website.views.error_404'
handler500 = 'website.views.error_500'

urlpatterns = patterns('',

    #### Dynamic website pages
    url(r'^', include('website.urls')),
    url(r'^filter/', include('website.filters.urls')),
    
    #### Admin pages
    url(r'^admin/', include(admin.site.urls)),
)

urlpatterns += patterns('django.contrib.auth.views',
    #### AUTH PAGES ####
    url(r'^login$', 'login', {'template_name': 'login.html'},
        name='website_login'),
    url(r'^logout$', 'logout', {'next_page': '/'}, name='website_logout'),
)
