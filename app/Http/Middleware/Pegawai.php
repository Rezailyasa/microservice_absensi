<?php

namespace App\Http\Middleware;

use App\Models\SettingRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Pegawai
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() != null) {
            $cek = SettingRole::where(['users_id' => Auth::user()->id, 'roles_id' => '5'])->first();
            if ($cek == null) {
                $response = [
                    'success' => false,
                    'message' => 'You are not allowed -> pegawai',
                ];
                return response()->json($response, 500);
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu',
            ];
            return response()->json($response, 500);
        }

        return $next($request);
    }
}
