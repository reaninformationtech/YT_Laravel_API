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


Then create RegisterController 

```
php artisan make:controller API/Auth/RegisterController

use App\Http\Requests\Auth\RegisterRequest;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController as BaseController;

public function register(RegisterRequest $request)
{
    DB::beginTransaction();
    try
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        DB::commit();
        return $this->sendResponse($user, 'Successfully created!.');

    } catch (Exception $ex)
    {
        DB::rollBack();
        abort(500, 'server.error');
    }
}

```

Then Create RegisterRequest
```
php artisan make:request  Auth/RegisterRequest

public function authorize(): bool
{
    return true;
}
public function rules(): array
{
    return [
        'email' => 'required|email|unique:users',
        'password' => 'required',
    ];
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
         return $this->sendResponse($arr, 'Server.error');
    }

    public function refresh(RefreshTokenRequest $request)
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'refresh_token' => $request->refresh_token,
        ]);
        return $this->sendResponse($response->json(), 'Retrieved successfully.');
    }
```
Then Create  Login Request
```
php artisan make:request  Auth/LoginRequest

public function authorize(): bool
{
    return true;
}


public function rules()
{
    return [
        'username' => 'required',
        'password' => 'required',
    ];
}
```
Then Create  RefreshToken Request
```
php artisan make:request  Auth/RefreshTokenRequest

public function authorize(): bool
{
    return true;
}

public function rules()
{
    return [
        'refresh_token' => ['required']
    ];
}

```

Then generate passport key 

```
php artisan passport:client --password

CLIENT_ID=xxxx
CLIENT_SECRET=2023

```


Then Add router in routes/api.php 
```
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('refreshtoken', [LoginController::class, 'refresh']);

```



Referent :https://laravel.com/docs/10.x/passport#requesting-password-grant-tokens

🌐 LET’S CONNECT 🌐 <br/>
↪ YouTube - https://www.youtube.com/@JoinCoder 
↪ TikTok - https://www.tiktok.com/@reantechnology <br/>
↪ Facebook - https://www.facebook.com/reaninformationtechnology <br/>
↪ Telegram - https://t.me/reanitofficial <br/>
↪ Telegram Document - https://t.me/reanitofficialsoftware <br/>
Telegram : https://t.me/reanitofficialsoftware <br/>