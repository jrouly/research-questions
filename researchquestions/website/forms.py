from website.models import Question, Comment, Reply

from django.db import models
from django.forms import ModelForm

class QuestionForm( ModelForm ):
    class Meta:
        model = Question
        fields = ('text',)
        exclude = ('user','date','rating')
        localized_fields = ('date',)

class CommentForm( ModelForm ):
    class Meta:
        model = Comment
        fields = ('text',)
        exclude = ('user','date','parent')
        localized_fields = ('date',)

class ReplyForm( ModelForm ):
    class Meta:
        model = Reply
        fields = ('text',)
        exclude = ('user','date','parent')
        localized_fields = ('date',)
