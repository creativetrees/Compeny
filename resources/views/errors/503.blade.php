<x-error-page
    code="503"
    reason="Service unavailable"
    title="We’re temporarily offline."
    message="The service is briefly unavailable — likely heavy load or a quick restart. Give it a moment and try again."
    :home="false">
    <x-slot:actions>
        <x-ui.button href="/">Try again</x-ui.button>
        <x-ui.button href="mailto:{{ $settings->contact_email ?? 'hello@creativetrees.group' }}" variant="ghost" :magnetic="false">Email us</x-ui.button>
    </x-slot:actions>
</x-error-page>
