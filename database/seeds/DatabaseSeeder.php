<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 执行的先后顺序不能变
        $this->call(UsersTableSeeder::class); // 先有用户
        $this->call(TopicsTableSeeder::class); // 用户发帖
        $this->call(ReplysTableSeeder::class); // 评论（回复）帖子
        $this->call(LinksTableSeeder::class); // 资源链接
    }
}
