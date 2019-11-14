<?php

namespace App\Http\Controllers;

use DB;
use Crypt;
use Carbon;
use App\Cable;
use App\Tenant;
use App\TenantSMS;
use App\Http\Requests;

use infobip\api\client\PreviewSms;
use infobip\api\client\SendSingleTextualSms;
use infobip\api\model\sms\mt\send\preview\Preview;
use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\send\preview\PreviewRequest;
use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;

class ScheduleController extends Controller
{
    /**
     * Send Notification & Emails
     *
     * @return Response
     */
    public function index()
    {
    	/* Alert 30 Days Before Expiration */
        $this->alertOne();

        /* Alert 7 Days Before Expiration */
        $this->alertTwo();

        /* Alert on Expiration Date */
        $this->expire();
    }

    public function alertOne()
    {
    	$now = Carbon::now()->format('H:i');
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $time = Carbon::parse($tenant->schedule->alert)->format('H:i');

            if($time == $now){
		        $currentDate = Carbon::today()->toDateString();

		        $cables = Cable::where('tenant_id', $tenant->id)->get();

			    foreach ($cables as $cable) {
			    	$subscription_expiration = Carbon::parse($cable->tenant->subscription->end_date)->toDateString();
			    	if($subscription_expiration >= $currentDate){
			    		$date = Carbon::parse($cable->alert->alert_one)->toDateString(); 

			    		if($currentDate == $date){
			    			/* Sms Credentials */
					        $username =  $cable->tenant->sms->username;
					        $password =  Crypt::decrypt($cable->tenant->sms->password);
					        $sender = $cable->tenant->sms->sender_name;
					        $phone = $cable->client->phone_number;
					        $phone2 = $cable->client->phone_number_2;

					        /* Messages */
					        $engMessage = 
					        	'Hello '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. This is a reminder that your cable subscription ('. $cable->type->name . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') will expire on ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Thank You - '. $cable->tenant->name;

					        $swaMessage = 
					        	'Habari Ndugu '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. Ujumbe huu nikukukumbusha ya kwamba '. $cable->type->name . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') itakwisha muda wake tarahe ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Asante - '. $cable->tenant->name;
					        
					        
							// Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($engMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $engSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						    if($sms->balance >= ($engSmsCount * $sms->price)){
						        // Creating request for English Text
					            $engRequest = new SMSTextualRequest();
					            $engRequest->setFrom($sender);
					            $engRequest->setTo($phone);
					            $engRequest->setText($engMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($engRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

					            $sms->save();
					        }

							// Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($swaMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $swaSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					        if($sms->balance >= ($swaSmsCount * $sms->price)){
					            // Creating request for Swahili Text
					            $swaRequest = new SMSTextualRequest();
					            $swaRequest->setFrom($sender);
					            $swaRequest->setTo($phone);
					            $swaRequest->setText($swaMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($swaRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

					            $sms->save();
					        }

				            if ($phone2) {
					         	if($cable->tenant->sms->balance >= ($engSmsCount * $cable->tenant->sms->price)){
					            	// Creating request for English Text
						            $engRequest = new SMSTextualRequest();
						            $engRequest->setFrom($sender);
						            $engRequest->setTo($phone2);
						            $engRequest->setText($engMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($engRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

						            $sms->save();
						        }

						        if($cable->tenant->sms->balance >= ($swaSmsCount * $cable->tenant->sms->price)){
						            // Creating request for Swahili Text
						            $swaRequest = new SMSTextualRequest();
						            $swaRequest->setFrom($sender);
						            $swaRequest->setTo($phone2);
						            $swaRequest->setText($swaMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($swaRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

						            $sms->save();
						        }
				            }
			    		}
			        }
			    }
			}
		}
    }

    public function alertTwo()
    {
    	$now = Carbon::now()->format('H:i');
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $time = Carbon::parse($tenant->schedule->alert)->format('H:i');

            if($time == $now){
		        $currentDate = Carbon::today()->toDateString();

		        $cables = Cable::where('tenant_id', $tenant->id)->get();

		        foreach ($cables as $cable) {
		        	$subscription_expiration = Carbon::parse($cable->tenant->subscription->end_date)->toDateString();
		        	if($subscription_expiration >= $currentDate){
			    		$date = Carbon::parse($cable->alert->alert_two)->toDateString(); 

			    		if($currentDate == $date){
			    			/* Sms Credentials */
					        $username =  $cable->tenant->sms->username;
					        $password =  Crypt::decrypt($cable->tenant->sms->password);
					        $sender = $cable->tenant->sms->sender_name;
					        $phone = $cable->client->phone_number;
					        $phone2 = $cable->client->phone_number_2;

					        /* Messages */
					        $engMessage = 
					        	'Hello '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. This is a reminder that your '. $cable->type->name  . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') will expire on ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Thank You - '. $cable->tenant->name;

					        $swaMessage = 
					        	'Habari Ndugu '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. Ujumbe huu nikukukumbusha ya kwamba '. $cable->type->name . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') itakwisha muda wake tarahe ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Asante - '. $cable->tenant->name;
					        
					        // Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($engMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $engSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						    if($sms->balance >= ($engSmsCount * $sms->price)){
						        // Creating request for English Text
					            $engRequest = new SMSTextualRequest();
					            $engRequest->setFrom($sender);
					            $engRequest->setTo($phone);
					            $engRequest->setText($engMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($engRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

					            $sms->save();
					        }

				            // Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($swaMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $swaSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					        if($sms->balance >= ($swaSmsCount * $sms->price)){
					            // Creating request for Swahili Text
					            $swaRequest = new SMSTextualRequest();
					            $swaRequest->setFrom($sender);
					            $swaRequest->setTo($phone);
					            $swaRequest->setText($swaMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($swaRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

					            $sms->save();
					        }

				            if ($phone2) {
					         if($cable->tenant->sms->balance >= ($engSmsCount * $cable->tenant->sms->price)){
					            	// Creating request for English Text
						            $engRequest = new SMSTextualRequest();
						            $engRequest->setFrom($sender);
						            $engRequest->setTo($phone2);
						            $engRequest->setText($engMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($engRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

						            $sms->save();
						        }

						        if($cable->tenant->sms->balance >= ($swaSmsCount * $cable->tenant->sms->price)){
						            // Creating request for Swahili Text
						            $swaRequest = new SMSTextualRequest();
						            $swaRequest->setFrom($sender);
						            $swaRequest->setTo($phone2);
						            $swaRequest->setText($swaMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($swaRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

						            $sms->save();
						        }
				            }
			    		}
			        }
			    }
			}
		}
    }

    public function expire()
    {
    	$now = Carbon::now()->format('H:i');
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $time = Carbon::parse($tenant->schedule->alert)->format('H:i');

            if($time == $now){
		        $currentDate = Carbon::today()->toDateString();

		        $cables = Cable::where('tenant_id', $tenant->id)->get();

		        foreach ($cables as $cable) {
		        	$subscription_expiration = Carbon::parse($cable->tenant->subscription->end_date)->toDateString();
		        	if($subscription_expiration >= $currentDate){
			    		$date = Carbon::parse($cable->alert->expiration_date)->toDateString(); 

			    		if($currentDate == $date){
			    			/* Sms Credentials */
					        $username =  $cable->tenant->sms->username;
					        $password =  Crypt::decrypt($cable->tenant->sms->password);
					        $sender = $cable->tenant->sms->sender_name;
					        $phone = $cable->client->phone_number;
					        $phone2 = $cable->client->phone_number_2;

					        /* Messages */
					        $engMessage = 
					        	'Hello '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. This is to notify you that your '. $cable->type->name . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') has expired today ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Thank You - '. $cable->tenant->name;

					        $swaMessage = 
					        	'Habari Ndugu '. 
					        	$cable->client->first_name . ' ' . $cable->client->middle_name . ' ' . $cable->client->last_name . 
					        	'. Ujumbe huu ni wa kukutaharifu ya kwamba '. $cable->type->name . ' (Cover Number: '. $cable->cover_number .' - '. $cable->coverage->name .' - Tsh ' . number_format($cable->total, 2) . '/= | ' . $cable->addition_detail . ') imekishwa muda wake leo tarahe ' . Carbon::createFromFormat('Y-m-d', $cable->alert->expiration_date)->toFormattedDateString() . '.' . PHP_EOL . 'Asante - '. $cable->tenant->name;
					        
					        // Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($engMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $engSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						    if($sms->balance >= ($engSmsCount * $sms->price)){
						        // Creating request for English Text
					            $engRequest = new SMSTextualRequest();
					            $engRequest->setFrom($sender);
					            $engRequest->setTo($phone);
					            $engRequest->setText($engMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($engRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

					            $sms->save();
					        }

				            // Initializing PreviewSms client with appropriate configuration
					        $client = new PreviewSms(new BasicAuthConfiguration($username, $password));
					        $previewRequest = new PreviewRequest();
					        $previewRequest->setText($swaMessage);
					        $previewResponse = $client->execute($previewRequest);
					        $noConfigurationPreview = $previewResponse->getPreviews()[0];
					        $swaSmsCount = $noConfigurationPreview->getMessageCount();

					        $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					        if($sms->balance >= ($swaSmsCount * $sms->price)){
					            // Creating request for Swahili Text
					            $swaRequest = new SMSTextualRequest();
					            $swaRequest->setFrom($sender);
					            $swaRequest->setTo($phone);
					            $swaRequest->setText($swaMessage);

				        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
					            $response = $clientile->execute($swaRequest);

					            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

					            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

					            $sms->save();
					        }

				            if ($phone2) {
					         if($cable->tenant->sms->balance >= ($engSmsCount * $cable->tenant->sms->price)){
					            	// Creating request for English Text
						            $engRequest = new SMSTextualRequest();
						            $engRequest->setFrom($sender);
						            $engRequest->setTo($phone2);
						            $engRequest->setText($engMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($engRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($engSmsCount * $sms->price);

						            $sms->save();
						        }

						        if($cable->tenant->sms->balance >= ($swaSmsCount * $cable->tenant->sms->price)){
						            // Creating request for Swahili Text
						            $swaRequest = new SMSTextualRequest();
						            $swaRequest->setFrom($sender);
						            $swaRequest->setTo($phone2);
						            $swaRequest->setText($swaMessage);

					        		$clientile = new SendSingleTextualSms(new BasicAuthConfiguration($username, $password));
						            $response = $clientile->execute($swaRequest);

						            $sms = TenantSMS::where('tenant_id', $cable->tenant->id)->first();

						            $sms->balance = $sms->balance - ($swaSmsCount * $sms->price);

						            $sms->save();
						        }
				            }
			    		}
			        }
			    }
			}
		}
    }
}
