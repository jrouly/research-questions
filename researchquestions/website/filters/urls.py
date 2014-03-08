from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.filters.views',

    #### Available filters
    url(r'^section/(?P<section>\w+)$', 'filter_section', name='filter_section'),
    url(r'^date$', 'filter_date', name='filter_date'),
    url(r'^comments$', 'filter_comments', name='filter_comments'),

    #### Invalid requests to redirect
#    url(r'^$', 'base_redirect'),
#    url(r'^section/?$', 'base_redirect'),
)
