from django.contrib.auth.models import User

def create_user(tree):
    username = tree[0][0].text

    user, user_created = User.objects.get_or_create(username=username)
    profile, created = user.get_profile()

    profile.email = tree[0][1].text
    profile.position = tree[0][2].text
    profile.save()
