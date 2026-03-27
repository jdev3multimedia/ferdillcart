<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $wallet;
    protected $status;
    protected $mail_type;

    public function __construct($status,$wallet,$mail_type='store')
    {
        $this->wallet = $wallet;
        $this->status = $status;
        $this->mail_type = $mail_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company_name = BusinessSetting::where('key', 'business_name')->first()->value;
        $status = $this->status;
        if($status == 'approved'){
            $data=EmailTemplate::where('type',$this->mail_type)->where('email_type', 'withdraw_approve')->first();
        }elseif($status == 'denied'){
            $data=EmailTemplate::where('type',$this->mail_type)->where('email_type', 'withdraw_deny')->first();
        }else{
            $data=EmailTemplate::where('type','admin')->where('email_type', 'withdraw_request')->first();
        }
        $template=$data?$data->email_template:6;
        $wallet = $this->wallet;
        $store_name = $wallet->vendor->f_name;
        $delivery_man_name = $wallet?->deliveryman?->f_name .' '.$wallet?->deliveryman?->l_name;
        $title = Helpers::text_variable_data_format( value:$data['title']??'',delivery_man_name:$delivery_man_name??'',store_name:$store_name??'',transaction_id:$transaction_id??'');
        $body = Helpers::text_variable_data_format( value:$data['body']??'',delivery_man_name:$delivery_man_name??'',store_name:$store_name??'',transaction_id:$transaction_id??'');
        $footer_text = Helpers::text_variable_data_format( value:$data['footer_text']??'',delivery_man_name:$delivery_man_name??'',store_name:$store_name??'',transaction_id:$transaction_id??'');
        $copyright_text = Helpers::text_variable_data_format( value:$data['copyright_text']??'',delivery_man_name:$delivery_man_name??'',store_name:$store_name??'',transaction_id:$transaction_id??'');
        return $this->subject(translate('Withdraw_Request'))->view('email-templates.new-email-format-'.$template, ['company_name'=>$company_name,'data'=>$data,'title'=>$title,'body'=>$body,'footer_text'=>$footer_text,'copyright_text'=>$copyright_text,'wallet'=>$wallet,'transaction_id'=>$wallet->id,'time'=>$wallet->created_at,'amount'=>$wallet->amount]);
    }
}
