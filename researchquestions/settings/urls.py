from django.conf.urls import patterns, include, url
from django.contrib import admin
from django.contrib import auth

admin.autodiscover()

urlpatterns = patterns('website.views',
    
    #### STATIC PAGES ####
    url(r'^instructions$', 'instructions', name='instructions'),

    #### DYNAMIC PAGES ####
    url(r'^submit$', 'submit_question', name='submit_question'),
    url(r'^feedback$', 'feedback', name='feedback'),
    url(r'^$', 'index', name='homepage'),
    url(r'^question/(?P<slug>[^\.]+)$', 'view_question', name='view_question'),
    url(r'^me$', 'my_questions', name='my_questions'),

    #### ADMIN PAGES ####
    url(r'^admin/', include(admin.site.urls)),
)

urlpatterns += patterns('django.contrib.auth.views',
    #### AUTH PAGES ####
    url(r'^login$', 'login', {'template_name': 'login.html'},
        name='website_login'),
    url(r'^logout$', 'logout', {'next_page': '/'}, name='website_logout'),
)
