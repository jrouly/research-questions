from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.views',

    #### Available filters
    url(r'^section/(?P<section>\w+)$', 'index'),
    url(r'^date$', 'index', {'sort': 'date'}),
    url(r'^comments$', 'index', {'sort': 'comments'}),

    #### Invalid requests to redirect
#    url(r'^$', 'base_redirect'),
#    url(r'^section/?$', 'base_redirect'),
)
