from django.db import models
from django.contrib import admin
from website.models import Question, Comment, Reply

class QuestionAdmin(admin.ModelAdmin):
    list_display = ('user',
                    'anonymized',
                    'date',
                    'preview_text')
    list_filter = ('section',)
    search_fields = ('user__username',
                    'user__first_name',
                    'user__last_name',
                    'text')

class CommentAdmin(admin.ModelAdmin):
    list_display = ('user',
                    'anonymized',
                    'date',
                    'preview_text')
    list_filter = ('parent__section',)
    search_fields = ('user__username',
                     'user__first_name',
                     'user__last_name',
                     'text')

class ReplyAdmin(admin.ModelAdmin):
    list_display = ('user',
                    'anonymized',
                    'date',
                    'preview_text')
    list_filter = ('parent__parent__section',)
    search_fields = ('user__username',
                     'user__first_name',
                     'user__last_name',
                     'text')

# Register your models here.
admin.site.register(Question, QuestionAdmin)
admin.site.register(Comment, CommentAdmin)
admin.site.register(Reply, ReplyAdmin)
