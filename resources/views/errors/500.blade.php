<x-error-page
    code="500"
    reason="Server error"
    title="{{ content('system.e500_title', 'Something broke on our end.') }}"
    message="{{ content('system.e500_message', 'That’s on us, not you. The team is alerted automatically — give it a moment, then try again.') }}" />
