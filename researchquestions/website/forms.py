from website.models import Question, Comment, Reply

from django import forms
from django.db import models
from django.forms import ModelForm, Textarea, TextInput

class QuestionForm( ModelForm ):
    class Meta:
        model = Question
        fields = ('text','section',)
        exclude = ('user','date','rating')
        localized_fields = ('date',)
        labels = {
            'text' : 'Question Text',
            'section' : 'Course Section',
        }
        widgets = {
            'text':Textarea(attrs={
                    'class': 'form-control',
                    'placeholder': 'Question Text',
            }),
            'section': TextInput(attrs={
                    'class': 'form-control',
                    'placeholder': 'Course Section',
                    'pattern': '^[a-zA-Z]+ [0-9]{3} [0-9]{3}$',
            }),
        }

    # Format every course id as all caps.
    def clean_section(self):
        data = self.cleaned_data.get('section')
        if data is not None:
            data = data.upper()
        return data

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
        widgets = {
            'text':Textarea(attrs={
                    'class': 'form-control',
                    'rows': 5,
            }),
        }

class FeedbackForm( forms.Form ):
    text = forms.CharField(
        widget=forms.Textarea(attrs={'class':'form-control'}),max_length=1000)

class CourseSectionFilterForm( forms.Form ):
    section = forms.CharField(
        widget = forms.TextInput(attrs={
                    'class': 'form-control',
                    'placeholder': 'Course Section',
                    'pattern': '^[a-zA-Z]+ [0-9]{3} [0-9]{3}$',
                }), max_length = 10)
