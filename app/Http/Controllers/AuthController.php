<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use App\Helper;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {
            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['user_not_found'], self::CODE_NOT_FOUND);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], self::CODE_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], self::CODE_UNAUTHORIZED);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }

        return response()->json(compact('token'));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Response
     */
    public function logout()
    {
        $this->jwt->parseToken()->invalidate();

        return response()->json(['ok']);
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function me(Request $request)
    {
        return response()->json($this->jwt->user());
    }

    /**
     * Refresh a token.
     *
     * @return Response
     */
    public function refresh()
    {
        $token = $this->jwt->refresh($this->jwt->getToken());

        $response = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->jwt->factory()->getTTL() * 60,
        ];

        return response()->json($response);
    }

    /**
     * Envia um email para o endereço de email cadastrado com uma nova senha gerada automaticamente
     * e expirada, ou seja, o usuário será obrigado a trocar a senha no primeiro acesso.
     *
     * @return Response
     */
    public function forgotPassword(Request $request)
    {
        $this->validate($request, ['email'    => 'required|email']);
        $email = $request->input('email');

        // Localiza o usuário
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'user_not_found'], self::CODE_NOT_FOUND);
        }

        $newPassword = Helper::generatePassword();

        $user->password = Hash::make($newPassword);
        $user->expiration_date = Carbon::yesterday();

        $user->save();

        // TODO: Desenvolver rotina para enviar o email.
        // Retornando a senha aqui apenas para teste

        return response()->json(['newPassword' => $newPassword]);
    }

    /**
     * Change the User password
     *
     * @param  Request  $request
     * @return Response
     */
    public function changePassword(Request $request)
    {
        try {
            $this->validate($request, [
                'email'    => 'required|email',
                'password' => 'required',
                'newPassword' => 'required',
            ]);

            // Verifica o password atual
            if ($this->jwt->attempt($request->only('email', 'password')) === false) {
                return response()->json(['error' => 'wrong_password'], self::CODE_BAD_REQUEST);
            }

            $user = User::where('email', $request->input('email'))->first();
            $user->password = Hash::make($request->input('newPassword'));
            $user->save();

            return response()->json(['ok']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], self::CODE_INTERNAL_ERROR);
        }
    }
}
