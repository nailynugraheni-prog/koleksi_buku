<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <h2>Login</h2>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR GLOBAL --}}
    @if($errors->any())
        <div>
            <ul>
                @foreach($errors->all() as $error)
                    <li style="color:red;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM LOGIN --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label>Username</label><br>
            <input 
                type="text" 
                name="username" 
                value="{{ old('username') }}" 
                required
            >
        </div>

        <br>

        <div>
            <label>Password</label><br>
            <input 
                type="password" 
                name="password" 
                required
            >
        </div>

        <br>

        <button type="submit">
            Login
        </button>

    </form>

</body>
</html>
