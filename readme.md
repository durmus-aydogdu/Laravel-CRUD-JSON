## CRUD on JSON File with Ajax and Twitter Bootstrap

Create/ Read/ Update and Delete records on JSON file with Ajax requests and Twitter Bootstrap.


## Installation

- Run `composer install`
- Run `php artisan key:generate` 

## Usage

- Run `php artisan serve`

## Read records from JSON

       public function index()
           {
               $users = [];
       
               if (Storage::exists('users.json')) {
                   $users = json_decode(Storage::get('users.json'));
               }
       
               return view('users')->with('users', $users);
           }



## Create record on JSON

       public function store(Request $request)
           {
               try {
                   $request->validate([
                       'name' => 'required|max:255',
                       'email' => 'required|email|max:255'
                   ]);
       
                   $users = [];
                   $userId = 1;
       
                   if (Storage::exists('users.json')) {
                       $users = json_decode(Storage::get('users.json'));
       
                       // Checking email is registered
                       $user = collect($users)
                           ->where('email', $request->email)
                           ->first();
       
                       if($user) {
                           throw new \Exception('The Email has already been taken');
                       }
       
                       // Finding last user id and increase 1
                       $lastUser = collect($users)->last();
                       $userId = $lastUser ? collect($users)->last()->id + 1 : 1;
                   }
       
                   $data = $request->only(['name', 'email']);
       
                   $data['created_at'] = date('Y-m-d H:i:s');
                   $data['updated_at'] = date('Y-m-d H:i:s');
       
                   $data['id'] = $userId;
       
                   array_push($users, $data);
       
                   Storage::put('users.json', json_encode($users));
       
                   return response()->json(200);
               }
               catch(\Exception $e) {
                   return response()->json($e->getMessage(), 500);
               }
           }


## Update record on JSON

        
        public function update(Request $request, $id)
            {
                try {
                    $request->validate([
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255'
                    ]);
        
                    $users = collect(json_decode(Storage::get('users.json')));
        
                    $data = ['id' => $request->id, 'name' => $request->name, 'email' => $request->email];
        
                    $users = $users->map(function($user) use ($data){
                        if ($user->id == $data['id']) {
                            $user->name = $data['name'];
                            $user->email = $data['email'];
                            $user->updated_at = date('Y-m-d H:i:s');
        
                            return $user;
                        }
        
                        return $user;
                    });
        
                    Storage::put('users.json', json_encode($users));
        
                    return response()->json(200);
                }
                catch(\Exception $e) {
                    return response()->json($e->getMessage(), 500);
                }
            }


## Delete record from JSON

       public function destroy($id)
           {
               try {
                   $users = json_decode(Storage::get('users.json'));
       
                   $user = collect($users)
                       ->where('id', $id)
                       ->first();
       
                   // Checking user exist or not
                   if ($user) {
                       foreach ($users as $key => $user) {
                           if ($user->id == $id) {
                               unset($users[$key]);
                           }
                       }
       
                       Storage::put('users.json', json_encode(array_values($users)));
       
                       return response()->json(200);
                   }
                   else {
                       throw new \Exception('User not found');
                   }
               }
               catch(\Exception $e) {
                   return response()->json($e->getMessage(), 500);
               }
           }
