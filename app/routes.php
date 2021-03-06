<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// app/routes.php`: 
 
Route::get('/', 'IndexController@getIndex');


//Trips

Route::group(array('prefix' => 'trips'), function(){
    Route::get('/', array('as' => 'trips', 'uses' => 'TripController@getList'));
    Route::get('/create', array('as' => 'trips.create.get', 'uses' => 'TripController@getCreate'));
    Route::post('/create', array('as' => 'trips.create.post', 'uses' => 'TripController@postCreate'));
    Route::get('/edit/{id}', array('as' => 'trips.edit.get', 'uses' => 'TripController@getEdit'));
    Route::post('/edit/{id}', array('as' => 'trips.edit.post', 'uses' => 'TripController@postEdit'));
    Route::get('/delete/{id}', array('as' => 'trips.delete.get', 'uses' => 'TripController@getDelete'));
    Route::post('/delete/{id}', array('as' => 'trips.delete.post', 'uses' => 'TripController@postDelete'));
});



Route::group(array('prefix' => 'itineraries'), function(){
    Route::get('/', array('as' => 'itineraries', 'uses' => 'ItineraryController@getList'));
    Route::get('/create', array('as' => 'itineraries.create.get', 'uses' => 'ItineraryController@getCreate'));
    Route::post('/create', array('as' => 'itineraries.create.post', 'uses' => 'ItineraryController@postCreate'));
    Route::get('/edit/{id}', array('as' => 'itineraries.edit.get', 'uses' => 'ItineraryController@getEdit'));
    Route::post('/edit/{id}', array('as' => 'itineraries.edit.post', 'uses' => 'ItineraryController@postEdit'));
    Route::get('/delete/{id}', array('as' => 'itineraries.delete.get', 'uses' => 'ItineraryController@getDelete'));
    Route::post('/delete/{id}', array('as' => 'itineraries.delete.post', 'uses' => 'ItineraryController@postDelete'));
});

    Route::group(array('prefix' => 'plan'), function(){
    Route::get('trip', array('as' => 'plan.trip', 'uses' => 'TripPlannerController@selectTrip'));
    Route::get('itinerary', array('as' => 'plan.trip.step2', 'uses' => 'TripPlannerController@selectItinerary'));
    Route::post('save', array('as' => 'plan.trip.save', 'uses' => 'TripPlannerController@saveTrip'));
});

    Route::get('/signup',
    array(
        'before' => 'guest',
         function() {
            return View::make('signup');
         }
 )
);
    Route::post('/signup', 
    array(
        'before' => 'csrf', 
        function() {

            $user = new User;
            $user->email    = Input::get('email');
            $user->password = Hash::make(Input::get('password'));

            # Rules 
            $rules= array('email'=> 'email|unique:users,email', 'password' => 'min:6');
            
            # Validation Fail
            $validator = Validator::make(Input::all(),$rules); 
            if($validator->fails())
            {
            return Redirect::to('/signup')->with('flash_message', 'Sign up failed; please try again.')->withInput();
            }
            
            # add the user 
            try {
                $user->save();
            }
            # Failure
            catch (Exception $e) {
                return Redirect::to('/signup')->with('flash_message', 'Sign up failed; please try again.')->withInput();
            }

            # Log the user in
            Auth::login($user);


            return Redirect::to('/itineraries')->with('flash_message', 'Welcome to Paris!');

        }
      )
);

Route::get('/login',
    array(
        'before' => 'guest',
        function() {
       
            return View::make('login');
        }
    )
);

    Route::post('/login', 
       array(
        'before' => 'csrf', 
         function() {

            $credentials = Input::only('email', 'password');

            if (Auth::attempt($credentials, $remember = true)) {
                return Redirect::intended('/')->with('flash_message', 'Welcome Back!');
            }
            else {
                return Redirect::to('/login')->with('flash_message', 'Log in failed; please try again.');
            }

            return Redirect::to('login');
        }
    )
);

     # /app/routes.php
      Route::get('/logout', function() {

      # Log out
      Auth::logout();

     # Send them to the homepage
     return Redirect::to('/');

});


# /app/routes.php
Route::get('/debug', function() {

    echo '<pre>';

    echo '<h1>environment.php</h1>';
    $path   = base_path().'/environment.php';

    try {
        $contents = 'Contents: '.File::getRequire($path);
        $exists = 'Yes';
    }
    catch (Exception $e) {
        $exists = 'No. Defaulting to `production`';
        $contents = '';
    }

    echo "Checking for: ".$path.'<br>';
    echo 'Exists: '.$exists.'<br>';
    echo $contents;
    echo '<br>';

    echo '<h1>Environment</h1>';
    echo App::environment().'</h1>';

    echo '<h1>Debugging?</h1>';
    if(Config::get('app.debug')) echo "Yes"; else echo "No";

    echo '<h1>Database Config</h1>';
    print_r(Config::get('database.connections.mysql'));

    echo '<h1>Test Database Connection</h1>';
    try {
        $results = DB::select('SHOW DATABASES;');
        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
        echo "<br><br>Your Databases:<br><br>";
        print_r($results);
    } 
    catch (Exception $e) {
        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
    }

    echo '</pre>';

});
Route::get('mysql-test', function() {

    # Print environment
    echo 'Environment: '.App::environment().'<br>';

    # Use the DB component to select all the databases
    $results = DB::select('SHOW DATABASES;');

    # If the "Pre" package is not installed, you should output using print_r instead
    print_r($results);

});
Route::get('/get-environment',function() {

    echo "Environment: ".App::environment();

});


Route::get('/hello', function()
{
	return View::make('hello');
});

