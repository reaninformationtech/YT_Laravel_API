# How to Build REST API Laravel 10

1-/ __composer create-project --prefer-dist laravel/laravel laravel-api __ <br/>
    a- connect database <br/>
    b- config valet <br/>
2-/ composer require laravel/passport 
3-/ php artisan migrate 
4-/ php artisan passport:install

5-/ update Model 
    a- app/Models/User.php => use Laravel\Passport\HasApiTokens; 
    b- config/auth.php =>
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],

6-/ php artisan make:controller Api/AuthController 
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Validation\Rules;

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


7-/ update routes/api.php 
    a- Route::post('login', [AuthController::class, 'login']);
    b- Route::post('register', [AuthController::class, 'register']);


8-/ php artisan make:controller Api/ProductController

9-/ php artisan make:model Product -mR   
    a- create table 
    
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 3)->nullable();
            $table->timestamps();
        });
