<x-mail::message>
# Welcome to PeopleDear!

{{ $inviterName }} has invited you to join PeopleDear as a **{{ $roleName }}**.

Click the button below to accept your invitation and create your account:

<x-mail::button :url="$url">
Accept Invitation
</x-mail::button>

This invitation will expire on **{{ $expiresAt }}**.

If you have any questions, please contact your administrator.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>