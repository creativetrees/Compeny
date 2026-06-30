<x-error-page
    code="429"
    reason="Too many requests"
    :title="content('system.e429_title', 'Slow down a moment.')"
    :message="content_rich('system.e429_message', 'You’ve sent a lot of requests in a short time. Wait a few seconds, then try again.')" />
