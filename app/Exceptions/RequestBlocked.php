<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Throw when a request is blocked for security reasons (abuse, suspicious
 * pattern, manual ban). Renders the dedicated dark "Access blocked" page with a
 * support reference, and is reported to the log so the team can correlate.
 *
 *   throw new \App\Exceptions\RequestBlocked('Suspicious request pattern');
 */
class RequestBlocked extends \Exception
{
    public string $reference;

    public function __construct(
        public string $reason = 'Suspicious request pattern',
        string $message = 'Request blocked by security policy.'
    ) {
        parent::__construct($message);

        $this->reference = 'CTG-'.Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4));
    }

    /** Custom HTML response (Laravel auto-renders exceptions with render()). */
    public function render(Request $request)
    {
        return response()->view('errors.security', [
            'reason' => $this->reason,
            'ref'    => $this->reference,
        ], 403);
    }

    /** Extra context in the log line for incident correlation. */
    public function context(): array
    {
        return [
            'security_ref' => $this->reference,
            'reason'       => $this->reason,
            'ip'           => request()->ip(),
            'path'         => request()->path(),
        ];
    }
}
