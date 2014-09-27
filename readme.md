# TestDummy [![Build Status](https://travis-ci.org/laracasts/TestDummy.svg?branch=master)](https://travis-ci.org/laracasts/TestDummy)

![image](https://dl.dropboxusercontent.com/u/774859/GitHub-Repos/testdummy/crashtestdummy.jpg)

TestDummy makes the process of preparing factories (dummy data) for your integration tests as easy as possible.

> [Want a video introduction?](http://player.vimeo.com/external/92517786.hd.mp4?s=3a8c7522b63a063f6a9176ddfe0030e8)

### Make a Post entity with dummy attributes.

```php
use Laracasts\TestDummy\Factory;

$post = Factory::build('Post');
```

If we then do `$post->toArray()`, this might return:

```bash
array(4) {
  ["title"]=>
  string(21) "The Title of the Post"
  ["author_id"]=>
  string(1) "5"
  ["body"]=>
  string(226) "Iusto qui optio et iste. Cumque aliquid et omnis enim. Nesciunt ad esse a reiciendis expedita quidem veritatis. Nostrum repellendus reiciendis distinctio amet sapiente. Eum molestias a recusandae modi aut et adipisci corrupti."
  ["publish_date"]=>
  string(19) "2014-03-02 11:05:48"
}
```

### Build a post, but override the default title

```php
use Laracasts\TestDummy\Factory;

Factory::build('Post', ['title' => 'Override Title']);
```

Again, when cast to an array...

```bash
array(4) {
  ["title"]=>
  string(14) "Override Title"
  ["author_id"]=>
  string(1) "5"
  ["body"]=>
  string(254) "In eos porro qui est rerum possimus voluptatem non. Repudiandae eaque nostrum eaque aut deleniti possimus quod minus. Molestiae commodi odit sunt dignissimos corrupti repudiandae quibusdam quo. Autem maxime tenetur autem corporis aut quis sint occaecati."
  ["publish_date"]=>
  string(19) "2013-06-24 10:01:30"
}
```

### Build a song entity, and save it to the database.

```php
use Laracasts\TestDummy\Factory;

$song = Factory::create('Song');
```

If the `songs` table has relationships - like `album_id`, then the relationship entities will be built and saved, too.

### Create and persist a comment three times

```php
use Laracasts\TestDummy\Factory;

Factory::times(3)->create('Comment');
```

In effect, this will give you three rows in your `comments` table. Again, if that table has relationships, those rows will be created with dummy data as well.

## Usage

### Step 1: Install

Pull this package in through Composer, per usual:

```js
"require-dev": {
    "laracasts/testdummy": "1.*"
}
```

### Step 2: Create a fixtures.yml file

TestDummy will fetch the default attributes for each of your entities from a `fixtures.yml` file that you place in your tests directory. If using Laravel, this file may be added anywhere under the `app/tests`
directory. Here's an example of a `Post` and an `Author`.

```yaml
Post:
  title: Hello World $string
  body: $text
  published_at: $date
  author_id:
    type: Author

Author:
    name: John Doe $integer
```

Take note that the object name should correspond to your full namespaced model. For instance, if you use a `Models` namespace, then do:

```yaml
Models\Post:
  title: Hello World $string
```

#### Dynamic Values

Also, notice that we can use a number of dynamic values here:

- `$string`: A simple placeholder word
- `$text`: A paragraph of dummy text
- `$date`: A date in the format of "Y-m-d H:i:s', suitable for database timestamps
- `$integer`: Any unique number
- `$digit`: A random number from 0 to 9
- `$boolean`: A boolean as an integer

Here are some usage examples:

```yaml
Post:
    title: Some Post: Number $integer
    body: $text
    publish_date: $date
    keywords: $string $string $string
    active: $boolean
```

This might return something similar to:

```bash
array(4) {
  ["title"]=>
  string(19) "Some Post: Number 1"
  ["body"]=>
  string(255) "Laborum rerum saepe et et voluptatem rerum. Debitis reiciendis dolores perferendis fugit. Et impedit sit reprehenderit quisquam. Dolor enim et quia. Excepturi rerum esse rerum amet omnis modi. Sint molestiae consequatur dolore omnis soluta minima tempora."
  ["publish_date"]=>
  string(19) "2013-05-05 20:33:12"
  ["keywords"]=>
  string(30) "consequatur provident pariatur"
  ["active"]=>
  int(1)
}
```

However, in addition to those, TestDummy will recognize all supported [Faker](https://github.com/fzaninotto/Faker) properties. As such, we can do things like:

```
Profile:
    username: $userName
    company: $company
    password: $password
```

Refer to the Faker docs for a massive list of options.

#### Relationships

Pay attention to how we reference relationship types:

```yaml
Post:
  title: Some Post: Number $integer
  author_id:
    type: Author
```

You need to let TestDummy know the type of its associated model. TestDummy will then automatically build and save that relationship as well.

> This means that, when you do, say, `Factory::create('Song')`, if one of your columns references a `album_id`, then TestDummy will save an `Album` record to the database, too. This
will continue recursively. So, if the `Album` has an `artist_id`, then an artist will also be created.

### Step 3: Setup

When testing against a database, it's recommended that each test works with the exact same database environment and structure. That way, you can protect yourself against false positives.
A SQLite database (maybe even one in memory) is a good choice in these cases.

```php
public function setUp()
{
    parent::setUp();

    Artisan::call('migrate');
}
```

Or, if a DB in memory isn't possible, to save a bit of time, a helper `Laracasts\TestDummy\DbTestCase` class is included with this package. If you extend it,
before each test, your test DB will be migrated (if necessary), and all DB modifications will be channelled through a transaction, and then rolled back on `tearDown`. This will give you a speed boost, and ensure that all tests start with the same database structure.

```php

use Laracasts\TestDummy\DbTestCase;

class ExampleTest extends DbTestCase {

    /** @test */
    function it_does_something()
    {
        // Before each test, your database will be rolled back
    }
}
```

### Step 4: Write Your Tests

You're all set to go now. Start testing! Here's some code to get you started. Assuming that you have a `Post` and `Comment` model created...

```php

use Laracasts\TestDummy\Factory;

$comment = Factory::create('Comment');
```

This will create and save both a `Comment`, as well as a `Post` record to the database.

Or, maybe you need to write a test to ensure that, if you have three songs with their respective lengths, when you call a `getTotalLength` method on the owning `Album` model, it will return the correct value. That's easy!

```php
// create three songs, and explicitly set the length
Factory::times(3)->create('Song', ['length' => 200]);

$album = Album::first(); // this will be created once automatically.

$this->assertEquals(600, $album->getTotalLength());
```


