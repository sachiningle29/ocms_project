<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HiringContract;
use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotificationCreated;
use Illuminate\Support\Carbon;

class SendHiringAlerts extends Command
{
    protected $signature = 'notifications:check-hiring';
    protected $description = 'Send notifications for overdue hiring contracts based on norm dates';

    public function handle()
    {
        // Norm fields to compare (key = norm date field, value = corresponding actual date field)
        $dateFields = [
            'reqmt_recd_norm_date' => 'reqmt_recd_date_actual_date',
            'case_initiation_norm_date' => 'case_initiation_date_actual_date',
            'aa_norm_date' => 'aa_date_actual_date',
            'sanction_norm_date' => 'sanction_date_actual_date',
            'indent_norm_date' => 'indent_date_actual_date',
            'nit_date_norm_date' => 'nit_date_actual_date',
            'tbo_date_norm_date' => 'tbo_date_actual_date',
            'pbo_date_norm_date' => 'pbo_date_actual_date',
            'noa_po_date_norm_date' => 'noa_po_date_actual_date',
            'delivery_date_norm_date' => 'delivery_date_actual_date',
            'contract_start_date_norm_date' => 'contract_start_date_actual_date',
            'contract_end_date_norm_date' => 'contract_end_date_actual_date',
        ];

        $today = now()->startOfDay();
        $admins = User::where('is_admin', 1)->get();
        $alertsSent = 0;

        // Loop through all contracts
        HiringContract::chunk(50, function ($contracts) use ($dateFields, $today, $admins, &$alertsSent) {
            foreach ($contracts as $contract) {
                foreach ($dateFields as $normField => $actualField) {
                    $normDate = $contract->$normField;
                    $actualDate = $contract->$actualField;

                    if ($normDate && Carbon::parse($normDate)->lt($today) && !$actualDate) {
                        // Check if notification already exists
                        $exists = Notification::where('type', 'hiring_contract_alert')
                            ->where('model_id', $contract->id)
                            ->where('message', 'like', "%{$normField}%")
                            ->exists();

                        if (!$exists) {
                            $notification = Notification::create([
                                'title' => 'Overdue Hiring Contract',
                                'message' => "Contract ID {$contract->rid} missed its norm date for {$normField}.",
                                'type' => 'hiring_contract_alert',
                                'model_id' => $contract->id,
                            ]);

                            foreach ($admins as $admin) {
                                $notification->users()->attach($admin->id, ['read' => false]);
                                event(new NewNotificationCreated($notification, $admin->id));
                            }

                            $alertsSent++;
                        }

                        break; // One overdue norm date is enough per contract
                    }
                }
            }
        });

        $this->info("Hiring contract alerts sent: $alertsSent");
    }
}
