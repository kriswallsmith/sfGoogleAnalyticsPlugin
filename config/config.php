<?php

sfMixer::register('sfComponent', array('sfGoogleAnalyticsActionMixin', 'setGoogleAnalyticsParam'));
sfMixer::register('sfComponent', array('sfGoogleAnalyticsActionMixin', 'addGoogleAnalyticsVar'));
sfMixer::register('sfComponent', array('sfGoogleAnalyticsActionMixin', 'addGoogleAnalyticsCustomVar'));
sfMixer::register('sfComponent', array('sfGoogleAnalyticsActionMixin', 'addGoogleAnalyticsCustomVarToFlash'));

sfMixer::register('sfUser', array('sfGoogleAnalyticsUserMixin', 'addGoogleAnalyticsCustomVarToFlash'));
