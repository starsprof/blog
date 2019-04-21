<?php


namespace App\Models\Repositories;


use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?User;

    /**
     * @param int[] $ids
     * @return User[]
     */
    public function findManyByIds($ids = array()): array;

    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool;

    /**
     * @param User $user
     * @return User
     */
    public function create(User $user): User;

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function checkAuth(string $email, string $password): ?User;

    /**
     * @param string $email
     * @return bool
     */
    public function checkEmailAvailability(string $email): bool;

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool;

}