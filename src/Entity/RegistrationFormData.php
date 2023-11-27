<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Account;

class RegistrationFormData
{
    private Account $account;
    private User $user;

    public function getAccount(): Account
    {
        return $this->account;
    }
    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
