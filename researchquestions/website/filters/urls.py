from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.views',

    #### Available filters
    url(r'^section/(?P<section>[a-zA-Z]+ {0,1}[0-9]*)$', 'index'),
    url(r'^date$', 'index', {'sort': 'date'}),
    url(r'^comments$', 'index', {'sort': 'comments'}),

)
