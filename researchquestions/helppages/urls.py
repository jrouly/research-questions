from django.conf.urls import patterns, include, url

urlpatterns = patterns('helppages.views',

    #### Static help pages
    url(r'^$', 'help', name='help'),

)
