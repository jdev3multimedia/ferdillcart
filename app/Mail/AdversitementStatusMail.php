<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdversitementStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $store_name,$email_type,$add_id;

    public function __construct($store_name,$email_type,$add_id = null)
    {
        $this->store_name = $store_name;
        $this->email_type = $email_type;
        $this->add_id = $add_id;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $company_name = BusinessSetting::where('key', 'business_name')->first()->value;



        $data=EmailTemplate::where('type','store')->where('email_type', $this->email_type)->first();
        $template=$data?$data->email_template:11;
        $store_name = $this->store_name;
        $add_id = $this->add_id;

        if($this->email_type == 'advertisement_pause'){
            $subject=translate('your_adversement_has_been_paused');
        }
        elseif ($this->email_type == 'advertisement_approved'){
            $subject=translate('your_adversement_is_approved');
        }
        elseif ($this->email_type == 'advertisement_create'){
            $subject=translate('your_adversement_is_Created_By_Admin');
        }
        elseif ($this->email_type == 'advertisement_deny'){
            $subject=translate('your_adversement_is_denied');
        }
        elseif ($this->email_type == 'advertisement_resume'){
            $subject=translate('your_adversement_is_resumed');
        }
        else{
            $subject=translate('your_adversement');

        }


        $title = Helpers::text_variable_data_format( value:$data['title']??'',user_name:$user_name??'',store_name:$store_name??'',delivery_man_name:$delivery_man_name??'',order_id:$order_id??'',add_id: $add_id );
        $body = Helpers::text_variable_data_format( value:$data['body']??'',user_name:$user_name??'',store_name:$store_name??'',delivery_man_name:$delivery_man_name??'',order_id:$order_id??'',add_id: $add_id );
        $footer_text = Helpers::text_variable_data_format( value:$data['footer_text']??'',user_name:$user_name??'',store_name:$store_name??'',delivery_man_name:$delivery_man_name??'',order_id:$order_id??'',add_id: $add_id );
        $copyright_text = Helpers::text_variable_data_format( value:$data['copyright_text']??'',user_name:$user_name??'',store_name:$store_name??'',delivery_man_name:$delivery_man_name??'',order_id:$order_id??'',add_id: $add_id );

        return $this->subject($subject)->view('email-templates.new-email-format-'.$template, ['company_name'=>$company_name,'data'=>$data,'title'=>$title,'body'=>$body,'footer_text'=>$footer_text,'copyright_text'=>$copyright_text]);
    }

}
