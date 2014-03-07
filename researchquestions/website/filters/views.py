from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required

@login_required
def base_redirect(request):
    return redirect('website.views.index')

@login_required
def filter_section(request, section):
    return render(request, 'index.html', {
    },
    )

@login_required
def filter_date(request):
    return render(request, 'index.html', {
    },
    )

@login_required
def filter_comments(request):
    return render(request, 'index.html', {
    },
    )
