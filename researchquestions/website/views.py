from website.models import Question, Comment, Reply
from website.forms import QuestionForm
from django.conf import settings
from django.shortcuts import render_to_response, get_object_or_404
from django.shortcuts import render
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.template import RequestContext
from django.http import HttpResponseRedirect

import requests

# Create your views here.
def instructions(request):
    return render_to_response('instructions.html', {
    },
    )

@login_required
def submit_question(request):
    if request.method == 'POST':
        form = QuestionForm(request.POST)
        if form.is_valid():
            question = form.save(commit=False)
            question.user = User.objects.get(id=request.user.id)
            question.save()
            return HttpResponseRedirect('/')
    else:
        form = QuestionForm()

    return render_to_response('submit_question.html', {
        'form' : form,
    },
    RequestContext(request),
    )

@login_required
def index(request):
    return render_to_response('index.html', {
    },
    )
