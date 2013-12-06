from django import template
register = template.Library()


@register.filter
def key( d, key_name ):
    """
      Filter: Grabs the value in a dictionary indexed by objects (or any
      data type).

      Usage:
        {{ dictionary|key:object }}
    """
    try:
        value = d[key_name]
    except KeyError:
        from django.conf import settings
        value = settings.TEMPLATE_STRING_IF_INVALID
    return value
