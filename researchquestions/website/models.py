from django.db import models
from django.utils import timezone
from datetime import datetime
from django.contrib.auth.models import User

# Create your models here.
class Question( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField(max_length=1000)
    rating = models.IntegerField(default=0)

    class Meta:
        ordering = ["rating"]

    def get_comments(self):
        comments = Comment.objects.filter(parent__pk=self.pk)
        return comments

    def get_absolute_url(self):
        from django.core.urlresolvers import reverse
        return reverse('website.views.view_question', args=[str(self.pk)])

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.text[:50] )

class Comment( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Question')

    def get_replies(self):
        replies = Reply.objects.filter(parent__pk=self.pk)
        return replies

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.text[:50] )

class Reply( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    class Meta:
        verbose_name_plural = "replies"

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.text[:50] )
