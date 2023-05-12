# How to Build REST API with Laravel 10

1-/ __composer create-project --prefer-dist laravel/laravel laravel-api__  <br/>
> Config <br/>
 >a- connect database <br/>
 >b- config valet <br/>

2-/ composer require laravel/passport <br/>
3-/ php artisan migrate <br/>
4-/ php artisan passport:install<br/>

5-/ update Model <br/>
> __Config__ <br/>
    a- app/Models/User.php => use Laravel\Passport\HasApiTokens; <br/>
    b- config/auth.php =>  <br/>

<html>
<body>
<p>update guards</p>
</body>
</html>

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],

6-/ php artisan make:controller API/BaseController  <br/>

    *** Create Function Login 

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



7-/ php artisan make:controller Auth/AuthController  <br/>
>`Import into controller `<br/>
>  <html>
>  <body>
>     <p>use App\Models\User; </p>
>     <p>use Illuminate\Support\Facades\Hash;</p>
>     <p>use Symfony\Component\HttpFoundation\Response;</p>
>     <p>use Illuminate\Validation\Rules;</p>
> </body>
> </html>

    *** Create Function Login 

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


7-/ update routes/api.php </br>
````
use App\Http\Controllers\Api\AuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
````
8-/ php artisan make:controller Api/ProductController</br>

    public function index(Request $request){
        $product=Product::all();
        return response($product);
    }

9-/ php artisan make:model Product -mR   </br>
## Create table 
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 3)->nullable();
            $table->timestamps();
        });

## Then => php artisan migrate 

10-/ update routes/api.php </br>
````
use App\Http\Controllers\Api\ProductController;

Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::resource('getproduct', ProductController::class);
});
````
## Data for testing
````
INSERT INTO products (name,price,created_at,updated_at) VALUES
	 ('iphone x',120.000,NULL,NULL),
	 ('Samsung',130.000,NULL,NULL);
````


