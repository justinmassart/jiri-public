<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jiri</title>
</head>

<table width="100%" bgcolor="#f4f4f4" style="font-family: Arial, sans-serif; color: #333;">
    <tr>
        <td align="center">
            <h1 style="color: #007BFF;">{!! __('mail.hello', ['firstname' => $accessToken->contact->firstname]) !!}</h1>
            <h2 style="color: #007BFF;">{!! __('mail.jiri-login', ['name' => $accessToken->jiri->name]) !!}</h2>
            <span style="color: #007BFF;"><b>Votre token : {!! $accessToken->token !!}</b></span>
            <br>
            <a href="{!! "http://jiri.test/evaluator/login/$accessToken->token" !!}" style="color: #fff; background: #007BFF;">{!! __('mail.jiri-login-link') !!}</a>
        </td>
    </tr>
</table>

</html>
