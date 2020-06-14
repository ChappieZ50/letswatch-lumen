<?php


namespace App;


use Illuminate\Support\Str;

class Helper
{
    const VISITOR_PREFIX = 'user#';

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function createHash()
    {
        return Str::uuid();
    }

    /**
     * @return float|int
     */
    public function createExpire()
    {
        return 60 * 60 * 12;
    }

    public function createVisitor()
    {
        return self::VISITOR_PREFIX . mt_rand(1, 9999);
    }
}
