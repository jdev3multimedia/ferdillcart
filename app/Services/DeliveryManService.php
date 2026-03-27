<?php

namespace App\Services;
use App\CentralLogics\Helpers;
use App\Models\DeliveryMan;
use App\Traits\FileManagerTrait;


class DeliveryManService
{
    use FileManagerTrait;

    public function getAddData(Object $request): array
    {
        if ($request->has('image')) {
            $imageName = $this->upload('delivery-man/', 'png', $request->file('image'));
        } else {
            $imageName = 'def.png';
        }

        $identityImageNames = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identityImage = $this->upload('delivery-man/', 'png', $img);
                array_push($identityImageNames, ['img'=>$identityImage, 'storage'=> Helpers::getDisk()]);
            }
            $identityImage = json_encode($identityImageNames);
        } else {
            $identityImage = json_encode([]);
        }

        if($request->referral_code){
            $referal_user = DeliveryMan::where('ref_code',$request->referral_code)->first();
            Helpers::deliverymanReferralNotification($referal_user);
        }

        return [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'vehicle_id' => $request->vehicle_id,
            'zone_id' => $request->zone_id,
            'identity_image' => $identityImage,
            'image' => $imageName,
            'active' => 0,
            'earning' => $request->earning,
            'password' => bcrypt($request->password),
            'ref_by' =>  $request->earning ? $referal_user?->id??null : null,
            'ref_code' => Helpers::generate_referer_code('deliveryman'),
        ];
    }

    public function getUpdateData(Object $request, Object $deliveryMan): array
    {
        if ($request->has('image')) {
            $imageName = $this->updateAndUpload('delivery-man/', $deliveryMan->image, 'png', $request->file('image'));
        } else {
            $imageName = $deliveryMan['image'];
        }

        $currentImages = json_decode($deliveryMan['identity_image'], true) ?? [];

        if ($request->has('delete_identity_image')) {
            foreach ($request->delete_identity_image as $delImg) {
                foreach ($currentImages as $key => $imgData) {
                    $imgName = is_array($imgData) ? $imgData['img'] : $imgData;
                    if ($imgName === $delImg) {
                        Helpers::check_and_delete('delivery-man/' , $imgData);
                        unset($currentImages[$key]);
                    }
                }
            }
            $currentImages = array_values($currentImages);
        }

        if ($request->has('identity_image')){
            foreach ($request->identity_image as $img) {
                $identityImage = $this->upload('delivery-man/', 'png', $img);
                array_push($currentImages, ['img'=>$identityImage, 'storage'=> Helpers::getDisk()]);
            }
        }

        $identityImage = json_encode($currentImages);

        return [
            "f_name" => $request->f_name,
            "l_name" => $request->l_name,
            "email" => $request->email,
            "phone" => $request->phone,
            "identity_number" => $request->identity_number,
            "vehicle_id" => $request->vehicle_id,
            "identity_type" => $request->identity_type,
            "zone_id" => $request->zone_id,
            "identity_image" => $identityImage,
            "image" => $imageName,
            "earning" => $request->earning,
            "password" => strlen($request->password)>1?bcrypt($request->password):$deliveryMan['password'],
            "application_status" => in_array($deliveryMan['application_status'], ['pending','denied']) ? 'approved' : $deliveryMan['application_status'],
            "status" => in_array($deliveryMan['application_status'], ['pending','denied']) ? 1 : $deliveryMan['status'],
        ];
    }

}
