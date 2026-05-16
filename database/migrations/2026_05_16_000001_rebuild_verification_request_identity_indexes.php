<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use MongoDB\Collection;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mongodb') {
            return;
        }

        $collection = DB::connection('mongodb')->getCollection('verification_requests');

        $this->dropIndexIfExists($collection, 'id_number_1');
        $this->dropIndexIfExists($collection, 'id_number_hash_1');

        $collection->createIndex(
            ['id_number_hash' => 1],
            [
                'name' => 'id_number_hash_1',
                'unique' => true,
                'partialFilterExpression' => [
                    'id_number_hash' => ['$type' => 'string'],
                ],
            ]
        );
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mongodb') {
            return;
        }

        $collection = DB::connection('mongodb')->getCollection('verification_requests');

        $this->dropIndexIfExists($collection, 'id_number_hash_1');

        $collection->createIndex(
            ['id_number' => 1],
            [
                'name' => 'id_number_1',
                'unique' => true,
                'partialFilterExpression' => [
                    'id_number' => ['$type' => 'string'],
                ],
            ]
        );
    }

    private function dropIndexIfExists(Collection $collection, string $name): void
    {
        foreach ($collection->listIndexes() as $index) {
            if ($index->getName() === $name) {
                $collection->dropIndex($name);
                return;
            }
        }
    }
};
