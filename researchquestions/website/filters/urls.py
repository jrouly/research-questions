from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.filters.views',

    #### Available filters
    url(r'^class/(?P<class>\w+)$', 'filter_class', name='filter_class'),
    url(r'^date$', 'filter_date', name='filter_date'),
    url(r'^comments$', 'filter_comments', name='filter_comments'),

)
