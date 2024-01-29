<?php

namespace App\Tests\Api\Bookmark;

use App\Entity\Bookmark;
use App\Factory\BookmarkFactory;
use App\Tests\Support\ApiTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;

class BookmarkGetCollectionCest
{
    protected static function expectedProperties(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'creationDate' => 'string:date',
            'isPublic' => 'boolean',
            'url' => 'string:url',
        ];
    }

    public function getBookmarkCollection(ApiTester $I): void
    {
        // 1. 'Arrange'
        BookmarkFactory::createMany(4, ['isPublic' => 1]);

        // 2. 'Act'
        $I->sendGet('/api/bookmarks');

        // 3. 'Assert'
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseIsACollection(Bookmark::class, '/api/bookmarks', [
            'hydra:member' => 'array',
            'hydra:totalItems' => 'integer',
            'hydra:search' => 'array',
        ]);
        $I->seeResponseContainsJson([
            'hydra:totalItems' => 4,
        ]);
        $I->seeResponseMatchesJsonType(self::expectedProperties(), '$["hydra:member"][0]');
    }

    #[DataProvider('providerGetFilteredBookmarkCollection')]
    public function getFilteredBookmarkCollection(ApiTester $I, Example $example): void
    {
        // 1. 'Arrange'
        BookmarkFactory::createSequence([
            [ // 'id' => 1,
                'name' => 'Ddddddda',
                'description' => 'keyword1 keyword2',
                'creationDate' => new \DateTimeImmutable('2022-04-14 10:00:00'),
                'isPublic' => 0,
            ],
            [ // 'id' => 2,
                'name' => 'Bbbbbbb',
                'description' => 'keyword1',
                'creationDate' => new \DateTimeImmutable('2022-04-14 09:00:00'),
                'isPublic' => 1,
            ],
            [ // 'id' => 3,
                'name' => 'Aaaaaaa',
                'description' => 'keyword3',
                'creationDate' => new \DateTimeImmutable('2022-04-15 09:00:00'),
                'isPublic' => 0,
            ],
            [ // 'id' => 4,
                'name' => 'Ccccccc',
                'description' => 'none',
                'creationDate' => new \DateTimeImmutable('2021-04-14 11:00:00'),
                'isPublic' => 1,
            ],
        ]);

        // 2. 'Act'
        $I->sendGet('/api/bookmarks?'.$example['queryString']);

        // 3. 'Assert'
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $jsonResponse = $I->grabJsonResponse();
        $I->assertArrayHasKey('hydra:member', $jsonResponse);
        $collection = $jsonResponse['hydra:member'];
        $I->assertCount(count($example['expectedIds']), $collection);
        $I->assertSame($example['expectedIds'], array_map(fn ($bookmark) => $bookmark['id'], $collection));
    }

    protected function providerGetFilteredBookmarkCollection(): array
    {
        return [
            [
                'queryString' => '',
                'expectedIds' => [3, 2, 4, 1],
            ],
            [
                'queryString' => 'order[name]=desc',
                'expectedIds' => [1, 4, 2, 3],
            ],
            [
                'queryString' => 'order[creationDate]=asc',
                'expectedIds' => [4, 2, 1, 3],
            ],
            [
                'queryString' => 'order[creationDate]=desc',
                'expectedIds' => [3, 1, 2, 4],
            ],
            [
                'queryString' => 'isPublic=true',
                'expectedIds' => [2, 4],
            ],
            [
                'queryString' => 'isPublic=false',
                'expectedIds' => [3, 1],
            ],
            [
                'queryString' => 'description=word',
                'expectedIds' => [3, 2, 1],
            ],
            [
                'queryString' => 'description=keyword1',
                'expectedIds' => [2, 1],
            ],
            [
                'queryString' => 'name=aa',
                'expectedIds' => [3],
            ],
            [
                'queryString' => 'name=a',
                'expectedIds' => [3, 1],
            ],
            [
                'queryString' => 'name=a&description=keyword3',
                'expectedIds' => [3],
            ],
        ];
    }
}
