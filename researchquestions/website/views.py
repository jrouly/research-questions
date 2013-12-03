from website.models import Question, Comment, Reply, User
from django.conf import settings
from django.shortcuts import render_to_response, get_object_or_404
from django.shortcuts import render
from django.contrib.auth.decorators import login_required

import requests

# Create your views here.
def instructions(request):
    return render_to_response('instructions.html', {
    },
    )

@login_required
def submit_question(request):
    return render_to_response('submit_question.html', {
    },
    )

@login_required
def index(request):
    return render_to_response('index.html', {
    },
    )
