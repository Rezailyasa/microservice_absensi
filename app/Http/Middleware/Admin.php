<?php

namespace App\Http\Middleware;

use App\Models\SettingRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //cek apakah sudah login atau belum
        if (Auth::user() != null) {
            //cek apakah user tersebut merupakan roles id = 4 atau bukan
            //roles_id 4 merupakan admin untuk di kasus yang saya, silahkan temen2 disesuaikan id nya ya
            $cek = SettingRole::where(['users_id' => Auth::user()->id, 'roles_id' => '4'])->first();
            //kalau tidak ada setting role nya akan mengembalikan error 500
            if ($cek == null) {
                $response = [
                    'success' => false,
                    'message' => 'You are not allowed -> admin',
                ];
                return response()->json($response, 500);
            }
        } else {
            //kondisi kalau belum login
            $response = [
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu',
            ];
            return response()->json($response, 500);
        }
        //kalau semuanya terpenuhi akan ke langkah selanjutnya
        //jadi middleware itu untuk menjaga hal-yang tidak di inginkan
        return $next($request);

    }
}
