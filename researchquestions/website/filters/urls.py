from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.views',

    #### Available filters
    url(r'^section/(?P<section>[a-zA-Z]+ [0-9]{3} [0-9]{3})$', 'index',
        name='filter'),
    url(r'^(?P<sort>[a-z]*)$', 'index', name='sort'),

    url(r'^section/(?P<section>[a-zA-Z]+ [0-9]{3} [0-9]{3})/(?P<sort>[a-z]*)$',
        'index', name='filter'),

)
