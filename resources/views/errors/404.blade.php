<x-error-page
    code="404"
    reason="Not found"
    title="{{ content('system.e404_title', 'This page isn’t here.') }}"
    message="{{ content('system.e404_message', 'The page you’re after moved, was renamed, or never existed. Everything still standing is one click away.') }}" />
