# How to Build REST API Laravel 10

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
<p>upadte guards</p>
</body>
</html>

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
6-/ php artisan make:controller Api/AuthController  <br/>
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

    public function login(Request $request)
    {
        if (auth()->attempt($request->all())) {
            return response([
                'user' => auth()->user(),
                'access_token' => auth()->user()->createToken('authToken')->accessToken
            ], Response::HTTP_OK);
        }
        return response([
            'message' => 'This User does not exist'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response($user, Response::HTTP_CREATED);
    }

7-/ update routes/api.php </br>
````
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

10-/ update routes/api.php </br>
````
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


