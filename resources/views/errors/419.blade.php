<x-error-page
    code="419"
    reason="Page expired"
    :title="content('system.e419_title', 'Your session expired.')"
    :message="content_rich('system.e419_message', 'For security, the page sat idle too long. Refresh it and submit again — nothing you typed was lost.')" />
