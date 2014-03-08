from django.db import models
from django.utils import timezone
from datetime import datetime
from django.contrib.auth.models import User
from django.conf import settings
import hashlib
import os

# Create your models here.
class Question( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField(max_length=1000)
    rating = models.IntegerField(default=0)
    section = models.TextField(max_length=10, blank=False)

    class Meta:
        ordering = ["-rating"]

    def get_comments(self):
        comments = Comment.objects.filter(parent__pk=self.pk)
        return comments

    def get_absolute_url(self):
        from django.core.urlresolvers import reverse
        return reverse('website.views.view_question', args=[str(self.pk)])

    def anonymized(self):
        return anonymized( self )

    def __unicode__(self):
        return '%s (%s): %s' % ( self.user, self.rating, self.text[:50] )

class Comment( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Question')

    def get_replies(self):
        replies = Reply.objects.filter(parent__pk=self.pk)
        return replies

    def anonymized(self):
        return anonymized( self )

    def __unicode__(self):
        return '%s: %s' % ( self.user, self.text[:50] )

class Reply( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    class Meta:
        verbose_name_plural = "replies"

    def anonymized(self):
        return anonymized( self )

    def __unicode__(self):
        return '%s: %s' % ( self.user, self.text[:50] )


def anonymized( obj ):
        user_hash_raw = hashlib.sha512(
            obj.user.first_name +
            obj.user.last_name +
            obj.user.username )
        h = user_hash_raw.hexdigest()
        n = int(h, base=16)
        noun_hash = int(str(n)[:4])*2 # 4 digits *2 allows for 20 000 nouns
        adj_hash = int(str(n)[4:6])*8 # 2 digits *8 allows for 800 users

        adj = "Anonymous"
        adjs = open(settings.DICTIONARY_ADJECTIVES, 'r')
        for i, line in enumerate(adjs):
            if i == adj_hash:
                adj = line.title()
                break

        noun = "Student"
        nouns = open(settings.DICTIONARY_NOUNS, 'r')
        for i, line in enumerate(nouns):
            if i == noun_hash:
                noun = line.title()
                break

        return adj + " " + noun
