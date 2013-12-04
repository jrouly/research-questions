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

    def get_comments(self):
        comments = Comment.objects.filter(parent__pk=self.pk)
        return comments

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Comment( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Question')

    def get_replies(self):
        replies = Reply.objects.filter(parent__pk=self.pk)
        return replies

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Reply( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )
