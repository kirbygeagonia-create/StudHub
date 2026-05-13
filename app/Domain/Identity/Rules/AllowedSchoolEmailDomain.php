<?php

namespace App\Domain\Identity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedSchoolEmailDomain implements ValidationRule
{
    /**
     * @param  array<int, string>|null  $domains  Override list; falls back to config.
     */
    public function __construct(private readonly ?array $domains = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            $fail('The :attribute must be a valid email address.');

            return;
        }

        $allowed = $this->allowedDomains();

        if ($allowed === []) {
            return;
        }

        $domain = strtolower(substr(strrchr($value, '@') ?: '@', 1));

        if (! in_array($domain, $allowed, true)) {
            $fail("Registration is restricted to school email addresses ({$this->humanList($allowed)}).");
        }
    }

    /**
     * @return array<int, string>
     */
    public static function configuredDomains(): array
    {
        $raw = (string) config('studhub.allowed_email_domains', '');

        $domains = array_filter(array_map(
            fn (string $d) => strtolower(trim($d)),
            explode(',', $raw),
        ));

        return array_values(array_unique($domains));
    }

    /**
     * @return array<int, string>
     */
    private function allowedDomains(): array
    {
        if ($this->domains !== null) {
            return array_values(array_filter(array_map(
                fn (string $d) => strtolower(trim($d)),
                $this->domains,
            )));
        }

        return self::configuredDomains();
    }

    /**
     * @param  array<int, string>  $domains
     */
    private function humanList(array $domains): string
    {
        return implode(', ', array_map(fn (string $d) => '@' . $d, $domains));
    }
}
