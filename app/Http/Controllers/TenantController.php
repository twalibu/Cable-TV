<?php

namespace App\Http\Controllers;

use Mail;
use Crypt;
use Alert;
use Carbon;
use Sentinel;
use Redirect;

use App\User;
use App\Role;
use App\Cable;
use App\Client;
use App\Tenant;
use App\Region;
use App\CableType;
use App\TenantSMS;
use App\CableAlert;
use App\TenantAddress;
use App\TenantContact;
use App\Http\Requests;
use App\TenantSchedule;
use App\SubscriptionType;
use App\SubscriptionAlert;
use App\TenantSubscription;


use Centaur\AuthManager;
use Illuminate\Http\Request;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;
use Cartalyst\Sentinel\Users\IlluminateUserRepository;
use App\Http\Requests\TenantFormRequest as TenantFormRequest;
use App\Http\Requests\TenantEditFormRequest as TenantEditFormRequest;
use App\Http\Requests\TenantAdminFormRequest as TenantAdminFormRequest;
use App\Http\Requests\TenantContactFormRequest as TenantContactFormRequest;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager)
    {
        // You must have admin access to proceed
        $this->middleware('sentry.auth');

        // Dependency Injection
        $this->userRepository = app()->make('sentinel.users');
        $this->authManager = $authManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = Tenant::all();

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $regions = Region::all();
        $types = SubscriptionType::all();

        return view('tenants.create', compact('regions', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin($id)
    {
        $tenant = Tenant::findorfail($id);

        return view('tenants.admin', compact('tenant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contact($id)
    {
        $tenant = Tenant::findorfail($id);

        return view('tenants.contact', compact('tenant'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAdmin(TenantAdminFormRequest $request)
    {
        // Assemble registration credentials and attributes
        $user_password = str_random(8);
        $credentials = [
            'email' => trim($request->get('email')),
            'password' => $user_password,
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'tenant_id' => $request->get('tenant'),
        ];
        $activate = (bool)(false);

        // Attempt the registration
        $result = $this->authManager->register($credentials, $activate);

        if ($result->isFailure()) {
            $tenant = Tenant::findorfail($request->tenant);

            Alert::error('New Tenant Admin Not Registered!');

            return redirect()->action(
                'TenantController@show', ['id' => $tenant->id]
            );
        }

        // Do we need to send an activation email?
        if (!$activate) {
            $email = $result->user->email;
            $code = $result->activation->getCode();
            $last_name = $result->user->last_name;

            $data = [
                'code' => $code,
                'email' => $email,
                'last_name' => $last_name,
                'password' => $user_password,
            ];

            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('mails.tenants.registration', $data, function($message) use ($email)
            {
                $message
                    ->from('no-reply@cable-alert.pro', 'Cable Alert')
                    ->to($email)
                    ->subject('Cable Alert | Account Activation');
            });
        }

        // Assign User Roles
        foreach ($request->get('roles', []) as $slug => $id) {
            $role = Sentinel::findRoleBySlug($slug);
            if ($role) {
                $role->users()->attach($result->user);
            }
        }

        $tenant = Tenant::findorfail($request->tenant);

        Alert::success('New Tenant Admin Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );      
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postContact(TenantContactFormRequest $request)
    {
        /* Save Tenant Contact Details */
        $contact = new TenantContact;

        $contact->first_name        = $request->first_name;
        $contact->last_name         = $request->last_name;
        $contact->position          = $request->position;
        $contact->phone_number      = $request->phone_number;
        $contact->email             = $request->email;
        $contact->tenant_id         = $request->tenant;

        $contact->save();

        Alert::success('New Tenant Contact Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $request->tenant]
        );      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TenantFormRequest $request)
    {
        /* Save Tenant */
        $tenant = new Tenant;

        $tenant->name       = $request->name;
        $tenant->slug       = $request->slug;

        $tenant->save();

        /* Compute & Save Subscription & Alert */
        $type = SubscriptionType::findorfail($request->subscription);
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addMonths($type->duration);

        $subscription = new TenantSubscription;

        $subscription->subscription_id      = $type->id;
        $subscription->start_date           = $start_date;
        $subscription->end_date             = $end_date;
        $subscription->tenant_id            = $tenant->id;

        $subscription->save();

        $alert = new SubscriptionAlert;

        $alert->alert_one           = Carbon::parse($subscription->end_date)->subDays(21);
        $alert->alert_two           = Carbon::parse($subscription->end_date)->subDays(14);
        $alert->alert_three         = Carbon::parse($subscription->end_date)->subDays(7);
        $alert->subscription_id     = $subscription->id;

        $alert->save();

        /* Save Tenant Address */
        $address = new TenantAddress;

        $address->address           = $request->address;
        $address->region_id         = $request->region;
        $address->tenant_id         = $tenant->id;

        $address->save();

        /* Save Tenant SMS Details */
        $sms = new TenantSMS;

        $sms->username          = $request->smsusername;
        $sms->password          = Crypt::encrypt($request->smspassword);
        $sms->sender_name       = $request->sender;
        $sms->price             = $request->price;
        $sms->balance           = $request->balance;
        $sms->tenant_id         = $tenant->id;

        $sms->save();

        /* Save Tenant Contact Details */
        $contact = new TenantContact;

        $contact->first_name        = $request->first_name;
        $contact->last_name         = $request->last_name;
        $contact->position          = $request->position;
        $contact->phone_number      = $request->phone_number;
        $contact->email             = $request->email;
        $contact->tenant_id         = $tenant->id;

        $contact->save();

        /* Innitiate Tenant Schedule */
        $schedule = new TenantSchedule;

        $schedule->alert          = '07:00:00';
        $schedule->report          = '00:00:00';
        $schedule->tenant_id         = $tenant->id;

        $schedule->save();

        /* Innitiate Tenant Admin Role */
        $slug = $tenant->slug . 'administrator';
        Sentinel::getRoleRepository()->create(array(
            'name' => 'Administrator',
            'slug' => $slug,
            'tenant_id' => $tenant->id,
            'permissions' => array(
                'users.create'              => true,
                'users.update'              => true,
                'users.view'                => true,
                'users.destroy'             => true,
                'roles.create'              => true,
                'roles.update'              => true,
                'roles.view'                => true,
                'roles.destroy'             => true,
                'clients.create'            => true,
                'clients.update'            => true,
                'clients.view'              => true,
                'clients.destroy'           => true,
                'cables.create'           => true,
                'cables.update'           => true,
                'cables.view'             => true,
                'cables.destroy'          => true,
                'reports.access'            => true,
                'settings.access'           => true,
                'communications.access'     => true,
            )
        ));

        $user_password = str_random(8);

        // Assemble registration credentials
        $credentials = [
            'email' => trim($request->email),
            'password' => $user_password,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'tenant_id' =>$tenant->id,
        ];

        $activate = (bool)(false);

        // Attempt the registration
        $result = $this->authManager->register($credentials, $activate);

        if ($result->isFailure()) {
            return $result->dispatch;
        }

        // Assign Role to User
        $role = Sentinel::findRoleBySlug($slug);
        if ($role) {
            $role->users()->attach($result->user);
        }

        // Do we need to send an activation email?
        if (!$activate) {
            // Send the activation email
            $code = $result->activation->getCode();
            $email = $result->user->email;

            $email = $contact->email;
            $data = [
                'email'         => $contact->email,
                'first_name'    => $contact->first_name,
                'last_name'     => $contact->last_name,
                'position'      => $contact->position,
                'tenant'        => $tenant->name,
                'password'      => $user_password,
                'code'          => $code,
            ];

            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('mails.tenants.activation', $data, function($message) use($email)
            {
                $message
                    ->from('no-reply@cable-alert.pro')
                    ->to($email)
                    ->subject('Welcome Message | Cable Alert!');
            });
        }

        /* Send Welcome Message to User */
        $username = env('INFOBIP_USERNAME');
        $password = env('INFOBIP_PASSWORD');
        $sender = env('INFOBIP_SENDER');

        $message = 'Hello '. $request->first_name . ' ' . $request->last_name . '. Thank you for joining Cable Alert Platform.'. PHP_EOL .'Your Organization "' . $tenant->name .'" has been registered. Please Follow Instructions sent to your email to continue.' . PHP_EOL . 'From ' . $sender . ' Team';

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

        // Creating request body
        $requestBody = new SMSTextualRequest();
        $requestBody->setFrom($sender);
        $requestBody->setTo($request->phone_number);
        $requestBody->setText($message);

        // Executing request
        $response = $client->execute($requestBody);

        Alert::success('New Tenant Registered!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenant = Tenant::findorfail($id);

        $sms = TenantSMS::where('tenant_id', $tenant->id)->first();

        $username =  $sms->username;
        $password =  Crypt::decrypt($sms->password);

        // Initializing GetAccountBalance client with appropriate configuration
        $client = new GetAccountBalance(new BasicAuthConfiguration($username, $password));
        // Executing request
        $response = $client->execute();

        $amount = $response->getBalance();
        $currency = $response->getCurrency();

        return view('tenants.show', compact('tenant', 'amount', 'currency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $regions = Region::all();
        $tenant = Tenant::findorfail($id);

        return view('tenants.edit', compact('tenant', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TenantEditFormRequest $request, $id)
    {
        $tenant = Tenant::findorfail($id);

        $tenant->name       = $request->name;

        $tenant->save();

        $sms = TenantSMS::where('tenant_id', $id)->first();

        $sms->sender_name       = $request->sender;

        $sms->save();

        $address = TenantAddress::where('tenant_id', $id)->first();

        $address->region_id         = $request->region;
        $address->address           = $request->address;

        $address->save();

        $sms = TenantSMS::where('tenant_id', $id)->first();

        $sms->sender_name         = $request->sender;

        $sms->save();

        Alert::success('Tenant Edited Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant->id]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {  
        $tenant = Tenant::findorfail($id);

        $sms = TenantSMS::where('tenant_id', $id)->delete();

        $address = TenantAddress::where('tenant_id', $id)->delete();        

        $schedule = TenantSchedule::where('tenant_id', $id)->delete();

        $subscription = TenantSubscription::where('tenant_id', $id)->first();

        $subscription->alert->delete();

        $subscription->delete();

        $cables = Cable::where('tenant_id', $id)->get();

        foreach ($cables as $cable) {
            $cable->alert->delete();
            $cable->delete();
        }

        $contacts = TenantContact::where('tenant_id', $id)->get();

        foreach ($contacts as $contact) {
            $contact->delete();
        }

        $cableTypes = CableType::where('tenant_id', $id)->get();

        foreach ($cableTypes as $cableType) {
            $cableType->delete();
        }

        $clients = Client::where('tenant_id', $id)->get();

        foreach ($clients as $client) {
            $client->delete();
        }

        $users = User::where('tenant_id', $id)->get();

        foreach ($users as $user) {
            $user->delete();
        }

        $roles = Role::where('tenant_id', $id)->get();

        foreach ($roles as $role) {
            $role->delete();
        }

        $tenant->delete();

        Alert::success('Tenant Delete Successfully!');

        return Redirect::to('admin/tenants');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyContact($id)
    {  
        $contact = TenantContact::findorfail($id);

        $tenant = $contact->tenant->id;

        $contact->delete();

        Alert::success('Tenant Contact Delete Successfully!');

        return redirect()->action(
            'TenantController@show', ['id' => $tenant]
        );
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  string  $hash
     * @return \Illuminate\Http\Response
     */
    public function destroyAdmin($id)
    {
        // Fetch the user object
        //$id = $this->decode($hash);
        $user = $this->userRepository->findById($id);

        $tenant = $user->tenant->id;

        // Remove the user
        $user->delete();

        // All done
        $message = "{$user->email} has been removed.";

        Alert::success($message, 'User Removed');
        
        return redirect()->action(
            'TenantController@show', ['id' => $tenant]
        );
    }


}
