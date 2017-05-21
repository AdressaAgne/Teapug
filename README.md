MVC made by Agne Ødegaard

# Installation 

1. Download the project
2. Set root folder as public
3. Edit database settings in App/Container/Config
4. Start making fancy websites

## Update

1. Download the project
2. Replace App/Container and public/index.php with the new files. Aditional files will be noted in patch notes. 


# Backend Documentation

## Logic and Basic Template - App/Controllers

View Logic is run here then passed as variables to the views.
```php
namespace App\Controllers;

use View;

class MainController extends Controller {
   public function index($params){
      $username = $params['username'];
      return View::make('index', [
         'var' => $username,
      ]);
   }
}
```
$params is $_GET and $_POST merged together
To make a JSON API just return an array insted of a View.

## Setup a view - App/Routing/RouteSetup.php
### Get Requests
This wil run the index method in the MainController class.
```php
Direct::get("/", 'MainController@index');
```

Direct::[get, post, put, patch, delete, debug](url, [controller@method, controller, callable])

Example:

Direct::get('/', 'MainController@index')

Direct::get('/profole', 'MainController@profole')->auth()

url = /test/{var}/{optional?}

add a ? at the end of a variable to make it optional like {var?}

if you do not set a method, it will try to call the route as a method instead

Direct::get("/home", 'MainController');

this will try to call the home method in the MainController

for GET, POST, PATCH, PUT, DELETE at the same time (does not include ERROR)

Direct::all(url, callable);

Or if you want more then one method but not all

Direct::on([GET, POST, PATCH, PUT, DELETE, ERROR], url, callable);

Filters:

* ->cache(callable*) //if callable return true page will be cached or if callable = null
* ->auth()
* ->mod()
* ->admin()
* ->http_code(int) Will set the the http status code for the page
* ->before(callable($request))
* ->after(callable($request))


### Post Requests
This will run the submit method in the MainController class when a post request is made to /submit
it will pass an argument to submit with mail and text in an array.
```php
Direct::post("/submit/{mail}/{text}", 'MainController@submit');
```
By setting a ? after the variable name means its an optional variable and will therfor not throw a 404 if its not included in the url.  
```php
Direct::post("/submit/{mail}/{text?}", 'MainController@submit');
```
Or you could just use normal $_POST variables
```php
Direct::post("/submit", 'MainController@submit');
```

_Note:_ put, post, patch, delete requires a csrf token. 

### Other HTTP requests
```php
Direct::put("/page", 'controller@method');
Direct::delete("/page", 'controller@method');
Direct::patch("/page", 'controller@method');
Direct::err("404", 'controller@method');
```

### Auth for HTTP requests
By using ->Auth() this will require a user to be logged inn. ->admin() requeris the logged inn user to be an admin
```php
Direct::get("/profile", 'controller@method')->auth($callback);
Direct::get("/admin", 'controller@method')->admin($callback);
```

## Database (App/Database/Database)

### Init
Use the App/Config.php to enter your database login information
All Controllers extend DB, so you can do $this->query() instead.
### Queries
```php
// Basic query
DB::query($SQL, [$params], $class);
DB::query("SELECT name, username FROM users WHERE id = :id", ['id' => 3]);
DB::query("SELECT name, username FROM users WHERE id = :id", ['id' => 3], 'User');
DB::query("SELECT name, username FROM users", 'User']);

//Select
DB::select($table, [$rows...], [$where], $join = 'AND');
DB::select($table, [$rows...], [$where], $class = null);
DB::select('users', ['name', 'username'], ['id' => 3, 'id' => 4], 'OR');
DB::select('users', ['name', 'username'], 'users', ['id' => 3, 'id' => 4], 'Recipe');

// Select everything
DB::all($table, [$rows]);
DB::all('users', ['name', 'username']);

//Insert rows
DB::insert($table, [[$row => $value]]);
DB::insert('users', [['name' => 'Frank'],['name' => 'George']]);

//Update rows
DB::update($table, [$rows], [$where], $rowsjoin = '=', $wherejoin = 'AND');
DB::update('users', ['name' => 'ron'], ['name' => 'Frank']);

//Delete a row
DB::deleteWhere($table, $col = 'id', $val = 0);
DB::deleteWhere('users', 'id', 10);
```

### Creating a table / Migrations (App/Database/Migration)
```php
$db = new DB();

$db->createTable('users', [
   new PID(), // Primary Key ID
   new Row('username', 'varchar'),
   new Row('password', 'varchar'),
   new Row('mail', 'varchar'),
   new Timestamp(),
]);

new Row($name, $type, $default = null, $not_null = true, $auto_increment = false);
new Varchar($name, $default = null);
new Integer($name, $default = null);
new Boolean($name, $default = 0);
new Timestamp();
new PID();  // Primary ID
```

## Caching
The framework will cache all pages and store them as html files in _/assets/cache/_ with name cached_url_path.html, the cache time can be changed in config.php, defaults to 3600s/1h.


## Security
###  SQL injection & Secondary SQL injection
By using the DB class everything is escaped, so you dont need to worry about SQL injection, if you use this all the time you will be safe.
```php
DB::query("SELECT name, username FROM users WHERE id = :id", ['id' => 3])->fetchAll();
DB::select(['name', 'username'], 'users', ['id' => 3])->fetchAll();
```
### XSS Injection
Using {{ }} to echo out will add a htmlspecialchars() function around
```html
{{ $variable }}
```
Using {! !} will echo a raw string, without htmlspecialchars(). Be carefull with this one.
```html
{! $variable !}
```

### CSRF token
Cross-site Request Forgery token are added to prevent people from spamming post requests from other sites.
This will echo out a form with both _method and _token
```html
@form('/login', 'put', ['class' => 'login'])
   <input type="text" placeholder="username">   
   <input type="password" placeholder="password"> 
@formend()
```

Will output:

```html
<form action="/login" method="POST" class="login">
   <input type="hidden" name="_method" value="PUT">
   <input type="hidden" name="_token" value="ujbf23kd872niw9">
   <input type="text" placeholder="username">   
   <input type="password" placeholder="password"> 
</form>
```

This will echo out the csrf token
```html
   @csrf()
```

## Views and HTML
Views are stores in view/<theme>/view
Please don't write any logic in a view, use the controller and pas data to the view as variables.

All files in the current theme is accesable with @layout('file', [vars]), for an admin page use @panel('file', [vars])

```html
@layout('layout.head')

<h1>Basic intruction; how to use this.</h1>


<h2>Echo php stuff</h2>
{{ $var }}

<h2>Echo Raw Code</h2>
{!  $user !}

<h2>if</h2>

@if(1 == 1)

<h3>yay 1 = 1</h3>

@else

<h3>boo 1 != 1</h3>

@endif

<h2>foreach loop</h2>

@foreach($arr as $key => $value)

<div>
    <h3>{{ $key }}</h3>
    <p>{{ $value }}</p>
</div>

@endforeach


<h2>for loop</h2>

@for($i = 0; $i > count($arr); $i++)

<div>
    <h3>{{ $i }}</h3>
    <p>{{ $arr[$i] }}</p>
</div>

@endfor

// shorthand comment will output <!--- text --->


@layout('layout.foot')
```

### Global Variables

$assets is a global var that outputs the themes assets directory
```html
{{$assets}}
```
$source is a global var that outputs the subfolder directory
```html
{{$source}}
```
This is for use when you use the framework in a subfolder, should be use before all links anyway, $assets have the $source prepended to it already.

*others:*

* assets (string)
* source (string)
* user (User Object, currently logged in user)
* global (access GlobalController)

## Subfolder
To use the framework in a subfolder go to the .htaccess file and add your folder there, further instructions are in the file.
