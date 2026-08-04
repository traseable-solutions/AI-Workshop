<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'manager_id', 'level', 'tpf', 'position', 'duty_station'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /** Days of leave everyone is granted per year. */
    const ANNUAL_LEAVE_DAYS = 31;

    /** Leave encashment package amount, by employee level range. */
    const LEAVE_PACKAGES = [
        ['min' => 1, 'max' => 5, 'amount' => 8000],
        ['min' => 6, 'max' => 12, 'amount' => 10000],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function manager(): ?self
    {
        return $this->manager_id ? self::find($this->manager_id) : null;
    }

    /** @return HasMany<User, $this> */
    public function employees(): HasMany
    {
        return $this->hasMany(self::class, 'manager_id');
    }

    /** @return HasMany<LeaveRequest, $this> */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function remainingLeaveBalance(): int
    {
        $usedDays = $this->leaveRequests()
            ->where('status', 'approved')
            ->get()
            ->sum(fn (LeaveRequest $request) => $request->daysRequested());

        return self::ANNUAL_LEAVE_DAYS - $usedDays;
    }

    public function leavePackageAmount(): int
    {
        foreach (self::LEAVE_PACKAGES as $package) {
            if ($this->level >= $package['min'] && $this->level <= $package['max']) {
                return $package['amount'];
            }
        }

        return 0;
    }
}
