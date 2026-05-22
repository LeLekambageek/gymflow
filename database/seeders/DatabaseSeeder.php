<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Coach;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Comptes utilisateurs ─────────────────────────────────────
        User::create([
            'name'     => 'Propriétaire',
            'email'    => 'owner@gymflow.sn',
            'password' => Hash::make('password'),
            'role'     => 'owner',
        ]);

        User::create([
            'name'     => 'Gérant Salle',
            'email'    => 'manager@gymflow.sn',
            'password' => Hash::make('password'),
            'role'     => 'manager',
        ]);

        // ── Plans tarifaires FIXES (comme demandé) ───────────────────
        $plans = [
            [
                'name'          => 'Mensuel avec tapis',
                'description'   => 'Accès salle 1 mois + utilisation des tapis',
                'price'         => 20000,
                'duration_days' => 30,
                'is_active'     => true,
            ],
            [
                'name'          => 'Mensuel sans tapis',
                'description'   => 'Accès salle 1 mois — sans tapis',
                'price'         => 15000,
                'duration_days' => 30,
                'is_active'     => true,
            ],
            [
                'name'          => 'Journalier avec tapis',
                'description'   => 'Accès 1 journée + utilisation des tapis',
                'price'         => 2000,
                'duration_days' => 1,
                'is_active'     => true,
            ],
            [
                'name'          => 'Journalier sans tapis',
                'description'   => 'Accès 1 journée — sans tapis',
                'price'         => 1500,
                'duration_days' => 1,
                'is_active'     => true,
            ],
        ];

        $planModels = [];
        foreach ($plans as $p) {
            $planModels[] = SubscriptionPlan::create($p);
        }

        // ── Coachs ──────────────────────────────────────────────────
        $coaches = [
            ['first_name' => 'Mamadou',  'last_name' => 'Diallo', 'email' => 'mamadou@gymflow.sn', 'speciality' => 'Musculation', 'hourly_rate' => 8000, 'phone' => '+221 77 100 00 01'],
            ['first_name' => 'Fatou',    'last_name' => 'Sarr',   'email' => 'fatou@gymflow.sn',   'speciality' => 'Yoga',         'hourly_rate' => 7000, 'phone' => '+221 76 100 00 02'],
        ];
        $coachModels = [];
        foreach ($coaches as $c) {
            $coachModels[] = Coach::create(array_merge($c, ['status' => 'active']));
        }

        // ── Cours ───────────────────────────────────────────────────
        $course = Course::create([
            'name' => 'CrossFit Matinal', 'category' => 'Cardio',
            'capacity' => 20, 'duration_minutes' => 60,
            'price' => 0, 'room' => 'Salle A',
            'coach_id' => $coachModels[0]->id, 'status' => 'active',
        ]);

        // Séances sur 5 jours
        for ($i = 0; $i < 5; $i++) {
            CourseSession::create([
                'course_id'        => $course->id,
                'start_time'       => Carbon::today()->addDays($i)->setHour(7),
                'end_time'         => Carbon::today()->addDays($i)->setHour(8),
                'registered_count' => rand(5, 18),
                'status'           => 'scheduled',
            ]);
        }

        // ── Membres de démo ─────────────────────────────────────────
        $memberData = [
            ['Amadou',   'Diop',     '+221 77 123 45 67', 0],
            ['Aïssatou', 'Fall',     '+221 76 234 56 78', 1],
            ['Ousmane',  'Ndiaye',   '+221 78 345 67 89', 0],
            ['Mariama',  'Cissé',    '+221 77 456 78 90', 1],
            ['Seydou',   'Touré',    '+221 76 567 89 01', 2],
            ['Rokhaya',  'Sow',      '+221 78 678 90 12', 3],
            ['Modou',    'Gueye',    '+221 77 789 01 23', 0],
            ['Ndéye',    'Mbaye',    '+221 76 890 12 34', 1],
            ['Alioune',  'Ba',       '+221 78 901 23 45', 2],
            ['Fatimata', 'Coulibaly','+221 77 012 34 56', 0],
        ];

        foreach ($memberData as [$fn, $ln, $phone, $planIndex]) {
            $member = Member::create([
                'first_name' => $fn,
                'last_name'  => $ln,
                'phone'      => $phone,
                'email'      => strtolower(Str::ascii($fn).'.'.$ln.'@demo.sn'),
                'status'     => 'active',
                'qr_code'    => Str::uuid(),
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
            ]);

            $plan  = $planModels[$planIndex];
            $start = Carbon::now()->subDays(rand(0, 20));

            $sub = Subscription::create([
                'member_id'            => $member->id,
                'subscription_plan_id' => $plan->id,
                'start_date'           => $start,
                'end_date'             => $start->copy()->addDays($plan->duration_days),
                'status'               => 'active',
                'amount_paid'          => $plan->price,
                'payment_method'       => ['cash', 'mobile', 'card'][rand(0, 2)],
            ]);

            // Paiement correspondant
            Payment::create([
                'member_id'       => $member->id,
                'subscription_id' => $sub->id,
                'amount'          => $plan->price,
                'type'            => 'subscription',
                'method'          => $sub->payment_method,
                'status'          => 'paid',
                'payment_date'    => $start,
            ]);

            // Historique — 2 à 4 mois précédents
            for ($i = 1; $i <= rand(2, 4); $i++) {
                $pastStart = $start->copy()->subMonths($i);
                $pastSub   = Subscription::create([
                    'member_id'            => $member->id,
                    'subscription_plan_id' => $plan->id,
                    'start_date'           => $pastStart,
                    'end_date'             => $pastStart->copy()->addDays($plan->duration_days),
                    'status'               => 'expired',
                    'amount_paid'          => $plan->price,
                    'payment_method'       => $sub->payment_method,
                ]);
                Payment::create([
                    'member_id'       => $member->id,
                    'subscription_id' => $pastSub->id,
                    'amount'          => $plan->price,
                    'type'            => 'subscription',
                    'method'          => $sub->payment_method,
                    'status'          => 'paid',
                    'payment_date'    => $pastStart,
                ]);
            }
        }
    }
}
