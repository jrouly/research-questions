from django.db import models
from datetime import datetime
from django.contrib.auth.models import User

# Create your models here.
class Question( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField(max_length=1000)
    rating = models.IntegerField(default=0)

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Comment( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField()
    parent = models.ForeignKey('Question')

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Reply( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )