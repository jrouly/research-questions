from django.db import models
from django.contrib import admin
from website.models import Question, Comment, Reply

# Register your models here.
admin.site.register(Question)
admin.site.register(Comment)
admin.site.register(Reply)
