<?php

namespace App\EntityListener;

use App\Entity\Account;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountListener {

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher) {
        $this->hasher = $hasher;
    }

    public function prePersist(Account $account) {
        $this->encodePassword($account);
    }

    public function preUpdate(Account $account) {
        $this->encodePassword($account);
    }
    /**
     * Encode password based on plainPassword
     */
    public function encodePassword(Account $account) {

        if ($account->getPlainPassword() === null) {
            return;
        }

        $account->setPassword(
            $this->hasher->hashPassword(
                $account,
                $account->getPlainPassword()
            )
        );

        $account->setPlainPassword(null);
    }
}