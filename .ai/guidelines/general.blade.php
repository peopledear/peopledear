# General Guidelines

- Don't include any superfluous PHP Annotations, except ones that start with `@` for typing variables.

## Session Keys

**ALWAYS use the `SessionKey` enum for session key management:**

- Centralized session key definitions in `app/Enums/SessionKey.php`
- Type-safe and prevents typos
- Easy to find all session keys used in the application
- Use `SessionKey::KeyName->value` to get the string value

@boostsnippet('Using SessionKey Enum', 'php')
<?php

// ✅ CORRECT - Use SessionKey enum
session([SessionKey::CurrentOrganization->value => $organization->id]);
$organizationId = session(SessionKey::CurrentOrganization->value);

// ❌ WRONG - Magic strings
session(['current_organization' => $organization->id]);
$organizationId = session('current_organization');

@endboostsnippet

@boostsnippet('SessionKey Enum', 'php')
<?php

namespace App\Enums;

enum SessionKey: string
{
    case CurrentOrganization = 'current_organization';
    // Add more session keys as needed
}

@endboostsnippet
