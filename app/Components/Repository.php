<?php

namespace App\Components;

abstract class Repository
{
    /**
     * @var int|null
     */
    protected $userId = null;

    /**
     * @var int|null
     */
    protected $personId = null;

    /**
     * ID del periodo.
     *
     * @var int|null
     */
    protected $periodId = null;

    /**
     * @var int|null
     */
    protected $role = null;

    public function setUser(?array $user): void
    {
        if (!$user) {
            return;
        }

        if (key_exists('id', $user)) {
            $this->userId = to_int($user['id']);
        }

        if (key_exists('person_id', $user)) {
            $this->personId = to_int($user['person_id']);
        }

        if (key_exists('role', $user)) {
            $this->role = trim((string) $user['role']);
        }
    }

    public function setPeriod(int $periodId): void
    {
        $this->periodId = $periodId;
    }
}
