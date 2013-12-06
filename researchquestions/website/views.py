from website.models import Question, Comment, Reply
from website.forms import QuestionForm, CommentForm, ReplyForm, FeedbackForm
from django.conf import settings
from django.core.paginator import Paginator, EmptyPage, PageNotAnInteger
from django.shortcuts import render_to_response, get_object_or_404
from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.template import RequestContext
from django.http import HttpResponseRedirect
from django.core.mail import EmailMessage
from django.utils import timezone
from django.forms import Textarea
from django.forms.models import modelformset_factory

import os
import requests

# Create your views here.
def instructions(request):
    return render(request, 'instructions.html', {
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

    return render(request, 'submit_question.html', {
        'form' : form,
    },
    )

@login_required
def index(request):
    questions =  Question.objects.all()
    paginator = Paginator(questions, 10) # show 25 questions per page

    page = request.GET.get('page')
    try:
        questions = paginator.page(page)
    except PageNotAnInteger:
        # if page is NaN, deliver first page
        questions = paginator.page(1)
    except EmptyPage:
        # if page is empty, deliver last page
        questions = paginator.page(paginator.num_pages)

    return render(request, 'index.html', {
        'questions' : questions,
        'page_range' : range(1, int(questions.paginator.num_pages)+1),
    },
    )

@login_required
def my_questions(request):
    questions =  Question.objects.filter(user__id=request.user.id)
    paginator = Paginator(questions, 10) # show 25 questions per page

    page = request.GET.get('page')
    try:
        questions = paginator.page(page)
    except PageNotAnInteger:
        questions = paginator.page(1)
    except EmptyPage:
        questions = paginator.page(paginator.num_pages)

    return render(request, 'my_questions.html', {
        'questions' : questions,
        'page_range' : range(1, int(questions.paginator.num_pages)+1),
    },
    )

@login_required
def feedback(request):
    if request.method == 'POST':
        form = FeedbackForm( request.POST )
        if form.is_valid():
            f = open(os.path.join(settings.MEDIA_ROOT, 'feedback.txt'), 'a')
            data = form.cleaned_data
            f.write( str(timezone.now()) )
            f.write( str('\n') )
            f.write( data['text'] )
            f.write( str('\n\n\n') )
            return HttpResponseRedirect('/')
    else:
        form = FeedbackForm()

    return render(request, 'feedback.html', {
        'form' : form,
    },
    )

@login_required
def view_question(request, slug):

    question = Question.objects.get(id=slug)
    comments = Comment.objects.filter(parent__pk=slug)
    current_user = User.objects.get(id=request.user.id)
    
    comment_form = CommentForm()

    reply_forms = {}
    for comment in comments:
        reply_forms[comment] = ReplyForm(prefix=comment.pk)

    if request.method == 'POST':
        # parse the comment form (Good Rating)
        if "comment_sub_a" in request.POST:
            comment_form = CommentForm(request.POST)
            if comment_form.is_valid():

                question.rating = question.rating + 1
                question.save()

                comment = comment_form.save(commit=False)
                comment.user = current_user
                comment.parent = question
                comment.save()
                return redirect('view_question', slug)

        # parse the comment form (Bad Rating)
        elif "comment_sub_b" in request.POST:
            comment_form = CommentForm(request.POST)
            if comment_form.is_valid():

                question.rating = question.rating - 1
                question.save()

                comment = comment_form.save(commit=False)
                comment.user = current_user
                comment.parent = question
                comment.save()
                return redirect('view_question', slug)

        # parse the reply forms
        elif "reply_sub" in request.POST:
            reply_forms = {}
            for comment in comments:
                reply_forms[comment] = ReplyForm(request.POST,prefix=comment.pk)
            for reply_form_index in reply_forms:
                reply_form = reply_forms.get( reply_form_index )
                if reply_form.is_valid():
                    reply = reply_form.save(commit=False)
                    reply.user = current_user
                    reply.parent = Comment.objects.get(id=reply_form.prefix)
                    reply.save()
            return redirect('view_question', slug)

    return render(request, 'question.html', {
        'question' : get_object_or_404(Question, pk=slug),
        'comment_form' : comment_form,
        'reply_forms' : reply_forms,
    },
    )
