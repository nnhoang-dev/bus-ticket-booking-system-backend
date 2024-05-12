<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div>Mã xác thực tài khoản của bạn là: {{ $otp }}</div>
    @if ($action == 'forgot-password')
        <div>Mã này dùng để xác thực cho chức năng quên mật khẩu</div>
    @else
        <div>Mã này dùng để xác thực cho chức năng đăng ký tài khoản</div>
    @endif
</body>

</html>
