from website.models import Question, Comment, Reply

from django import forms
from django.db import models
from django.forms import ModelForm, Textarea

class QuestionForm( ModelForm ):
    class Meta:
        model = Question
        fields = ('text',)
        exclude = ('user','date','rating')
        localized_fields = ('date',)
        widgets = {
            'text':Textarea(attrs={'class': 'form-control',}),
        }

class CommentForm( ModelForm ):
    class Meta:
        model = Comment
        fields = ('text',)
        exclude = ('user','date','parent')
        localized_fields = ('date',)
        widgets = {
            'text':Textarea(attrs={
                    'class': 'form-control',
                    'rows': 5,
            }),
        }

class ReplyForm( ModelForm ):
    class Meta:
        model = Reply
        fields = ('text',)
        exclude = ('user','date','parent')
        localized_fields = ('date',)

class FeedbackForm( forms.Form ):
    text = forms.CharField(
        widget=forms.Textarea(attrs={'class':'form-control'}),max_length=1000)
