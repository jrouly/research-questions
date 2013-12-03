from django.db import models
from datetime import datetime

# Create your models here.
class Question( models.Model ):
    user = models.ForeignKey('User')
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField(max_length=1000)
    rating = models.IntegerField(default=0)

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Comment( models.Model ):
    user = models.ForeignKey('User')
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField()
    parent = models.ForeignKey('Question')

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class Reply( models.Model ):
    user = models.ForeignKey('User')
    date = models.DateTimeField(default=datetime.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    def __unicode__(self):
        return '%s, %s' % ( self.user, self.date )

class User( models.Model ):
    userid = models.CharField(max_length=15)
    name = models.CharField(max_length=100)
    section = models.CharField(max_length=5)
    role = models.CharField(max_length=20)
    date = models.DateTimeField(default=datetime.now())
    
    def email(self):
        return self.userid + "@gmu.edu"

    def __unicode__(self):
        return self.name
