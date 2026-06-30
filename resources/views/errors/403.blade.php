<x-error-page
    code="403"
    reason="Forbidden"
    :title="content('system.e403_title', 'You can’t open this.')"
    :message="content_rich('system.e403_message', 'This page is locked to your account. If you think that’s a mistake, get in touch and we’ll sort it out.')" />
