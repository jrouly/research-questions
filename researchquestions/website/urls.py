from django.conf.urls import patterns, include, url

urlpatterns = patterns('website.views',

    #### Dynamic website pages
    url(r'^$', 'index', name='homepage'),
    url(r'^public$', 'public', name='public_landing'),
    url(r'^feedback$', 'feedback', name='feedback'),
    url(r'^me$', 'view_user', name='my_questions'),
    url(r'^submit$', 'submit_question', name='submit_question'),
    url(r'^question/(?P<slug>[^\.]+)$', 'view_question', name='view_question'),
    url(r'^help$', 'help', name='help'),
    url(r'^uid/(?P<user_id>\w+)$', 'view_user', name='view_user'),

)
