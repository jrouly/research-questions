from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.views',

    #### Dynamic website pages
    url(r'^$', 'index', name='homepage'),
    url(r'^feedback$', 'feedback', name='feedback'),
    url(r'^me$', 'my_questions', name='my_questions'),
    url(r'^submit$', 'submit_question', name='submit_question'),
    url(r'^question/(?P<slug>[^\.]+)$', 'view_question', name='view_question'),

)
