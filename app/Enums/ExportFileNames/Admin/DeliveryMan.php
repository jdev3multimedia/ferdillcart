<?php

namespace App\Enums\ExportFileNames\Admin;

enum DeliveryMan
{
    const EXPORT_CSV = 'DeliveryMans.csv';
    const EXPORT_XLSX = 'DeliveryMans.xlsx';
    const REVIEW_EXPORT_CSV = 'DeliveryManReviews.csv';
    const REVIEW_EXPORT_XLSX = 'DeliveryManReviews.xlsx';
    const LOYALTY_POINT_EXPORT_CSV = 'DeliveryManLoyaltyPoints.csv';
    const LOYALTY_POINT_EXPORT_XLSX = 'DeliveryManLoyaltyPoints.xlsx';
    const REFERRAL_EARN_EXPORT_CSV = 'DeliveryManReferralEarn.csv';
    const REFERRAL_EARN_EXPORT_XLSX = 'DeliveryManReferralEarn.xlsx';
}
