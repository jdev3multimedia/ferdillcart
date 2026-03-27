<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */


    protected $name;
    protected $url;

    public function __construct($name,$url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company_name = BusinessSetting::where('key', 'business_name')->first()->value;
        $data=EmailTemplate::where('type','store')->where('email_type', 'subscription-successful')->first();

        $template=$data?$data->email_template:5;
        $url = $this->url;
        $store_name = $this->name;
        $title = Helpers::text_variable_data_format( value:$data['title']??'',store_name:$store_name??'');
        $body = Helpers::text_variable_data_format( value:$data['body']??'',store_name:$store_name??'');
        $footer_text = Helpers::text_variable_data_format( value:$data['footer_text']??'',store_name:$store_name??'');
        $copyright_text = Helpers::text_variable_data_format( value:$data['copyright_text']??'',store_name:$store_name??'');
        return $this->subject(translate('Subscription_successful'))->view('email-templates.new-email-format-'.$template, ['company_name'=>$company_name,'data'=>$data,'title'=>$title,'body'=>$body,'footer_text'=>$footer_text,'copyright_text'=>$copyright_text,'url'=>$url,'type'=> 'invoice']);
    }
}
