<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LargeOrderSeeder extends Seeder
{
    public function run()
    {
        // $this->command->info('Starting large order data seeding...');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // DB::table('orders')->truncate();
        // DB::table('order_taxes')->truncate();

        // $this->seedOrders();

        //  $this->seedOrderTaxes();

        //  DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // $this->command->info('Large order data seeding completed!');
    }

    protected function seedOrders()
    {
        $batchSize = 5000;
        $totalOrders = 1000000;
        $batches = ceil($totalOrders / $batchSize);
        $startingOrderId = 1000000;

        $this->command->info("Seeding $totalOrders orders starting from ID $startingOrderId in $batches batches...");

        $orderStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatuses = ['unpaid', 'paid', 'partial'];
        $paymentMethods = ['cash_on_delivery', 'credit_card', 'paypal', 'bank_transfer'];
        $addressTypes = ['Delivery', 'Pickup', 'Shipping'];

        for ($i = 1; $i <= $batches; $i++) {
            $orders = [];
            $startOrderId = $startingOrderId + (($i - 1) * $batchSize);
            $endOrderId = min($startingOrderId + ($i * $batchSize) - 1, $startingOrderId + $totalOrders - 1);

            for ($j = $startOrderId; $j <= $endOrderId; $j++) {
                $createdAt = Carbon::now()->subDays(rand(0, 365))->subHours(rand(0, 24));

                $orders[] = [
                    'id' => $j,
                    'user_id' => rand(1, 10000),
                    'store_id' => 3,
                    'module_id' => 3,
                    'order_amount' => rand(10000, 500000) / 100,
                    'total_tax_amount' => rand(10, 5000) / 100,
                    'delivery_address' => json_encode([
                        'contact_person_name' => 'Customer ' . $j,
                        'contact_person_number' => '01' . rand(10000000, 99999999),
                        'address_type' => $addressTypes[array_rand($addressTypes)],
                        'address' => 'Address ' . $j,
                        'longitude' => rand(90000000, 91000000) / 1000000,
                        'latitude' => rand(23000000, 24000000) / 1000000
                    ]),
                    'order_status' => $orderStatuses[array_rand($orderStatuses)],
                    'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            DB::table('orders')->insert($orders);
            $this->command->info("Inserted batch $i/$batches (Orders $startOrderId-$endOrderId)");
        }
    }

    protected function seedOrderTaxes()
    {
        $batchSize = 1000;
        $totalTaxes = 10000000;
        $batches = ceil($totalTaxes / $batchSize);

        $this->command->info("Seeding $totalTaxes order taxes in $batches batches...");

        $taxTypes = ['GST', 'VAT', 'Service Tax', 'Sales Tax'];

        for ($i = 1; $i <= $batches; $i++) {
            $taxes = [];
            $startId = ($i - 1) * $batchSize + 1;
            $endId = min($i * $batchSize, $totalTaxes);

            for ($j = $startId; $j <= $endId; $j++) {
                $orderId = rand(1000000, 1100000);
                $taxId = rand(1, 4);
                $taxAmount = rand(100, 2000) / 100;
                $createdAt = Carbon::now()->subDays(rand(0, 365))->subHours(rand(0, 24));

                $taxes[] = [
                    'id' => $j,
                    'tax_name' => $taxTypes[$taxId - 1],
                    'tax_type' => 'category_wise',
                    'tax_on' => 'basic',
                    'tax_amount' => $taxAmount,
                    'tax_id' => $taxId,
                    'order_id' => $orderId,
                    'store_id' => 3,
                    'system_tax_setup_id' => 1,
                    'taxable_type' => 'App\\Models\\Order',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
            foreach (array_chunk($taxes, 100) as $chunk) {
                DB::table('order_taxes')->insert($chunk);
            }

            $this->command->info("Inserted batch $i/$batches (Taxes $startId-$endId)");
        }
    }
}
