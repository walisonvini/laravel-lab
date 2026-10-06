<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    /**
     * Create a user and the customer profile associated with it.
     *
     * @param  array{name: string, email: string, password: string, document: string, phone?: string|null, birth_date?: string|null}  $data
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data): Customer {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $customer = $user->customer()->create([
                'document' => $data['document'],
                'phone' => $data['phone'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
            ]);

            return $customer->setRelation('user', $user);
        });
    }
}
