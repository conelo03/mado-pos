<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MADO POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200" data-theme="light">
    <div class="min-h-screen flex items-center justify-center">
        <div class="card w-96 bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-3xl justify-center mb-6">MADO POS</h1>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            {{-- value="{{ old('email') }}" --}}
                            value="superadmin@example.com"
                            class="input input-bordered w-full"
                            required
                        >
                        @error('email') 
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-6">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            value="password"
                            class="input input-bordered w-full"
                            required
                        >
                        @error('password') 
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="btn btn-primary w-full"
                    >
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
