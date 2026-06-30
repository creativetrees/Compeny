<x-error-page
    code="401"
    reason="Unauthorized"
    :title="content('system.e401_title', 'Sign in to continue.')"
    :message="content_rich('system.e401_message', 'This page needs a verified session. Sign in, then head back to where you were going.')" />
