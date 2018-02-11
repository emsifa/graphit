<?php

namespace Example\App\Repositories;

class DummyUsersRepository
{

    protected $users = [
        [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com',
            'password' => 's3cr3t',
            'role' => 'admin',
            'created_at' => '2009-10-11 12:13:14',
            'updated_at' => '2010-11-12 13:14:15',
            'deleted_at' => null,
        ],
        [
            'id' => 2,
            'name' => 'Jane Doe',
            'email' => 'janedoe@mail.com',
            'password' => 's3cr3t',
            'role' => 'member',
            'created_at' => '2011-11-11 11:11:11',
            'updated_at' => '2012-12-12 12:12:12',
            'deleted_at' => null,
        ]
    ];

    public function all()
    {
        return $this->users; 
    }

    public function findById($id)
    {
        $users = array_filter($this->users, function ($user) use ($id) {
            return $user['id'] == $id;
        });

        return count($users) ? $users[0] : null;
    }

    public function create(array $data)
    {
        return array_merge($data, [
            'id' => count($this->users) + 1,
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null,
        ]);
    }

}