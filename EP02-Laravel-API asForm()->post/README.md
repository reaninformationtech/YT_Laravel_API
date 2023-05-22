# Build REST API with Laravel with Http::asForm()->post

## Create new project

```
composer create-project --prefer-dist laravel/laravel EP02-laravel-api
```
Then connect database 
```
DB_DATABASE=ep02_api
DB_USERNAME=root
DB_PASSWORD=xxxxxxx
```
Then create valet link 
```
valet link ep02-valet
```

Then install passport
```
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

Then config on Model : User
```
use Laravel\Passport\HasApiTokens;
```
Then go to => config/auth.php (Add guards)

```
    'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
```

Then Create Controller BaseController for respone message have two function 

```
php artisan make:controller API/BaseController

    public function sendResponse($result, $message)
        {
            $response = [
                'success' => true,
                'data'    => $result,
                'message' => $message,
                'statusCode' =>'200'
            ];
            return response()->json($response, 200);
        }

        public function sendError($error, $errorMessages = [], $code = 404)
        {
            $response = [
                'success' => false,
                'message' => $error,
                'statusCode'=> $code
            ];
            if(!empty($errorMessages)){
                $response['data'] = $errorMessages;
            }
            return response()->json($response, $code);
        }
```
Then Create LoginController
```
php artisan make:controller API/Auth/LoginController

use Illuminate\Support\Facades\Http;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Controllers\API\BaseController as BaseController;

 public function login(LoginRequest $request)
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope' => '*',
        ]);
        $arr = json_decode($response, true);
        if ($response->successful()){
            $array = array(
                'token_type' => $arr['token_type'],
                'accessTokenExpiration' => $arr['expires_in'],
                'accessToken' => $arr['access_token'],
                'refreshToken' => $arr['refresh_token']
            );
            return $this->sendResponse($array, 'User info retrieved successfully.');
        }
        return $this->sendResponse($arr, 'User info retrieved successfully.');
    }

    public function refresh(RefreshTokenRequest $request)
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'refresh_token' => $request->refresh_token,
        ]);
        return $this->sendResponse($response->json(), 'User info retrieved successfully.');
    }
```
Then Create  Login Request
```
php artisan make:request  Auth/LoginRequest

public function rules()
{
    return [
        'username' => 'required',
        'password' => 'required',
    ];
}
```
Then create RegisterController 

```
php artisan make:controller API/Auth/RegisterController

use Illuminate\Support\Facades\Http;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Controllers\API\BaseController as BaseController;

```

Then Create RegisterRequest
```
php artisan make:request  Auth/RegisterRequest

public function rules()
{
    return [
        'username' => 'required',
        'password' => 'required',
    ];
}
```
Then Add router in routes/api.php 
```
use App\Http\Controllers\API\Auth\LoginController;

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

```