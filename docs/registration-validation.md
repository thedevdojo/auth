# Registration field lengths

Password/email registration validates names as strings when the name field is enabled, and validates emails as strings before email-format and uniqueness checks. Both fields have a default maximum of 191 characters. Invalid field types stop validation before the uniqueness query, and the limits apply to both steps of the registration form.

Configure `devdojo.auth.settings.registration_name_max_length` and `registration_email_max_length` to match the consuming application's user and social-provider columns. The conservative defaults also fit applications using Laravel's 191-character default string length. Applications with larger columns can raise these limits explicitly. Inputs are rejected rather than silently truncating account names or email addresses.

New social accounts and new social-provider links apply the same name/email length limits before either table is written. Social names remain nullable as before. Failures retain the callback's generic authentication error. Already linked provider identities can still sign in if their current display name exceeds the signup limit; this flow does not rewrite stored names.

Username generation belongs to the consuming application's user model. It must independently bound the generated username after transliteration and reserve room for collision suffixes. A bounded input name alone cannot guarantee a bounded generated slug. This package does not create usernames or change their generation policy.
