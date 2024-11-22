<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Jiri</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        h1 {
            color: #007BFF;
        }

        span {
            display: block;
            margin-bottom: 20px;
        }

        div {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 400px;
        }

        h2 {
            color: #007BFF;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
    </style>
</head>

<body>
    <h1>{!! __('mail.recover_password_hello', ['firstname' => $user->firstname]) !!}</h1>
    <span>{!! __('mail.recover_password_ifnotyou') !!}
    </span>
    <div>
        <h2>{!! __('mail.recover_password_code') !!}
        </h2>
        <p>{!! $password->token !!}</p>
    </div>
</body
