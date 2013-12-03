from django.conf.urls import patterns, include, url
from django.contrib import admin
from django.contrib import auth

admin.autodiscover()

urlpatterns = patterns('website.views',
    
    #### STATIC PAGES ####
    url(r'^instructions$', 'instructions', name='instructions'),
    url(r'^submit$', 'submit_question', name='submit_question'),

    url(r'^admin/', include(admin.site.urls)),
)
