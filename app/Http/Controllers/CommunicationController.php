<?php

namespace App\Http\Controllers;

use Alert;
use Crypt;
use Input;
use Sentinel;
use App\Group;
use App\Tenant;
use App\Client;
use App\TenantSMS;
use App\TenantContact;
use Illuminate\Http\Request;

use infobip\api\client\PreviewSms;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;
use App\Http\Requests\BulkCommunicationFormRequest as BulkCommunicationFormRequest;
use App\Http\Requests\GroupCommunicationFormRequest as GroupCommunicationFormRequest;
use App\Http\Requests\AdminCommunicationFormRequest as AdminCommunicationFormRequest;
use App\Http\Requests\ClientCommunicationFormRequest as ClientCommunicationFormRequest;


class CommunicationController extends Controller
{
    public function __construct()
    {
        // Admin Middleware
        $this->middleware('sentry.auth', ['only' => ['admin', 'postAdmin']]);

        // Admin Middleware
        $this->middleware('sentinel.access:communications.access', ['only' => ['client', 'postClient', 'sendBulk', 'sendGroup']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        $username =  env('INFOBIP_USERNAME');
        $password =  env('INFOBIP_PASSWORD');
        $sender =  env('INFOBIP_SENDER');

        // Initializing GetAccountBalance client with appropriate configuration
        $client = new GetAccountBalance(new BasicAuthConfiguration($username, $password));
        // Executing request
        $response = $client->execute();

        $amount = $response->getBalance();
        $currency = $response->getCurrency();
        $tenants = Tenant::all();
        
        return view('communications.admin.index',compact('amount', 'currency', 'tenants', 'sender'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postAdmin(AdminCommunicationFormRequest $request)
    {
        $username =  env('INFOBIP_USERNAME');
        $password =  env('INFOBIP_PASSWORD');
        $sender =  env('INFOBIP_SENDER');

        $message = $request->message . PHP_EOL . 'By ' . $sender;
        
        $receiver = Input::get('receiver');

        if(in_array("all", $receiver)){
            $clients = TenantContact::all()->pluck('phone_number')->toArray();

            // Initializing SendSingleTextualSms client with appropriate configuration
            $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

            // Creating request body
            $requestBody = new SMSTextualRequest();
            $requestBody->setFrom($sender);
            $requestBody->setTo($clients);
            $requestBody->setText($message);

            // Executing request
            $response = $client->execute($requestBody);

            Alert::success('SMS Sent Successfully!');

            return back();
        }

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

        // Creating request body
        $requestBody = new SMSTextualRequest();
        $requestBody->setFrom($sender);
        $requestBody->setTo($receiver);
        $requestBody->setText($message);

        // Executing request
        $response = $client->execute($requestBody);

        Alert::success('SMS Sent Successfully!');

        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function client()
    {
        /// Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $clients = Client::where('tenant_id', $tenant)->get();
        $sms = TenantSMS::where('tenant_id', $tenant)->get()->first();
                
        return view('communications.tenant.index',compact('sms', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postClient(ClientCommunicationFormRequest $request)
    {
        // Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;
        $name = Tenant::where('id', $tenant)->value('name');
        $sms = TenantSMS::where('tenant_id', $tenant)->get()->first();

        $username =  $sms->username;
        $password =  Crypt::decrypt($sms->password);
        $sender = $sms->sender_name;

        $message = $request->message . PHP_EOL . 'By ' . $name;
        $receiver = Input::get('receiver');

        // Initializing PreviewSms client with appropriate configuration
        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
        $previewRequest = new PreviewRequest();
        $previewRequest->setText($message);
        $previewResponse = $client->execute($previewRequest);
        $noConfigurationPreview = $previewResponse->getPreviews()[0];
        $smsCount = $noConfigurationPreview->getMessageCount();

        if(in_array("all", $receiver)){
            $clients_1 = Client::where('tenant_id', $tenant)->pluck('phone_number')->toArray();
            $clients_2 = Client::where([['tenant_id', $tenant],['phone_number_2', '!=', 0]])->pluck('phone_number_2')->toArray();
            $clients = array_merge($clients_1, $clients_2);
            $clientCount = count($clients);

            if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
                // Initializing SendSingleTextualSms client with appropriate configuration
                $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

                // Creating request body
                $requestBody = new SMSTextualRequest();
                $requestBody->setFrom($sender);
                $requestBody->setTo($clients);
                $requestBody->setText($message);

                // Executing request
                $response = $client->execute($requestBody);

                $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

                $sms->save();

                Alert::success('SMS Sent Successfully!');

                return back();
            }else{
                Alert::error('SMS Not Sent!', 'Insuficient Balance');

                return back();
            }
        }

        $clientCount = count($receiver);

        if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
            // Initializing SendSingleTextualSms client with appropriate configuration
            $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

            // Creating request body
            $requestBody = new SMSTextualRequest();
            $requestBody->setFrom($sender);
            $requestBody->setTo($receiver);
            $requestBody->setText($message);

            // Executing request
            $response = $client->execute($requestBody);

            $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

            $sms->save();

            Alert::success('SMS Sent Successfully!');

            return back();
        }else{
            Alert::error('SMS Not Sent!', 'Insuficient Balance');

            return back();
        }        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulk(BulkCommunicationFormRequest $request)
    {
        /// Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $name = Tenant::where('id', $tenant)->value('name');

        $sms = TenantSMS::where('tenant_id', $tenant)->get()->first();

        $username =  $sms->username;
        $password =  Crypt::decrypt($sms->password);
        $sender = $sms->sender_name;

        $message = $request->message . PHP_EOL . 'By ' . $name;
        $receiver = Input::get('receiver');

        // Initializing PreviewSms client with appropriate configuration
        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
        $previewRequest = new PreviewRequest();
        $previewRequest->setText($message);
        $previewResponse = $client->execute($previewRequest);
        $noConfigurationPreview = $previewResponse->getPreviews()[0];
        $smsCount = $noConfigurationPreview->getMessageCount();

        if(in_array("all", $receiver)){
            $groups = Group::where('tenant_id', $tenant)->get();

            $clients = [];

            foreach ($groups as $group) {
                foreach ($group->contacts as $contact) {
                    $clients = array_prepend($clients, $contact->phone_number);
                }
            }

            $clientCount = count($clients);

            if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
                // Initializing SendSingleTextualSms client with appropriate configuration
                $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

                // Creating request body
                $requestBody = new SMSTextualRequest();
                $requestBody->setFrom($sender);
                $requestBody->setTo($clients);
                $requestBody->setText($message);

                // Executing request
                $response = $client->execute($requestBody);

                $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

                $sms->save();

                Alert::success('SMS Sent Successfully!');

                return back();
            }else{
                Alert::error('SMS Not Sent!', 'Insuficient Balance');

                return back();
            }
        }

        $clients = [];

        foreach ($receiver as $one) {
            $group = Group::findorfail($one);

            foreach ($group->contacts as $contact) {
                $clients = array_prepend($clients, $contact->phone_number);
            }
        }

        $clientCount = count($clients);

        if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
            // Initializing SendSingleTextualSms client with appropriate configuration
            $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

            // Creating request body
            $requestBody = new SMSTextualRequest();
            $requestBody->setFrom($sender);
            $requestBody->setTo($clients);
            $requestBody->setText($message);

            // Executing request
            $response = $client->execute($requestBody);

            $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

            $sms->save();

            Alert::success('SMS Sent Successfully!');

            return back();
        }else{
            Alert::error('SMS Not Sent!', 'Insuficient Balance');

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendGroup(GroupCommunicationFormRequest $request)
    {
        /// Retrieving Tenant Details
        $tenant = Sentinel::getUser()->tenant->id;

        $name = Tenant::where('id', $tenant)->value('name');

        $sms = TenantSMS::where('tenant_id', $tenant)->get()->first();

        $username =  $sms->username;
        $password =  Crypt::decrypt($sms->password);
        $sender = $sms->sender_name;

        $message = $request->message . PHP_EOL . 'By ' . $name;
        $receiver = Input::get('receiver');

        // Initializing PreviewSms client with appropriate configuration
        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
        $previewRequest = new PreviewRequest();
        $previewRequest->setText($message);
        $previewResponse = $client->execute($previewRequest);
        $noConfigurationPreview = $previewResponse->getPreviews()[0];
        $smsCount = $noConfigurationPreview->getMessageCount();

        if(in_array("all", $receiver)){
            $group = Group::findorfail($request->group);

            $clients = [];

            foreach ($group->contacts as $contact) {
                $clients = array_prepend($clients, $contact->phone_number);
            }

            $clientCount = count($clients);

            if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
                // Initializing SendSingleTextualSms client with appropriate configuration
                $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

                // Creating request body
                $requestBody = new SMSTextualRequest();
                $requestBody->setFrom($sender);
                $requestBody->setTo($clients);
                $requestBody->setText($message);

                // Executing request
                $response = $client->execute($requestBody);

                $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

                $sms->save();

                Alert::success('SMS Sent Successfully!');

                return back();
            }else{
                Alert::error('SMS Not Sent!', 'Insuficient Balance');

                return back();
            }
        }

        // Initializing SendSingleTextualSms client with appropriate configuration
        $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

        $clientCount = count($receiver);

        if (($clientCount * $smsCount * $sms->price) <= $sms->balance) {
            // Initializing SendSingleTextualSms client with appropriate configuration
            $client = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));

            // Creating request body
            $requestBody = new SMSTextualRequest();
            $requestBody->setFrom($sender);
            $requestBody->setTo($receiver);
            $requestBody->setText($message);

            // Executing request
            $response = $client->execute($requestBody);

            $sms->balance = $sms->balance - ($clientCount * $smsCount * $sms->price);

            $sms->save();

            Alert::success('SMS Sent Successfully!');

            return back();
        }else{
            Alert::error('SMS Not Sent!', 'Insuficient Balance');

            return back();
        }
    }
}
