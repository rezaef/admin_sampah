<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Google\Client as GoogleClient;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:30', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['display_name'],
            'display_name' => $data['display_name'],
            'username' => strtolower($data['username']),
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => 'user',
            'provider' => 'local',
            'points_balance' => 0,
        ]);

        $token = $user->createToken('mobile-app', ['mobile'])->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => $this->transformUser($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = strtolower(trim($data['identifier']));
        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [$identifier])
            ->orWhereRaw('LOWER(email) = ?', [$identifier])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Username/email atau password tidak sesuai.'], 422);
        }

        if ($user->role !== 'user') {
            return response()->json(['message' => 'Akun ini tidak dapat digunakan pada aplikasi mobile.'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app', ['mobile'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => $this->transformUser($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->transformUser($request->user())]);
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update(['email' => strtolower($data['email'])]);

        return response()->json([
            'message' => 'Email berhasil diperbarui.',
            'user' => $this->transformUser($user->fresh()),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Password saat ini tidak sesuai.'], 422);
        }

        $user->update(['password' => $data['password']]);

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    private function transformUser(User $user): array
    {
        return [
            'id' => $user->id,
            'display_name' => $user->display_name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'provider' => $user->provider,
            'points_balance' => $user->points_balance,
        ];
    }
    public function google(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'display_name' => ['nullable', 'string', 'max:100'],
        ]);

        $client = new GoogleClient([
            'client_id' => config('services.google.web_client_id'),
        ]);

        $payload = $client->verifyIdToken($data['id_token']);

        if (! $payload) {
            return response()->json([
                'message' => 'ID token Google tidak valid.',
            ], 401);
        }

        $googleId = $payload['sub'] ?? null;
        $email = strtolower($payload['email'] ?? $data['email'] ?? '');
        $displayName = $payload['name'] ?? $data['display_name'] ?? 'Pengguna';
        $emailVerified = (bool) ($payload['email_verified'] ?? false);

        if (! $googleId || ! $email) {
            return response()->json([
                'message' => 'Data akun Google tidak lengkap.',
            ], 422);
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $user) {
            $baseUsername = Str::slug(Str::before($email, '@'), '');
            $baseUsername = $baseUsername !== '' ? $baseUsername : 'user';
            $username = $baseUsername;
            $counter = 1;

            while (User::where('username', $username)->exists()) {
                $username = $baseUsername.$counter;
                $counter++;
            }

            $user = User::create([
                'name' => $displayName,
                'display_name' => $displayName,
                'username' => $username,
                'email' => $email,
                'password' => bcrypt(Str::random(40)),
                'role' => 'user',
                'provider' => 'google',
                'google_id' => $googleId,
                'points_balance' => 0,
                'email_verified_at' => $emailVerified ? now() : null,
            ]);
        } else {
            if ($user->role !== 'user') {
                return response()->json([
                    'message' => 'Akun ini tidak dapat digunakan pada aplikasi mobile.',
                ], 403);
            }

            $user->update([
                'name' => $user->name ?: $displayName,
                'display_name' => $displayName,
                'provider' => 'google',
                'google_id' => $googleId,
                'email_verified_at' => $user->email_verified_at ?: ($emailVerified ? now() : null),
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app', ['mobile'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => $this->transformUser($user->fresh()),
        ]);
    }
}
