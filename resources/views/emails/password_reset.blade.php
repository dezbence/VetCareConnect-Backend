<x-mail::message>
# Üdvözli Önt a VetCareConnect!
<img src="{{ $message->embed(public_path().'/VetCareConnect_green.png') }}">

<x-mail::panel>
Az új jelszavának beállításához keresse fel a lenti linket vagy kattintson a gombra.
</x-mail::panel>

<x-mail::button :url="$url">
Új jelszó létrehozása
</x-mail::button>
Amennyiben a fenti gombra kattintás nem működik, a linket másolja ki a böngészőjébe. Ha a jelszócserét nem Ön kezdeményezte, akkor hagyja levelünket figyelmen kívül. Sikeres jelszócserét követően felhasználói nevével és új jelszavával ismét használhatja fiókját.<br>{{ $url }}

Köszönjük,<br>
VetCareConnect
</x-mail::message>

