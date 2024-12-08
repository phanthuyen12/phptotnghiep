<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowedOrigins = ['http://localhost:5173', 'http://localhost:3000'];

        // Kiểm tra nếu Origin trong danh sách cho phép
        $origin = $request->headers->get('Origin');
        if (in_array($origin, $allowedOrigins)) {
            if ($request->isMethod('options')) {
                return response()->json([], 200)
                    ->header('Access-Control-Allow-Origin', $origin) // Sử dụng Origin đã được kiểm tra
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                    ->header('Access-Control-Allow-Credentials', 'true'); // Thêm header này
            }
        
            return $next($request)
                ->header('Access-Control-Allow-Origin', $origin) // Sử dụng Origin đã được kiểm tra
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true'); // Thêm header này
        }

        return $next($request); // Nếu Origin không được cho phép, chỉ xử lý yêu cầu bình thường
    }
}
