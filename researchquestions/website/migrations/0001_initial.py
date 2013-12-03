# -*- coding: utf-8 -*-
from south.utils import datetime_utils as datetime
from south.db import db
from south.v2 import SchemaMigration
from django.db import models


class Migration(SchemaMigration):

    def forwards(self, orm):
        # Adding model 'Question'
        db.create_table(u'website_question', (
            (u'id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('user', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['website.User'])),
            ('date', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 12, 2, 0, 0))),
            ('text', self.gf('django.db.models.fields.TextField')(max_length=1000)),
            ('rating', self.gf('django.db.models.fields.IntegerField')()),
        ))
        db.send_create_signal(u'website', ['Question'])

        # Adding model 'Comment'
        db.create_table(u'website_comment', (
            (u'id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('user', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['website.User'])),
            ('date', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 12, 2, 0, 0))),
            ('text', self.gf('django.db.models.fields.TextField')()),
            ('parent', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['website.Question'])),
        ))
        db.send_create_signal(u'website', ['Comment'])

        # Adding model 'Reply'
        db.create_table(u'website_reply', (
            (u'id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('user', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['website.User'])),
            ('date', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 12, 2, 0, 0))),
            ('text', self.gf('django.db.models.fields.TextField')()),
            ('parent', self.gf('django.db.models.fields.related.ForeignKey')(to=orm['website.Comment'])),
        ))
        db.send_create_signal(u'website', ['Reply'])

        # Adding model 'User'
        db.create_table(u'website_user', (
            (u'id', self.gf('django.db.models.fields.AutoField')(primary_key=True)),
            ('userid', self.gf('django.db.models.fields.CharField')(max_length=15)),
            ('name', self.gf('django.db.models.fields.CharField')(max_length=100)),
            ('section', self.gf('django.db.models.fields.CharField')(max_length=5)),
            ('role', self.gf('django.db.models.fields.CharField')(max_length=20)),
            ('date', self.gf('django.db.models.fields.DateTimeField')(default=datetime.datetime(2013, 12, 2, 0, 0))),
        ))
        db.send_create_signal(u'website', ['User'])


    def backwards(self, orm):
        # Deleting model 'Question'
        db.delete_table(u'website_question')

        # Deleting model 'Comment'
        db.delete_table(u'website_comment')

        # Deleting model 'Reply'
        db.delete_table(u'website_reply')

        # Deleting model 'User'
        db.delete_table(u'website_user')


    models = {
        u'website.comment': {
            'Meta': {'object_name': 'Comment'},
            'date': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime(2013, 12, 2, 0, 0)'}),
            u'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'parent': ('django.db.models.fields.related.ForeignKey', [], {'to': u"orm['website.Question']"}),
            'text': ('django.db.models.fields.TextField', [], {}),
            'user': ('django.db.models.fields.related.ForeignKey', [], {'to': u"orm['website.User']"})
        },
        u'website.question': {
            'Meta': {'object_name': 'Question'},
            'date': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime(2013, 12, 2, 0, 0)'}),
            u'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'rating': ('django.db.models.fields.IntegerField', [], {}),
            'text': ('django.db.models.fields.TextField', [], {'max_length': '1000'}),
            'user': ('django.db.models.fields.related.ForeignKey', [], {'to': u"orm['website.User']"})
        },
        u'website.reply': {
            'Meta': {'object_name': 'Reply'},
            'date': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime(2013, 12, 2, 0, 0)'}),
            u'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'parent': ('django.db.models.fields.related.ForeignKey', [], {'to': u"orm['website.Comment']"}),
            'text': ('django.db.models.fields.TextField', [], {}),
            'user': ('django.db.models.fields.related.ForeignKey', [], {'to': u"orm['website.User']"})
        },
        u'website.user': {
            'Meta': {'object_name': 'User'},
            'date': ('django.db.models.fields.DateTimeField', [], {'default': 'datetime.datetime(2013, 12, 2, 0, 0)'}),
            u'id': ('django.db.models.fields.AutoField', [], {'primary_key': 'True'}),
            'name': ('django.db.models.fields.CharField', [], {'max_length': '100'}),
            'role': ('django.db.models.fields.CharField', [], {'max_length': '20'}),
            'section': ('django.db.models.fields.CharField', [], {'max_length': '5'}),
            'userid': ('django.db.models.fields.CharField', [], {'max_length': '15'})
        }
    }

    complete_apps = ['website']