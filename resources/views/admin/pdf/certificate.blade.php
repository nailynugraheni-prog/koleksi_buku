<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat</title>
    <style>
        @page {
            margin: 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            border: 6px solid #2c3e50;
            padding: 40px 30px;
        }

        h1 {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .nama {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
        }

        .role {
            font-size: 20px;
            margin-bottom: 40px;
        }

        .footer {
            margin-top: 50px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h1>SERTIFIKAT</h1>
    <div class="subtitle">Diberikan kepada</div>

    <div class="name">{{ trim($user->name ?? $user->username ?? 'Nama Pengguna') }}</div>

        <div class="role">Peran / Role: {{ $roleName }}</div>

    <div class="footer">
        Dikeluarkan pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}.
    </div>

</body>
</html>