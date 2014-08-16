from django.db import models
from django.utils import timezone
from datetime import datetime
from django.contrib.auth.models import User
from django.conf import settings
from django.core.validators import RegexValidator
import hashlib
import os

# Create your models here.
class Question( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField(max_length=1000)
    rating = models.IntegerField(default=0)
    section_regex = RegexValidator(regex = r'^[a-zA-Z]+ [0-9]{3} [0-9]{3}$')
    section = models.CharField(
        max_length=15,
        blank=True,
        validators = [section_regex]
    )

    class Meta:
        ordering = ["-rating"]

    def get_comments(self):
        comments = Comment.objects.filter(parent__pk=self.pk)
        return comments

    def preview_text(self):
        return '%s' % (self.text[:20])

    def get_absolute_url(self):
        from django.core.urlresolvers import reverse
        return reverse('website.views.view_question', args=[str(self.pk)])

    def anonymized(self):
        return anonymized( self )

    def __unicode__(self):
        return '%s [%s]: %s' % ( self.user, self.section, self.text[:20] )

class Comment( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Question', related_name='comments', related_query_name='comments')

    def get_replies(self):
        replies = Reply.objects.filter(parent__pk=self.pk)
        return replies

    def preview_text(self):
        return '%s' % (self.text[:20])

    def anonymized(self):
        return anonymized( self )

    def __unicode__(self):
        return '%s [%s]: %s' % ( self.user, self.parent.section, self.text[:20] )

class Reply( models.Model ):
    user = models.ForeignKey(User)
    date = models.DateTimeField(default=timezone.now())
    text = models.TextField()
    parent = models.ForeignKey('Comment')

    class Meta:
        verbose_name_plural = "replies"

    def preview_text(self):
        return '%s' % (self.text[:20])

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
        noun_hash = int(str(n)[:2])*3 # 2 digits *3 allows for 300 nouns
        adj_hash = int(str(n)[2:4])*2 # 1 digits *20 allows for 200 adjs
        # 300 * 200 = 60 000 total possible usernames

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
