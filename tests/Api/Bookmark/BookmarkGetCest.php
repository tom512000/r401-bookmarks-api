<?php

namespace App\Tests\Api\Bookmark;

use App\Entity\Bookmark;
use App\Factory\BookmarkFactory;
use App\Tests\Support\ApiTester;

class BookmarkGetCest
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

    public function getBookmarkDetail(ApiTester $I): void
    {
        // 1. 'Arrange'
        $data = [
            'name' => 'My bookmark',
            'description' => 'Bookmark description',
            'creationDate' => new \DateTimeImmutable('now'),
            'isPublic' => true,
            'url' => 'https://example.com/mybookmark#fragment',
        ];
        BookmarkFactory::createOne($data);

        // 2. 'Act'
        $I->sendGet('/api/bookmarks/1');

        // 3. 'Assert'
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseIsAnEntity(Bookmark::class, '/api/bookmarks/1');
        // Transform Date to W3C date string ("Y-m-d\\TH:i:sP")
        $data['creationDate'] = $data['creationDate']->format(\DateTimeInterface::W3C);
        $I->seeResponseIsAnItem(self::expectedProperties(), $data);
    }
}
